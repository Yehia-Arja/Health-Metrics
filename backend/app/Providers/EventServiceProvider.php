<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Events\CsvUploaded;
use App\Listeners\GeneratePredictionsAfterCsvUpload;
class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    protected $listen = [
        CsvUploaded::class => [
            GeneratePredictionsAfterCsvUpload::class,
        ],
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
