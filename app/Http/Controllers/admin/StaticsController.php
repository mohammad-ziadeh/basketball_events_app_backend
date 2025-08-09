<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\NextEvent;
use App\Http\Controllers\Controller;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class StaticsController extends Controller
{
    public function index()
    {
        // 1. Users Male vs Female
        $maleCount = User::where('gender', 'male')->count();
        $femaleCount = User::where('gender', 'female')->count();

        $genderChart = new Chart;
        $genderChart->labels(['Male', 'Female']);
        $genderChart->dataset('Users by Gender', 'pie', [$maleCount, $femaleCount])
            ->backgroundColor(['#1A1A1D', '#eeeeee'])
            ->options([
                'maintainAspectRatio' => false,
                'responsive' => true,
                'height' => 300,
                'width' => 300,
            ]);


        // 2. Number of Events by Month (this month example)
        // Group events by month name, counting how many per month
        $eventsByMonth = NextEvent::query()
            ->selectRaw("DATE_FORMAT(start_date, '%M') AS month, COUNT(*) AS total")
            ->groupBy('month')
            ->orderByRaw("MIN(start_date)")
            ->pluck('total', 'month');

        $eventChart = new Chart;
        $eventChart->labels($eventsByMonth->keys()->all());
        $eventChart->dataset('Events by Month', 'bar', $eventsByMonth->values()->all())
            ->backgroundColor('#1A1A1D');

        $eventChart->options([
            'maintainAspectRatio' => false,
            'responsive' => true,
            'height' => 300,
            'width' => 900,
        ]);




        return view('dashboard', compact('genderChart', 'eventChart'));
    }
}
