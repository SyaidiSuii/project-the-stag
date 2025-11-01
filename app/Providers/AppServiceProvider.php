<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver; // PHASE 1.5: Import UserObserver
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // PHASE 3 & 7: Register Loyalty Services as Singletons
        $this->app->singleton(\App\Services\Loyalty\LoyaltyService::class);
        $this->app->singleton(\App\Services\Loyalty\RewardRedemptionService::class);
        $this->app->singleton(\App\Services\Loyalty\VoucherService::class);
        $this->app->singleton(\App\Services\Loyalty\TierService::class); // PHASE 7
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // PHASE 1.5: Register UserObserver for automatic audit logging
        User::observe(UserObserver::class);

        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        if ($this->app->environment('production')) {
            Artisan::command('migrate:fresh', function () {
                $this->error('❌ migrate:fresh is disabled in production!');
            });

            Artisan::command('migrate:refresh', function () {
                $this->error('❌ migrate:refresh is disabled in production!');
            });
        }

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Sila Sahkan Email Anda')
                ->greeting("Salam Sejahtera {$notifiable->name},")
                ->line('Terima kasih kerana mendaftar ke The Stag SmartDine. Sila sahkan email anda. Klik butang di bawah untuk pengesahan.')
                ->action('Sahkan Email Anda', $url)
                ->salutation('Terima kasih.');
        });

        Gate::define('access-roles', function (User $user) {
            return $user->hasRole('Admin');
        });

        // Give super-admin role all permissions automatically
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
