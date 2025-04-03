<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PredictionService;
use App\Services\ApiResponseService;

class PredictionController extends Controller
{
    public function goalPrediction()
    {
        $userId = 1;
        $prediction = PredictionService::generateGoalPrediction($userId);
        return ApiResponseService::success('Goal prediction completed.', $prediction);
    }

    public function anomalyDetection()
    {
        $userId = 1;
        $anomalyData = PredictionService::generateAnomalyDetection($userId);
        return ApiResponseService::success('Anomaly detection completed.', $anomalyData);
    }

    public function futureTrendPrediction(Request $request)
    {
        $userId = 1;
        $trendPrediction = PredictionService::generateFutureProjection($userId);
        return ApiResponseService::success('Future trend prediction completed.', $trendPrediction);
    }

    public function actionableInsights()
    {
        $userId = 1;
        $insights = PredictionService::generateActionableInsights($userId);
        return ApiResponseService::success('Actionable insights generated.', $insights);
    }
}
