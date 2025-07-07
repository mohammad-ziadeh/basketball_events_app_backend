<?php

namespace App\Http\Controllers\Admin;

use App\Models\NextEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Notifications\CreatingEventNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class NextEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = NextEvent::query();

        // -------{{ Start Filters }}------- //

        if ($request->has('title') && !empty($request->title)) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->has('event_code') && $request->event_code !== 'all') {
            $query->where('event_code', $request->event_code);
        }

        if ($request->has('deleted')) {
            if ($request->deleted === 'only') {
                $query->onlyTrashed();
            } elseif ($request->deleted === 'with') {
                $query->withTrashed();
            }
        }

        if ($request->has('sort') && $request->sort === 'desc') {
            $query->orderByDesc('id');
        } else {
            $query->orderBy('id');
        }

        // -------{{ End Filters }}------- //

        $events = $query->paginate(9);
        $categories = Category::all();

        return view('admin.tables.event.events', compact('events', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tables.event.event-create-modal');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'event_code' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categories' => 'required|array|min:1',
            'categories.*.name' => 'required|string|max:255',
            'categories.*.capacity' => 'nullable|integer|min:1|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            $imageUrl = asset('storage/' . $path);
        }

        $defaultCapacity = 12;

        DB::transaction(function () use ($request, &$event, $imageUrl, $defaultCapacity) {
            $event = NextEvent::create([
                'title' => $request->title,
                'location' => $request->location,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'event_code' => $request->event_code,
                'image_url' => $imageUrl,
            ]);

            foreach ($request->categories as $categoryData) {
                $event->categories()->create([
                    'name' => $categoryData['name'],
                    'capacity' => $categoryData['capacity'] ?? $defaultCapacity,
                ]);
            }

            // Notify users
            $users = User::all();
            foreach ($users as $user) {
                $user->notify(new CreatingEventNotification($event));
            }
        });

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $event = NextEvent::withTrashed()->findOrFail($id);
        return view('admin.tables.event.event-edit-modal', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $event = NextEvent::withTrashed()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'event_code' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categories' => 'required|array|min:1',
            'categories.*.id' => 'nullable|integer|exists:categories,id',
            'categories.*.name' => 'required|string|max:255',
            'categories.*.capacity' => 'nullable|integer|min:1|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['title', 'location', 'start_date', 'end_date', 'event_code']);

        if ($request->hasFile('image')) {
            if ($event->image_url) {
                $oldPath = str_replace(asset('storage/') . '/', '', $event->image_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $path = $request->file('image')->store('events', 'public');
            $data['image_url'] = asset('storage/' . $path);
        }

        DB::transaction(function () use ($event, $data, $request) {
            // Update the event data
            $event->update($data);

            $submittedCategoryIds = collect($request->categories)->pluck('id')->filter()->all();

            // Delete categories that were removed in the form
            $event->categories()->whereNotIn('id', $submittedCategoryIds)->delete();

            // Add or update categories
            foreach ($request->categories as $categoryData) {
                if (!empty($categoryData['id'])) {
                    // Update existing category
                    $category = $event->categories()->find($categoryData['id']);
                    if ($category) {
                        $category->update([
                            'name' => $categoryData['name'],
                            'capacity' => $categoryData['capacity'] ?? 12,
                        ]);
                    }
                } else {
                    // Create new category
                    $event->categories()->create([
                        'name' => $categoryData['name'],
                        'capacity' => $categoryData['capacity'] ?? 12,
                    ]);
                }
            }
        });

        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $event = NextEvent::findOrFail($id);
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }

    /**
     * Restore the specified resource from soft delete.
     */
    public function restore($id)
    {
        $event = NextEvent::onlyTrashed()->findOrFail($id);
        $event->restore();

        return redirect()->route('events.index')->with('success', 'Event restored successfully.');
    }

    /**
     * Permanently delete the specified resource.
     */
    public function deletePermanently($id)
    {
        $event = NextEvent::onlyTrashed()->findOrFail($id);

        if ($event->image_url) {
            $path = str_replace(asset('storage/') . '/', '', $event->image_url);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $event->forceDelete();

        return redirect()->route('events.index')->with('success', 'Event permanently deleted.');
    }
}
