<?php

namespace App\Services;

use App\Models\Dashboard;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Csv\Reader;


class DashboardService{
    public static function getDashboardDataForUser(int $userId): array{
        $dailyRecords = Dashboard::where('user_id', $userId)
            ->orderBy('date')
            ->get();


        $daily = [
            'dates' => $dailyRecords->pluck('date')->toArray(),
            'steps' => $dailyRecords->pluck('steps')->toArray(),
            'active_minutes' => $dailyRecords->pluck('active_minutes')->toArray(),
            'distance' => $dailyRecords->pluck('distance')->toArray(),
        ];


        $weeklyGroups = $dailyRecords->groupBy(function ($record) {
            return Carbon::parse($record->date)->format('o-W');
        });


        $weeklyDates = [];
        $weeklySteps = [];
        $weeklyActiveMinutes = [];
        $weeklyDistance = [];

        foreach ($weeklyGroups as $weekKey => $group) {
            $weeklyDates[] = $group->first()->date;
            $weeklySteps[] = $group->sum('steps');
            $weeklyActiveMinutes[] = $group->sum('active_minutes');
            $weeklyDistance[] = $group->sum('distance');
        }

        $weekly = [
            'dates' => $weeklyDates,
            'steps' => $weeklySteps,
            'active_minutes' => $weeklyActiveMinutes,
            'distance' => $weeklyDistance,
        ];


        return [
            'daily'  => $daily,
            'weekly' => $weekly,
        ];
    }
    public static function processCsvAndStore(string $filePath) {
        $fullPath = storage_path('app/' . $filePath);

        if (!file_exists($fullPath)) {
            throw new \Exception("CSV file not found at $fullPath");
        }

        $csv = Reader::createFromPath($fullPath, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $record) {
            $date = Carbon::parse($record['date']);
            $steps = (int) $record['steps'];
            $userId = Auth::id();

            Dashboard::updateOrCreate(
                [
                    'user_id' => $userId,
                    'date' => $date->toDateString()
                ],
                [
                    'steps' => $steps
                ]
            );
        }
    }

}


