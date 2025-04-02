<?php

namespace App\Services;

use App\Models\Dashboard;
use Carbon\Carbon;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public static function getDashboardDataForUser(int $userId): array
    {
        $cached = DB::table('dashboard_cache')->where('user_id', $userId)->first();

        if (!$cached) {
            return ['daily_by_week' => [], 'weekly_by_month' => []];
        }

        return [
            'daily_by_week' => json_decode(gzuncompress(base64_decode($cached->daily_by_week)), true),
            'weekly_by_month' => json_decode(gzuncompress(base64_decode($cached->weekly_by_month)), true),
        ];
    }

    public static function processCsvAndStore(string $filePath)
    {
        $records = self::parseCsv(storage_path('app/' . $filePath));
        $chunks = array_chunk($records, 500);

        foreach ($chunks as $chunk) {
            self::upsertDashboardRecords($chunk);
            usleep(200000);
        }

        self::generateAndCacheDashboardData(1);
    }

    public static function generateAndCacheDashboardData(int $userId): void
    {
        $dailyByWeek = [];
        $weeklyByMonth = [];

        
        Dashboard::where('user_id', $userId)
            ->orderBy('date')
            ->chunk(500, function ($records) use (&$dailyByWeek) {
                foreach ($records as $record) {
                    $dt = Carbon::parse($record->date);
                    $key = $dt->format('Y-\\WW');
                    $dailyByWeek[$key][] = [
                        'date' => $dt->format('Y-m-d'),
                        'steps' => $record->steps,
                        'active_minutes' => $record->active_minutes,
                        'distance' => $record->distance,
                    ];
                }
            });

        $weeklyRaw = DB::table('dashboards')
            ->select(
                DB::raw('YEARWEEK(date, 1) as week_key'),
                DB::raw('WEEK(date, 1) as week_number'),
                DB::raw('YEAR(date) as year_number'),
                DB::raw('MONTH(date) as month_number'),
                DB::raw('MIN(date) as start_date'),
                DB::raw('SUM(steps) as total_steps'),
                DB::raw('SUM(active_minutes) as total_active_minutes'),
                DB::raw('SUM(distance) as total_distance')
            )
            ->where('user_id', $userId)
            ->groupBy('week_key', 'week_number', 'month_number', 'year_number')
            ->orderBy('start_date')
            ->get();

        foreach ($weeklyRaw as $week) {
            
            $monthKey = Carbon::parse($week->start_date)->format('Y-m');
            $weeklyByMonth[$monthKey][] = [
                'week' => $week->week_number,
                'start_date' => Carbon::parse($week->start_date)->format('Y-m-d'),
                'total_steps' => $week->total_steps,
                'total_active_minutes' => $week->total_active_minutes,
                'total_distance' => $week->total_distance,
            ];
        }

       
        DB::table('dashboard_cache')->updateOrInsert(
            ['user_id' => $userId],
            [
                'daily_by_week' => base64_encode(gzcompress(json_encode($dailyByWeek))),
                'weekly_by_month' => base64_encode(gzcompress(json_encode($weeklyByMonth))),
                'updated_at' => now(),
            ]
        );
    }

    private static function parseCsv(string $fullPath): array
    {
        if (!file_exists($fullPath)) {
            throw new \Exception("CSV file not found at $fullPath");
        }

        $csv = Reader::createFromPath($fullPath, 'r');
        $csv->setHeaderOffset(0);

        $records = [];
        foreach ($csv->getRecords() as $record) {
            $records[] = [
                'user_id' => 1,
                'date' => Carbon::parse($record['date'])->toDateString(),
                'steps' => (int) $record['steps'],
                'active_minutes' => (int) $record['active_minutes'],
                'distance' => (float) $record['distance_km'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $records;
    }

    private static function upsertDashboardRecords(array $records)
    {
        Dashboard::upsert(
            $records,
            ['user_id', 'date'],
            ['steps', 'active_minutes', 'distance', 'updated_at']
        );
    }
}
