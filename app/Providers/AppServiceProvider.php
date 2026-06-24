<?php

namespace App\Providers;

use App\Services\ResumeTextExtractor;
use Illuminate\Support\ServiceProvider;
use Smalot\PdfParser\Parser;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ResumeTextExtractor::class, function () {
            return new ResumeTextExtractor(new Parser());
        });
    }

    public function boot(): void
    {
        //
    }
}