<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Prediction;

class PredictionService
{
    const GEMINI_API_KEY = 'AIzaSyA7llMcacIIdTtTcQ7ZZ9RDvzIp2BpzSKU';
    const GEMINI_API_ENDPOINT = 'https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent';

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

    public static function generateAndStorePrediction(int $userId, string $type, string $prompt): array
    {
        $message = self::callGeminiApi($prompt);
        
        Prediction::updateOrCreate(
            ['user_id' => $userId, 'prediction_type' => $type],
            [ 'updated_at' => now(),'content' => $message,]
        );

        return ['prediction_message' => $message];
    }

    public static function generateGoalPrediction(int $userId): array
    {
        $data = self::getLatest7DaysData($userId, ['date', 'steps', 'active_minutes']);
        if (empty($data)) return ['prediction_message' => 'Not enough data to predict goal achievement.'];

        $formatted = array_map(fn($r) => ['date' => Carbon::parse($r->date)->format('Y-m-d'), 'steps' => $r->steps, 'active_minutes' => $r->active_minutes], $data);

        $prompt = "You are a health analytics assistant. Given this 7-day dataset containing steps and active minutes:" .
            json_encode($formatted, JSON_PRETTY_PRINT) .
            "Evaluate whether the user is likely to meet the following daily goals: 10,000 steps and 70 active minutes. " .
            "Only respond with a prediction message keep it simple just a one line if he's gonna meet or not and based on what.";

        return self::generateAndStorePrediction($userId, 'goal_prediction', $prompt);
    }

    public static function generateAnomalyDetection(int $userId): array
    {
        $data = self::getLatest7DaysData($userId, ['date', 'active_minutes']);
        if (empty($data)) return ['prediction_message' => 'Not enough data to analyze anomalies.'];

        $formatted = array_map(fn($r) => ['date' => Carbon::parse($r->date)->format('Y-m-d'), 'active_minutes' => $r->active_minutes], $data);

        $prompt = "You are a health analytics assistant. Given this array of 7 days of activity minutes data:" .
            json_encode($formatted, JSON_PRETTY_PRINT) .
            "Please: - Calculate the average and standard deviation of minutes active. - Identify and list any anomalies. " .
            "- Briefly explain the anomalies and suggest possible reasons. Only respond with a simple message that these days u were below average.";

        return self::generateAndStorePrediction($userId, 'anomaly_detection', $prompt);
    }

    public static function generateFutureProjection(int $userId): array
    {
        $data = self::getLatest7DaysData($userId, ['date', 'steps', 'active_minutes']);
        if (empty($data)) return ['prediction_message' => 'Not enough data to project future trends.'];

        $formatted = array_map(fn($r) => ['date' => Carbon::parse($r->date)->format('Y-m-d'), 'steps' => $r->steps, 'active_minutes' => $r->active_minutes], $data);

        $prompt = "You are a health data predictor. Based on this past week's steps and active minutes data:" .
            json_encode($formatted, JSON_PRETTY_PRINT) .
            "Project how the user might perform in the upcoming 7 days. Provide a one-line summary of expected average steps and active minutes and just answer by the prediction message directly.";

        return self::generateAndStorePrediction($userId, 'future_projection', $prompt);
    }

    public static function generateActionableInsights(int $userId): array
    {
        $data = self::getLatest7DaysData($userId, ['date', 'steps', 'active_minutes']);
        if (empty($data)) return ['prediction_message' => 'Not enough data to generate insights.'];

        $formatted = array_map(fn($r) => ['date' => Carbon::parse($r->date)->format('Y-m-d'), 'steps' => $r->steps, 'active_minutes' => $r->active_minutes], $data);

        $prompt = "You are a health insights generator. Based on this 7-day activity log:" .
            json_encode($formatted, JSON_PRETTY_PRINT) .
            "Suggest practical changes or improvements the user can apply to optimize their health routine. Keep it short and actionable and answer directly with the message.";

        return self::generateAndStorePrediction($userId, 'actionable_insights', $prompt);
    }

    public static function fetchAllPredictions(int $userId): array
    {
        $predictions = Prediction::where('user_id', $userId)->get();
        $result = [];

        foreach ($predictions as $p) {
            $result[$p->prediction_type] = $p->content;
        }

        return $result;
    }

    private static function callGeminiApi(string $prompt): string
    {
        $client = new Client();
        $url = self::GEMINI_API_ENDPOINT . '?key=' . self::GEMINI_API_KEY;

        try {
            $response = $client->post($url, [
                'json' => [
                    'contents' => [[
                        'parts' => [[ 'text' => $prompt ]]
                    ]],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 300,
                    ],
                ],
                'headers' => [ 'Content-Type' => 'application/json' ],
                'timeout' => 30,
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No prediction generated.';

        } catch (\Exception $e) {
            return "Error generating prediction: " . $e->getMessage();
        }
    }
}
