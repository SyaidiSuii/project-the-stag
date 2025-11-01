<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVoucherTemplateRequest;
use App\Models\VoucherTemplate;
use App\Models\CustomerVoucher;
use App\Models\User;
use App\Services\Loyalty\VoucherService;
use Illuminate\Http\Request;

/**
 * PHASE 4: Voucher Management Controller
 *
 * Manages voucher templates and voucher collections.
 * Uses VoucherService from Phase 3 for business logic.
 */
class VoucherManagementController extends Controller
{
    protected VoucherService $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin|manager');
        $this->voucherService = $voucherService;
    }

    // ==================== VOUCHER TEMPLATES ====================

    /**
     * Display voucher templates listing
     */
    public function indexTemplates()
    {
        $templates = VoucherTemplate::withCount('rewards')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.rewards.voucher-templates.index', compact('templates'));
    }

    /**
     * Show form for creating voucher template
     */
    public function createTemplate()
    {
        return view('admin.rewards.voucher-templates.form');
    }

    /**
     * Store voucher template
     */
    public function storeTemplate(StoreVoucherTemplateRequest $request)
    {
        $data = $request->validated();

        $template = VoucherTemplate::create($data);

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', 'Voucher template created successfully')
            ->with('active_tab', 'voucher-templates');
    }

    /**
     * Show form for editing voucher template
     */
    public function editTemplate(VoucherTemplate $template)
    {
        return view('admin.rewards.voucher-templates.form', compact('template'));
    }

    /**
     * Update voucher template
     */
    public function updateTemplate(StoreVoucherTemplateRequest $request, VoucherTemplate $template)
    {
        $data = $request->validated();

        $template->update($data);

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', 'Voucher template updated successfully')
            ->with('active_tab', 'voucher-templates');
    }

    /**
     * Delete voucher template
     */
    public function destroyTemplate(VoucherTemplate $template)
    {
        // Check if template is used by rewards
        if ($template->rewards()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete template that is linked to rewards');
        }

        // Check if there are active vouchers from this template
        $activeVouchers = CustomerVoucher::where('voucher_template_id', $template->id)
            ->whereIn('status', ['active'])
            ->count();

        if ($activeVouchers > 0) {
            return redirect()
                ->back()
                ->with('error', "Cannot delete template with {$activeVouchers} active vouchers");
        }

        $template->delete();

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', 'Voucher template deleted successfully')
            ->with('active_tab', 'voucher-templates');
    }

    // ==================== VOUCHER COLLECTIONS ====================

    /**
     * Display voucher collections (templates with source_type='collection')
     */
    public function indexCollections()
    {
        $collections = VoucherTemplate::collectionVouchers()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.rewards.voucher-collections.index', compact('collections'));
    }

    /**
     * Show form for creating voucher collection
     */
    public function createCollection()
    {
        return view('admin.rewards.voucher-collections.form');
    }

    /**
     * Store voucher collection
     */
    public function storeCollection(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'minimum_spend' => 'nullable|numeric|min:0',
            'spending_requirement' => 'required|numeric|min:0',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'max_total_uses' => 'nullable|integer|min:1',
            'valid_until' => 'nullable|date',
            'expiry_days' => 'nullable|integer|min:1|max:365',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['source_type'] = 'collection'; // Force collection type
        $data['is_active'] = $request->has('is_active');

        VoucherTemplate::create($data);

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', 'Voucher collection created successfully')
            ->with('active_tab', 'voucher-collections');
    }

    /**
     * Show form for editing voucher collection
     */
    public function editCollection(VoucherTemplate $collection)
    {
        // Ensure it's a collection type
        if ($collection->source_type !== 'collection') {
            return redirect()
                ->route('admin.voucher-collections.index')
                ->with('error', 'This is not a collection voucher');
        }

        return view('admin.rewards.voucher-collections.form', compact('collection'));
    }

    /**
     * Update voucher collection
     */
    public function updateCollection(Request $request, VoucherTemplate $collection)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'minimum_spend' => 'nullable|numeric|min:0',
            'spending_requirement' => 'required|numeric|min:0',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'max_total_uses' => 'nullable|integer|min:1',
            'valid_until' => 'nullable|date',
            'expiry_days' => 'nullable|integer|min:1|max:365',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $collection->update($data);

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', 'Voucher collection updated successfully')
            ->with('active_tab', 'voucher-collections');
    }

    /**
     * Delete voucher collection
     */
    public function destroyCollection(VoucherTemplate $collection)
    {
        // Check active vouchers
        $activeVouchers = CustomerVoucher::where('voucher_template_id', $collection->id)
            ->whereIn('status', ['active'])
            ->count();

        if ($activeVouchers > 0) {
            return redirect()
                ->back()
                ->with('error', "Cannot delete collection with {$activeVouchers} active vouchers");
        }

        $collection->delete();

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', 'Voucher collection deleted successfully')
            ->with('active_tab', 'voucher-collections');
    }

    // ==================== BULK OPERATIONS ====================

    /**
     * Generate vouchers from template (bulk issuance)
     */
    public function generateVouchers(Request $request, VoucherTemplate $template)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        try {
            $issuedCount = $this->voucherService->bulkIssueVouchers(
                $request->user_ids,
                $template,
                'promotion'
            );

            return redirect()
                ->back()
                ->with('success', "Successfully issued {$issuedCount} vouchers to selected users");

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to generate vouchers: ' . $e->getMessage());
        }
    }

    /**
     * View voucher usage statistics
     */
    public function showStats(VoucherTemplate $template)
    {
        $stats = [
            'total_issued' => CustomerVoucher::where('voucher_template_id', $template->id)->count(),
            'active' => CustomerVoucher::where('voucher_template_id', $template->id)->active()->count(),
            'used' => CustomerVoucher::where('voucher_template_id', $template->id)->used()->count(),
            'expired' => CustomerVoucher::where('voucher_template_id', $template->id)->where('status', 'expired')->count(),
            'cancelled' => CustomerVoucher::where('voucher_template_id', $template->id)->where('status', 'cancelled')->count(),
        ];

        $recentVouchers = CustomerVoucher::with('customerProfile.user')
            ->where('voucher_template_id', $template->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.rewards.voucher-templates.stats', compact('template', 'stats', 'recentVouchers'));
    }
}
