<?php

/** @var \Illuminate\Notifications\DatabaseNotification|null $notification */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomPlayerController;
use App\Http\Controllers\Api\MatchupController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\MatchSummaryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CarouselController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\SocialStatsController;
use App\Http\Controllers\Api\NextEventController;
use App\Http\Resources\NotificationResource;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\GalleryController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum', 'verified')->get('/user', function (Request $request) {
//     return $request->user();
// });




Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/carousel', [CarouselController::class, 'index']);

Route::get('/events', [NextEventController::class, 'index']);
Route::get('/events/{id}', [NextEventController::class, 'show']);
Route::get('/events-leaderboard/{id}', [NextEventController::class, 'eventLeaderboard']);
//
Route::get('/next-events/search', [NextEventController::class, 'search']);
Route::get('/next-events/suggestions', [NextEventController::class, 'suggestions']);
Route::get('/next-events/{id}', [NextEventController::class, 'show']);



Route::get('/gallery', [GalleryController::class, 'index']);



Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('teams', TeamController::class);
    Route::post('teams/{team}/players', [PlayerController::class, 'store']);
    Route::delete('teams/{team}/players/{player}', [PlayerController::class, 'destroy']);
    Route::post('/teams/{team}/events/{event}/assign', [TeamController::class, 'assignToEvent']);
});

Route::middleware('auth:sanctum')->get('/users', [UserController::class, 'index']);



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile/upload-image', [ProfileController::class, 'uploadImage']);
});
Route::get('/player-profile/{id}', [ProfileController::class, 'showPlayerProfile']);


Route::delete('/teams/{team}/players/{player}', [PlayerController::class, 'destroy']);
Route::get('/players/search', [PlayerController::class, 'search']);
Route::get('/players/suggestions', [PlayerController::class, 'suggestions']);



Route::get('/matchups/{matchup}/summary', [MatchSummaryController::class, 'show']);



Route::get('/leaderboard/players', [LeaderboardController::class, 'playerLeaderboard']);
Route::get('/leaderboard/teams', [LeaderboardController::class, 'teamLeaderboard']);
Route::get('/leaderboard/teams/{teamId}/players', [LeaderboardController::class, 'teamPlayers']);




Route::get('events/{event}/teams', [NextEventController::class, 'getTeamsAssignedToEvent']);

Route::get('/matchups', [MatchupController::class, 'index']);
Route::get('/matchups/{id}', [MatchupController::class, 'show']);

// Ignore red line error its just a false error

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', function () {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        return NotificationResource::collection($user->notifications);
    });

    Route::get('/notifications/unread-count', function () {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'unread_count' => $user->unreadNotifications->count()
        ]);
    });

    Route::post('/notifications/{id}/read', function ($id) {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    });

    Route::post('/notifications/read-all', function () {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read']);
    });

    Route::delete('/notifications/{id}', function ($id) {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Notification deleted']);
    });

    Route::delete('/notifications', function () {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user->notifications()->delete();

        return response()->json(['message' => 'All notifications deleted']);
    });
});



Route::post('/teams/{team}/categories/{category}/assign', [TeamController::class, 'assignToCategory'])->middleware('auth:sanctum');
Route::get('/events/{event}/categories', [CategoryController::class, 'index']);



Route::get('/events/{event}/teams-by-category', [CategoryController::class, 'teamsByCategory']);
Route::get('/events/{eventId}/teams-by-category-with-names', [CategoryController::class, 'teamsByCategoryWithNames']);
Route::get('/teams/{team}/players', [TeamController::class, 'players']);




// verify email


Route::middleware('auth:sanctum')->post('/send-verification-code', [AuthController::class, 'sendVerificationCode']);
Route::middleware('auth:sanctum')->post('/verify-email-code', [AuthController::class, 'verifyEmailCode']);



// socials 

Route::get('/social-stats', [SocialStatsController::class, 'getStats']);
