<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Sila Sahkan Email Anda')
                ->greeting("Salam sejahtera {$notifiable->name},")
                ->line('Terima kasih kerana mendaftar ke KelasProgramming.com. Sila sahkan email anda. Klik butang di bawah untuk pengesahan.')
                ->action('Sahkan Email Anda', $url)
                ->salutation('Terima kasih.');
        });

        Gate::define('access-roles', function (User $user) {
            return $user->hasRole('Admin');
        });
    }
}
