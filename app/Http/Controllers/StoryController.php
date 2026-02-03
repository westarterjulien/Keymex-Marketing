<?php

namespace App\Http\Controllers;

use App\Services\MongoPropertyService;
use App\Services\StoryGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    public function __construct(
        protected StoryGeneratorService $storyGenerator,
        protected MongoPropertyService $propertyService
    ) {}

    /**
     * Generate a story for a property
     */
    public function generate(Request $request, string $propertyId, string $type)
    {
        // Validate type
        if (!in_array($type, ['sous-compromis', 'vendu', 'nouveau-mandat'])) {
            return response()->json(['error' => 'Type invalide'], 400);
        }

        try {
            // Get property data from MongoDB
            $rawProperty = $this->propertyService->find($propertyId);

            if (!$rawProperty) {
                return response()->json(['error' => 'Bien non trouvé'], 404);
            }

            // Get selected photo index (default to 0)
            $photoIndex = (int) $request->input('photoIndex', 0);

            // Prepare property data for the story
            $property = $this->preparePropertyData($rawProperty, $type, $photoIndex);

            // Generate the story
            $filepath = match($type) {
                'sous-compromis' => $this->storyGenerator->generateSousCompromis($property),
                'vendu' => $this->storyGenerator->generateVendu($property),
                'nouveau-mandat' => $this->storyGenerator->generateNouveauMandat($property),
            };

            // Return the URL to the generated image
            return response()->json([
                'success' => true,
                'url' => asset('storage/' . $filepath),
                'filename' => basename($filepath),
            ]);

        } catch (\Exception $e) {
            \Log::error('Story generation failed', [
                'propertyId' => $propertyId,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Erreur lors de la génération: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview story template (for testing)
     */
    public function preview(Request $request, string $type)
    {
        $property = [
            'type' => $request->get('type', 'Appartement'),
            'city' => $request->get('city', 'Mortefontaine-en-Thelle'),
            'postal_code' => $request->get('postal_code', '60510'),
            'photo' => $request->get('photo', 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800'),
            'days' => $request->get('days', 95),
        ];

        // Get assets from media library
        $assets = $this->getPreviewAssets();

        return view('stories.' . $type, [
            'property' => $property,
            'assets' => $assets,
        ]);
    }

    /**
     * Get assets for preview (using signed URLs directly, not base64)
     */
    protected function getPreviewAssets(): array
    {
        $assets = [
            'logo' => null,
            'burst_left' => null,
            'burst_right' => null,
            'overlay' => null,
            'overlay_compromis' => null,
            'overlay_vendu' => null,
        ];

        // Find logo (name containing "logo" and "rouge" for red logo)
        $logo = \App\Models\StoryMedia::where(function ($q) {
            $q->where('name', 'like', '%logo%rouge%')
              ->orWhere('name', 'like', '%Logo%rouge%')
              ->orWhere('name', 'like', '%keymex%')
              ->orWhere('name', 'like', '%KEYMEX%');
        })->first();

        // Fallback: any logo
        if (!$logo) {
            $logo = \App\Models\StoryMedia::where('name', 'like', '%logo%')
                ->orWhere('category', 'logo')
                ->first();
        }

        if ($logo) {
            $assets['logo'] = $logo->getSignedUrl(1440);
        }

        // Find icons for burst decorations
        // "Icone haut titre" = burst_left
        // "Icone Bas titre" = burst_right
        $iconHaut = \App\Models\StoryMedia::where('name', 'like', '%icone%haut%')
            ->orWhere('name', 'like', '%Icone%haut%')
            ->first();

        $iconBas = \App\Models\StoryMedia::where('name', 'like', '%icone%bas%')
            ->orWhere('name', 'like', '%Icone%bas%')
            ->first();

        if ($iconHaut) {
            $assets['burst_left'] = $iconHaut->getSignedUrl(1440);
        }

        if ($iconBas) {
            $assets['burst_right'] = $iconBas->getSignedUrl(1440);
        }

        // Find overlay for "Nouveau Mandat" story
        // Le fichier s'appelle "STORIES VENDU - SS COMPROMIS" mais c'est bien le calque pour Nouveau Mandat
        $overlay = \App\Models\StoryMedia::where('name', 'like', '%nouveau%mandat%')
            ->orWhere('name', 'like', '%calque%nouveau%')
            ->orWhere('name', 'like', '%STORIES%')
            ->first();

        if ($overlay) {
            $assets['overlay'] = $overlay->getSignedUrl(1440);
        }

        // Find overlay for "Sous Compromis" story
        // Priorité: "Calque Sous compromis" puis fallback sur d'autres patterns
        $overlayCompromis = \App\Models\StoryMedia::where('name', 'like', '%calque%sous%compromis%')
            ->orWhere('name', 'like', '%calque%compromis%')
            ->first();

        if (!$overlayCompromis) {
            // Fallback: chercher "sous compromis" mais exclure "vendu"
            $overlayCompromis = \App\Models\StoryMedia::where('name', 'like', '%sous%compromis%')
                ->where('name', 'not like', '%vendu%')
                ->first();
        }

        if ($overlayCompromis) {
            $assets['overlay_compromis'] = $overlayCompromis->getSignedUrl(1440);
        }

        // Find overlay for "Vendu" story
        // Priorité: "Calque Vendu" spécifiquement
        $overlayVendu = \App\Models\StoryMedia::where('name', 'like', '%calque%vendu%')
            ->first();

        if (!$overlayVendu) {
            // Fallback: chercher "vendu" mais exclure "compromis"
            $overlayVendu = \App\Models\StoryMedia::where('name', 'like', '%vendu%')
                ->where('name', 'not like', '%compromis%')
                ->first();
        }

        if ($overlayVendu) {
            $assets['overlay_vendu'] = $overlayVendu->getSignedUrl(1440);
        }

        return $assets;
    }

    /**
     * Download a generated story
     */
    public function download(string $filename)
    {
        $filepath = storage_path('app/public/stories/' . $filename);

        if (!file_exists($filepath)) {
            abort(404);
        }

        return response()->download($filepath, $filename, [
            'Content-Type' => 'image/png',
        ]);
    }

    /**
     * Prepare property data from formatted MongoDB data
     */
    protected function preparePropertyData(array $property, string $type, int $photoIndex = 0): array
    {
        // Get the selected photo and convert to base64
        $photos = $property['photos'] ?? [];
        $photoUrl = $photos[$photoIndex] ?? $photos[0] ?? null;
        $photoBase64 = $photoUrl ? $this->storyGenerator->imageToBase64($photoUrl) : null;

        // Get property type (already formatted by MongoPropertyService)
        $propertyType = $property['type'] ?? 'Bien immobilier';

        // Get city and postal code from address
        $city = $property['address']['city'] ?? '';
        $postalCode = $property['address']['postal_code'] ?? '';

        // Get days for "Vendu" stories
        $days = null;
        if ($type === 'vendu') {
            $days = $property['sale_duration_days'] ?? $property['compromis_delay_days'] ?? null;
        }

        return [
            'type' => $propertyType,
            'city' => $city,
            'postal_code' => $postalCode,
            'photo' => $photoBase64,
            'days' => $days,
        ];
    }
}
