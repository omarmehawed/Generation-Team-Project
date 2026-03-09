<?php

namespace App\Http\Controllers;

use App\Models\Poster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PosterController extends Controller
{
    public function index()
    {
        $posters = Poster::orderBy('order')->get();
        return view('posters.index', compact('posters'));
    }

    public function create()
    {
        return view('posters.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_type' => 'required|in:standard,slider,profile_card',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'slider_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'links' => 'nullable|string',
            'text_color' => 'nullable|string|max:20',
            'text_position' => 'required|in:above,below,overlay',
            'image_size' => 'required|in:small,medium,large,full',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $storedPath = $request->file('image')->store('posters', 'r2');
            $imagePath = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);
        }
        
        $imagesPaths = [];
        if ($request->hasFile('slider_images')) {
            foreach ($request->file('slider_images') as $file) {
                $storedPath = $file->store('posters/sliders', 'r2');
                $imagesPaths[] = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);
            }
        }

        $links = json_decode($request->links, true) ?? [];

        $maxOrder = Poster::max('order') ?? 0;

        Poster::create([
            'title' => $request->title,
            'description' => $request->description,
            'template_type' => $request->template_type,
            'image_path' => $imagePath,
            'images' => $imagesPaths,
            'links' => $links,
            'text_color' => $request->text_color ?? '#ffffff',
            'text_position' => $request->text_position,
            'image_size' => $request->image_size,
            'order' => $maxOrder + 1,
            'created_by' => \Illuminate\Support\Facades\Auth::id(),
        ]);

        return redirect()->route('posters.index')->with('success', 'Poster created successfully.');
    }

    public function edit(Poster $poster)
    {
        return view('posters.edit', compact('poster'));
    }

    public function update(Request $request, Poster $poster)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_type' => 'required|in:standard,slider,profile_card',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'slider_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'links' => 'nullable|string',
            'text_color' => 'nullable|string|max:20',
            'text_position' => 'required|in:above,below,overlay',
            'image_size' => 'required|in:small,medium,large,full',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'template_type' => $request->template_type,
            'text_color' => $request->text_color ?? '#ffffff',
            'text_position' => $request->text_position,
            'image_size' => $request->image_size,
        ];

        if ($request->hasFile('image')) {
            if ($poster->image_path && str_starts_with($poster->image_path, 'http')) {
                $pathToDelete = str_replace(env('CLOUDFLARE_R2_URL') . '/', '', $poster->image_path);
                \Illuminate\Support\Facades\Storage::disk('r2')->delete($pathToDelete);
            } elseif ($poster->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($poster->image_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($poster->image_path);
            }
            $storedPath = $request->file('image')->store('posters', 'r2');
            $data['image_path'] = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);
        }

        if ($request->hasFile('slider_images')) {
            if (!empty($poster->images)) {
                foreach ($poster->images as $oldImg) {
                    if (str_starts_with($oldImg, 'http')) {
                        $pathToDelete = str_replace(env('CLOUDFLARE_R2_URL') . '/', '', $oldImg);
                        \Illuminate\Support\Facades\Storage::disk('r2')->delete($pathToDelete);
                    } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldImg)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldImg);
                    }
                }
            }
            $imagesPaths = [];
            foreach ($request->file('slider_images') as $file) {
                $storedPath = $file->store('posters/sliders', 'r2');
                $imagesPaths[] = \Illuminate\Support\Facades\Storage::disk('r2')->url($storedPath);
            }
            $data['images'] = $imagesPaths;
        }

        if ($request->has('links')) {
            $data['links'] = json_decode($request->links, true) ?? [];
        }

        $poster->update($data);

        return redirect()->route('posters.index')->with('success', 'Poster updated successfully.');
    }

    public function destroy(Poster $poster)
    {
        if ($poster->image_path) {
            if (str_starts_with($poster->image_path, 'http')) {
                $pathToDelete = str_replace(env('CLOUDFLARE_R2_URL') . '/', '', $poster->image_path);
                \Illuminate\Support\Facades\Storage::disk('r2')->delete($pathToDelete);
            } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($poster->image_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($poster->image_path);
            }
        }
        if (!empty($poster->images)) {
            foreach ($poster->images as $oldImg) {
                if (str_starts_with($oldImg, 'http')) {
                    $pathToDelete = str_replace(env('CLOUDFLARE_R2_URL') . '/', '', $oldImg);
                    \Illuminate\Support\Facades\Storage::disk('r2')->delete($pathToDelete);
                } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldImg)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldImg);
                }
            }
        }
        
        $poster->delete();

        return redirect()->route('posters.index')->with('success', 'Poster deleted successfully.');
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:posters,id'
        ]);

        foreach ($request->order as $index => $id) {
            Poster::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function editLayout(Poster $poster)
    {
        return view('posters.layout', compact('poster'));
    }

    public function updateLayout(Request $request, Poster $poster)
    {
        $request->validate([
            'layout_settings' => 'required|array',
        ]);

        $poster->update([
            'layout_settings' => $request->layout_settings,
        ]);

        return response()->json(['success' => true]);
    }
}
