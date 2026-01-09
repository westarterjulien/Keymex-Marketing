<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use MongoDB\Client;
use MongoDB\Database;

class MongoPropertyService
{
    protected $connection = 'mongodb';
    protected $collection = 'sale_files';

    /**
     * Cache des photos par numéro de compromis
     */
    protected array $photosCache = [];

    /**
     * Instance MongoDB (singleton)
     */
    protected static ?Database $mongoDb = null;

    /**
     * Cache des compromis préchargés (durée: 10 minutes)
     */
    protected const COMPROMIS_CACHE_KEY = 'kpi_all_compromis_data';
    protected const COMPROMIS_CACHE_TTL = 600; // 10 minutes

    /**
     * Cache des mandats exclusifs préchargés (durée: 10 minutes)
     */
    protected const MANDATS_EXCLU_CACHE_KEY = 'kpi_all_mandats_exclusifs_data';
    protected const MANDATS_EXCLU_CACHE_TTL = 600; // 10 minutes

    /**
     * Obtient l'instance MongoDB (singleton)
     */
    protected function getMongoDb(): Database
    {
        if (self::$mongoDb === null) {
            $dsn = config('database.connections.mongodb.dsn');
            $database = config('database.connections.mongodb.database');
            $client = new Client($dsn);
            self::$mongoDb = $client->selectDatabase($database);
        }
        return self::$mongoDb;
    }

    /**
     * Précharge TOUS les compromis en un seul appel MongoDB
     * et les met en cache pour 10 minutes
     * Retourne un tableau de compromis avec leurs infos
     */
    protected function getAllCompromisData(): array
    {
        return Cache::remember(self::COMPROMIS_CACHE_KEY, self::COMPROMIS_CACHE_TTL, function () {
            $agencySlug = config('keymex.agence.slug', 'keymex-synergie');
            $db = $this->getMongoDb();
            $properties = $db->selectCollection('properties');

            $propertiesWithCompromis = $properties->find([
                'agency_slug' => $agencySlug,
                'raw_data.compromis.0' => ['$exists' => true],
            ]);

            $allCompromis = [];

            foreach ($propertiesWithCompromis as $property) {
                $raw = (array) ($property['raw_data'] ?? []);
                $compromisArray = (array) ($raw['compromis'] ?? []);
                $suiviPar = (array) ($raw['suivi_par'] ?? []);

                $advisorId = (int) ($suiviPar['id'] ?? 0);
                $advisorName = trim(($suiviPar['firstname'] ?? '') . ' ' . ($suiviPar['lastname'] ?? ''));
                if (!$advisorName || $advisorName === ' ' || !$advisorId) {
                    $advisorName = 'Non attribué';
                    $advisorId = 0;
                }

                foreach ($compromisArray as $compromis) {
                    $compromis = (array) $compromis;
                    $status = (array) ($compromis['status'] ?? []);
                    $statusText = $status['text'] ?? '';

                    // Ignorer si annulé
                    $dateAnnulation = $compromis['date_annulation'] ?? null;
                    if ($dateAnnulation && $dateAnnulation !== '0000-00-00' && $dateAnnulation !== '') {
                        continue;
                    }

                    // Seulement les statuts valides
                    if (!in_array($statusText, ['Compromis', "Vendu par l'agence"])) {
                        continue;
                    }

                    $dateCompromis = $compromis['date_compromis'] ?? null;
                    if (!$dateCompromis || $dateCompromis === '0000-00-00') {
                        continue;
                    }

                    // Extraire la date (format Y-m-d)
                    $datePart = preg_replace('/[T ].*$/', '', $dateCompromis);

                    $allCompromis[] = [
                        'date' => $datePart,
                        'status' => $statusText,
                        'seller_fees' => (float) ($compromis['seller_fees'] ?? 0),
                        'buyer_fees' => (float) ($compromis['buyer_fees'] ?? 0),
                        'advisor_id' => $advisorId,
                        'advisor_name' => $advisorName,
                    ];
                }
            }

            return $allCompromis;
        });
    }

    /**
     * Récupère les KPI des compromis pour une période donnée
     * OPTIMISÉ: utilise le cache des compromis préchargés
     */
    public function getCompromisStats(Carbon $startDate, Carbon $endDate): array
    {
        $allCompromis = $this->getAllCompromisData();

        $startStr = $startDate->format('Y-m-d');
        $endStr = $endDate->format('Y-m-d');

        $count = 0;
        $totalCommissionTTC = 0;

        foreach ($allCompromis as $c) {
            if ($c['date'] >= $startStr && $c['date'] <= $endStr) {
                $totalCommissionTTC += $c['seller_fees'] + $c['buyer_fees'];
                $count++;
            }
        }

        return [
            'count' => $count,
            'total_price' => 0,
            'total_commission' => $totalCommissionTTC,
        ];
    }

