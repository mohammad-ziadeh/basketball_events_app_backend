<?php

namespace App\Http\Controllers\Admin;


use App\Models\Matchup;
use App\Models\NextEvent;
use App\Models\Category;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MatchupController extends Controller
{
    public function index(Request $request)
    {
        $query = NextEvent::with([
            'categories.teams',
            'teams',
            'matchups' => function ($matchupQuery) use ($request) {
                $matchupQuery->with(['teamA', 'teamB', 'category']);

                if ($request->filled('round')) {
                    $matchupQuery->where('round', $request->round);
                }
            },
        ]);


        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('event_code')) {
            $query->where('event_code', $request->event_code);
        }

        if ($request->filled('sort')) {
            $query->orderBy('start_date', $request->sort === 'desc' ? 'desc' : 'asc');
        }

        if ($request->filled('filter')) {
            $now = now();
            if ($request->filter === 'upcoming') {
                $query->where('start_date', '>', $now);
            } elseif ($request->filter === 'past') {
                $query->where('end_date', '<=', $now);
            } elseif ($request->filter === 'ongoing') {
                $query->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            }
        }


        $events = $query->orderBy('created_at', 'desc')->paginate(5);

        foreach ($events as $event) {
            $matchupsByCategory = $event->matchups->groupBy('category_id');

            foreach ($event->categories as $category) {
                $category->matchups = $matchupsByCategory->get($category->id, collect());
            }
        }

        $availableRounds = Matchup::select('round')->distinct()->whereNotNull('round')->pluck('round');

        return view('admin.tables.matchups.matchup', compact('events', 'availableRounds'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:next_events,id',
            'category_id' => 'required|exists:categories,id',
            'team_a_id' => 'required|exists:teams,id|different:team_b_id',
            'team_b_id' => 'required|exists:teams,id',
            'match_time' => 'nullable|date',
            'location' => 'nullable|string',
            'round' => 'nullable|string',
        ]);

        Matchup::create($request->all());

        return back()->with('success', 'Matchup created successfully.');
    }

    public function update(Request $request, Matchup $matchup)
    {
        $request->validate([
            'team_a_id' => 'required|exists:teams,id|different:team_b_id',
            'team_b_id' => 'required|exists:teams,id',
            'match_time' => 'nullable|date',
            'location' => 'nullable|string',
            'round' => 'nullable|string',
        ]);

        $matchup->update($request->all());

        return back()->with('success', 'Matchup updated successfully.');
    }

    public function destroy(Matchup $matchup)
    {
        $matchup->delete();

        return back()->with('success', 'Matchup deleted.');
    }


    public function getTeamsByCategory(Request $request, $categoryId)
    {
        $eventId = $request->query('event_id');

        if (!$eventId) {
            return response()->json(['error' => 'Event ID is required'], 400);
        }

        $category = Category::findOrFail($categoryId);

        $teams = $category->teams()->wherePivot('event_id', $eventId)->get();

        return response()->json($teams);
    }
}
