<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matchup;
use App\Models\MatchStat;
use App\Models\MatchupPlayer;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;

class RefereeController extends Controller
{
    public function showMatchControl($id)
    {
        $matchup = Matchup::with([
            'teamA.players.user',
            'teamB.players.user',
            'activePlayers.player.user',
            'stats.player.user'
        ])->findOrFail($id);

        return view('admin.referee.match-control', compact('matchup'));
    }

    public function assignActivePlayers(Request $request, Matchup $matchup)
    {
        $request->validate([
            'team_a_players' => 'required|array|min:2|max:4',
            'team_b_players' => 'required|array|min:2|max:4',
        ]);

        $matchup->activePlayers()->delete();

        foreach ($request->team_a_players as $playerId) {
            MatchupPlayer::create([
                'matchup_id' => $matchup->id,
                'player_id' => $playerId,
                'team_side' => 'A'
            ]);
        }

        foreach ($request->team_b_players as $playerId) {
            MatchupPlayer::create([
                'matchup_id' => $matchup->id,
                'player_id' => $playerId,
                'team_side' => 'B'
            ]);
        }

        return back()->with('success', 'Players assigned successfully');
    }

    public function updatePlayerStat(Request $request)
    {
        $data = $request->validate([
            'matchup_id' => 'required|exists:matchups,id',
            'player_id' => 'required|exists:players,id',
            'points' => 'nullable|integer|min:0',
            'assists' => 'nullable|integer|min:0',
            'fouls' => 'nullable|integer|min:0',
        ]);

        $player = Player::with('user', 'team')->findOrFail($data['player_id']);

        $existingStat = MatchStat::where('matchup_id', $data['matchup_id'])
            ->where('player_id', $data['player_id'])
            ->first();

        $previousPoints = $existingStat?->points ?? 0;
        $newPoints = $data['points'] ?? 0;

        $stat = MatchStat::updateOrCreate(
            [
                'matchup_id' => $data['matchup_id'],
                'player_id' => $data['player_id'],
            ],
            $data
        );

        $diff = $newPoints - $previousPoints;

        $player->user->increment('points', $diff);
        $player->team->increment('points', $diff);

        return back()->with('success', 'Match stats and totals updated successfully');
    }

    public function startMatchTimer(Matchup $matchup)
    {
        $matchup->update(['start_time' => now()]);
        return back()->with('success', 'Match timer started');
    }

    public function resetMatchTimer(Matchup $matchup)
    {
        $matchup->update(['start_time' => null]);
        return back()->with('success', 'Match timer reset');
    }

    public function setWinner(Request $request, Matchup $matchup)
    {
        $request->validate([
            'winner_team_id' => 'required|in:' . $matchup->team_a_id . ',' . $matchup->team_b_id,
        ]);

        $matchup->update(['winner_team_id' => $request->winner_team_id]);

        return back()->with('success', 'Winner set');
    }

    public function showScoreboard(Matchup $matchup)
    {

        $matchup->load(['stats.player.team']);

        $teamAFouls = $matchup->stats->filter(function ($stat) use ($matchup) {
            return $stat->player && $stat->player->team_id === $matchup->team_a_id;
        })->sum('fouls');

        $teamBFouls = $matchup->stats->filter(function ($stat) use ($matchup) {
            return $stat->player && $stat->player->team_id === $matchup->team_b_id;
        })->sum('fouls');

        return view('admin.referee.scoreboard', compact('matchup', 'teamAFouls', 'teamBFouls'));
    }
}
