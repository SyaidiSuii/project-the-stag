<?php
use Illuminate\Support\Facades\Route;
use App\Models\Promotion;
use App\Models\TableQrcode;

Route::get('/debug-promotions', function() {
    $promos = Promotion::all();
    
    echo "<h1>All Promotions in Database (" . $promos->count() . ")</h1>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr>
        <th>ID</th>
        <th>Name</th>
        <th>Type</th>
        <th>Is Active</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Valid Now?</th>
    </tr>";
    
    foreach($promos as $p) {
        $isValid = $p->isValid();
        $startOk = $p->start_date <= now();
        $endOk = $p->end_date >= now();
        
        echo "<tr>";
        echo "<td>{$p->id}</td>";
        echo "<td>{$p->name}</td>";
        echo "<td>{$p->promotion_type}</td>";
        echo "<td style='color: " . ($p->is_active ? 'green' : 'red') . "'>" . ($p->is_active ? 'YES' : 'NO') . "</td>";
        echo "<td>{$p->start_date->format('Y-m-d')}</td>";
        echo "<td>{$p->end_date->format('Y-m-d')}</td>";
        echo "<td style='color: " . ($isValid ? 'green' : 'red') . "'>" . ($isValid ? 'VALID' : 'INVALID') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Active Promotions from Service</h2>";
    $service = app(\App\Services\Promotions\PromotionService::class);
    $activePromos = $service->getActivePromotions(null);
    echo "<p>Count: " . $activePromos->count() . "</p>";
    echo "<ul>";
    foreach($activePromos as $ap) {
        echo "<li>{$ap->name} ({$ap->promotion_type})</li>";
    }
    echo "</ul>";
});

Route::get('/test-complete-qr/{id}', function($id) {
    $session = TableQrcode::find($id);
    if (!$session) {
        return 'Session not found';
    }
    
    $session->complete();
    return 'QR session completed';
});
