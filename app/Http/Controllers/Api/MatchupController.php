<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NextEvent;
use App\Models\Matchup;
use Illuminate\Http\Request;

class MatchupController extends Controller
{
    public function index(Request $request)
    {
        $events = NextEvent::with([
            'categories.groups.matchups' => function ($query) {
                $query->select(
                    'id',
                    'event_id',
                    'category_id',
                    'team_a_id',
                    'team_b_id',
                    'group_id',
                    'match_time',
                    'location',
                    'start_time',
                    'round',
                    'team_a_score',
                    'team_b_score',
                    'winner_team_id'
                );
            },
            'categories.groups.matchups.teamA:id,name',
            'categories.groups.matchups.teamB:id,name',
            'categories.groups.matchups.category:id,name',
            'matchups.teamA:id,name',
            'matchups.teamB:id,name',
            'matchups.category:id,name',
            'categories.teams:id,name',
        ])->get(['id', 'title', 'event_code']);

        // Only attach grouped matchups to their corresponding groups
        foreach ($events as $event) {
            foreach ($event->categories as $category) {
                // Filter matchups belonging to this category
                $categoryMatchups = $event->matchups->where('category_id', $category->id);

                // Group by group_id only
                $grouped = $categoryMatchups->whereNotNull('group_id')->groupBy('group_id');

                // Attach matchups to their group
                $category->groups->each(function ($group) use ($grouped) {
                    $group->matchups = $grouped->get($group->id, collect())->values();
                });
            }
        }

        return response()->json($events);
    }

    public function show($id)
    {
        $matchup = Matchup::with([
            'teamA:id,name',
            'teamB:id,name',
            'category:id,name',
        ])->select(
            'id',
            'event_id',
            'category_id',
            'team_a_id',
            'team_b_id',
            'start_time',
            'match_time',
            'location',
            'round',
            'team_a_score',
            'team_b_score',
            'winner_team_id'
        )->findOrFail($id);

        return response()->json($matchup);
    }
}
