<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Player;
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
                    'player_id' => $user->id,
                    'name' => $user->name,
                    'team_name' => $user->player->team->name ?? 'Unassigned',
                    'avatar_url' => $user->avatar_url ?? '',
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

    public function teamPlayers($teamId)
    {
        $team = Team::find($teamId);

        if (!$team) {
            return response()->json(['message' => 'Team not found'], 404);
        }

        $players = Player::with('user')
            ->where('team_id', $teamId)
            ->get()
            ->map(function ($player) {
                return [
                    'player_id' => $player->user->id,
                    'name' => $player->user->name,
                    'avatar_url' => $player->user->avatar_url ?? '',
                    'points' => $player->user->points ?? 0,
                ];
            });

        return response()->json([
            'team_id' => $team->id,
            'team_name' => $team->name,
            'players' => $players,
        ]);
    }
}
