<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\StockTransaction;
use App\Services\StockReplenishmentService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StockDashboardController extends Controller
{
    protected $replenishmentService;

    public function __construct(StockReplenishmentService $replenishmentService)
    {
        $this->replenishmentService = $replenishmentService;
    }

    /**
     * Display stock management dashboard
     */
    public function index()
    {
        // Get dashboard summary
        $summary = $this->replenishmentService->getDashboardSummary();

        // Get low stock items (limit 5 for widget)
        $lowStockItems = StockItem::active()
            ->lowStock()
            ->with('supplier')
            ->limit(5)
            ->get();

        // Get critical stock items
        $criticalStockItems = StockItem::active()
            ->criticalStock()
            ->with('supplier')
            ->get();

        // Recent transactions (last 10)
        $recentTransactions = StockTransaction::with(['stockItem', 'creator'])
            ->latest()
            ->limit(10)
            ->get();

        // Pending purchase orders
        $pendingPOs = PurchaseOrder::with('supplier')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        // Stock by category for chart
        $stockByCategory = StockItem::active()
            ->selectRaw('category, COUNT(*) as count, SUM(current_quantity * unit_price) as total_value')
            ->groupBy('category')
            ->get();

        // Stock status distribution
        $totalItems = StockItem::active()->count();
        $goodStock = $totalItems - ($summary['low_stock_items'] ?? 0);

        return view('admin.stock.dashboard', compact(
            'summary',
            'lowStockItems',
            'criticalStockItems',
            'recentTransactions',
            'pendingPOs',
            'stockByCategory',
            'goodStock'
        ));
    }

    /**
     * Get low stock alerts (AJAX)
     */
    public function lowStockAlert()
    {
        $items = $this->replenishmentService->checkStockLevels();
        return response()->json($items);
    }

    /**
     * Get critical stock alerts (AJAX)
     */
    public function criticalAlert()
    {
        $items = $this->replenishmentService->getCriticalStockItems();
        return response()->json($items);
    }

    /**
     * Get recent transactions (AJAX)
     */
    public function getTransactions(Request $request)
    {
        $limit = $request->input('limit', 20);

        $transactions = StockTransaction::with(['stockItem', 'creator'])
            ->latest()
            ->limit($limit)
            ->get();

        return response()->json($transactions);
    }
}
