<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Team;

class LeaderBoardController extends Controller
{


    public function playerLeaderboard()
    {
        $players = User::whereHas('player')
            ->with('player.team')
            ->orderByDesc('points')
            ->get()
            ->map(function ($user) {
                return [
                    'player_id' => $user->player->id,
                    'name' => $user->name,
                    'team_name' => $user->player->team->name ?? 'Unassigned',
                    'points' => $user->points,
                ];
            });

        return response()->json($players);
    }


    public function teamLeaderboard()
    {
        $teams = Team::orderByDesc('points')->get([
            'id',
            'name',
            'points'
        ]);

        return response()->json($teams);
    }
}
