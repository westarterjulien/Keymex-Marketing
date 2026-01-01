<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MongoPropertyService
{
    protected $connection = 'mongodb';
    protected $collection = 'sale_files';

    /**
     * Cache des photos par numéro de compromis
     */
    protected array $photosCache = [];

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
}
