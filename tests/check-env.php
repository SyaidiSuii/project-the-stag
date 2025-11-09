<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Environment Variables:\n";
echo "==============================\n";
echo "FIREBASE_PROJECT_ID=" . env('FIREBASE_PROJECT_ID') . "\n";
echo "FIREBASE_SERVICE_ACCOUNT_PATH=" . env('FIREBASE_SERVICE_ACCOUNT_PATH') . "\n";
echo "NOTIFICATIONS_ENABLED=" . env('NOTIFICATIONS_ENABLED') . "\n";
echo "\nChecking Config:\n";
echo "================\n";
echo "config('services.fcm.project_id')=" . config('services.fcm.project_id') . "\n";
echo "config('services.fcm.service_account_path')=" . config('services.fcm.service_account_path') . "\n";
echo "config('services.fcm.enabled')=" . config('services.fcm.enabled') . "\n";
