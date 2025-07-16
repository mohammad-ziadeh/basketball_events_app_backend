<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Team;
use App\Notifications\TeamAcceptedNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AcceptCategoryTeamController extends Controller
{
    public function index($eventId)
    {
        $cats = Category::with(['teams' => function ($query) use ($eventId) {
            $query->wherePivot('event_id', $eventId);
        }])->where('event_id', $eventId);

        $categories = $cats->paginate(10);

        return view('admin.acceptTeams.acceptTeams', compact('categories', 'eventId'));
    }

    public function updateStatus(Request $request, $teamId, $categoryId)
    {
        $request->validate([
            'status' => 'required|in:approved,denied',
        ]);

        DB::table('event_team')
            ->where('team_id', $teamId)
            ->where('category_id', $categoryId)
            ->update(['status' => $request->status]);


        if ($request->status === 'approved') {
            $team = Team::with('coach')->findOrFail($teamId);
            $category = Category::findOrFail($categoryId);

            if ($team->coach) {
                $team->coach->notify(new TeamAcceptedNotification($team, $category));
            }
        }

        return redirect()->back()->with('success', 'Team status updated successfully.');
    }
}
