<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NextEvent;
use Illuminate\Http\Request;

class MatchupController extends Controller
{
    public function index(Request $request)
    {
        $events = NextEvent::with([
            'matchups' => function ($q) {
                $q->select(
                    'id',
                    'event_id',
                    'category_id',
                    'team_a_id',
                    'team_b_id',
                    'match_time',
                    'location',
                    'start_time',
                    'round',
                    'team_a_score',
                    'team_b_score',
                    'winner_team_id'
                );
            },
            'matchups.teamA:id,name',
            'matchups.teamB:id,name',
            'matchups.category:id,name',
        ])
            ->get(['id', 'title', 'event_code']);

        return response()->json($events);
    }

    public function show($id)
    {
        $matchup = \App\Models\Matchup::with([
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
