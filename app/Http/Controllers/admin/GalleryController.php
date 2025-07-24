<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NextEvent;
use App\Models\Gallery;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = Gallery::with('event');

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('title')) {
            $query->whereHas('event', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->title . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        $galleries = $query->latest()->paginate(20);

        $events = NextEvent::all();

        return view('admin.tables.gallery.gallery', compact('galleries', 'events'));
    }

    public function create()
    {
        $events = NextEvent::all();
        return view('admin.tables.gallery.gallery-create-modal', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,jpg,png,mp4,mov,avi|max:20480',
            'event_id' => 'required|exists:next_events,id',
        ]);

        $file = $request->file('file');
        $mimeType = $file->getMimeType();
        $isVideo = str_contains($mimeType, 'video');
        $type = $isVideo ? 'video' : 'photo';

        $path = $file->store('galleries', 'public');

        $data = [
            'type' => $type,
            'photo' => $isVideo ? null : $path,
            'video' => $isVideo ? $path : null,
            'event_id' => $request->event_id,
        ];

        Gallery::create($data);

        return redirect()->route('gallery.index')->with('success', 'Media uploaded successfully.');
    }

    public function edit(Gallery $gallery)
    {
        $events = NextEvent::all();
        return view('admin.tables.gallery.gallery-edit-modal', compact('gallery', 'events'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'event_id' => 'required|exists:next_events,id',
            'file' => 'nullable|file|mimes:jpeg,jpg,png,mp4,mov,avi|max:20480',
        ]);

        $data = ['event_id' => $request->event_id];

        if ($request->hasFile('file')) {
            if ($gallery->photo) Storage::disk('public')->delete($gallery->photo);
            if ($gallery->video) Storage::disk('public')->delete($gallery->video);

            $file = $request->file('file');
            $mimeType = $file->getMimeType();
            $isVideo = str_contains($mimeType, 'video');
            $type = $isVideo ? 'video' : 'photo';

            $path = $file->store('galleries', 'public');

            $data['type'] = $type;
            $data['photo'] = $isVideo ? null : $path;
            $data['video'] = $isVideo ? $path : null;
        }

        $gallery->update($data);

        return redirect()->route('gallery.index')->with('success', 'Gallery updated successfully.');
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->photo) Storage::disk('public')->delete($gallery->photo);
        if ($gallery->video) Storage::disk('public')->delete($gallery->video);

        $gallery->delete();

        return redirect()->route('gallery.index')->with('success', 'Deleted successfully.');
    }
}
