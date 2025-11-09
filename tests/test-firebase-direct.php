<?php

require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

$serviceAccountPath = __DIR__ . '/storage/app/firebase/firebase_credentials.json';
$projectId = 'the-stag-notification';

try {
    $factory = (new Factory)
        ->withServiceAccount($serviceAccountPath)
        ->withProjectId($projectId);

    $messaging = $factory->createMessaging();

    echo "Firebase Messaging instance created successfully!\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

