<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;


class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user->load('player.team.events.categories.groups.matchups.stats');

        $player = $user->player;

        $events = [];

        if ($player && $player->team) {
            $teamId = $player->team->id;

            $events = $player->team->events->map(function ($event) use ($player, $teamId) {
                $category = $player->team->getCategoryForEvent($event->id);

                $matchups = $event->categories
                    ->flatMap(fn($cat) => $cat->groups)
                    ->flatMap(fn($group) => $group->matchups);

                $teamMatchups = $matchups->filter(
                    fn($matchup) =>
                    $matchup->team_a_id === $teamId || $matchup->team_b_id === $teamId
                );

                $points = $teamMatchups->reduce(function ($carry, $matchup) use ($teamId) {
                    if ($matchup->team_a_id === $teamId) {
                        return $carry + ($matchup->team_a_score ?? 0);
                    } elseif ($matchup->team_b_id === $teamId) {
                        return $carry + ($matchup->team_b_score ?? 0);
                    }
                    return $carry;
                }, 0);


                return [
                    'event_name' => $event->title,
                    'event_id' => $event->id,
                    'points' => $points,
                    'team_name' => $player->team->name,
                    'category_name' => $category?->name,
                    'start_date' => $event->start_date->format('M d, Y'),
                    'date_range' => $event->start_date->format('M d, Y') . ' - ' . $event->end_date->format('M d, Y'),

                ];
            });
        }


        $allPlayers = User::where('role', 'player')
            ->orderByDesc('points')
            ->pluck('id');

        $rankIndex = $allPlayers->search($user->id);
        $rank = $rankIndex === false ? null : $rankIndex + 1;

        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'name' => $user->first_name . ' ' . $user->last_name,
            'email' => $user->email,
            'gender' => $user->gender,
            'birth_date' => $user->birth_date,
            'role' => $user->role,
            'points' => $user->points ?? 0,
            'rank' => $rank ? (string)$rank : 'N/A',
            'avatar_url' => $user->avatar_url,
            'background_url' => $user->background_url,
            'events' => $events,
            'badges' => $this->getBadges($user),
        ]);
    }


    protected function getBadges($user)
    {
        $points = $user->points ?? 0;

        return [
            ['title' => 'Rookie', 'unlocked' => $points >= 0],
            ['title' => 'Pro Player', 'unlocked' => $points >= 75],
            ['title' => 'All-Star', 'unlocked' => $points >= 120],
            ['title' => 'Elite', 'unlocked' => $points >= 200],
            ['title' => 'Legend', 'unlocked' => $points >= 350],
        ];
    }



    public function uploadImage(Request $request)
    {
        $request->validate([
            'type' => 'required|in:avatar_url,background_url',
            'image' => 'required|image|max:2048',
        ]);

        $user = \App\Models\User::find(auth()->id());

        $path = $request->file('image')->store('profile_images', 'public');

        $user->{$request->type} = asset('storage/' . $path);
        $user->save();

        return response()->json(['message' => 'Image updated successfully']);
    }


    public function showPlayerProfile($id)
    {
        $user = User::with([
            'player.team.events.categories.groups.matchups.stats'
        ])->find($id);

        if (!$user) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        $player = $user->player;
        $events = [];

        if ($player && $player->team) {
            $teamId = $player->team->id;

            $events = $player->team->events->map(function ($event) use ($player, $teamId) {
                $category = $player->team->getCategoryForEvent($event->id);

                $matchups = $event->categories
                    ->flatMap(fn($cat) => $cat->groups)
                    ->flatMap(fn($group) => $group->matchups);

                $teamMatchups = $matchups->filter(
                    fn($matchup) =>
                    $matchup->team_a_id === $teamId || $matchup->team_b_id === $teamId
                );

                $points = $teamMatchups->reduce(function ($carry, $matchup) use ($teamId) {
                    if ($matchup->team_a_id === $teamId) {
                        return $carry + ($matchup->team_a_score ?? 0);
                    } elseif ($matchup->team_b_id === $teamId) {
                        return $carry + ($matchup->team_b_score ?? 0);
                    }
                    return $carry;
                }, 0);

                return [
                    'event_name' => $event->title,
                    'event_id' => $event->id,
                    'points' => $points,
                    'team_name' => $player->team->name,
                    'category_name' => $category?->name,
                    'start_date' => $event->start_date->format('M d, Y'),
                    'date_range' => $event->start_date->format('M d, Y') . ' - ' . $event->end_date->format('M d, Y'),
                ];
            });
        }

        $allPlayers = User::where('role', 'player')
            ->orderByDesc('points')
            ->pluck('id');

        $rankIndex = $allPlayers->search($user->id);
        $rank = $rankIndex === false ? null : $rankIndex + 1;

        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'name' => $user->first_name . ' ' . $user->last_name,
            'email' => $user->email,
            'gender' => $user->gender,
            'birth_date' => $user->birth_date,
            'role' => $user->role,
            'points' => $user->points ?? 0,
            'rank' => $rank ? (string)$rank : 'N/A',
            'avatar_url' => $user->avatar_url,
            'background_url' => $user->background_url,
            'events' => $events,
            'badges' => $this->getBadges($user),
        ]);
    }
}
