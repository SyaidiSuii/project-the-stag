<?php

echo "Checking Firebase Service Account File...\n";
echo "=========================================\n";

$path = 'firebase-service-account.json';

if (!file_exists($path)) {
    echo "❌ File not found at: $path\n";
    exit(1);
}

echo "✓ File exists\n";

$content = file_get_contents($path);
$json = json_decode($content, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "❌ Invalid JSON: " . json_last_error_msg() . "\n";
    exit(1);
}

echo "✓ Valid JSON\n";

echo "\nKey fields in service account:\n";
echo "==============================\n";
echo "project_id: " . ($json['project_id'] ?? 'MISSING') . "\n";
echo "client_email: " . ($json['client_email'] ?? 'MISSING') . "\n";
echo "type: " . ($json['type'] ?? 'MISSING') . "\n";

if (isset($json['project_id'])) {
    echo "\n✅ Service account file looks good!\n";
} else {
    echo "\n⚠️  Service account missing required fields\n";
}
