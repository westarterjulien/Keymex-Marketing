<?php

namespace App\Http\Controllers;

use App\Models\StoryMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = StoryMedia::query()->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('filename', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $medias = $query->paginate(20);
        $categories = [
            'logo' => 'Logos',
            'icon' => 'Icônes',
            'background' => 'Fonds',
            'decoration' => 'Décorations',
            'other' => 'Autres',
        ];

        return view('stories.media-library', compact('medias', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:png,jpg,jpeg,gif,svg,webp|max:10240',
            'name' => 'required|string|max:255',
            'category' => 'required|in:logo,icon,background,decoration,other',
            'description' => 'nullable|string|max:1000',
        ]);

        $file = $request->file('file');
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $path = 'story-media/' . $filename;

        // Upload to S3
        Storage::disk('s3')->put($path, file_get_contents($file->getRealPath()));

        StoryMedia::create([
            'name' => $request->name,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'disk' => 's3',
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'category' => $request->category,
            'description' => $request->description,
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->route('stories.media')->with('success', 'Média uploadé avec succès.');
    }

    public function destroy(StoryMedia $media)
    {
        $media->delete();
        return redirect()->route('stories.media')->with('success', 'Média supprimé.');
    }

    public function copyUrl(StoryMedia $media)
    {
        $url = $media->getSignedUrl(1440); // 24h validity
        return response()->json(['url' => $url]);
    }
}
