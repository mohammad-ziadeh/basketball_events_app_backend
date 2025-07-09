<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

use App\Models\NextEvent;

class CategoryController extends Controller
{


    public function index(NextEvent $event)
    {
        // Eager load categories for the event
        $categories = $event->categories()->get();

        return response()->json($categories);
    }
    public function teamsByCategory($eventId)
    {
        $categories = Category::where('event_id', $eventId)->with(['teams'])->get();

        $teams = [];

        foreach ($categories as $category) {
            $teams[$category->id] = $category->teams;
        }

        return response()->json($teams);
    }
}
