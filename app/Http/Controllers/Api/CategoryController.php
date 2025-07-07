<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NextEvent;

class CategoryController extends Controller
{
    public function index(NextEvent $event)
    {
        // Eager load categories for the event
        $categories = $event->categories()->get();

        return response()->json($categories);
    }

    
}
