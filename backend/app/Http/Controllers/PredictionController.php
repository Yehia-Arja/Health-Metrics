<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PredictionService;

class PredictionController extends Controller
{    public function goalPrediction(Request $request)
    {
        $userId = 1; 
        $prediction = PredictionService::generateGoalPrediction($userId);
        return response()->json([
            'success' => true,
            'message' => 'Goal prediction generated successfully.',
            'data' => $prediction,
        ]);
    }

   
    public function anomalyDetection(Request $request)
    {
        $userId = 1;
        $anomalyData = PredictionService::generateAnomalyDetection($userId);
        return response()->json([
            'success' => true,
            'message' => 'Anomaly detection completed.',
            'data' => $anomalyData,
        ]);
    }
}