    /**
     * Vérifie si une date est dans une période donnée
     */
    protected function isDateInPeriod(?string $dateStr, Carbon $dateDebut, Carbon $dateFin): bool
    {
        if (!$dateStr || $dateStr === '0000-00-00' || $dateStr === '') {
            return false;
        }

        try {
            $datePart = preg_replace('/[T ].*$/', '', $dateStr);
            $date = Carbon::parse($datePart);
            return $date->between($dateDebut, $dateFin);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Récupère le Top 10 CA Compromis par conseiller
     * OPTIMISÉ: utilise le cache des compromis préchargés
     */
    public function getTopCompromisParConseiller(Carbon $startDate, Carbon $endDate, int $limit = 10): array
    {
        $allCompromis = $this->getAllCompromisData();

        $startStr = $startDate->format('Y-m-d');
        $endStr = $endDate->format('Y-m-d');

        $conseillers = [];

        foreach ($allCompromis as $c) {
            if ($c['date'] >= $startStr && $c['date'] <= $endStr) {
                $advisorId = $c['advisor_id'] ?: 'unknown';

                if (!isset($conseillers[$advisorId])) {
                    $conseillers[$advisorId] = [
                        'id' => $advisorId,
                        'name' => $c['advisor_name'],
                        'nb_compromis' => 0,
                        'ca_compromis' => 0,
                    ];
                }

                $conseillers[$advisorId]['nb_compromis']++;
                $conseillers[$advisorId]['ca_compromis'] += $c['seller_fees'] + $c['buyer_fees'];
            }
        }

        usort($conseillers, fn($a, $b) => $b['ca_compromis'] <=> $a['ca_compromis']);
        $conseillers = array_slice($conseillers, 0, $limit);

        foreach ($conseillers as $index => &$conseiller) {
            $conseiller['classement'] = $index + 1;
        }

        return $conseillers;
    }

    /**
     * Précharge TOUS les mandats exclusifs en un seul appel MongoDB
     * et les met en cache pour 10 minutes
     * NOTE: Utilise created_at comme date car le critère 163 (date_mandat)
     * n'est rempli que pour ~9% des mandats
     */
    protected function getAllMandatsExclusifsData(): array
    {
        return Cache::remember(self::MANDATS_EXCLU_CACHE_KEY, self::MANDATS_EXCLU_CACHE_TTL, function () {
            $agencySlug = config('keymex.agence.slug', 'keymex-synergie');
            $db = $this->getMongoDb();
            $collection = $db->selectCollection('properties');

            // Requête optimisée: filtrer les mandats exclusifs côté MongoDB
            $cursor = $collection->find([
                'agency_slug' => $agencySlug,
                'raw_data.criteres_text' => [
                    '$elemMatch' => [
                        'critere_id' => 124,
                        'critere_value' => 'Exclusif'
                    ]
                ]
            ], [
                'projection' => [
                    'raw_data.created_at' => 1,
                    'raw_data.suivi_par' => 1
                ]
            ]);

            $allMandats = [];

            foreach ($cursor as $property) {
                $rawData = (array) ($property['raw_data'] ?? []);

                // Utiliser raw_data.created_at comme date du mandat
                $createdAt = $rawData['created_at'] ?? null;
                if (!$createdAt) {
                    continue;
                }

                // Convertir la date au format Y-m-d pour comparaison
                try {
                    if ($createdAt instanceof \MongoDB\BSON\UTCDateTime) {
                        $date = $createdAt->toDateTime();
                        $datePart = $date->format('Y-m-d');
                    } else {
                        // Format ISO 8601: 2025-11-18T13:15:33+01:00
                        $date = Carbon::parse($createdAt);
                        $datePart = $date->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    continue;
                }

                // Conseiller (suivi_par)
                $rawData = (array) ($property['raw_data'] ?? []);
                $suiviPar = (array) ($rawData['suivi_par'] ?? []);
                $advisorId = (int) ($suiviPar['id'] ?? 0);
                $advisorName = trim(($suiviPar['firstname'] ?? '') . ' ' . ($suiviPar['lastname'] ?? ''));
                if (!$advisorName || $advisorName === ' ' || !$advisorId) {
                    $advisorName = 'Non attribué';
                    $advisorId = 0;
                }

                $allMandats[] = [
                    'date' => $datePart,
                    'advisor_id' => $advisorId,
                    'advisor_name' => $advisorName,
                ];
            }

            return $allMandats;
        });
    }

    /**
     * Récupère le Top 10 Mandats Exclusifs par conseiller
     * OPTIMISÉ: utilise le cache des mandats préchargés
     */
    public function getTopMandatsExclusParConseiller(Carbon $startDate, Carbon $endDate, int $limit = 10): array
    {
        $allMandats = $this->getAllMandatsExclusifsData();

        $startStr = $startDate->format('Y-m-d');
        $endStr = $endDate->format('Y-m-d');

        $conseillers = [];

        foreach ($allMandats as $m) {
            if ($m['date'] >= $startStr && $m['date'] <= $endStr) {
                $advisorId = $m['advisor_id'] ?: 'unknown';

                if (!isset($conseillers[$advisorId])) {
                    $conseillers[$advisorId] = [
                        'id' => $advisorId,
                        'name' => $m['advisor_name'],
                        'nb_mandats' => 0,
                    ];
                }

                $conseillers[$advisorId]['nb_mandats']++;
            }
        }

        usort($conseillers, fn($a, $b) => $b['nb_mandats'] <=> $a['nb_mandats']);
        $conseillers = array_slice($conseillers, 0, $limit);

        foreach ($conseillers as $index => &$conseiller) {
            $conseiller['classement'] = $index + 1;
        }

        return $conseillers;
    }

    /**
     * Récupère les KPI des mandats exclusifs pour une période donnée
     * OPTIMISÉ: utilise le cache des mandats préchargés
     */
    public function getMandatesExclusStats(Carbon $startDate, Carbon $endDate): array
    {
        $allMandats = $this->getAllMandatsExclusifsData();

        $startStr = $startDate->format('Y-m-d');
        $endStr = $endDate->format('Y-m-d');

        $count = 0;

        foreach ($allMandats as $m) {
            if ($m['date'] >= $startStr && $m['date'] <= $endStr) {
                $count++;
            }
        }

        return [
            'count' => $count,
        ];
    }

    /**
     * Récupère les KPI des ventes (actes signés) pour une période donnée
     * Utilise >= startDate et < endDate+1 jour pour gérer les dates ISO
     */
    public function getVentesStats(Carbon $startDate, Carbon $endDate): array
    {
        $endDateExclusive = $endDate->copy()->addDay()->format('Y-m-d');

        $query = DB::connection($this->connection)
            ->table($this->collection)
            ->where('raw_data.status.text', 'Vendu par l\'agence')
            ->where('raw_data.date_acte', '>=', $startDate->format('Y-m-d'))
            ->where('raw_data.date_acte', '<', $endDateExclusive)
            ->where(function($q) {
                $q->whereNull('raw_data.date_annulation')
                  ->orWhere('raw_data.date_annulation', '')
                  ->orWhere('raw_data.date_annulation', '0000-00-00');
            })
            ->get();

        $count = $query->count();
        $totalPrice = $query->sum(fn($p) => (float) ($p->raw_data['real_estate_price'] ?? 0));
        $totalCommission = $query->sum(fn($p) =>
            (float) ($p->raw_data['seller_fees'] ?? 0) + (float) ($p->raw_data['buyer_fees'] ?? 0)
        );

        return [
            'count' => $count,
            'total_price' => $totalPrice,
            'total_commission' => $totalCommission,
        ];
    }

    /**
     * Récupère les compromis en cours (biens sous compromis non encore vendus)
     */
    public function getCompromisProperties(): Collection
    {
        $query = DB::connection($this->connection)
            ->table($this->collection)
            ->where('raw_data.status.text', 'Compromis')
            ->where(function($q) {
                $q->whereNull('raw_data.date_annulation')
                  ->orWhere('raw_data.date_annulation', '')
                  ->orWhere('raw_data.date_annulation', '0000-00-00');
            })
            ->where(function($q) {
                $q->whereNull('raw_data.transfer_act_date')
                  ->orWhere('raw_data.transfer_act_date', '')
                  ->orWhere('raw_data.transfer_act_date', '0000-00-00');
            })
            ->orderBy('raw_data.date_compromis', 'desc')
            ->get();

        // Pré-charger les photos pour tous les compromis
        $numbers = $query->map(fn($p) => $p->raw_data['number'] ?? null)->filter()->values()->toArray();
        $this->preloadPhotos($numbers);

        return $query->map(fn ($property) => $this->formatProperty($property));
    }

    /**
     * Pré-charge les photos pour plusieurs biens
     */
    protected function preloadPhotos(array $numbers): void
    {
        if (empty($numbers)) {
            return;
        }

        // Recherche dans properties via compromis.number
        $properties = DB::connection($this->connection)
            ->table('properties')
            ->whereIn('raw_data.compromis.number', $numbers)
            ->get();

        foreach ($properties as $property) {
            $rawData = (array) ($property->raw_data ?? []);
            $compromisArray = (array) ($rawData['compromis'] ?? []);
            $photos = $rawData['products_photos'] ?? [];
            $photosArray = is_array($photos) ? $photos : (array) $photos;

            // Récupérer les URLs des photos (triées par sort_order)
            $photoUrls = collect($photosArray)
                ->sortBy('sort_order')
                ->map(fn($photo) => $photo['chemin'] ?? null)
                ->filter()
                ->values()
                ->toArray();

            // Associer les photos à chaque numéro de compromis
            foreach ($compromisArray as $compromis) {
                $number = $compromis['number'] ?? null;
                if ($number) {
                    $this->photosCache[$number] = $photoUrls;
                }
            }
        }
    }

    /**
     * Récupère les photos pour un numéro de compromis
     */
    protected function getPhotosForCompromis(string $number): array
    {
        if (isset($this->photosCache[$number])) {
            return $this->photosCache[$number];
        }

        // Recherche individuelle si pas en cache
        $property = DB::connection($this->connection)
            ->table('properties')
            ->where('raw_data.compromis.number', $number)
            ->first();

        if (!$property) {
            return [];
        }

        $rawData = (array) ($property->raw_data ?? []);
        $photos = $rawData['products_photos'] ?? [];
        $photosArray = is_array($photos) ? $photos : (array) $photos;

        return collect($photosArray)
            ->sortBy('sort_order')
            ->map(fn($photo) => $photo['chemin'] ?? null)
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * Récupère les biens vendus (acte signé)
     */
    public function getSoldProperties(int $limit = 50): Collection
    {
        $query = DB::connection($this->connection)
            ->table($this->collection)
            ->where('raw_data.status.text', 'Vendu par l\'agence')
            ->where(function($q) {
                $q->whereNull('raw_data.date_annulation')
                  ->orWhere('raw_data.date_annulation', '')
                  ->orWhere('raw_data.date_annulation', '0000-00-00');
            })
            ->orderBy('raw_data.transfer_act_date', 'desc')
            ->limit($limit)
            ->get();

        return $query->map(fn ($property) => $this->formatProperty($property));
    }

    /**
     * Trouve un bien par son ID MongoDB
     */
    public function find(string $id): ?array
    {
        $property = DB::connection($this->connection)
            ->table($this->collection)
            ->where('_id', $id)
            ->first();

        return $property ? $this->formatProperty($property) : null;
    }

    /**
     * Récupère les ventes récentes (actes signés dans les X derniers jours)
     */
    public function getRecentlySold(int $days = 30): Collection
    {
        $since = Carbon::now()->subDays($days)->format('Y-m-d');

        // Utiliser date_acte car transfer_act_date est souvent vide (0000-00-00)
        $query = DB::connection($this->connection)
            ->table($this->collection)
            ->where('raw_data.status.text', 'Vendu par l\'agence')
            ->where('raw_data.date_acte', '>=', $since)
            ->where(function($q) {
                $q->whereNull('raw_data.date_annulation')
                  ->orWhere('raw_data.date_annulation', '')
                  ->orWhere('raw_data.date_annulation', '0000-00-00');
            })
            ->orderBy('raw_data.date_acte', 'desc')
            ->get();

        // Pré-charger les photos pour tous les biens
        $numbers = $query->map(fn($p) => $p->raw_data['number'] ?? null)->filter()->values()->toArray();
        $this->preloadPhotos($numbers);

        return $query->map(fn ($property) => $this->formatProperty($property));
    }

    /**
     * Récupère les biens à vendre (statut en cours, mandat exclusif, web true)
     */
    public function getPropertiesForSale(): Collection
    {
        // Récupérer les biens publiés sur le web
        $properties = DB::connection($this->connection)
            ->table('properties')
            ->where('raw_data.status_web', true)
            ->orderBy('raw_data.created_at', 'desc')
            ->get();

        // Filtrer en PHP (elemMatch ne fonctionne pas bien avec le driver)
        $filtered = $properties->filter(function ($property) {
            $raw = (array) ($property->raw_data ?? []);
            $criteresText = $raw['criteres_text'] ?? [];

            $hasC121 = false;
            $hasC124 = false;

            foreach ($criteresText as $c) {
                $c = (array) $c;
                $critereId = $c['critere_id'] ?? null;
                $critereValue = $c['critere_value'] ?? null;

                if ($critereId == 121 && $critereValue === 'EnCours') {
                    $hasC121 = true;
                }
                if ($critereId == 124 && $critereValue === 'Exclusif') {
                    $hasC124 = true;
                }
            }

            return $hasC121 && $hasC124;
        });

        return $filtered->map(fn ($property) => $this->formatPropertyForSale($property));
    }

    /**
     * Formate un document properties pour les biens à vendre
     */
    protected function formatPropertyForSale(object $property): array
    {
        $rawData = (array) ($property->raw_data ?? []);

        // Helper pour extraire une valeur des critères
        $getCritereText = function($critereId) use ($rawData) {
            $criteres = $rawData['criteres_text'] ?? [];
            foreach ($criteres as $c) {
                $c = (array) $c;
                if (($c['critere_id'] ?? null) == $critereId) {
                    return $c['critere_value'] ?? null;
                }
            }
            return null;
        };

        $getCritereNumber = function($critereId) use ($rawData) {
            $criteres = $rawData['criteres_number'] ?? [];
            foreach ($criteres as $c) {
                $c = (array) $c;
                if (($c['critere_id'] ?? null) == $critereId) {
                    return $c['critere_value'] ?? null;
                }
            }
            return null;
        };

        // Date de création
        $creationDate = null;
        if (isset($rawData['created_at']) && $rawData['created_at']) {
            try {
                $creationDate = Carbon::parse($rawData['created_at']);
            } catch (\Exception $e) {
                // Ignore
            }
        }

        // Date de mandat (critère 163)
        $mandateDate = null;
        $mandateDateStr = $getCritereText(163);
        if ($mandateDateStr) {
            try {
                $mandateDate = Carbon::createFromFormat('d/m/Y', $mandateDateStr);
            } catch (\Exception $e) {
                // Ignore
            }
        }

        // Adresse depuis critères
        $ville = $getCritereText(54); // Ville
        $codePostal = $getCritereText(52); // Code postal
        $adresse = $getCritereText(122); // Adresse

        // Type de bien (critère 27)
        $typeBienValue = $getCritereText(27);
        $typeBienMap = [
            '1' => 'Maison', '2' => 'Appartement', '3' => 'Terrain', '4' => 'Immeuble',
            '5' => 'Parking/Box', '6' => 'Local commercial', '10' => 'Terrain constructible',
            '17' => 'Bureau', '22' => 'Villa', '23' => 'Maison de ville', '27' => 'Pavillon',
            '30' => 'Studio',
        ];
        // Chercher le critere_value_name pour le type de bien
        $typeBien = null;
        $criteresText = $rawData['criteres_text'] ?? [];
        foreach ($criteresText as $c) {
            $c = (array) $c;
            if (($c['critere_id'] ?? null) == 27) {
                $typeBien = $c['critere_value_name'] ?? $typeBienMap[$c['critere_value'] ?? ''] ?? 'N/A';
                break;
            }
        }
        $typeBien = $typeBien ?? 'N/A';

        // Conseiller (suivi_par)
        $suiviPar = isset($rawData['suivi_par']) ? (array) $rawData['suivi_par'] : [];
        $advisorName = '';
        if ($suiviPar) {
            $prenom = $suiviPar['firstname'] ?? '';
            $nom = $suiviPar['lastname'] ?? '';
            $advisorName = trim("$prenom $nom");
        }

        // Photos
        $photos = $rawData['products_photos'] ?? [];
        $photosArray = is_array($photos) ? $photos : (is_object($photos) ? (array) $photos : []);
        $photoUrls = collect($photosArray)
            ->sortBy('sort_order')
            ->map(fn($photo) => is_array($photo) ? ($photo['chemin'] ?? null) : (is_object($photo) ? ($photo->chemin ?? null) : null))
            ->filter()
            ->values()
            ->toArray();

        // Référence (critère 57)
        $reference = $getCritereText(57);

        // Données numériques
        $prix = $getCritereNumber(30); // Prix
        $surface = $getCritereNumber(34); // Surface
        $nbPieces = $getCritereNumber(33); // Nombre pièces
        $nbChambres = $getCritereNumber(38); // Chambres

        // ID MongoDB (accessible via ->id ou ->immofacile_id)
        $mongoId = (string) ($property->id ?? $property->immofacile_id ?? '');

        return [
            'id' => $mongoId,
            'reference' => 'IF-' . ($reference ?? 'N/A'),
            'number' => $reference,
            'type' => $typeBien,
            'address' => [
                'city' => $ville ?? '',
                'postal_code' => $codePostal ?? '',
                'street' => $adresse ?? '',
                'full' => trim(($codePostal ?? '') . ' ' . ($ville ?? '')),
            ],
            'surface' => $surface,
            'rooms' => $nbPieces,
            'bedrooms' => $nbChambres,
            'price' => $prix,
            'advisor' => [
                'id' => '',
                'name' => $advisorName,
            ],
            'photos' => $photoUrls,
            'dates' => [
                'creation' => $creationDate?->format('Y-m-d'),
                'mandate' => $mandateDate?->format('Y-m-d'),
            ],
        ];
    }

    /**
     * Formate un document MongoDB en tableau exploitable
     */
    protected function formatProperty(object $property): array
    {
        $rawData = $property->raw_data ?? [];

        // Dates
        $compromisDate = isset($rawData['date_compromis']) && $rawData['date_compromis'] && $rawData['date_compromis'] !== '0000-00-00'
            ? Carbon::parse($rawData['date_compromis'])
            : null;

        // Date de mandat (format dd/mm/yyyy dans mandate_date)
        $mandateDate = null;
        if (isset($rawData['mandate_date']) && $rawData['mandate_date']) {
            try {
                $mandateDate = Carbon::createFromFormat('d/m/Y', $rawData['mandate_date']);
            } catch (\Exception $e) {
                // Fallback sur creation_date si le parsing échoue
                if (isset($rawData['creation_date']) && $rawData['creation_date']) {
                    $mandateDate = Carbon::parse($rawData['creation_date']);
                }
            }
        } elseif (isset($rawData['creation_date']) && $rawData['creation_date']) {
            $mandateDate = Carbon::parse($rawData['creation_date']);
        }

        // Date de vente (date_acte en priorité car transfer_act_date est souvent vide)
        $saleDate = null;
        if (isset($rawData['date_acte']) && $rawData['date_acte'] && $rawData['date_acte'] !== '0000-00-00') {
            $saleDate = Carbon::parse($rawData['date_acte']);
        } elseif (isset($rawData['transfer_act_date']) && $rawData['transfer_act_date'] && $rawData['transfer_act_date'] !== '0000-00-00') {
            $saleDate = Carbon::parse($rawData['transfer_act_date']);
        }

        // Date de fin SRU (délai de rétractation)
        $dateSru = null;
        if (isset($rawData['date_fin_sru']) && $rawData['date_fin_sru'] && $rawData['date_fin_sru'] !== '0000-00-00') {
            $dateSru = Carbon::parse($rawData['date_fin_sru']);
        }

        // Calcul du délai légal (compromis + 15 jours pour le délai de rétractation)
        $legalDeadline = $dateSru ?? $compromisDate?->copy()->addDays(15);
        $isLegalDeadlinePassed = $legalDeadline?->isPast() ?? false;

        // Calcul du délai mandat → compromis
        $compromisDelay = ($mandateDate && $compromisDate)
            ? (int) $mandateDate->diffInDays($compromisDate)
            : null;

        // Calcul du délai mandat → vente (pour les biens vendus)
        $saleDuration = ($mandateDate && $saleDate)
            ? (int) $mandateDate->diffInDays($saleDate)
            : null;

        // Adresse
        $adresse = $rawData['postcode_city'] ?? '';
        $ville = null;
        $codePostal = null;
        if ($adresse) {
            $parts = explode(' ', $adresse, 2);
            if (count($parts) >= 1 && is_numeric($parts[0])) {
                $codePostal = $parts[0];
                $ville = $parts[1] ?? '';
            }
        }

        // Type de bien
        $typeBienMap = [
            '1' => 'Maison', '2' => 'Appartement', '3' => 'Terrain', '4' => 'Immeuble',
            '5' => 'Parking/Box', '6' => 'Local commercial', '10' => 'Terrain constructible',
            '17' => 'Bureau', '22' => 'Villa', '23' => 'Maison de ville', '27' => 'Pavillon',
            '30' => 'Studio',
        ];
        $typeBien = $typeBienMap[$rawData['type_bien'] ?? ''] ?? ($rawData['type_bien'] ?? 'N/A');

        // Conseiller (admin_followed)
        $advisorId = $rawData['product_admin_id'] ?? null;
        $adminFollowed = $rawData['admin_followed'] ?? [];
        $advisorName = '';
        if ($adminFollowed) {
            $prenom = $adminFollowed['firstname'] ?? '';
            $nom = $adminFollowed['lastname'] ?? '';
            $advisorName = trim("$prenom $nom");
        }

        // Statut
        $status = $rawData['status'] ?? [];
        $statusText = is_array($status) ? ($status['text'] ?? '') : (is_object($status) ? ($status->text ?? '') : '');

        // Numéro de référence
        $number = $rawData['number'] ?? null;

        // Photos depuis la collection properties
        $photos = $number ? $this->getPhotosForCompromis($number) : [];

        return [
            'id' => (string) ($property->_id ?? ''),
            'reference' => 'IF-' . ($number ?? 'N/A'),
            'number' => $number,
            'type' => $typeBien,
            'address' => [
                'city' => $ville ?? '',
                'postal_code' => $codePostal ?? '',
                'street' => $rawData['mandate_locatisation'] ?? '',
                'full' => $adresse,
            ],
            'surface' => $rawData['surface'] ?? null,
            'rooms' => $rawData['room_number'] ?? null,
            'bedrooms' => $rawData['bedroom_number'] ?? null,
            'price' => $rawData['real_estate_price'] ?? null,
            'price_hai' => $rawData['price_hai'] ?? null,
            'commission' => ($rawData['seller_fees'] ?? 0) + ($rawData['buyer_fees'] ?? 0),
            'seller_fees' => $rawData['seller_fees'] ?? 0,
            'buyer_fees' => $rawData['buyer_fees'] ?? 0,
            'status' => $statusText,
            'advisor' => [
                'id' => (string) $advisorId,
                'name' => $advisorName,
            ],
            'photos' => $photos,
            'dates' => [
                'mandate' => $mandateDate?->format('Y-m-d'),
                'compromis' => $compromisDate?->format('Y-m-d'),
                'sale' => $saleDate?->format('Y-m-d'),
                'legal_deadline' => $legalDeadline?->format('Y-m-d'),
                'sru' => $dateSru?->format('Y-m-d'),
            ],
            'is_legal_deadline_passed' => $isLegalDeadlinePassed,
            'compromis_delay_days' => $compromisDelay,
            'sale_duration_days' => $saleDuration,
        ];
    }

    /**
     * Récupère TOUS les conseillers actifs avec leur CA sur une période
     * Utilisé pour la page KeyPerformeurs
     *
     * LOGIQUE DE CALCUL:
     * - Source: collection sale_files (dossiers de vente)
     * - Filtre: date_compromis dans la période, non annulé
     * - Calcul: (seller_fees + buyer_fees) / 1.20 = honoraires HT
     * - Répartition: par nego selon leur percentage dans le tableau fees
     *
     * Inclut tous les conseillers actifs, même ceux avec 0€ de CA
     */
    public function getAllConseillersCA(Carbon $startDate, Carbon $endDate): array
    {
        // 1. D'abord récupérer TOUS les conseillers actifs
        $allActiveAdvisors = $this->getAllActiveAdvisors();

        // 2. Initialiser tous les conseillers avec 0 CA
        $conseillers = [];
        foreach ($allActiveAdvisors as $advisor) {
            $conseillers[$advisor['id']] = [
                'id' => $advisor['id'],
                'name' => $advisor['name'],
                'ca' => 0,
                'nb_dossiers' => 0,
            ];
        }

        // 3. Charger les données sale_files et répartir le CA par nego
        $allSaleFiles = $this->getAllSaleFilesCAData();
        $startStr = $startDate->format('Y-m-d');
        $endStr = $endDate->format('Y-m-d');

        foreach ($allSaleFiles as $dossier) {
            // Vérifier que la date est dans la période
            if ($dossier['date'] >= $startStr && $dossier['date'] <= $endStr) {
                // Répartir le CA entre les negos
                foreach ($dossier['negos'] as $nego) {
                    $negoId = $nego['nego_id'];
                    $partHT = $nego['part_ht'];

                    // Si le conseiller existe dans notre liste (actif)
                    if (isset($conseillers[$negoId])) {
                        $conseillers[$negoId]['ca'] += $partHT;
                        $conseillers[$negoId]['nb_dossiers']++;
                    } else {
                        // Conseiller non actif mais avec du CA - on l'ajoute quand même
                        $conseillers[$negoId] = [
                            'id' => $negoId,
                            'name' => $nego['nego_name'],
                            'ca' => $partHT,
                            'nb_dossiers' => 1,
                        ];
                    }
                }
            }
        }

        // 4. Ajouter la catégorie à chaque conseiller
        foreach ($conseillers as &$conseiller) {
            $conseiller['category'] = \App\Livewire\Kpi\KeyPerformeurs::getCategory($conseiller['ca']);
            $conseiller['ca_formatted'] = number_format($conseiller['ca'] / 1000, 1, ',', ' ') . 'K€ HT';
        }

        // 5. Trier par CA décroissant
        usort($conseillers, fn($a, $b) => $b['ca'] <=> $a['ca']);

        // 6. Ajouter le classement
        foreach ($conseillers as $index => &$conseiller) {
            $conseiller['rank'] = $index + 1;
        }

        return $conseillers;
    }

    /**
     * Cache des conseillers actifs (durée: 10 minutes)
     */
    protected const ADVISORS_CACHE_KEY = 'kpi_all_active_advisors';
    protected const ADVISORS_CACHE_TTL = 600; // 10 minutes

    /**
     * Cache des sale_files pour le calcul du CA par nego
     */
    protected const SALE_FILES_CA_CACHE_KEY = 'kpi_sale_files_ca_data';
    protected const SALE_FILES_CA_CACHE_TTL = 600; // 10 minutes

    /**
     * Précharge TOUS les sale_files avec leurs fees pour le calcul du CA par nego
     * Retourne un tableau avec date_compromis, honoraires HT et répartition par nego
     */
    protected function getAllSaleFilesCAData(): array
    {
        return Cache::remember(self::SALE_FILES_CA_CACHE_KEY, self::SALE_FILES_CA_CACHE_TTL, function () {
            $agencySlug = config('keymex.agence.slug', 'keymex-synergie');
            $db = $this->getMongoDb();
            $saleFiles = $db->selectCollection('sale_files');

            // Récupérer tous les dossiers avec une date_compromis valide
            $cursor = $saleFiles->find([
                'agency_slug' => $agencySlug,
                'raw_data.date_compromis' => ['$exists' => true, '$ne' => null, '$ne' => '0000-00-00'],
            ], [
                'projection' => [
                    'raw_data.date_compromis' => 1,
                    'raw_data.date_annulation' => 1,
                    'raw_data.seller_fees' => 1,
                    'raw_data.buyer_fees' => 1,
                    'raw_data.fees' => 1,
                ]
            ]);

            $allData = [];

            foreach ($cursor as $dossier) {
                $raw = (array) ($dossier['raw_data'] ?? []);

                // 1. Filtrer les dossiers valides
                $dateCompromis = $raw['date_compromis'] ?? null;
                if (!$dateCompromis || $dateCompromis === '0000-00-00') {
                    continue;
                }

                // 2. Exclure les annulés
                $dateAnnulation = $raw['date_annulation'] ?? null;
                if ($dateAnnulation && $dateAnnulation !== '0000-00-00' && $dateAnnulation !== '') {
                    continue;
                }

                // 3. Extraire la date (format Y-m-d)
                try {
                    $datePart = preg_replace('/[T ].*$/', '', $dateCompromis);
                    // Valider que c'est une date valide
                    Carbon::parse($datePart);
                } catch (\Exception $e) {
                    continue;
                }

                // 4. Calculer les honoraires HT
                $sellerFees = (float) ($raw['seller_fees'] ?? 0);
                $buyerFees = (float) ($raw['buyer_fees'] ?? 0);
                $totalTTC = $sellerFees + $buyerFees;
                $honorairesHT = $totalTTC / 1.20;

                // 5. Répartir par conseiller (fees avec person_type = 'nego')
                $fees = (array) ($raw['fees'] ?? []);
                $negoDistribution = [];

                foreach ($fees as $fee) {
                    $fee = (array) $fee;

                    // Uniquement les intervenants de type "nego"
                    if (($fee['person_type'] ?? '') === 'nego') {
                        $percentage = (float) ($fee['percentage'] ?? 0);
                        $partHT = ($honorairesHT * $percentage) / 100;

                        // Récupérer l'ID et nom du conseiller
                        $nego = (array) ($fee['nego'] ?? []);
                        $negoId = (int) ($nego['id'] ?? 0);
                        $negoNom = trim(($nego['firstname'] ?? '') . ' ' . ($nego['lastname'] ?? ''));

                        if ($negoId > 0 && $partHT > 0) {
                            $negoDistribution[] = [
                                'nego_id' => $negoId,
                                'nego_name' => $negoNom,
                                'percentage' => $percentage,
                                'part_ht' => $partHT,
                            ];
                        }
                    }
                }

                // Ajouter le dossier s'il y a des negos
                if (!empty($negoDistribution)) {
                    $allData[] = [
                        'date' => $datePart,
                        'honoraires_ht' => $honorairesHT,
                        'negos' => $negoDistribution,
                    ];
                }
            }

            return $allData;
        });
    }

    /**
     * Récupère tous les conseillers actifs depuis MongoDB
     * Mis en cache pour 10 minutes
     */
    protected function getAllActiveAdvisors(): array
    {
        return Cache::remember(self::ADVISORS_CACHE_KEY, self::ADVISORS_CACHE_TTL, function () {
            $agencySlug = config('keymex.agence.slug', 'keymex-synergie');
            $db = $this->getMongoDb();
            $advisorsCollection = $db->selectCollection('advisors');

            $cursor = $advisorsCollection->find([
                'agency_slug' => $agencySlug,
                'raw_data.status' => 1, // Seulement les conseillers actifs
            ], [
                'projection' => [
                    'immofacile_id' => 1,
                    'raw_data.firstname' => 1,
                    'raw_data.lastname' => 1,
                ]
            ]);

            $advisors = [];
            foreach ($cursor as $advisor) {
                $rawData = (array) ($advisor['raw_data'] ?? []);
                $id = (int) ($advisor['immofacile_id'] ?? 0);
                $name = trim(($rawData['firstname'] ?? '') . ' ' . ($rawData['lastname'] ?? ''));

                if ($id > 0 && $name) {
                    $advisors[] = [
                        'id' => $id,
                        'name' => $name,
                    ];
                }
            }

            return $advisors;
        });
    }

    /**
     * Recherche un conseiller par email dans MongoDB
     * Retourne les données complètes du conseiller ou null
     */
    public function getAdvisorByEmail(string $email): ?array
    {
        $agencySlug = config('keymex.agence.slug', 'keymex-synergie');
        $db = $this->getMongoDb();
        $advisorsCollection = $db->selectCollection('advisors');

        // Recherche insensible à la casse
        $advisor = $advisorsCollection->findOne([
            'agency_slug' => $agencySlug,
            'raw_data.email' => ['$regex' => '^' . preg_quote($email, '/') . '$', '$options' => 'i'],
        ]);

        if (!$advisor) {
            return null;
        }

        $rawData = (array) ($advisor['raw_data'] ?? []);

        return [
            'id' => (int) ($advisor['immofacile_id'] ?? 0),
            'firstname' => $rawData['firstname'] ?? '',
            'lastname' => $rawData['lastname'] ?? '',
            'email' => $rawData['email'] ?? '',
            'mobile_phone' => $rawData['mobile_phone'] ?? '',
            'phone' => $rawData['phone'] ?? '',
            'picture' => $rawData['picture'] ?? '',
            'status' => (int) ($rawData['status'] ?? 0),
            'job_title' => 'Conseiller Immobilier',
        ];
    }
}
