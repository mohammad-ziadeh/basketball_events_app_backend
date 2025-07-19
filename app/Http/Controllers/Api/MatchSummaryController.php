<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Matchup;

class MatchSummaryController extends Controller
{
    public function show($matchupId)
    {
        $matchup = Matchup::with([
            'teamA',
            'teamB',
            'winner',
            'stats.player.user',
            'activePlayers',
            'category'
        ])->findOrFail($matchupId);

        $stats = $matchup->stats;

        $topScorer = $stats->sortByDesc('points')->first();
        $topAssist = $stats->sortByDesc('assists')->first();

        $teamPoints = ['A' => 0, 'B' => 0];
        $teamFouls = ['A' => 0, 'B' => 0];

        foreach ($stats as $stat) {
            $active = $matchup->activePlayers
                ->firstWhere('player_id', $stat->player_id);

            if ($active) {
                $side = $active->team_side;
                $teamPoints[$side] += $stat->points;
                $teamFouls[$side] += $stat->fouls;
            }
        }

        return response()->json([
            'match_time' => optional($matchup->match_time)->toIso8601String(),
            'location' => $matchup->location,
            'round' => $matchup->round,
            'category' => [
                'id' => $matchup->category?->id,
                'name' => $matchup->category?->name,
            ],

            'team_a_name' => $matchup->teamA->name,
            'team_b_name' => $matchup->teamB->name,
            'winner_team' => $matchup->winner?->name,

            'final_score' => [
                'team_a' => $matchup->team_a_score ?? $teamPoints['A'],
                'team_b' => $matchup->team_b_score ?? $teamPoints['B'],
            ],

            'top_score_player' => $topScorer ? [
                'player_id' => $topScorer->player->id,
                'player_name' => $topScorer->player->user->name,
                'user_id' => $topScorer->player->user->id,
                'points' => $topScorer->points,
                'assists' => $topScorer->assists,
                'fouls' => $topScorer->fouls,
            ] : null,

            'top_assist_player' => $topAssist ? [
                'player_id' => $topAssist->player->id,
                'player_name' => $topAssist->player->user->name,
                'points' => $topAssist->points,
                'user_id' => $topAssist->player->user->id,
                'assists' => $topAssist->assists,
                'fouls' => $topAssist->fouls,
            ] : null,

            'most_foul_team' => $teamFouls['A'] > $teamFouls['B']
                ? [
                    'team_id' => $matchup->teamA->id,
                    'team_name' => $matchup->teamA->name,
                    'fouls' => $teamFouls['A'],
                ]
                : ($teamFouls['B'] > $teamFouls['A'] ? [
                    'team_id' => $matchup->teamB->id,
                    'team_name' => $matchup->teamB->name,
                    'fouls' => $teamFouls['B'],
                ] : null),

            'team_a_points' => [
                'team_id' => $matchup->teamA->id,
                'team_name' => $matchup->teamA->name,
                'points' => $teamPoints['A'],
            ],
            'team_b_points' => [
                'team_id' => $matchup->teamB->id,
                'team_name' => $matchup->teamB->name,
                'points' => $teamPoints['B'],
            ],

            'all_player_stats' => $stats->map(function ($stat) {
                return [
                    'player_id' => $stat->player->id,
                    'player_name' => $stat->player->user->name,
                    'points' => $stat->points,
                    'assists' => $stat->assists,
                    'user_id' => $stat->player->user->id,
                    'fouls' => $stat->fouls,
                ];
            })->values(),
        ]);
    }
}
