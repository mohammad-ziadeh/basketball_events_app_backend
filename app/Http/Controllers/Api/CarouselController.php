<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Carousel;




class CarouselController extends Controller
{
    public function index(): JsonResponse
    {
        $carousels = Carousel::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'image_url' => $item->image_url,
                'tag' => $item->tag,
                'title' => $item->title,
                'updated_at' => $item->updated_at ? $item->updated_at->timestamp : null,
            ];
        });

        return response()->json($carousels);
    }
}
