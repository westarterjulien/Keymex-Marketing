<?php

namespace App\Services;

use App\Models\StoryMedia;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoryGeneratorService
{
    protected string $storagePath;
    protected array $cachedAssets = [];

    public function __construct()
    {
        $this->storagePath = storage_path('app/public/stories');

        if (!file_exists($this->storagePath)) {
            mkdir($this->storagePath, 0755, true);
        }
    }

    /**
     * Generate a "Sous Compromis" story image
     */
    public function generateSousCompromis(array $property): string
    {
        $assets = $this->getStoryAssets();

        $html = view('stories.sous-compromis', [
            'property' => $property,
            'assets' => $assets,
        ])->render();

        return $this->generateImage($html, 'sous-compromis');
    }

    /**
     * Generate a "Vendu" story image
     */
    public function generateVendu(array $property): string
    {
        $assets = $this->getStoryAssets();

        $html = view('stories.vendu', [
            'property' => $property,
            'assets' => $assets,
        ])->render();

        return $this->generateImage($html, 'vendu');
    }

    /**
     * Generate a "Nouveau Mandat" story image
     */
    public function generateNouveauMandat(array $property): string
    {
        $assets = $this->getStoryAssets();

        $html = view('stories.nouveau-mandat', [
            'property' => $property,
            'assets' => $assets,
        ])->render();

        return $this->generateImage($html, 'nouveau-mandat');
    }

    /**
     * Get story assets from media library
     * Looks for specific media by name pattern
     */
    protected function getStoryAssets(): array
    {
        if (!empty($this->cachedAssets)) {
            return $this->cachedAssets;
        }

        $assets = [
            'logo' => null,
            'burst_left' => null,
            'burst_right' => null,
            'overlay' => null,
            'overlay_compromis' => null,
            'overlay_vendu' => null,
        ];

        // Find logo (name containing "logo" and "rouge" for red logo)
        $logo = StoryMedia::where(function ($q) {
            $q->where('name', 'like', '%logo%rouge%')
              ->orWhere('name', 'like', '%Logo%rouge%')
              ->orWhere('name', 'like', '%keymex%')
              ->orWhere('name', 'like', '%KEYMEX%');
        })->first();

        // Fallback: any logo
        if (!$logo) {
            $logo = StoryMedia::where('name', 'like', '%logo%')
                ->orWhere('category', 'logo')
                ->first();
        }

        if ($logo) {
            $assets['logo'] = $this->mediaToBase64($logo);
        }

        // Find icons for burst decorations
        // "Icone haut titre" = burst_left (en haut à gauche)
        // "Icone Bas titre" = burst_right (en bas à droite)
        $iconHaut = StoryMedia::where('name', 'like', '%icone%haut%')
            ->orWhere('name', 'like', '%Icone%haut%')
            ->first();

        $iconBas = StoryMedia::where('name', 'like', '%icone%bas%')
            ->orWhere('name', 'like', '%Icone%bas%')
            ->first();

        if ($iconHaut) {
            $assets['burst_left'] = $this->mediaToBase64($iconHaut);
        }

        if ($iconBas) {
            $assets['burst_right'] = $this->mediaToBase64($iconBas);
        }

        // Fallback: look for burst/éclat patterns
        if (!$assets['burst_left'] || !$assets['burst_right']) {
            $decorations = StoryMedia::where('category', 'decoration')
                ->orWhere('category', 'icon')
                ->orWhere('name', 'like', '%burst%')
                ->orWhere('name', 'like', '%éclat%')
                ->orWhere('name', 'like', '%eclat%')
                ->get();

            foreach ($decorations as $deco) {
                $nameLower = strtolower($deco->name);
                if (!$assets['burst_left']) {
                    $assets['burst_left'] = $this->mediaToBase64($deco);
                } elseif (!$assets['burst_right']) {
                    $assets['burst_right'] = $this->mediaToBase64($deco);
                }
            }
        }

        // Find overlay for "Nouveau Mandat" story
        // Le fichier s'appelle "STORIES VENDU - SS COMPROMIS" mais c'est bien le calque pour Nouveau Mandat
        $overlay = StoryMedia::where('name', 'like', '%nouveau%mandat%')
            ->orWhere('name', 'like', '%calque%nouveau%')
            ->orWhere('name', 'like', '%STORIES%')
            ->first();

        if ($overlay) {
            $assets['overlay'] = $this->mediaToBase64($overlay);
        }

        // Find overlay for "Sous Compromis" story
        // Priorité: "Calque Sous compromis" puis fallback sur d'autres patterns
        $overlayCompromis = StoryMedia::where('name', 'like', '%calque%sous%compromis%')
            ->orWhere('name', 'like', '%calque%compromis%')
            ->first();

        if (!$overlayCompromis) {
            // Fallback: chercher "sous compromis" mais exclure "vendu"
            $overlayCompromis = StoryMedia::where('name', 'like', '%sous%compromis%')
                ->where('name', 'not like', '%vendu%')
                ->first();
        }

        if ($overlayCompromis) {
            $assets['overlay_compromis'] = $this->mediaToBase64($overlayCompromis);
        }

        // Find overlay for "Vendu" story
        // Priorité: "Calque Vendu" spécifiquement
        $overlayVendu = StoryMedia::where('name', 'like', '%calque%vendu%')
            ->first();

        if (!$overlayVendu) {
            // Fallback: chercher "vendu" mais exclure "compromis"
            $overlayVendu = StoryMedia::where('name', 'like', '%vendu%')
                ->where('name', 'not like', '%compromis%')
                ->first();
        }

        if ($overlayVendu) {
            $assets['overlay_vendu'] = $this->mediaToBase64($overlayVendu);
        }

        $this->cachedAssets = $assets;
        return $assets;
    }

    /**
     * Convert a StoryMedia to base64
     */
    protected function mediaToBase64(StoryMedia $media): string
    {
        try {
            $url = $media->getSignedUrl(60); // 1h validity
            $content = file_get_contents($url);

            $mimeType = $media->mime_type ?: 'image/png';
            return 'data:' . $mimeType . ';base64,' . base64_encode($content);
        } catch (\Exception $e) {
            \Log::warning('Failed to convert media to base64', [
                'media_id' => $media->id,
                'error' => $e->getMessage(),
            ]);
            return '';
        }
    }

    /**
     * Generate image from HTML
     */
    protected function generateImage(string $html, string $prefix): string
    {
        $filename = $prefix . '-' . Str::random(10) . '.png';
        $filepath = $this->storagePath . '/' . $filename;

        Browsershot::html($html)
            ->setNodeBinary('/home/julien/.nvm/versions/node/v22.16.0/bin/node')
            ->setNpmBinary('/home/julien/.nvm/versions/node/v22.16.0/bin/npm')
            ->setChromePath('/usr/bin/google-chrome')
            ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox'])
            ->windowSize(1080, 1920) // Story format 9:16
            ->deviceScaleFactor(1)
            ->setScreenshotType('png')
            ->waitUntilNetworkIdle()
            ->timeout(60)
            ->save($filepath);

        return 'stories/' . $filename;
    }

    /**
     * Get logo as base64 for embedding in HTML
     */
    protected function getLogoBase64(): string
    {
        $logoPath = public_path('images/logo-keymex-synergie.png');

        if (file_exists($logoPath)) {
            $content = file_get_contents($logoPath);
            return 'data:image/png;base64,' . base64_encode($content);
        }

        return '';
    }

    /**
     * Convert remote image URL to base64
     */
    public function imageToBase64(string $url): string
    {
        try {
            $content = file_get_contents($url);
            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $mimeType = match($extension) {
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                default => 'image/jpeg',
            };
            return 'data:' . $mimeType . ';base64,' . base64_encode($content);
        } catch (\Exception $e) {
            return $url;
        }
    }

    /**
     * Clean old story files (older than 24 hours)
     */
    public function cleanOldStories(): int
    {
        $count = 0;
        $files = glob($this->storagePath . '/*.png');

        foreach ($files as $file) {
            if (filemtime($file) < strtotime('-24 hours')) {
                unlink($file);
                $count++;
            }
        }

        return $count;
    }
}
