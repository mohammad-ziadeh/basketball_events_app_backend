<?php

namespace App\Http\Controllers\Api;


use App\Models\Team;
use App\Models\Category;
use App\Models\NextEvent;
use App\Notifications\TeamAssignedNotification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeamController extends Controller
{
    public function index()
    {
        return Team::with(['players.user', 'events'])
            ->where('coach_id', auth()->id())
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'location' => 'required|string',
            // 'email' => 'required|email',
            // 'phone' => 'required|string',
        ]);

        $team = Team::create([
            'name' => $request->name,
            'location' => $request->location,
            'coach_id' => auth()->id(),
            // 'email' => $request->email,
            // 'phone' => $request->phone,
        ]);

        return response()->json($team, 201);
    }

    public function destroy(Team $team)
    {
        if ($team->coach_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $team->delete();
        return response()->noContent();
    }

    public function assignToEvent(Team $team, NextEvent $event)
    {
        if ($team->coach_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $oldAssignedTeam = Team::where('coach_id', auth()->id())
            ->whereHas('events', function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })
            ->first();

        if ($oldAssignedTeam && $oldAssignedTeam->id !== $team->id) {
            $oldAssignedTeam->events()->detach($event->id);
        }

        $team->events()->syncWithoutDetaching([$event->id]);

        foreach ($team->players as $player) {
            $player->user->notify(new TeamAssignedNotification($event, auth()->user()));
        }


        return response()->json(['message' => 'Team assigned to event']);
    }


    public function assignToCategory(Request $request, Team $team, Category $category)
    {
        $request->validate([
            'email' => 'required|email',
            'phone' => 'required|string',
        ]);

        if (!$category->hasCapacity()) {
            return response()->json(['message' => 'This category is full. Cannot assign more teams.'], 400);
        }

        $alreadyAssigned = $team->categories()
            ->wherePivot('event_id', $category->event_id)
            ->first();

        if ($alreadyAssigned) {
            return response()->json([
                'message' => "This team is already assigned to another category in this event.",
            ], 400);
        }

        // Update email and phone in team
        $team->email = $request->email;
        $team->phone = $request->phone;
        $team->save();

        // Assign team to category with event pivot data
        $team->categories()->syncWithoutDetaching([
            $category->id => ['event_id' => $category->event_id],
        ]);

        foreach ($team->players as $player) {
            $player->user->notify(new TeamAssignedNotification($category->event, auth()->user()));
        }

        return response()->json(['message' => 'Team assigned to category successfully']);
    }



    public function update(Request $request, Team $team)
    {
        if ($team->coach_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string',
            'location' => 'required|string',
            // 'email' => $request->email,
            // 'phone' => $request->phone,
        ]);

        $team->update([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        return response()->json($team);
    }

    
}
