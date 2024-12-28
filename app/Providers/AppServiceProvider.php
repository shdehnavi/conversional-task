<?php

namespace App\Providers;

use App\Contracts\Services\InvoiceServiceInterface;
use App\Services\InvoiceService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind service contracts
        $this->registerServicesContracts();

        // Use HTTPS in production
        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        // No mass assignment protection at all.
        Model::unguard();

        // Prevent lazy loading, but only when the app is not in production.
        Model::preventLazyLoading(! app()->isProduction());

        // Prevent accessing attributes that were not loaded from the database. Instead of returning null, an exception will be thrown
        Model::preventAccessingMissingAttributes();

        // Production prohibits: db:wipe, migrate:fresh, migrate:refresh, and migrate:reset
        DB::prohibitDestructiveCommands(app()->isProduction());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    protected function registerServicesContracts(): void
    {
        $this->app->bind(InvoiceServiceInterface::class, InvoiceService::class);
    }
}
