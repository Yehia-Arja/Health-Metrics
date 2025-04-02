<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use GuzzleHttp\Client;

class PredictionService
{
    const GEMINI_API_KEY = 'AIzaSyA7llMcacIIdTtTcQ7ZZ9RDvzIp2BpzSKU';
    const GEMINI_API_ENDPOINT = 'https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent';

    //Generate prediction for the last 7 days of step data
    public static function generateGoalPrediction(int $userId): array
    {
        $last7DaysData = DB::table('dashboards')
            ->where('user_id', $userId)
            ->where('date', '>=', Carbon::now()->subDays(7)->toDateString())
            ->orderBy('date')
            ->get(['date', 'steps', 'active_minutes']);

        if ($last7DaysData->isEmpty()) {
            return [
                'prediction_message' => 'Not enough data to predict goal achievement.'
            ];
        }

        $rawData = [];
        foreach ($last7DaysData as $record) {
            $rawData[] = [
                'date' => Carbon::parse($record->date)->format('Y-m-d'),
                'steps' => $record->steps,
                'active_minutes' => $record->active_minutes
            ];
        }

        $prompt = "You are a health analytics assistant. Given this 7-day dataset containing steps and active minutes:"
                . json_encode($rawData, JSON_PRETTY_PRINT)
                . "Evaluate whether the user is likely to meet the following daily goals: 10,000 steps and 70 active minutes."
                . "Only respond with a prediction message keep it simple just a one line if he's gonna meet or no and based on what.";

        $geminiMessage = self::callGeminiApi($prompt);

        return [
            'prediction_message' => $geminiMessage
        ];
    }

    //Generate anomaly detection for the last 7 days of step data
    public static function generateAnomalyDetection(int $userId): array
    {
        $last7DaysData = DB::table('dashboards')
            ->where('user_id', $userId)
            ->where('date', '>=', Carbon::now()->subDays(7)->toDateString())
            ->orderBy('date')
            ->get(['date', 'active_minutes']);

        if ($last7DaysData->isEmpty()) {
            return [
                'prediction_message' => 'Not enough data to analyze anomalies.'
            ];
        }

        $rawData = [];
        foreach ($last7DaysData as $record) {
            $rawData[] = [
                'date' => Carbon::parse($record->date)->format('Y-m-d'),
                'active_minutes' => $record->active_minutes
            ];
        }

        $prompt = "You are a health analytics assistant. Given this array of 7 days of activity minutes data:"
                . json_encode($rawData, JSON_PRETTY_PRINT)
                . "Please:"
                . "- Calculate the average and standard deviation of minutes active."
                . "- Identify and list any anomalies."
                . "- Briefly explain the anomalies and suggest possible reasons."
                . "Only respond with a simple message that these days u were below average.";

        $geminiMessage = self::callGeminiApi($prompt);

        return [
            'prediction_message' => $geminiMessage
        ];
    }

   
    private static function callGeminiApi(string $prompt): string
    {
        $client = new Client();
        $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent?key=' . self::GEMINI_API_KEY;

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
