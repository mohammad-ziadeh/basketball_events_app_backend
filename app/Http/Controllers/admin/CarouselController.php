<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Carousel;
use Illuminate\Support\Facades\Storage;

class CarouselController extends Controller
{
    /**
     * Display a listing of the carousels (admin index page).
     */
    public function index()
    {
        $carousels = Carousel::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'image_url' => $item->image_url, // Already full URL in DB
                'tag' => $item->tag,
                'title' => $item->title,
            ];
        });

        return view('admin.tables.carousels.carousels', compact('carousels'));
    }

    /**
     * Show the form to create a new carousel.
     */
    public function create()
    {
        return view('admin.tables.carousels.carousel-create-modal');
    }

    /**
     * Store a newly created carousel with image upload.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'tag' => 'required|string|max:255',
            'title' => 'required|string',
        ]);

        if ($request->hasFile('image')) {
            // Store in 'carousels' folder
            $path = $request->file('image')->store('carousels', 'public');

            // Save full URL in DB
            $imageUrl = asset('storage/' . $path);

            Carousel::create([
                'image_url' => $imageUrl,
                'tag' => $validated['tag'],
                'title' => $validated['title'],
            ]);

            return redirect()->back()->with('success', 'Carousel item created successfully!');
        }

        return back()->with('error', 'Image upload failed.');
    }

    /**
     * Show the form to edit a carousel.
     */
    public function edit($id)
    {
        $carousel = Carousel::findOrFail($id);

        return view('admin.tables.carousels.carousel-edit-modal', compact('carousel'));
    }

    /**
     * Update the specified carousel.
     */
    public function update(Request $request, $id)
    {
        $carousel = Carousel::findOrFail($id);

        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'tag' => 'required|string|max:255',
            'title' => 'required|string',
        ]);

        $data = [
            'tag' => $validated['tag'],
            'title' => $validated['title'],
        ];

        if ($request->hasFile('image')) {
            // Extract relative path from full URL
            $relativePath = str_replace(asset('storage') . '/', '', $carousel->image_url);

            // Delete old image
            if ($carousel->image_url && Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }

            // Store new image and update full URL
            $path = $request->file('image')->store('carousels', 'public');
            $data['image_url'] = asset('storage/' . $path);
        }

        $carousel->update($data);

        return redirect()->back()->with('success', 'Carousel updated successfully!');
    }

    /**
     * Remove the specified carousel.
     */
    public function destroy($id)
    {
        $carousel = Carousel::findOrFail($id);

        // Extract relative path from full URL
        $relativePath = str_replace(asset('storage') . '/', '', $carousel->image_url);

        // Delete image from disk
        if ($carousel->image_url && Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }

        $carousel->delete();

        return redirect()->back()->with('success', 'Carousel deleted successfully!');
    }
}
