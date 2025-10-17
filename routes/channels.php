<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Analytics updates channel (public for simplicity, or add admin check)
Broadcast::channel('analytics-updates', function ($user) {
    // Allow all authenticated users to listen (or restrict to admin)
    return true; // Set to $user->hasRole('admin') if you want admin-only
});
