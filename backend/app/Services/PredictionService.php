<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use GuzzleHttp\Client;

class PredictionService
{
    const GEMINI_API_KEY = 'AIzaSyA7llMcacIIdTtTcQ7ZZ9RDvzIp2BpzSKU';
    const GEMINI_API_ENDPOINT = 'https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent';

    // Fetch the most recent 7 days of data
    private static function getLatest7DaysData(int $userId, array $columns): array
    {
        $records = DB::table('dashboards')
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->limit(7)
            ->get($columns)
            ->reverse()
            ->values();

        return $records->toArray();
    }

    public static function generateGoalPrediction(int $userId): array
    {
        $last7DaysData = self::getLatest7DaysData($userId, ['date', 'steps', 'active_minutes']);

        if (empty($last7DaysData)) {
            return [
                'prediction_message' => 'Not enough data to predict goal achievement.'
            ];
        }

        $rawData = array_map(function ($record) {
            return [
                'date' => Carbon::parse($record->date)->format('Y-m-d'),
                'steps' => $record->steps,
                'active_minutes' => $record->active_minutes
            ];
        }, $last7DaysData);

        $prompt = "You are a health analytics assistant. Given this 7-day dataset containing steps and active minutes:" .
            json_encode($rawData, JSON_PRETTY_PRINT) .
            "Evaluate whether the user is likely to meet the following daily goals: 10,000 steps and 70 active minutes. " .
            "Only respond with a prediction message keep it simple just a one line if he's gonna meet or not and based on what.";

        return [
            'prediction_message' => self::callGeminiApi($prompt)
        ];
    }

    public static function generateAnomalyDetection(int $userId): array
    {
        $last7DaysData = self::getLatest7DaysData($userId, ['date', 'active_minutes']);

        if (empty($last7DaysData)) {
            return [
                'prediction_message' => 'Not enough data to analyze anomalies.'
            ];
        }

        $rawData = array_map(function ($record) {
            return [
                'date' => Carbon::parse($record->date)->format('Y-m-d'),
                'active_minutes' => $record->active_minutes
            ];
        }, $last7DaysData);

        $prompt = "You are a health analytics assistant. Given this array of 7 days of activity minutes data:" .
            json_encode($rawData, JSON_PRETTY_PRINT) .
            "Please: - Calculate the average and standard deviation of minutes active. - Identify and list any anomalies. " .
            "- Briefly explain the anomalies and suggest possible reasons. Only respond with a simple message that these days u were below average.";

        return [
            'prediction_message' => self::callGeminiApi($prompt)
        ];
    }

    public static function generateFutureProjection(int $userId): array
    {
        $last7DaysData = self::getLatest7DaysData($userId, ['date', 'steps', 'active_minutes']);

        if (empty($last7DaysData)) {
            return [
                'prediction_message' => 'Not enough data to project future trends.'
            ];
        }

        $rawData = array_map(function ($record) {
            return [
                'date' => Carbon::parse($record->date)->format('Y-m-d'),
                'steps' => $record->steps,
                'active_minutes' => $record->active_minutes
            ];
        }, $last7DaysData);

        $prompt = "You are a health data predictor. Based on this past week's steps and active minutes data:" .
            json_encode($rawData, JSON_PRETTY_PRINT) .
            "Project how the user might perform in the upcoming 7 days. Provide a one-line summary of expected average steps and active minutes and just answer by the preidction message directly.";

        return [
            'prediction_message' => self::callGeminiApi($prompt)
        ];
    }

    public static function generateActionableInsights(int $userId): array
    {
        $last7DaysData = self::getLatest7DaysData($userId, ['date', 'steps', 'active_minutes']);

        if (empty($last7DaysData)) {
            return [
                'prediction_message' => 'Not enough data to generate insights.'
            ];
        }

        $rawData = array_map(function ($record) {
            return [
                'date' => Carbon::parse($record->date)->format('Y-m-d'),
                'steps' => $record->steps,
                'active_minutes' => $record->active_minutes
            ];
        }, $last7DaysData);

        $prompt = "You are a health insights generator. Based on this 7-day activity log:" .
            json_encode($rawData, JSON_PRETTY_PRINT) .
            "Suggest practical changes or improvements the user can apply to optimize their health routine. Keep it short and actionable and answer dirctly with the message.";

        return [
            'prediction_message' => self::callGeminiApi($prompt)
        ];
    }

    private static function callGeminiApi(string $prompt): string
    {
        $client = new Client();
        $url = self::GEMINI_API_ENDPOINT . '?key=' . self::GEMINI_API_KEY;

        try {
            $response = $client->post($url, [
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 300,
                    ],
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 30,
            ]);

            $responseData = json_decode($response->getBody(), true);

            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                return $responseData['candidates'][0]['content']['parts'][0]['text'];
            }

        } catch (\Exception $e) {
            return "Error generating prediction: " . $e->getMessage();
        }

        return "No prediction generated.";
    }
}
