<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\NextEvent;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class StaticsController extends Controller
{
    public function index()
    {


        // Users Male vs Female
        $maleCount = User::where('gender', 'male')->count();
        $femaleCount = User::where('gender', 'female')->count();

        $genderChart = new Chart;
        $genderChart->labels(['Male', 'Female']);
        $genderChart->dataset('Users by Gender', 'pie', [$maleCount, $femaleCount])
            ->backgroundColor(['#1A1A1D', '#eeeeee'])
            ->options([
                'maintainAspectRatio' => false,
                'responsive' => true,
            ]);

        // Users by Ages
        $now = Carbon::now();

        $ageRanges = [
            '0-12'  => [0, 12],
            '13-18' => [13, 18],
            '19-25' => [19, 25],
            '26-35' => [26, 35],
            '36+'   => [36, null],
        ];

        $ageGroups = [];

        foreach ($ageRanges as $label => [$min, $max]) {
            if ($max === null) {
                $count = User::where('birth_date', '<=', $now->copy()->subYears($min)->endOfDay())->count();
            } elseif ($min === 0) {
                $count = User::where('birth_date', '>=', $now->copy()->subYears($max)->startOfDay())->count();
            } else {
                $count = User::whereBetween('birth_date', [
                    $now->copy()->subYears($max)->startOfDay(),
                    $now->copy()->subYears($min)->endOfDay()
                ])->count();
            }

            $ageGroups[$label] = $count;
        }

        $ageChart = new Chart;
        $ageChart->labels(array_keys($ageGroups));
        $ageChart->dataset('Users by Age Group', 'line', array_values($ageGroups))
            ->backgroundColor('#1A1A1D', '#eeeeee')
            ->options([
                'maintainAspectRatio' => false,
                'responsive' => true,
            ]);


        // Group events by month name
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
        ]);




        return view('dashboard', compact('genderChart', 'eventChart', 'ageChart'));
    }
}
