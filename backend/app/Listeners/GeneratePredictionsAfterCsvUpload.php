<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\CsvUploaded;
use App\Services\PredictionService;
class GeneratePredictionsAfterCsvUpload
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CsvUploaded $event): void
    {
        PredictionService::generateGoalPrediction($event->userId);
        PredictionService::generateAnomalyDetection($event->userId);
        PredictionService::generateFutureProjection($event->userId);
        PredictionService::generateActionableInsights($event->userId);
    }
}
