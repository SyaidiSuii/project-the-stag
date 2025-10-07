@extends('layouts.admin')

@section('title', 'Rewards Management')
@section('page-title', 'View Reward')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}?v={{ time() }}">
@endsection

@section('content')

  <!-- Mobile Overlay -->
  <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Stats Cards -->
    <div class="admin-cards">
      <div class="admin-card">
        <div class="admin-card-header">
          <div class="admin-card-title">Active Rewards</div>
          <div class="admin-card-icon icon-blue"><i class="fas fa-gift"></i></div>
        </div>
        <div class="admin-card-value" id="stats-active-rewards">0</div>
        <div class="admin-card-desc">Total configurable rewards</div>
      </div>

      <div class="admin-card">
        <div class="admin-card-header">
          <div class="admin-card-title">Rewards Redeemed</div>
          <div class="admin-card-icon icon-green"><i class="fas fa-ticket-alt"></i></div>
        </div>
        <div class="admin-card-value" id="stats-rewards-redeemed">0</div>
        <div class="admin-card-desc">Across all rewards</div>
      </div>

      <div class="admin-card">
        <div class="admin-card-header">
          <div class="admin-card-title">Total Points in System</div>
          <div class="admin-card-icon icon-orange"><i class="fas fa-users"></i></div>
        </div>
        <div class="admin-card-value" id="stats-total-points">0</div>
        <div class="admin-card-desc">Customer points balance</div>
      </div>

      <div class="admin-card">
        <div class="admin-card-header">
          <div class="admin-card-title">Active Special Events</div>
          <div class="admin-card-icon icon-red"><i class="fas fa-calendar-check"></i></div>
        </div>
        <div class="admin-card-value" id="stats-active-events">0</div>
        <div class="admin-card-desc">Currently running</div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="admin-tabs">
      <div class="admin-tab" data-tab="rewards">Rewards</div>
      <div class="admin-tab" data-tab="templates">Voucher Templates</div>
      <div class="admin-tab" data-tab="checkin">Check-in Settings</div>
      <div class="admin-tab" data-tab="events">Special Events</div>
      <div class="admin-tab" data-tab="tiers">Tiers & Levels</div>
      <div class="admin-tab" data-tab="redemptions">Redemptions</div>
      <div class="admin-tab" data-tab="members">Members</div>
      <div class="admin-tab" data-tab="achievements">Achievements</div>
      <div class="admin-tab" data-tab="vouchers">Voucher Collection</div>
      <div class="admin-tab" data-tab="bonus-points">Bonus Points</div>
    </div>

    <!-- Rewards Section -->
    <div class="admin-section" id="rewards-section">
      <div class="section-header">
        <h2 class="section-title">View Rewards</h2>
        <a href="{{ route('admin.rewards.rewards.create') }}" class="admin-btn btn-primary">
          <i class="fas fa-plus"></i> New Reward
        </a>
      </div>

      <div class="section-content">
        <div class="admin-table-container" id="rewards-table-container">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Reward</th>
                <th>Points Required</th>
                <th>Redeemed</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="rewards-table-body">
              <!-- Reward rows will be dynamically inserted here -->
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Voucher Templates Section (Initially Hidden) -->
    <div class="admin-section hidden" id="templates-section">
      <div class="section-header">
        <h2 class="section-title">Voucher Templates</h2>
        <a href="{{ route('admin.rewards.voucher-templates.create') }}" class="admin-btn btn-primary">
          <i class="fas fa-plus"></i> New Template
        </a>
      </div>

      <div class="section-content">
        <p style="margin-bottom: 20px; color: var(--text-2);">Create templates to quickly generate multiple vouchers with the same settings. Each template can be used to generate multiple unique vouchers.</p>

        <div class="admin-table-container" id="templates-table-container">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Template Name</th>
                <th>Discount Type</th>
                <th>Discount Value</th>
                <th>Expiry Days</th>
                <th>Generated Vouchers</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="templates-table-body">
              <!-- Template rows will be dynamically inserted here -->
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Check-in Settings Section (Initially Hidden) -->
    <div class="admin-section hidden" id="checkin-section">
        <div class="section-header">
            <h2 class="section-title">Daily Check-in Points</h2>
            <button type="button" class="admin-btn btn-primary" id="saveCheckinBtn" style="pointer-events: auto; z-index: 1;" onclick="console.log('Inline click handler worked'); saveCheckinSettings();">Save Changes</button>
        </div>

        <div class="section-content">
          <p style="margin-bottom: 20px; color: var(--text-2);">Set the points awarded for each day of the weekly check-in streak (Sunday to Saturday).</p>
          <form id="checkin-form" class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
              <!-- Fields for each day's points will be generated here by JS -->
          </form>
        </div>
    </div>

    <!-- Special Events Section (Initially Hidden) -->
    <div class="admin-section hidden" id="events-section">
        <div class="section-header">
            <h2 class="section-title">Special Events</h2>
            <a href="{{ route('admin.rewards.special-events.create') }}" class="admin-btn btn-primary">
              <i class="fas fa-plus"></i> New Event
            </a>
        </div>

        <div class="section-content">
          <table class="admin-table">
              <thead>
                  <tr>
                      <th>Event Title</th>
                      <th>Description</th>
                      <th>Status</th>
                      <th>Actions</th>
                  </tr>
              </thead>
              <tbody id="events-table-body">
                  <!-- Event rows will be dynamically inserted here -->
              </tbody>
          </table>
        </div>
    </div>


    <!-- Tiers Section (Initially Hidden) -->
    <div class="admin-section hidden" id="tiers-section">
      <div class="section-header">
        <h2 class="section-title">Loyalty Tiers & Levels</h2>
        <a href="{{ route('admin.rewards.loyalty-tiers.create') }}" class="admin-btn btn-primary">
          <i class="fas fa-plus"></i> Add Tier
          </a>
        </div>
      </div>

      <table class="admin-table">
        <thead>
          <tr>
            <th>Tier Name</th>
            <th>Minimum Spending</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="tiers-table-body">
        </tbody>
      </table>
    </div>

    <!-- Redemptions Section (Initially Hidden) -->
    <div class="admin-section hidden" id="redemptions-section">
      <div class="section-header">
        <h2 class="section-title">Reward Redemptions</h2>
        <div style="position: relative; width: 350px;">
          <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #999; pointer-events: none;"></i>
          <input type="text" class="form-control" id="redemption-search" placeholder="Search by customer, reward, or code..." style="width: 100%; padding: 8px 12px 8px 36px; border-radius: 8px;">
        </div>
      </div>

      <div class="section-content">
        <div class="admin-table-container" id="redemptions-table-container">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>Reward</th>
                <th>Points</th>
                <th>Code</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="redemptions-table-body">
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Members Section (Initially Hidden) -->
    <div class="admin-section hidden" id="members-section">
      <div class="section-header">
        <h2 class="section-title">Loyalty Program Members</h2>
      </div>

      <div class="section-content">
        <table class="admin-table">
        <thead>
          <tr>
            <th>Customer</th>
            <th>Points</th>
            <th>Current Tier</th>
            <th>Vouchers</th>
          </tr>
        </thead>
        <tbody id="members-table-body">
        </tbody>
      </table>
      </div>
    </div>

    <!-- Achievements Section (Initially Hidden) -->
    <div class="admin-section hidden" id="achievements-section">
      <div class="section-header">
        <h2 class="section-title">Achievement Management</h2>
        <a href="{{ route('admin.rewards.achievements.create') }}" class="admin-btn btn-primary">
          <i class="fas fa-plus"></i> Add Achievement
          </a>
        </div>
      </div>

      <div class="section-content">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Achievement</th>
              <th>Description</th>
              <th>Target</th>
              <th>Reward</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="achievements-table-body">
          </tbody>
        </table>
      </div>
    </div>

    <!-- Voucher Collection Section (Initially Hidden) -->
    <div class="admin-section hidden" id="vouchers-section">
      <div class="section-header">
        <h2 class="section-title">Voucher Collection Management</h2>
        <a href="{{ route('admin.rewards.voucher-collections.create') }}" class="admin-btn btn-primary">
          <i class="fas fa-plus"></i> Add Collection
          </a>
        </div>
      </div>

      <div class="section-content">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Collection Name</th>
              <th>Spending Requirement</th>
              <th>Voucher Type</th>
              <th>Voucher Value</th>
              <th>Valid Until</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="vouchers-table-body">
          </tbody>
        </table>
      </div>
    </div>

    <!-- Bonus Points Section (Initially Hidden) -->
    <div class="admin-section hidden" id="bonus-points-section">
      <div class="section-header">
        <h2 class="section-title">Bonus Points Challenges</h2>
        <a href="{{ route('admin.rewards.bonus-challenges.create') }}" class="admin-btn btn-primary">
          <i class="fas fa-plus"></i> Add Challenge
          </a>
        </div>
      </div>

      <div class="section-content">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Challenge Name</th>
              <th>Description</th>
              <th>Condition</th>
              <th>Bonus Points</th>
              <th>Duration</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="bonus-points-table-body">
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- Add/Edit Reward Modal -->
  <div class="modal-overlay" id="reward-modal">
    <div class="modal">
      <div class="modal-header">
        <h2 class="modal-title" id="reward-modal-title">Add New Reward</h2>
        <button class="modal-close" id="reward-modal-close-btn">×</button>
      </div>
      <div class="modal-body">
        <form id="reward-form">
          <div class="form-grid">
            <div class="form-group">
              <label for="reward-name" class="form-label">Reward Name</label>
              <input type="text" id="reward-name" class="form-control" placeholder="e.g., Free Coffee">
            </div>
            <div class="form-group">
              <label for="reward-points" class="form-label">Points Required</label>
              <input type="number" id="reward-points" class="form-control" placeholder="e.g., 50" min="0">
            </div>
          </div>
          
          <div class="form-group">
            <label for="reward-description" class="form-label">Description</label>
            <textarea id="reward-description" class="form-control" rows="3" placeholder="Describe the reward..."></textarea>
          </div>
          
          <div class="form-grid">
            <div class="form-group">
              <label for="reward-status" class="form-label">Status</label>
              <select id="reward-status" class="form-control">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>

            <div class="form-group">
              <label for="reward-type" class="form-label">Reward Type</label>
              <select id="reward-type" class="form-control">
                <option value="discount">Discount</option>
                <option value="freebie">Free Item</option>
                <option value="percentage">Percentage Off</option>
                <option value="points">Points Bonus</option>
              </select>
            </div>

            <div class="form-group">
              <label for="reward-value" class="form-label">Reward Value (RM)</label>
              <input type="number" id="reward-value" class="form-control" step="0.01" min="0" placeholder="e.g., 10.00">
              <small style="color: #666; font-size: 12px;">The discount amount or value of the reward (e.g., RM10 off)</small>
            </div>

            <div class="form-group">
              <label for="reward-minimum-order" class="form-label">Minimum Spend to Use Reward (Optional)</label>
              <input type="number" id="reward-minimum-order" class="form-control" step="0.01" min="0" value="0" placeholder="0.00 - Leave 0 for no minimum">
              <small style="color: #666; font-size: 12px;">Minimum order amount required when using this reward (e.g., must spend RM50 to use the voucher)</small>
            </div>

            <div class="form-group">
              <label for="reward-validity-days" class="form-label">Validity Period (Days)</label>
              <input type="number" id="reward-validity-days" class="form-control" min="1" value="30" placeholder="30">
              <small style="color: #666; font-size: 12px;">How long the reward is valid after redemption</small>
            </div>

            <div class="form-group">
              <label for="reward-usage-limit" class="form-label">Total Usage Limit</label>
              <input type="number" id="reward-usage-limit" class="form-control" min="1" value="1" placeholder="1">
              <small style="color: #666; font-size: 12px;">Total number of times this reward can be used by ALL customers</small>
            </div>

            <div class="form-group">
              <label for="reward-max-redemptions" class="form-label">Max Redemptions per Customer</label>
              <input type="number" id="reward-max-redemptions" class="form-control" min="1" placeholder="Leave empty for unlimited">
              <small style="color: #666; font-size: 12px;">How many times ONE customer can use this reward</small>
            </div>

            <div class="form-group">
              <label for="reward-redemption-method" class="form-label">Redemption Method</label>
              <select id="reward-redemption-method" class="form-control" required>
                <option value="show_to_staff">Show to Staff</option>
                <option value="bring_voucher">Bring Voucher to Cashier</option>
                <option value="qr_code_scan">QR Code Scan</option>
                <option value="auto_applied">Auto Applied</option>
                <option value="phone_verification">Phone Verification</option>
              </select>
            </div>


          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="admin-btn btn-secondary" id="reward-modal-cancel-btn">Cancel</button>
        <button type="button" class="admin-btn btn-primary" id="reward-modal-save-btn">Save Reward</button>
      </div>
    </div>
  </div>

  <!-- Add/Edit Event Modal -->
  <div class="modal-overlay" id="event-modal">
    <div class="modal">
      <div class="modal-header">
        <h2 class="modal-title" id="event-modal-title">Add New Event</h2>
        <button class="modal-close" id="event-modal-close-btn">×</button>
      </div>
      <div class="modal-body">
        <form id="event-form">
            <div class="form-group">
              <label for="event-title" class="form-label">Event Title</label>
              <input type="text" id="event-title" class="form-control" placeholder="e.g., Double Points Weekend!">
            </div>
            <div class="form-group">
              <label for="event-description" class="form-label">Description</label>
              <textarea id="event-description" class="form-control" rows="3" placeholder="Describe the event..."></textarea>
            </div>
            <div class="form-group">
              <label for="event-status" class="form-label">Status</label>
              <select id="event-status" class="form-control">
                <option value="active">Active</option>
                <option value="coming">Coming Soon</option>
                <option value="expired">Expired</option>
              </select>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="admin-btn btn-secondary" id="event-modal-cancel-btn">Cancel</button>
        <button type="button" class="admin-btn btn-primary" id="event-modal-save-btn">Save Event</button>
      </div>
    </div>
  </div>

  <!-- Add/Edit Tier Modal -->
  <div class="modal-overlay" id="tier-modal">
    <div class="modal">
      <div class="modal-header">
        <h2 class="modal-title" id="tier-modal-title">Add New Tier</h2>
        <button class="modal-close" id="tier-modal-close-btn">×</button>
      </div>
      <div class="modal-body">
        <form id="tier-form">
            <div class="form-group">
              <label for="tier-name" class="form-label">Tier Name</label>
              <input type="text" id="tier-name" class="form-control" placeholder="e.g., Bronze" required>
            </div>
            <div class="form-group">
              <label for="tier-spending" class="form-label">Minimum Spending (RM)</label>
              <input type="number" id="tier-spending" class="form-control" placeholder="e.g., 0.00" min="0" step="0.01" required>
            </div>
            <div class="form-group">
              <label for="tier-color" class="form-label">Color (Hex Code)</label>
              <input type="color" id="tier-color" class="form-control" value="#CD7F32" required>
            </div>
            <div class="form-group">
              <label for="tier-icon" class="form-label">Icon (Emoji)</label>
              <input type="text" id="tier-icon" class="form-control" placeholder="e.g., 🥉" maxlength="10" required>
            </div>
            <div class="form-group">
              <label for="tier-order" class="form-label">Sort Order</label>
              <input type="number" id="tier-order" class="form-control" placeholder="e.g., 1" min="0" required>
            </div>
            <div class="form-group">
              <label for="tier-status" class="form-label">Status</label>
              <select id="tier-status" class="form-control">
                <option value="true">Active</option>
                <option value="false">Inactive</option>
              </select>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="admin-btn btn-secondary" id="tier-modal-cancel-btn">Cancel</button>
        <button type="button" class="admin-btn btn-primary" id="tier-modal-save-btn">Save Tier</button>
      </div>
    </div>
  </div>

  <!-- Add/Edit Template Modal -->
  <div class="modal-overlay" id="template-modal">
    <div class="modal">
      <div class="modal-header">
        <h2 class="modal-title" id="template-modal-title">Add New Voucher Template</h2>
        <button class="modal-close" id="template-modal-close-btn">×</button>
      </div>
      <div class="modal-body">
        <form id="template-form">
          <div class="form-group">
            <label for="template-name" class="form-label">Template Name</label>
            <input type="text" id="template-name" class="form-control" placeholder="e.g., RM10 Discount Voucher" required>
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label for="template-discount-type" class="form-label">Discount Type</label>
              <select id="template-discount-type" class="form-control" required>
                <option value="percentage">Percentage (%)</option>
                <option value="fixed">Fixed Amount (RM)</option>
              </select>
            </div>
            <div class="form-group">
              <label for="template-discount-value" class="form-label">Discount Value</label>
              <input type="number" id="template-discount-value" class="form-control" placeholder="e.g., 10" min="0" step="0.01" required>
            </div>
          </div>
          <div class="form-group">
            <label for="template-expiry-days" class="form-label">Validity Period (Days)</label>
            <input type="number" id="template-expiry-days" class="form-control" placeholder="e.g., 30" min="1" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="admin-btn btn-secondary" id="template-modal-cancel-btn">Cancel</button>
        <button type="button" class="admin-btn btn-primary" id="template-modal-save-btn">Save Template</button>
      </div>
    </div>
  </div>

  <!-- Generate Vouchers Modal -->
  <div class="modal-overlay" id="generate-vouchers-modal">
    <div class="modal">
      <div class="modal-header">
        <h2 class="modal-title" id="generate-vouchers-modal-title">Generate Vouchers from Template</h2>
        <button class="modal-close" id="generate-vouchers-modal-close-btn">×</button>
      </div>
      <div class="modal-body">
        <form id="generate-vouchers-form">
          <div class="form-group">
            <label class="form-label">Template</label>
            <input type="text" id="generate-template-name" class="form-control" readonly style="background-color: #f5f5f5;">
          </div>
          <div class="form-group">
            <label for="generate-quantity" class="form-label">Number of Vouchers</label>
            <input type="number" id="generate-quantity" class="form-control" placeholder="e.g., 100" min="1" max="1000" required>
            <small style="color: var(--text-2); margin-top: 5px; display: block;">Generate up to 1000 vouchers at once</small>
          </div>
          <div class="form-group">
            <label for="generate-points-required" class="form-label">Points Required (Per Voucher)</label>
            <input type="number" id="generate-points-required" class="form-control" placeholder="e.g., 50" min="1" required>
            <small style="color: var(--text-2); margin-top: 5px; display: block;">How many points customers need to redeem each voucher</small>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="admin-btn btn-secondary" id="generate-vouchers-modal-cancel-btn">Cancel</button>
        <button type="button" class="admin-btn btn-primary" id="generate-vouchers-modal-generate-btn">Generate Vouchers</button>
      </div>
    </div>
  </div>

  <!-- Confirmation Modal -->
  <div class="modal-overlay" id="confirm-modal" style="display: none;">
    <div class="modal" style="max-width: 400px;">
      <div class="modal-header">
        <h2 class="modal-title" id="confirm-modal-title">Confirm Action</h2>
        <button class="modal-close" id="confirm-modal-close-btn" aria-label="Close">×</button>
      </div>
      <div class="modal-body">
        <p id="confirm-modal-text">Are you sure you want to proceed?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="admin-btn btn-secondary" id="confirm-modal-cancel-btn">Cancel</button>
        <button type="button" class="admin-btn btn-primary" id="confirm-modal-confirm-btn">Confirm</button>
      </div>
    </div>
  </div>

  <script type="application/json" id="reward-data">
    {
      "rewards": @json($rewards ?? []),
      "promotions": @json($promotions ?? []),
      "vouchers": @json($vouchers ?? []),
      "menuItems": @json($menuItems ?? []),
      "checkinSettings": @json($checkinSettings ?? null),
      "specialEvents": @json($specialEvents ?? []),
      "rewardsContent": @json($rewardsContent ?? null),
      "loyaltyTiers": @json($loyaltyTiers ?? []),
      "redemptions": @json($redemptions ?? []),
      "members": @json($members ?? []),
      "achievements": @json($achievements ?? []),
      "voucherCollections": @json($voucherCollections ?? []),
      "bonusPointsChallenges": @json($bonusPointsChallenges ?? []),
      "voucherTemplates": @json($voucherTemplates ?? [])
    }
  </script>

  <script>
    (function() {
      const BOOKING_STORAGE_KEY = 'THE_STAG_BOOKINGS';
      // --- Data loaded from PHP database ---
      const rewardData = JSON.parse(document.getElementById('reward-data').textContent);

      const AppData = {
        get: function(key, defaultValue) {
            // Return database data
            switch(key) {
                case 'rewards':
                    return (rewardData.rewards || []).map(r => ({
                        id: r.id,
                        name: r.name,
                        points: r.points_required,
                        redeemed: r.redemptions_count || 0,
                        status: r.status,
                        description: r.description,
                        category: 'reward',
                        expiry: null
                    }));
                case 'promotions': return rewardData.promotions || [];
                case 'vouchers': return rewardData.vouchers || [];
                case 'checkin':
                    console.log('AppData.get checkin called, checkinSettings:', rewardData.checkinSettings);
                    if (rewardData.checkinSettings && rewardData.checkinSettings.daily_points && Array.isArray(rewardData.checkinSettings.daily_points)) {
                        console.log('Returning existing daily_points:', rewardData.checkinSettings.daily_points);
                        return rewardData.checkinSettings.daily_points;
                    }
                    console.log('Returning default points: [25, 5, 5, 10, 10, 15, 20]');
                    return [25, 5, 5, 10, 10, 15, 20];
                case 'events':
                    return (rewardData.specialEvents || []).map(e => ({
                        id: e.id,
                        title: e.title,
                        description: e.description,
                        status: e.status
                    }));
                case 'tiers':
                    return (rewardData.loyaltyTiers || []).map(t => ({
                        id: t.id,
                        name: t.name,
                        minimum_spending: t.minimum_spending,
                        color: t.color,
                        icon: t.icon,
                        sort_order: t.sort_order,
                        is_active: t.is_active
                    }));
                case 'redemptions':
                    return (rewardData.redemptions || []).map(r => ({
                        id: r.id,
                        date: new Date(r.created_at).toLocaleDateString(),
                        customer: r.user ? r.user.name : 'Unknown Customer',
                        reward: r.exchange_point ? r.exchange_point.name : 'Unknown Reward',
                        points: r.points_spent,
                        redemption_code: r.redemption_code || 'N/A',
                        status: r.status || 'pending'
                    }));
                case 'points':
                    return rewardData.members && rewardData.members.length > 0 ?
                           rewardData.members.reduce((total, member) => total + (member.points_balance || 0), 0) : 0;
                case 'achievements':
                    return rewardData.achievements || [];
                case 'voucherCollections':
                    return rewardData.voucherCollections || [];
                case 'bonusPointsChallenges':
                    return rewardData.bonusPointsChallenges || [];
                case 'voucherTemplates':
                    return rewardData.voucherTemplates || [];
                default: return defaultValue;
            }
        },
        set: function(key, value) {
            // Update local data cache
            switch(key) {
                case 'redemptions':
                    // Update the rewardData.redemptions with the new data
                    if (rewardData.redemptions && Array.isArray(value)) {
                        // Map the updated values back to the original format
                        value.forEach(updatedItem => {
                            const originalIndex = rewardData.redemptions.findIndex(r => r.id == updatedItem.id);
                            if (originalIndex !== -1) {
                                rewardData.redemptions[originalIndex].status = updatedItem.status;
                            }
                        });
                    }
                    break;
                case 'rewards':
                    if (rewardData.rewards) {
                        rewardData.rewards = value;
                    }
                    break;
                case 'events':
                    if (rewardData.specialEvents) {
                        rewardData.specialEvents = value;
                    }
                    break;
                // Add more cases as needed
            }
        }
      };

      let BOOKINGS = [];
      let confirmationCallback = null;

      // --- Element References ---
      const getEl = (id) => document.getElementById(id);
      const query = (selector) => document.querySelector(selector);

      const elements = {
        rewardsTableBody: getEl('rewards-table-body'),
        checkinForm: getEl('checkin-form'),
        saveCheckinBtn: getEl('saveCheckinBtn'),
        eventsTableBody: getEl('events-table-body'),
        addEventBtn: getEl('addEventBtn'),
        eventModal: getEl('event-modal'),
        eventModalTitle: getEl('event-modal-title'),
        eventForm: getEl('event-form'),
        eventModalSaveBtn: getEl('event-modal-save-btn'),
        addRewardBtn: getEl('addRewardBtn'),
        filterSelect: getEl('filterSelect'),
        rewardModal: getEl('reward-modal'),
        rewardForm: getEl('reward-form'),
        rewardModalTitle: getEl('reward-modal-title'),
        rewardModalSaveBtn: getEl('reward-modal-save-btn'),
        tiersTableBody: getEl('tiers-table-body'),
        addTierBtn: getEl('addTierBtn'),
        tierModal: getEl('tier-modal'),
        tierModalTitle: getEl('tier-modal-title'),
        tierForm: getEl('tier-form'),
        tierModalSaveBtn: getEl('tier-modal-save-btn'),
        redemptionsTableBody: getEl('redemptions-table-body'),
        membersTableBody: getEl('members-table-body'),
        achievementsTableBody: getEl('achievements-table-body'),
        addAchievementBtn: getEl('add-achievement-btn'),
        vouchersTableBody: getEl('vouchers-table-body'),
        addVoucherCollectionBtn: getEl('add-voucher-collection-btn'),
        bonusPointsTableBody: getEl('bonus-points-table-body'),
        addBonusPointsBtn: getEl('add-bonus-points-btn'),
        templatesTableBody: getEl('templates-table-body'),
        addTemplateBtn: getEl('addTemplateBtn'),
        templateModal: getEl('template-modal'),
        templateModalTitle: getEl('template-modal-title'),
        templateForm: getEl('template-form'),
        templateModalSaveBtn: getEl('template-modal-save-btn'),
        generateVouchersModal: getEl('generate-vouchers-modal'),
        generateVouchersForm: getEl('generate-vouchers-form'),
        generateVouchersModalGenerateBtn: getEl('generate-vouchers-modal-generate-btn'),
        confirmModal: getEl('confirm-modal'),
        adminSidebar: getEl('adminSidebar'),
        hamburgerBtn: getEl('hamburgerBtn'),
        mobileOverlay: getEl('mobileOverlay'),
        logoutBtn: getEl('logoutBtn'),
        currentDate: getEl('currentDate'),
        viewSiteBtn: getEl('viewSiteBtn'),
      };

      function loadBookings() {
          const stored = null // Bookings from database;
          if (stored) {
              try {
                  BOOKINGS = JSON.parse(stored).map(b => ({ ...b, date: new Date(b.date) }));
              } catch (e) {
                  BOOKINGS = [];
              }
          }
          // If storage is empty, use default data just for the badge count
          if (BOOKINGS.length === 0) {
              BOOKINGS = [
                { id: 'BK-2024-001', customer: 'Sarah Johnson', date: new Date(new Date().setHours(19, 0, 0, 0)), partySize: 4, table: 'T-03', status: 'confirmed', phone: '+60 12 345 6789', email: 'sarah@example.com', notes: 'Window seating preferred. Celebrating anniversary.'},
                { id: 'BK-2024-002', customer: 'Robert Chen', date: new Date(new Date().setHours(20, 30, 0, 0)), partySize: 2, table: 'T-01', status: 'confirmed', phone: '+60 12 345 6789', email: 'robert@example.com', notes: 'Quiet table if possible.'},
                { id: 'BK-2024-003', customer: 'Lisa Wong', date: new Date(new Date().setDate(new Date().getDate() + 1)).setHours(18, 0, 0, 0), partySize: 6, table: 'T-06', status: 'pending', phone: '+60 12 345 6789', email: 'lisa@example.com', notes: 'Birthday celebration. Will bring cake.'},
              ];
          }
      }

      function updateSidebarBadge() {
        const sidebarBadge = document.getElementById('bookings-sidebar-badge');
        if (sidebarBadge) {
          const pendingBookings = BOOKINGS.filter(b => b.status === 'pending').length;
          sidebarBadge.textContent = pendingBookings;
          sidebarBadge.style.display = pendingBookings > 0 ? 'grid' : 'none';
        }
      }

      // --- Render Functions ---
      function renderAll() {
        renderRewardsTable();
        renderCheckinForm();
        renderEventsTable();
        renderTiersTable();
        renderRedemptionsTable();
        renderMembersTable();
        renderAchievementsTable();
        renderVouchersTable();
        renderBonusPointsTable();
        renderTemplatesTable();
        updateStats();
      }

      // Render only the active section's data
      function renderActiveSection(tabName) {
        updateStats(); // Always update stats

        switch(tabName) {
          case 'rewards':
            renderRewardsTable();
            break;
          case 'templates':
            renderTemplatesTable();
            break;
          case 'checkin':
            renderCheckinForm();
            break;
          case 'events':
            renderEventsTable();
            break;
          case 'tiers':
            renderTiersTable();
            break;
          case 'redemptions':
            renderRedemptionsTable();
            break;
          case 'members':
            renderMembersTable();
            break;
          case 'achievements':
            renderAchievementsTable();
            break;
          case 'vouchers':
            renderVouchersTable();
            break;
          case 'bonus-points':
            renderBonusPointsTable();
            break;
        }
      }
      
      function updateStats() {
        const rewards = AppData.get('rewards', []);
        const events = AppData.get('events', []);
        const points = AppData.get('points', 0);
        const redemptions = AppData.get('redemptions', []);
        
        getEl('stats-active-rewards').textContent = rewards.filter(r => r.status === 'active').length;
        getEl('stats-rewards-redeemed').textContent = redemptions.length;
        getEl('stats-total-points').textContent = points;
        getEl('stats-active-events').textContent = events.filter(e => e.status === 'active').length;
        updateSidebarBadge(); // Call the badge update
      }

      function renderRewardsTable() {
        // Skip rendering if elements don't exist (section-based navigation is being used)
        if (!elements.filterSelect || !elements.rewardsTableBody) {
          return;
        }

        const filterValue = elements.filterSelect.value;
        let rewards = AppData.get('rewards', []);

        if (filterValue === 'active' || filterValue === 'inactive') {
          rewards = rewards.filter(r => r.status === filterValue);
        } else if (filterValue === 'popular') {
          rewards.sort((a, b) => (b.redeemed || 0) - (a.redeemed || 0));
        }

        elements.rewardsTableBody.innerHTML = '';
        if (rewards.length === 0) {
          elements.rewardsTableBody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 20px;">No rewards found.</td></tr>`;
          return;
        }
        rewards.forEach(reward => {
          const statusClass = `status-${reward.status}`;
          const toggleButtonClass = reward.status === 'active' ? 'action-btn delete-btn' : 'action-btn view-btn';
          const toggleButtonText = reward.status === 'active' ? 'Disable' : 'Enable';
          const row = `
            <tr>
              <td>${reward.name}</td>
              <td>${reward.points} points</td>
              <td>${reward.redeemed} times</td>
              <td><span class="status ${statusClass}">${reward.status.charAt(0).toUpperCase() + reward.status.slice(1)}</span></td>
              <td class="table-actions">
                <button class="action-btn edit-btn" data-type="reward" data-id="${reward.id}">Edit</button>
                <button class="${toggleButtonClass}" data-type="reward" data-id="${reward.id}" data-action="toggle">${toggleButtonText}</button>
                <button class="action-btn delete-btn" data-type="reward" data-id="${reward.id}" data-action="delete">Delete</button>
              </td>
            </tr>
          `;
          elements.rewardsTableBody.insertAdjacentHTML('beforeend', row);
        });
      }
      
      function renderCheckinForm() {
        const checkinPoints = AppData.get('checkin', []);
        console.log('Rendering checkin form with points:', checkinPoints);
        const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        if (!elements.checkinForm) {
            console.error('checkinForm element not found');
            return;
        }

        elements.checkinForm.innerHTML = '';
        days.forEach((day, index) => {
            const formGroup = `
                <div class="form-group">
                    <label for="checkin-day-${index}" class="form-label">${day}</label>
                    <input type="number" id="checkin-day-${index}" class="form-control" value="${checkinPoints[index] || 0}" min="0">
                </div>
            `;
            elements.checkinForm.insertAdjacentHTML('beforeend', formGroup);
        });
      }
      
      function renderEventsTable() {
        const events = AppData.get('events', []);
        elements.eventsTableBody.innerHTML = '';
        if (events.length === 0) {
            elements.eventsTableBody.innerHTML = `<tr><td colspan="4" style="text-align: center; padding: 20px;">No special events found.</td></tr>`;
            return;
        }
        events.forEach(event => {
            const statusClass = `status-${event.status}`;
            const toggleButtonClass = event.status === 'active' ? 'action-btn delete-btn' : 'action-btn view-btn';
            const toggleButtonText = event.status === 'active' ? 'Disable' : 'Enable';
            const row = `
                <tr>
                    <td>${event.title}</td>
                    <td>${event.description}</td>
                    <td><span class="status ${statusClass}">${event.status.charAt(0).toUpperCase() + event.status.slice(1)}</span></td>
                    <td class="table-actions">
                        <button class="action-btn edit-btn" data-type="event" data-id="${event.id}">Edit</button>
                        <button class="${toggleButtonClass}" data-type="event" data-id="${event.id}" data-action="toggle">${toggleButtonText}</button>
                        <button class="action-btn delete-btn" data-type="event" data-id="${event.id}" data-action="delete">Delete</button>
                    </td>
                </tr>
            `;
            elements.eventsTableBody.insertAdjacentHTML('beforeend', row);
        });
      }
      

      function renderTiersTable() {
        const tiers = AppData.get('tiers', []);
        elements.tiersTableBody.innerHTML = '';
        if (tiers.length === 0) {
            elements.tiersTableBody.innerHTML = `<tr><td colspan="4" style="text-align: center; padding: 20px;">No tiers found.</td></tr>`;
            return;
        }
        tiers.forEach(tier => {
            const statusText = tier.is_active ? 'active' : 'inactive';
            const statusClass = `status-${statusText}`;
            const row = `
                <tr>
                    <td>${tier.name}</td>
                    <td>RM ${parseFloat(tier.minimum_spending || 0).toFixed(2)}+</td>
                    <td><span class="status ${statusClass}">${statusText.charAt(0).toUpperCase() + statusText.slice(1)}</span></td>
                    <td class="table-actions">
                        <button class="action-btn edit-btn" data-type="tier" data-id="${tier.id}">Edit</button>
                        <button class="action-btn delete-btn" data-type="tier" data-id="${tier.id}" data-action="delete">Delete</button>
                    </td>
                </tr>
            `;
            elements.tiersTableBody.insertAdjacentHTML('beforeend', row);
        });
      }

      function renderRedemptionsTable(searchTerm = '') {
        const allRedemptions = AppData.get('redemptions', []);
        console.log('renderRedemptionsTable called with redemptions:', allRedemptions);
        console.log('elements.redemptionsTableBody element:', elements.redemptionsTableBody);

        if (!elements.redemptionsTableBody) {
            console.error('redemptions-table-body element not found!');
            return;
        }

        // Filter redemptions based on search term
        const redemptions = searchTerm ?
            allRedemptions.filter(item =>
                item.customer.toLowerCase().includes(searchTerm.toLowerCase()) ||
                item.reward.toLowerCase().includes(searchTerm.toLowerCase()) ||
                (item.redemption_code && item.redemption_code.toLowerCase().includes(searchTerm.toLowerCase()))
            ) : allRedemptions;

        filteredRedemptions = redemptions;

        elements.redemptionsTableBody.innerHTML = '';
        if (redemptions.length === 0) {
            const message = searchTerm ? 'No redemptions found matching your search.' : 'No redemptions yet.';
            elements.redemptionsTableBody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 20px;">${message}</td></tr>`;
            updateTableScrollability('redemptions-table-container', 0);
            return;
        }

        redemptions.forEach(item => {
            console.log('Rendering item:', item, 'Status:', item.status);
            const statusClass = item.status === 'redeemed' ? 'status-active' : 'status-pending';
            const statusText = item.status === 'redeemed' ? 'Redeemed' : 'Pending';
            const actionButtons = item.status === 'pending'
                ? `<button class="action-btn view-btn" onclick="viewRedemption(${item.id})">View</button>
                   <button class="action-btn edit-btn" onclick="markAsRedeemed(${item.id})">Mark as Redeemed</button>`
                : `<button class="action-btn view-btn" onclick="viewRedemption(${item.id})">View</button>
                   <span class="completed-badge">✓ Completed</span>`;

            const row = `
                <tr>
                    <td>${item.date}</td>
                    <td>${item.customer}</td>
                    <td>${item.reward}</td>
                    <td>${item.points}</td>
                    <td><code style="background: #f5f5f5; padding: 2px 6px; border-radius: 4px; font-family: monospace;">${item.redemption_code || 'N/A'}</code></td>
                    <td><span class="status ${statusClass}">${statusText}</span></td>
                    <td class="table-actions">${actionButtons}</td>
                </tr>
            `;
            elements.redemptionsTableBody.insertAdjacentHTML('beforeend', row);
        });

        // Add dynamic scrollable class if more than 5 rows
        updateTableScrollability('redemptions-table-container', redemptions.length);

        console.log('Final table HTML after render:', elements.redemptionsTableBody.innerHTML);
      }

      function renderMembersTable() {
        const members = rewardData.members || [];
        const tiers = AppData.get('tiers', []).sort((a,b) => b.points - a.points);

        elements.membersTableBody.innerHTML = '';
        if (members.length === 0) {
            elements.membersTableBody.innerHTML = `<tr><td colspan="4" style="text-align: center; padding: 20px;">No loyalty program members found.</td></tr>`;
            return;
        }

        members.forEach(member => {
            const currentTier = tiers.find(t => (member.points_balance || 0) >= t.points) || { name: 'Bronze' };
            const row = `
                <tr>
                    <td>${member.name || 'Unknown Customer'}</td>
                    <td>${member.points_balance || 0}</td>
                    <td>${currentTier.name}</td>
                    <td>0</td>
                </tr>
            `;
            elements.membersTableBody.insertAdjacentHTML('beforeend', row);
        });
      }

      function renderAchievementsTable() {
        const achievements = AppData.get('achievements', []);

        elements.achievementsTableBody.innerHTML = '';
        if (achievements.length === 0) {
            elements.achievementsTableBody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 20px;">No achievements found. <button onclick="showAchievementModal(null)" style="background: none; border: none; color: var(--brand); text-decoration: underline; cursor: pointer;">Add your first achievement</button></td></tr>`;
            return;
        }

        achievements.forEach(achievement => {
            const statusClass = achievement.status === 'active' ? 'status-active' : 'status-inactive';
            const row = `
                <tr>
                    <td><strong>${achievement.name || 'Unnamed Achievement'}</strong></td>
                    <td>${achievement.description || 'No description'}</td>
                    <td>${achievement.target_value || 0} ${achievement.target_type || 'actions'}</td>
                    <td>${achievement.reward_points || 0} points</td>
                    <td><span class="status ${statusClass}">${(achievement.status || 'inactive').charAt(0).toUpperCase() + (achievement.status || 'inactive').slice(1)}</span></td>
                    <td>
                        <button class="action-btn edit-btn" data-type="achievement" data-id="${achievement.id}">Edit</button>
                        <button class="action-btn delete-btn" data-type="achievement" data-id="${achievement.id}" data-action="delete">Delete</button>
                    </td>
                </tr>
            `;
            elements.achievementsTableBody.insertAdjacentHTML('beforeend', row);
        });
      }

      function renderVouchersTable() {
        const vouchers = AppData.get('voucherCollections', []);

        elements.vouchersTableBody.innerHTML = '';
        if (vouchers.length === 0) {
            elements.vouchersTableBody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 20px;">No voucher collections found. <button onclick="showVoucherModal(null)" style="background: none; border: none; color: var(--brand); text-decoration: underline; cursor: pointer;">Add your first voucher collection</button></td></tr>`;
            return;
        }

        vouchers.forEach(voucher => {
            const statusClass = voucher.status === 'active' ? 'status-active' : 'status-inactive';
            const validUntil = voucher.valid_until ? new Date(voucher.valid_until).toLocaleDateString() : 'No expiry';
            const row = `
                <tr>
                    <td><strong>${voucher.name || 'Unnamed Collection'}</strong></td>
                    <td>$${voucher.spending_requirement || 0}</td>
                    <td>${voucher.voucher_type || 'discount'}</td>
                    <td>${voucher.voucher_value || 0}${voucher.voucher_type === 'percentage' ? '%' : '$'}</td>
                    <td>${validUntil}</td>
                    <td><span class="status ${statusClass}">${(voucher.status || 'inactive').charAt(0).toUpperCase() + (voucher.status || 'inactive').slice(1)}</span></td>
                    <td>
                        <button class="action-btn edit-btn" data-type="voucher" data-id="${voucher.id}">Edit</button>
                        <button class="action-btn delete-btn" data-type="voucher" data-id="${voucher.id}" data-action="delete">Delete</button>
                    </td>
                </tr>
            `;
            elements.vouchersTableBody.insertAdjacentHTML('beforeend', row);
        });
      }

      function renderBonusPointsTable() {
        const bonusPoints = AppData.get('bonusPointsChallenges', []);

        elements.bonusPointsTableBody.innerHTML = '';
        if (bonusPoints.length === 0) {
            elements.bonusPointsTableBody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 20px;">No bonus point challenges found. <button onclick="showBonusPointsModal(null)" style="background: none; border: none; color: var(--brand); text-decoration: underline; cursor: pointer;">Add your first bonus challenge</button></td></tr>`;
            return;
        }

        bonusPoints.forEach(challenge => {
            const statusClass = challenge.status === 'active' ? 'status-active' : 'status-inactive';
            const endDate = challenge.end_date ? new Date(challenge.end_date).toLocaleDateString() : 'No end date';
            const row = `
                <tr>
                    <td><strong>${challenge.name || 'Unnamed Challenge'}</strong></td>
                    <td>${challenge.description || 'No description'}</td>
                    <td>${challenge.condition || 'No condition'}</td>
                    <td>${challenge.bonus_points || 0} points</td>
                    <td>${endDate}</td>
                    <td><span class="status ${statusClass}">${(challenge.status || 'inactive').charAt(0).toUpperCase() + (challenge.status || 'inactive').slice(1)}</span></td>
                    <td>
                        <button class="action-btn edit-btn" data-type="bonus-challenge" data-id="${challenge.id}">Edit</button>
                        <button class="action-btn delete-btn" data-type="bonus-challenge" data-id="${challenge.id}" data-action="delete">Delete</button>
                    </td>
                </tr>
            `;
            elements.bonusPointsTableBody.insertAdjacentHTML('beforeend', row);
        });
      }

      function renderTemplatesTable() {
        const templates = AppData.get('voucherTemplates', []);

        elements.templatesTableBody.innerHTML = '';
        if (templates.length === 0) {
            elements.templatesTableBody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 20px;">No voucher templates found. <button onclick="showTemplateModal(null)" style="background: none; border: none; color: var(--brand); text-decoration: underline; cursor: pointer;">Create your first template</button></td></tr>`;
            return;
        }

        templates.forEach(template => {
            const discountDisplay = template.discount_type === 'percentage'
                ? `${template.discount_value}%`
                : `RM${template.discount_value}`;

            const row = `
                <tr>
                    <td><strong>${template.name || 'Unnamed Template'}</strong></td>
                    <td>${template.discount_type === 'percentage' ? 'Percentage' : 'Fixed Amount'}</td>
                    <td>${discountDisplay}</td>
                    <td>${template.expiry_days || 0} days</td>
                    <td>${template.exchange_points_count || 0} vouchers</td>
                    <td>
                        <button class="action-btn" style="background-color: var(--brand);" onclick="showGenerateVouchersModal(${template.id}, '${template.name}')">Generate</button>
                        <button class="action-btn edit-btn" data-type="template" data-id="${template.id}">Edit</button>
                        <button class="action-btn delete-btn" data-type="template" data-id="${template.id}" data-action="delete">Delete</button>
                    </td>
                </tr>
            `;
            elements.templatesTableBody.insertAdjacentHTML('beforeend', row);
        });
      }

      // --- Modal Functions ---
      function showRewardModal(reward) {
        elements.rewardForm.reset();
        elements.rewardModalTitle.textContent = reward ? 'Edit Reward' : 'Add New Reward';
        if (reward) {
          const setValueSafely = (id, value) => {
            const el = getEl(id);
            if (el) el.value = value || '';
          };

          setValueSafely('reward-name', reward.name);
          setValueSafely('reward-points', reward.points_required || reward.points);
          setValueSafely('reward-description', reward.description);
          setValueSafely('reward-status', reward.status || 'active');
          setValueSafely('reward-type', reward.reward_type || 'discount');
          setValueSafely('reward-value', reward.reward_value);
          setValueSafely('reward-validity-days', reward.validity_days || '30');
          setValueSafely('reward-usage-limit', reward.usage_limit || '1');
          setValueSafely('reward-minimum-order', reward.minimum_order || '0');
          setValueSafely('reward-redemption-method', reward.redemption_method || 'show_to_staff');
          setValueSafely('reward-max-redemptions', reward.max_redemptions_per_customer);
          setValueSafely('reward-expires-at', reward.expires_at);
          elements.rewardModalSaveBtn.dataset.id = reward.id;
        } else {
          elements.rewardModalSaveBtn.dataset.id = '';
        }
        elements.rewardModal.style.display = 'flex';
      }

      function closeRewardModal() {
        elements.rewardModal.style.display = 'none';
      }
      
      function showEventModal(event) {
        elements.eventForm.reset();
        elements.eventModalTitle.textContent = event ? 'Edit Event' : 'Add New Event';
        if (event) {
            getEl('event-title').value = event.title;
            getEl('event-description').value = event.description;
            getEl('event-status').value = event.status;
            elements.eventModalSaveBtn.dataset.id = event.id;
        } else {
            elements.eventModalSaveBtn.dataset.id = '';
        }
        elements.eventModal.style.display = 'flex';
      }
      
      function closeEventModal() {
        elements.eventModal.style.display = 'none';
      }

      function showTierModal(tier) {
        elements.tierForm.reset();
        elements.tierModalTitle.textContent = tier ? 'Edit Tier' : 'Add New Tier';
        if (tier) {
            getEl('tier-name').value = tier.name || '';
            getEl('tier-spending').value = tier.minimum_spending || 0;
            getEl('tier-color').value = tier.color || '#000000';
            getEl('tier-icon').value = tier.icon || '';
            getEl('tier-order').value = tier.sort_order || 0;
            getEl('tier-status').value = tier.is_active ? 'true' : 'false';
            elements.tierModalSaveBtn.dataset.id = tier.id;
        } else {
            getEl('tier-spending').value = 0;
            getEl('tier-color').value = '#000000';
            getEl('tier-order').value = 0;
            getEl('tier-status').value = 'true';
            elements.tierModalSaveBtn.dataset.id = '';
        }
        elements.tierModal.style.display = 'flex';
      }

      function closeTierModal() {
        elements.tierModal.style.display = 'none';
      }

      function showConfirmModal(text, onConfirm) {
        getEl('confirm-modal-text').textContent = text;
        confirmationCallback = onConfirm;
        elements.confirmModal.style.display = 'flex';
      }

      function handleConfirmation(isConfirmed) {
        if (isConfirmed && typeof confirmationCallback === 'function') {
          confirmationCallback();
        }
        confirmationCallback = null;
        elements.confirmModal.style.display = 'none';
      }

      // New Modal Functions for Additional Sections
      function showAchievementModal(achievement) {
        showGenericModal('Achievement', achievement, [
          { id: 'achievement-name', label: 'Achievement Name', type: 'text', value: achievement?.name || '' },
          { id: 'achievement-description', label: 'Description', type: 'textarea', value: achievement?.description || '' },
          { id: 'achievement-target-type', label: 'Target Type', type: 'select', options: ['orders', 'spending', 'visits', 'points'], value: achievement?.target_type || 'orders' },
          { id: 'achievement-target-value', label: 'Target Value', type: 'number', value: achievement?.target_value || 0 },
          { id: 'achievement-reward-points', label: 'Reward Points', type: 'number', value: achievement?.reward_points || 0 },
          { id: 'achievement-status', label: 'Status', type: 'select', options: ['active', 'inactive'], value: achievement?.status || 'active' }
        ], (data) => saveAchievement(data, achievement?.id));
      }

      function showVoucherModal(voucher) {
        showGenericModal('Voucher Collection', voucher, [
          { id: 'voucher-name', label: 'Collection Name', type: 'text', value: voucher?.name || '' },
          { id: 'voucher-spending-requirement', label: 'Spending Requirement ($)', type: 'number', value: voucher?.spending_requirement || 0 },
          { id: 'voucher-type', label: 'Voucher Type', type: 'select', options: ['discount', 'percentage', 'freebie'], value: voucher?.voucher_type || 'discount' },
          { id: 'voucher-value', label: 'Voucher Value', type: 'number', value: voucher?.voucher_value || 0 },
          { id: 'voucher-valid-until', label: 'Valid Until', type: 'date', value: voucher?.valid_until || '' },
          { id: 'voucher-status', label: 'Status', type: 'select', options: ['active', 'inactive'], value: voucher?.status || 'active' }
        ], (data) => saveVoucher(data, voucher?.id));
      }

      function showBonusPointsModal(challenge) {
        showGenericModal('Bonus Points Challenge', challenge, [
          { id: 'bonus-name', label: 'Challenge Name', type: 'text', value: challenge?.name || '' },
          { id: 'bonus-description', label: 'Description', type: 'textarea', value: challenge?.description || '' },
          { id: 'bonus-condition', label: 'Condition', type: 'text', value: challenge?.condition || '' },
          { id: 'bonus-points', label: 'Bonus Points', type: 'number', value: challenge?.bonus_points || 0 },
          { id: 'bonus-end-date', label: 'End Date', type: 'date', value: challenge?.end_date || '' },
          { id: 'bonus-status', label: 'Status', type: 'select', options: ['active', 'inactive'], value: challenge?.status || 'active' }
        ], (data) => saveBonusChallenge(data, challenge?.id));
      }

      function showTemplateModal(template) {
        elements.templateForm.reset();
        elements.templateModalTitle.textContent = template ? 'Edit Voucher Template' : 'Add New Voucher Template';
        if (template) {
          getEl('template-name').value = template.name || '';
          getEl('template-discount-type').value = template.discount_type || 'percentage';
          getEl('template-discount-value').value = template.discount_value || '';
          getEl('template-expiry-days').value = template.expiry_days || 30;
          elements.templateModalSaveBtn.dataset.id = template.id;
        } else {
          getEl('template-discount-type').value = 'percentage';
          getEl('template-expiry-days').value = 30;
          elements.templateModalSaveBtn.dataset.id = '';
        }

        // Auto-generate template name when type or value changes
        const autoGenerateName = () => {
          const type = getEl('template-discount-type').value;
          const value = getEl('template-discount-value').value;
          if (value) {
            const nameField = getEl('template-name');
            if (type === 'percentage') {
              nameField.value = `${value}% Discount Voucher`;
            } else {
              nameField.value = `RM${value} Discount Voucher`;
            }
          }
        };

        getEl('template-discount-type').addEventListener('change', autoGenerateName);
        getEl('template-discount-value').addEventListener('input', autoGenerateName);

        elements.templateModal.style.display = 'flex';
      }

      function closeTemplateModal() {
        elements.templateModal.style.display = 'none';
      }

      function showGenerateVouchersModal(templateId, templateName) {
        elements.generateVouchersForm.reset();
        getEl('generate-template-name').value = templateName;
        elements.generateVouchersModalGenerateBtn.dataset.templateId = templateId;
        elements.generateVouchersModal.style.display = 'flex';
      }

      function closeGenerateVouchersModal() {
        elements.generateVouchersModal.style.display = 'none';
      }

      // Expose to global scope for onclick handlers
      window.showGenerateVouchersModal = showGenerateVouchersModal;
      window.showTemplateModal = showTemplateModal;

      function showGenericModal(title, data, fields, onSave) {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.style.display = 'flex';

        const fieldsHtml = fields.map(field => {
          if (field.type === 'select') {
            const options = field.options.map(opt => `<option value="${opt}" ${field.value === opt ? 'selected' : ''}>${opt}</option>`).join('');
            return `
              <div class="form-group">
                <label for="${field.id}" class="form-label">${field.label}</label>
                <select id="${field.id}" class="form-control">${options}</select>
              </div>
            `;
          } else if (field.type === 'textarea') {
            return `
              <div class="form-group">
                <label for="${field.id}" class="form-label">${field.label}</label>
                <textarea id="${field.id}" class="form-control" rows="3">${field.value}</textarea>
              </div>
            `;
          } else {
            return `
              <div class="form-group">
                <label for="${field.id}" class="form-label">${field.label}</label>
                <input type="${field.type}" id="${field.id}" class="form-control" value="${field.value}">
              </div>
            `;
          }
        }).join('');

        modal.innerHTML = `
          <div class="modal">
            <div class="modal-header">
              <h2 class="modal-title">${data ? 'Edit' : 'Add'} ${title}</h2>
              <button class="modal-close" onclick="this.closest('.modal-overlay').remove()">×</button>
            </div>
            <div class="modal-body">
              ${fieldsHtml}
            </div>
            <div class="modal-footer">
              <button type="button" class="admin-btn btn-secondary" onclick="this.closest('.modal-overlay').remove()">Cancel</button>
              <button type="button" class="admin-btn btn-primary" onclick="handleGenericSave(this, ${JSON.stringify(fields.map(f => f.id))}, onSave)">Save</button>
            </div>
          </div>
        `;

        modal.querySelector('.admin-btn.btn-primary').onclick = () => {
          const formData = {};
          fields.forEach(field => {
            const element = modal.querySelector('#' + field.id);
            formData[field.id.replace(/^[^-]+-/, '')] = element.value;
          });
          onSave(formData);
          modal.remove();
        };

        document.body.appendChild(modal);

        modal.addEventListener('click', (e) => {
          if (e.target === modal) modal.remove();
        });
      }

      // --- Save Functions ---
      function saveReward() {
        const id = elements.rewardModalSaveBtn.dataset.id;

        const getValueSafely = (id) => {
          const el = getEl(id);
          return el ? el.value : '';
        };

        const rewardData = {
          name: getValueSafely('reward-name').trim(),
          points_required: parseInt(getValueSafely('reward-points'), 10),
          description: getValueSafely('reward-description').trim(),
          status: getValueSafely('reward-status') || 'active',
          reward_type: getValueSafely('reward-type') || 'discount',
          reward_value: parseFloat(getValueSafely('reward-value')) || 0,
          validity_days: parseInt(getValueSafely('reward-validity-days'), 10) || 30,
          usage_limit: parseInt(getValueSafely('reward-usage-limit'), 10) || 1,
          minimum_order: parseFloat(getValueSafely('reward-minimum-order')) || 0,
          redemption_method: getValueSafely('reward-redemption-method') || 'show_to_staff',
          max_redemptions_per_customer: parseInt(getValueSafely('reward-max-redemptions')) || null,
          expires_at: getValueSafely('reward-expires-at') || null,
          transferable: false  // All rewards are non-transferrable
        };

        if (!rewardData.name || isNaN(rewardData.points_required)) {
          console.log('Validation Error: Please fill in all required fields.');
          return;
        }

        const url = id ? `/admin/rewards/${id}` : '/admin/rewards/store';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(rewardData)
        })
        .then(async response => {
            if (!response.ok) {
                const text = await response.text();
                console.error('Server response:', text);
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                console.log('Reward Saved:', data.message);
                alert('Reward saved successfully!');
                closeRewardModal();
                setTimeout(() => window.location.reload(), 200);
            } else {
                console.error('Error:', data.message || 'Failed to save reward');
                alert(data.message || 'Failed to save reward');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to save reward: ' + error.message);
        });
      }
      
      function saveCheckinSettings() {
        console.log('saveCheckinSettings function called');

        const newCheckinPoints = [];
        for (let i = 0; i < 7; i++) {
            const element = document.getElementById(`checkin-day-${i}`);
            if (!element) {
                console.error(`Element checkin-day-${i} not found`);
                return;
            }
            const value = parseInt(element.value, 10) || 0;
            newCheckinPoints.push(value);
        }

        console.log('Sending checkin points:', newCheckinPoints);

        fetch('/admin/rewards/checkin-settings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ daily_points: newCheckinPoints })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error("Response is not JSON");
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                console.log('Settings Updated:', data.message);
                alert('Settings updated successfully!');
                // Update local data
                if (typeof rewardData !== 'undefined') {
                    rewardData.checkinSettings = { daily_points: newCheckinPoints };
                }
            } else {
                const errorMessage = data.message || 'Failed to save check-in settings';
                console.log('Error:', errorMessage);
                alert(errorMessage);
                if (data.errors) {
                    console.log('Validation errors:', data.errors);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            console.log('Error: Failed to save check-in settings');
            alert('Failed to save check-in settings: ' + error.message);
        });
      }

      // Make function globally accessible for inline onclick
      window.saveCheckinSettings = saveCheckinSettings;
      
      function saveEvent() {
        const id = elements.eventModalSaveBtn.dataset.id;
        const eventData = {
            title: getEl('event-title').value.trim(),
            description: getEl('event-description').value.trim(),
            status: getEl('event-status').value,
        };

        if (!eventData.title) {
            console.log('Validation Error: Please enter a title.');
            return;
        }

        const url = id ? `/admin/rewards/special-events/${id}` : '/admin/rewards/special-events';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(eventData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Event Saved:', data.message);

                // Add success animation to modal
                const modal = elements.eventModal;
                if (modal) {
                    modal.style.transform = 'scale(1.05)';
                    modal.style.transition = 'transform 0.2s ease';
                    setTimeout(() => {
                        modal.style.transform = 'scale(1)';
                        setTimeout(() => {
                            closeEventModal();
                            setTimeout(() => window.location.reload(), 200);
                        }, 200);
                    }, 200);
                }
            } else {
                console.log('Error: Failed to save event');

                // Add error animation to modal
                const modal = elements.eventModal;
                if (modal) {
                    modal.style.animation = 'errorShake 0.5s ease-in-out';
                    setTimeout(() => {
                        modal.style.animation = '';
                    }, 500);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            console.log('Error: Failed to save event');
        });
      }
      

      function saveTier() {
        const id = elements.tierModalSaveBtn.dataset.id;
        const tierData = {
            name: getEl('tier-name').value.trim(),
            minimum_spending: parseFloat(getEl('tier-spending').value) || 0,
            color: getEl('tier-color').value.trim(),
            icon: getEl('tier-icon').value.trim(),
            sort_order: parseInt(getEl('tier-order').value, 10) || 0,
            is_active: getEl('tier-status').value === 'true'
        };

        if (!tierData.name || isNaN(tierData.minimum_spending)) {
            console.log('Validation Error: Please fill all required fields.');
            return;
        }

        const url = id ? `/admin/rewards/loyalty-tiers/${id}` : '/admin/rewards/loyalty-tiers';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(tierData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Tier Saved:', data.message);

                // Add success animation to modal
                const modal = elements.tierModal;
                if (modal) {
                    modal.style.transform = 'scale(1.05)';
                    modal.style.transition = 'transform 0.2s ease';
                    setTimeout(() => {
                        modal.style.transform = 'scale(1)';
                        setTimeout(() => {
                            closeTierModal();
                            setTimeout(() => window.location.reload(), 200);
                        }, 200);
                    }, 200);
                }
            } else {
                console.log('Error: Failed to save tier');

                // Add error animation to modal
                const modal = elements.tierModal;
                if (modal) {
                    modal.style.animation = 'errorShake 0.5s ease-in-out';
                    setTimeout(() => {
                        modal.style.animation = '';
                    }, 500);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            console.log('Error: Failed to save tier');
        });
      }

      function saveTemplate() {
        const id = elements.templateModalSaveBtn.dataset.id;
        const templateData = {
          name: getEl('template-name').value.trim(),
          discount_type: getEl('template-discount-type').value,
          discount_value: parseFloat(getEl('template-discount-value').value) || 0,
          expiry_days: parseInt(getEl('template-expiry-days').value, 10) || 30
        };

        if (!templateData.name || isNaN(templateData.discount_value) || isNaN(templateData.expiry_days)) {
          console.log('Validation Error: Please fill all required fields.');
          return;
        }

        const url = id ? `/admin/rewards/voucher-templates/${id}` : '/admin/rewards/voucher-templates';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
          method: method,
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(templateData)
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            console.log('Template Saved:', data.message);
            closeTemplateModal();
            setTimeout(() => window.location.reload(), 200);
          } else {
            console.log('Error: Failed to save template');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          console.log('Error: Failed to save template');
        });
      }

      function generateVouchersFromTemplate() {
        const templateId = elements.generateVouchersModalGenerateBtn.dataset.templateId;
        const quantity = parseInt(getEl('generate-quantity').value, 10);
        const pointsRequired = parseInt(getEl('generate-points-required').value, 10);

        if (!quantity || quantity < 1 || quantity > 1000) {
          console.log('Validation Error: Please enter a valid quantity (1-1000).');
          return;
        }

        if (!pointsRequired || pointsRequired < 1) {
          console.log('Validation Error: Please enter valid points required.');
          return;
        }

        const url = `/admin/rewards/voucher-templates/${templateId}/generate`;

        fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ quantity, points_required: pointsRequired })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            console.log('Vouchers Generated:', data.message);
            closeGenerateVouchersModal();
            setTimeout(() => window.location.reload(), 200);
          } else {
            console.log('Error: Failed to generate vouchers');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          console.log('Error: Failed to generate vouchers');
        });
      }

      // --- New Save Functions ---
      function saveAchievement(data, id) {
        const achievementData = {
          name: data.name,
          description: data.description,
          target_type: data['target-type'],
          target_value: parseInt(data['target-value']),
          reward_points: parseInt(data['reward-points']),
          status: data.status
        };

        const url = id ? `/admin/rewards/achievements/${id}` : '/admin/rewards/achievements';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
          method: method,
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(achievementData)
        })
        .then(response => response.json())
        .then(result => {
          if (result.success) {
            renderAchievementsTable();
            console.log('Success:', `Achievement ${id ? 'updated' : 'created'} successfully`);
          } else {
            console.log('Error: Failed to save achievement');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          console.log('Error: Failed to save achievement');
        });
      }

      function saveVoucher(data, id) {
        const voucherData = {
          name: data.name,
          spending_requirement: parseFloat(data['spending-requirement']),
          voucher_type: data.type,
          voucher_value: parseFloat(data.value),
          valid_until: data['valid-until'],
          status: data.status
        };

        const url = id ? `/admin/rewards/vouchers/${id}` : '/admin/rewards/vouchers';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
          method: method,
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(voucherData)
        })
        .then(response => response.json())
        .then(result => {
          if (result.success) {
            renderVouchersTable();
            console.log('Success:', `Voucher collection ${id ? 'updated' : 'created'} successfully`);
          } else {
            console.log('Error: Failed to save voucher collection');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          console.log('Error: Failed to save voucher collection');
        });
      }

      function saveBonusChallenge(data, id) {
        const challengeData = {
          name: data.name,
          description: data.description,
          condition: data.condition,
          bonus_points: parseInt(data.points),
          end_date: data['end-date'],
          status: data.status
        };

        const url = id ? `/admin/rewards/bonus-challenges/${id}` : '/admin/rewards/bonus-challenges';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
          method: method,
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(challengeData)
        })
        .then(response => response.json())
        .then(result => {
          if (result.success) {
            renderBonusPointsTable();
            console.log('Success:', `Bonus challenge ${id ? 'updated' : 'created'} successfully`);
          } else {
            console.log('Error: Failed to save bonus challenge');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          console.log('Error: Failed to save bonus challenge');
        });
      }

      // --- Delete Functions ---
      function deleteAchievement(id) {
        showConfirmModal('Are you sure you want to delete this achievement?', () => {
          fetch(`/admin/rewards/achievements/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })
          .then(response => response.json())
          .then(result => {
            if (result.success) {
              renderAchievementsTable();
              console.log('Success: Achievement deleted successfully');
            } else {
              console.log('Error: Failed to delete achievement');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            console.log('Error: Failed to delete achievement');
          });
        });
      }

      function deleteVoucher(id) {
        showConfirmModal('Are you sure you want to delete this voucher collection?', () => {
          fetch(`/admin/rewards/vouchers/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })
          .then(response => response.json())
          .then(result => {
            if (result.success) {
              renderVouchersTable();
              console.log('Success: Voucher collection deleted successfully');
            } else {
              console.log('Error: Failed to delete voucher collection');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            console.log('Error: Failed to delete voucher collection');
          });
        });
      }

      function deleteBonusChallenge(id) {
        showConfirmModal('Are you sure you want to delete this bonus challenge?', () => {
          fetch(`/admin/rewards/bonus-challenges/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })
          .then(response => response.json())
          .then(result => {
            if (result.success) {
              renderBonusPointsTable();
              console.log('Success: Bonus challenge deleted successfully');
            } else {
              console.log('Error: Failed to delete bonus challenge');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            console.log('Error: Failed to delete bonus challenge');
          });
        });
      }

      // --- Event Handlers ---
      function handleTableActions(e) {
        const button = e.target.closest('.action-btn');
        if (!button) return;

        const id = button.dataset.id;
        const type = button.dataset.type;
        const action = button.dataset.action || 'edit'; // Default to edit

        // Add click animation
        button.classList.add('btn-clicked');
        setTimeout(() => button.classList.remove('btn-clicked'), 200);

        // Get item data for display
        let items = [];
        if (type === 'reward') items = AppData.get('rewards', []);
        else if (type === 'event') items = AppData.get('events', []);
        else if (type === 'tier') items = AppData.get('tiers', []);
        else if (type === 'achievement') items = AppData.get('achievements', []);
        else if (type === 'voucher') items = AppData.get('voucherCollections', []);
        else if (type === 'bonus-challenge') items = AppData.get('bonusPointsChallenges', []);
        else if (type === 'template') items = AppData.get('voucherTemplates', []);

        const item = items.find(i => i.id == id);
        if (!item) return;

        const row = button.closest('tr');

        if (action === 'delete') {
            showConfirmModal(`Are you sure you want to delete "${item.name || item.title}"?`, () => {
                // Add loading state to button
                button.classList.add('btn-loading');
                button.disabled = true;

                const urls = {
                    'reward': `/admin/rewards/${id}`,
                    'event': `/admin/rewards/special-events/${id}`,
                    'tier': `/admin/rewards/loyalty-tiers/${id}`,
                    'achievement': `/admin/rewards/achievements/${id}`,
                    'voucher': `/admin/rewards/vouchers/${id}`,
                    'bonus-challenge': `/admin/rewards/bonus-challenges/${id}`,
                    'template': `/admin/rewards/voucher-templates/${id}`
                };

                fetch(urls[type], {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Add delete animation to row
                        if (row) {
                            row.classList.add('row-deleting');
                            setTimeout(() => {
                                console.log('Item Deleted:', data.message);
                                window.location.reload();
                            }, 500);
                        } else {
                            window.location.reload();
                        }
                    } else {
                        if (row) row.classList.add('row-error');
                        button.classList.remove('btn-loading');
                        button.disabled = false;
                        console.log('Error: Failed to delete item');
                        setTimeout(() => { if (row) row.classList.remove('row-error'); }, 500);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (row) row.classList.add('row-error');
                    button.classList.remove('btn-loading');
                    button.disabled = false;
                    console.log('Error: Failed to delete item');
                    setTimeout(() => { if (row) row.classList.remove('row-error'); }, 500);
                });
            });
        } else if (action === 'toggle') {
            // Add loading state to button
            button.classList.add('btn-loading');
            button.disabled = true;

            const urls = {
                'reward': `/admin/rewards/${id}/toggle`,
                'event': `/admin/rewards/special-events/${id}/toggle`,
                'tier': `/admin/rewards/loyalty-tiers/${id}/toggle`
            };

            if (urls[type]) {
                fetch(urls[type], {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Add success animation to row
                        if (row) {
                            row.classList.add('row-success', 'status-changing');

                            // Update button text and class based on status
                            if (type === 'tier') {
                                // For loyalty tiers, use is_active instead of status
                                if (data.is_active) {
                                    button.textContent = 'Disable';
                                    button.classList.remove('view-btn');
                                    button.classList.add('delete-btn');
                                } else {
                                    button.textContent = 'Enable';
                                    button.classList.remove('delete-btn');
                                    button.classList.add('view-btn');
                                }

                                // Update status badge
                                const statusBadge = row.querySelector('.status');
                                if (statusBadge) {
                                    const statusText = data.is_active ? 'Active' : 'Inactive';
                                    statusBadge.textContent = statusText;
                                    statusBadge.className = `status status-${data.is_active ? 'active' : 'inactive'}`;
                                }
                            } else {
                                // For other items, use status
                                if (data.status === 'active') {
                                    button.textContent = 'Disable';
                                    button.classList.remove('view-btn');
                                    button.classList.add('delete-btn');
                                } else {
                                    button.textContent = 'Enable';
                                    button.classList.remove('delete-btn');
                                    button.classList.add('view-btn');
                                }

                                // Update status badge
                                const statusBadge = row.querySelector('.status');
                                if (statusBadge) {
                                    statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                                    statusBadge.className = `status status-${data.status}`;
                                }
                            }
                        }

                        if (typeof RewardNotifications !== 'undefined') {
                            console.log('Status Updated:', data.message);
                        }

                        // Remove animations after completion
                        setTimeout(() => {
                            if (row) {
                                row.classList.remove('row-success', 'status-changing');
                            }
                        }, 1000);
                    } else {
                        if (row) row.classList.add('row-error');
                        if (typeof RewardNotifications !== 'undefined') {
                            console.log('Error: Failed to update status');
                        }
                        setTimeout(() => { if (row) row.classList.remove('row-error'); }, 500);
                    }

                    button.classList.remove('btn-loading');
                    button.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (row) row.classList.add('row-error');
                    if (typeof RewardNotifications !== 'undefined') {
                        console.log('Error: Failed to update status');
                    }
                    setTimeout(() => { if (row) row.classList.remove('row-error'); }, 500);
                    button.classList.remove('btn-loading');
                    button.disabled = false;
                });
            }
        } else { // Edit
            if (type === 'reward') showRewardModal(item);
            if (type === 'event') showEventModal(item);
            if (type === 'tier') showTierModal(item);
            if (type === 'achievement') showAchievementModal(item);
            if (type === 'voucher') showVoucherModal(item);
            if (type === 'bonus-challenge') showBonusPointsModal(item);
            if (type === 'template') showTemplateModal(item);
        }
      }


      // --- Initialization ---
      function init() {
        elements.currentDate.textContent = new Date().toLocaleDateString('en-MY', {
          weekday: 'short', year: 'numeric', month: 'short', day: 'numeric'
        });

        loadBookings();
        // Don't render all - let restoreActiveTab handle rendering the active section only
        updateStats();

        // Event Listeners
        elements.viewSiteBtn.addEventListener('click', () => {

        });

        // Setup notifications

        elements.hamburgerBtn.addEventListener('click', () => {
          elements.adminSidebar.classList.toggle('open');
          elements.mobileOverlay.classList.toggle('active');
        });
        elements.mobileOverlay.addEventListener('click', () => {
          elements.adminSidebar.classList.remove('open');
          elements.mobileOverlay.classList.remove('active');
        });
        elements.logoutBtn.addEventListener('click', () => {
          if (confirm('Are you sure you want to log out?')) {
            document.getElementById('logoutForm').submit();
          }
        });

        // Tab switching with state persistence
        function switchToTab(tabName) {
          // Update tab active states
          document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
          const targetTab = document.querySelector(`[data-tab="${tabName}"]`);
          if (targetTab) {
            targetTab.classList.add('active');
          }

          // Show/hide sections using class toggle
          document.querySelectorAll('.admin-section').forEach(section => {
            section.classList.add('hidden');
          });
          const activeSection = document.getElementById(`${tabName}-section`);
          if (activeSection) {
            activeSection.classList.remove('hidden');
          }

          // Render only the active section's data
          renderActiveSection(tabName);

          // Save current tab to localStorage
          localStorage.setItem('rewards_admin_active_tab', tabName);
        }

        // Restore active tab on page load
        function restoreActiveTab() {
          const savedTab = localStorage.getItem('rewards_admin_active_tab');
          if (savedTab) {
            // Check if the saved tab element exists
            const tabElement = document.querySelector(`[data-tab="${savedTab}"]`);
            if (tabElement) {
              switchToTab(savedTab);
              return;
            }
          }
          // Default to 'rewards' if no saved tab or saved tab doesn't exist
          switchToTab('rewards');
        }

        // Set up tab click handlers
        document.querySelectorAll('.admin-tab').forEach(tab => {
          tab.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            switchToTab(tabName);
          });
        });

        // Restore the active tab on page load
        restoreActiveTab();

        // Only attach event listeners if elements exist (backward compatibility)
        if (elements.addRewardBtn) elements.addRewardBtn.addEventListener('click', () => showRewardModal(null));
        if (elements.addEventBtn) elements.addEventBtn.addEventListener('click', () => showEventModal(null));
        if (elements.addAchievementBtn) elements.addAchievementBtn.addEventListener('click', () => showAchievementModal(null));
        if (elements.addVoucherCollectionBtn) elements.addVoucherCollectionBtn.addEventListener('click', () => showVoucherModal(null));
        if (elements.addBonusPointsBtn) elements.addBonusPointsBtn.addEventListener('click', () => showBonusPointsModal(null));
        if (elements.addTemplateBtn) elements.addTemplateBtn.addEventListener('click', () => showTemplateModal(null));
        if (elements.filterSelect) elements.filterSelect.addEventListener('change', renderRewardsTable);
        if (elements.rewardsTableBody) elements.rewardsTableBody.addEventListener('click', handleTableActions);
        if (elements.eventsTableBody) elements.eventsTableBody.addEventListener('click', handleTableActions);
        if (elements.tiersTableBody) elements.tiersTableBody.addEventListener('click', handleTableActions);
        if (elements.achievementsTableBody) elements.achievementsTableBody.addEventListener('click', handleTableActions);
        if (elements.vouchersTableBody) elements.vouchersTableBody.addEventListener('click', handleTableActions);
        if (elements.bonusPointsTableBody) elements.bonusPointsTableBody.addEventListener('click', handleTableActions);
        if (elements.templatesTableBody) elements.templatesTableBody.addEventListener('click', handleTableActions);

        // Modal listeners
        getEl('reward-modal-close-btn').addEventListener('click', closeRewardModal);
        getEl('reward-modal-cancel-btn').addEventListener('click', closeRewardModal);
        elements.rewardModalSaveBtn.addEventListener('click', saveReward);
        
        getEl('event-modal-close-btn').addEventListener('click', closeEventModal);
        getEl('event-modal-cancel-btn').addEventListener('click', closeEventModal);
        elements.eventModalSaveBtn.addEventListener('click', saveEvent);

        getEl('tier-modal-close-btn').addEventListener('click', closeTierModal);
        getEl('tier-modal-cancel-btn').addEventListener('click', closeTierModal);
        elements.tierModalSaveBtn.addEventListener('click', saveTier);

        getEl('template-modal-close-btn').addEventListener('click', closeTemplateModal);
        getEl('template-modal-cancel-btn').addEventListener('click', closeTemplateModal);
        elements.templateModalSaveBtn.addEventListener('click', saveTemplate);

        getEl('generate-vouchers-modal-close-btn').addEventListener('click', closeGenerateVouchersModal);
        getEl('generate-vouchers-modal-cancel-btn').addEventListener('click', closeGenerateVouchersModal);
        elements.generateVouchersModalGenerateBtn.addEventListener('click', generateVouchersFromTemplate);

        // Confirmation modal listeners
        getEl('confirm-modal-close-btn').addEventListener('click', () => handleConfirmation(false));
        getEl('confirm-modal-cancel-btn').addEventListener('click', () => handleConfirmation(false));
        getEl('confirm-modal-confirm-btn').addEventListener('click', () => handleConfirmation(true));
        
        // Save buttons
        if (elements.saveCheckinBtn) {
            elements.saveCheckinBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Check-in save button clicked');
                saveCheckinSettings();
            });
        } else {
            console.error('saveCheckinBtn element not found');
        }


        if (elements.addTierBtn) {
            elements.addTierBtn.addEventListener('click', () => showTierModal(null));
        }

        // Add search input event listener for redemptions
        const redemptionSearch = document.getElementById('redemption-search');
        if (redemptionSearch) {
            redemptionSearch.addEventListener('input', searchRedemptions);
            redemptionSearch.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    searchRedemptions();
                }
            });
        }
      }

      // ===== Redemption Search and Dynamic Height Functions =====
      let filteredRedemptions = [];

      // Function to update table scrollability based on row count
      function updateTableScrollability(containerId, rowCount) {
        const container = document.getElementById(containerId);
        if (container) {
          if (rowCount > 5) {
            container.classList.add('scrollable');
          } else {
            container.classList.remove('scrollable');
          }
        }
      }

      // Search function for redemptions
      function searchRedemptions() {
        const searchInput = document.getElementById('redemption-search');
        const searchTerm = searchInput.value.trim();
        renderRedemptionsTable(searchTerm);
      }

      // Clear search function
      function clearRedemptionSearch() {
        const searchInput = document.getElementById('redemption-search');
        searchInput.value = '';
        renderRedemptionsTable('');
      }

      // ===== Redemption Management Functions =====
      function viewRedemption(redemptionId) {
        const redemptions = AppData.get('redemptions', []);
        const redemption = redemptions.find(r => r.id == redemptionId);

        if (!redemption) {
          RewardNotifications.createNotification('Error', 'Redemption not found');
          return;
        }

        const modalContent = `
          <div class="redemption-details">
            <h3>Redemption Details</h3>
            <div class="detail-row">
              <label>Redemption ID:</label>
              <span>#${redemption.id}</span>
            </div>
            <div class="detail-row">
              <label>Customer:</label>
              <span>${redemption.customer}</span>
            </div>
            <div class="detail-row">
              <label>Reward:</label>
              <span>${redemption.reward}</span>
            </div>
            <div class="detail-row">
              <label>Points Spent:</label>
              <span>${redemption.points}</span>
            </div>
            <div class="detail-row">
              <label>Date:</label>
              <span>${redemption.date}</span>
            </div>
            <div class="detail-row">
              <label>Status:</label>
              <span class="status ${redemption.status === 'redeemed' ? 'status-active' : 'status-pending'}">${redemption.status === 'redeemed' ? 'Redeemed' : 'Pending'}</span>
            </div>
          </div>
        `;

        showInfoModal('Redemption Details', modalContent);
      }

      function markAsRedeemed(redemptionId) {
        showConfirmModal(
          'Are you sure you want to mark this redemption as redeemed?',
          () => {
            fetch(`/admin/rewards/redemptions/${redemptionId}/mark-redeemed`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              }
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                console.log('Success:', data.message);
                // Update the local data
                let redemptions = AppData.get('redemptions', []);
                console.log('Current redemptions:', redemptions);
                console.log('Looking for redemption ID:', redemptionId);

                const index = redemptions.findIndex(r => {
                  console.log('Comparing:', r.id, 'with', redemptionId);
                  return r.id == redemptionId;
                });

                console.log('Found index:', index);
                if (index !== -1) {
                  console.log('Updating status from', redemptions[index].status, 'to redeemed');
                  redemptions[index].status = 'redeemed';
                  console.log('Before AppData.set - redemption data:', redemptions[index]);
                  AppData.set('redemptions', redemptions);
                  console.log('After AppData.set - checking updated data:', AppData.get('redemptions')[index]);

                  // Force a fresh render by clearing cache first
                  console.log('Updated redemptions before render:', AppData.get('redemptions', []));
                  console.log('About to call renderRedemptionsTable...');
                  console.log('Current DOM state - redemptions table body exists:', !!document.getElementById('redemptions-table-body'));
                  renderRedemptionsTable();
                  console.log('renderRedemptionsTable call completed');
                } else {
                  console.error('Redemption not found in local data');
                  // Fallback: reload the page data
                  location.reload();
                }
              } else {
                console.log('Error:', data.message || 'Failed to update redemption status');
              }
            })
            .catch(error => {
              console.error('Error:', error);
              console.log('Error: Failed to update redemption status');
            });
          }
        );
      }

      // Make functions globally available
      window.viewRedemption = viewRedemption;
      window.markAsRedeemed = markAsRedeemed;

      document.addEventListener('DOMContentLoaded', init);
    })();


    function showInfoModal(title, content) {
      const modal = document.createElement('div');
      modal.className = 'modal-overlay';
      modal.innerHTML = `
        <div class="modal-dialog">
          <div class="modal-header">
            <h3>${title}</h3>
            <button class="modal-close" onclick="this.closest('.modal-overlay').remove()">&times;</button>
          </div>
          <div class="modal-body">
            ${content}
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" onclick="this.closest('.modal-overlay').remove()">Close</button>
          </div>
        </div>
      `;

      modal.style.display = 'flex';
      document.body.appendChild(modal);

      // Close on backdrop click
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          modal.remove();
        }
      });
    }

  </script>
</body>
</html>
@endsection