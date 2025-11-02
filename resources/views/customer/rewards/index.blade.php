@extends('layouts.customer')

@section('title', 'Rewards - The Stag SmartDine')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/rewards.css') }}?v={{ time() }}">
@endsection

@section('content')
<!-- Main Content -->
<main class="main-content">
    @if(!Auth::check())
    <!-- Guest Message -->
    <div style="background: linear-gradient(135deg, #ff6b35, #f7931e); color: white; padding: 2rem; border-radius: 20px; margin: 2rem; text-align: center;">
        <h2 style="margin-bottom: 1rem;">🎁 Rewards Program</h2>
        <p style="margin-bottom: 1.5rem; font-size: 1.1rem;">Please login to access your rewards, check-in daily, and redeem points for exclusive benefits.</p>
        <a href="{{ route('login') }}" style="background: white; color: #ff6b35; padding: 1rem 2rem; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-block;">
            <i class="fas fa-sign-in-alt"></i> Login to View Rewards
        </a>
    </div>
    @else
    <!-- Header Section (Scrollable) -->
    <div class="header-section">
        <!-- Search Bar -->
        <div class="search-bar-container" role="search">
            <div class="search-bar">
                <span class="search-icon" aria-hidden="true">🔎</span>
                <input type="text" class="search-input" placeholder="Search rewards, vouchers..." id="searchInput" aria-label="Search rewards" />
                <button class="clear-btn" id="clearSearch" aria-label="Clear search">✕</button>
            </div>
        </div>

        <!-- Dynamic Category Title -->
        <h1 class="category-title" id="categoryTitle">My Rewards</h1>
    </div>

    <!-- Top Row: Points & Check-in -->
    <div class="grid-two">
        <!-- Points Display & Check-in -->
        <div class="card points-card">
            <div class="points-display">
                <div class="points-label" id="points-label">Your Points</div>
                <div class="points-number" id="points">{{ number_format($user->points_balance ?? 0) }}</div>
                <div class="points-label">Keep collecting! 🌟</div>
            </div>

            <h3 id="checkin-header">📅 Daily Check-In Streak</h3>
            <p id="checkin-desc" style="color: rgba(255,255,255,0.9); font-weight: 600;">
                Check in daily to earn bonus points!
            </p>
            <div class="checkin-track" id="checkinTrack"></div>
            <button id="checkInBtn" class="btn-primary pulse">Check In Today</button>
            <div id="checkinMessage" style="margin-top: 12px; text-align: center; font-size: 0.9rem; color: rgba(255,255,255,0.9); display: none; font-weight: 600;"></div>
        </div>

        <!-- Points Exchange -->
        <div class="card">
            <h2>💳 Exchange Points</h2>
            <p>Redeem your points for exclusive rewards and discounts.</p>
            <div class="reward-list" id="redeemList">
                @if(isset($availableRewards) && $availableRewards->count() > 0)
                @foreach($availableRewards->take(4) as $reward)
                <div class="reward-item">
                    <div class="reward-info">
                        <div class="reward-name">
                            {{ $reward->title }}
                            @if($reward->requiredTier)
                            <span style="background: #E3F2FD; color: #2196F3; padding: 2px 8px; border-radius: 8px; font-size: 0.75rem; margin-left: 8px;">
                                <i class="fas fa-star"></i> {{ $reward->requiredTier->name }}
                            </span>
                            @endif
                        </div>
                        <div class="reward-cost">
                            {{ $reward->description ?? 'Exclusive reward' }} • {{ $reward->points_required }} points
                            @if($reward->expires_at)
                            <span class="expiry">• Expires: {{ $reward->expires_at->format('M j, Y') }}</span>
                            @endif
                            @if($reward->terms_conditions)
                            <div class="reward-terms">{{ $reward->terms_conditions }}</div>
                            @endif
                        </div>
                    </div>
                    <button class="btn-secondary redeem-btn"
                        data-reward-id="{{ $reward->id }}"
                        data-points-required="{{ $reward->points_required }}"
                        onclick="redeemReward('{{ $reward->id }}', '{{ $reward->points_required }}')">
                        Redeem
                    </button>
                </div>
                @endforeach
                @else
                <div class="reward-item">
                    <div class="reward-info">
                        <div class="reward-name">No rewards available</div>
                        <div class="reward-cost">Check back later for new rewards!</div>
                    </div>
                </div>
                @endif
            </div>

            @if(isset($hasMoreRewards) && $hasMoreRewards)
            <!-- See All Button -->
            <div style="text-align: center; margin-top: 16px;">
                <button class="see-all-btn" onclick="showAllExchangePoints()">
                    See All <i class="fas fa-arrow-right"></i>
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Special Events -->
    @if(isset($specialEvents) && $specialEvents->count() > 0)
    <div class="card special-events">
        <h2 style="color: #92400e;">🎪 Special Events</h2>
        <div class="event-list" id="event-list">
            @foreach($specialEvents as $event)
            <div class="event-item">
                <div class="event-icon">🎉</div>
                <div class="event-details">
                    <h3>{{ $event->name }}</h3>
                    <p>{{ $event->description }}</p>
                    @if($event->end_date)
                    <div style="font-size: 0.9rem; color: var(--text-2); margin-top: 0.5rem;">
                        Valid until: {{ $event->end_date->format('M j, Y') }}
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Spending Rewards -->
    <div class="card">
        <h2>💰 Spend & Earn More</h2>
        <div class="grid-two">
            <div>
                <h3>🎟️ Collect Vouchers</h3>
                <p>Spend more to unlock exclusive vouchers!</p>
                <div class="reward-list" id="voucherList">
                    @if(isset($voucherCollections) && $voucherCollections->count() > 0)
                        @foreach($voucherCollections as $collection)
                        <div class="reward-item">
                            <div class="reward-info">
                                <div class="reward-name">{{ $collection->name }}</div>
                                <div class="reward-requirement">{{ $collection->description ?? 'Exclusive voucher collection' }}</div>
                                <div class="reward-requirement">Spend RM{{ number_format($collection->spending_requirement ?? 0, 2) }} or more</div>
                            </div>
                            <button class="btn-secondary" onclick="collectVoucher({{ $collection->id }}, '{{ $collection->name }}')">Collect</button>
                        </div>
                        @endforeach
                    @else
                        <div class="empty-state" style="padding: 1rem; text-align: center; color: var(--text-2); font-size: 0.9rem;">
                            <p>No voucher collections available</p>
                        </div>
                    @endif
                </div>
            </div>
            <div>
                <h3>⭐ Earn Bonus Points</h3>
                <p>Complete challenges to earn extra points!</p>
                <div class="reward-list" id="bonusPointsList">
                    @if(isset($bonusChallenges) && $bonusChallenges->count() > 0)
                        @php
                            $hasEligibleChallenges = false;
                        @endphp
                        @foreach($bonusChallenges as $challenge)
                            @php
                                $eligibility = $challenge->isEligibleFor($user);
                            @endphp
                            @if($eligibility['eligible'])
                                @php
                                    $hasEligibleChallenges = true;
                                @endphp
                                <div class="reward-item">
                                    <div class="reward-info">
                                        <div class="reward-name">{{ $challenge->name }}</div>
                                        <div class="reward-requirement">
                                            {{ $challenge->description ?? $challenge->condition }}
                                            @if($challenge->condition_type && $challenge->min_requirement)
                                                <br>
                                                <span style="font-size: 0.85rem; color: var(--primary);">
                                                    @if($challenge->condition_type === 'orders')
                                                        Requires: {{ $challenge->min_requirement }} order{{ $challenge->min_requirement > 1 ? 's' : '' }}
                                                    @elseif($challenge->condition_type === 'spending')
                                                        Requires: RM{{ number_format($challenge->min_requirement, 2) }} spending
                                                    @elseif($challenge->condition_type === 'visits')
                                                        Requires: {{ $challenge->min_requirement }} visit{{ $challenge->min_requirement > 1 ? 's' : '' }}
                                                    @elseif($challenge->condition_type === 'checkin_streak')
                                                        Requires: {{ $challenge->min_requirement }}-day check-in streak
                                                    @elseif($challenge->condition_type === 'referrals')
                                                        Requires: {{ $challenge->min_requirement }} referral{{ $challenge->min_requirement > 1 ? 's' : '' }}
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                        @if($challenge->end_date)
                                        <div style="font-size: 0.8rem; color: var(--text-2); margin-top: 4px;">
                                            Ends: {{ $challenge->end_date->format('M j, Y') }}
                                        </div>
                                        @endif
                                        @if($challenge->max_claims_per_user > 0)
                                        <div style="font-size: 0.75rem; color: var(--text-2); margin-top: 4px;">
                                            Remaining claims: {{ $challenge->max_claims_per_user - $challenge->getClaimCountByUser($user) }}
                                        </div>
                                        @endif
                                    </div>
                                    <button class="btn-secondary" onclick="claimBonusChallenge({{ $challenge->id }}, '{{ $challenge->name }}', {{ $challenge->bonus_points }})">
                                        Claim +{{ $challenge->bonus_points }} pts
                                    </button>
                                </div>
                            @endif
                        @endforeach

                        @if(!$hasEligibleChallenges)
                        <div class="empty-state" style="padding: 1rem; text-align: center; color: var(--text-2); font-size: 0.9rem;">
                            <p>No eligible challenges available</p>
                            <p style="font-size: 0.8rem; margin-top: 0.5rem;">Complete more orders or activities to unlock new challenges!</p>
                        </div>
                        @endif
                    @else
                        <!-- Default/fallback challenges -->
                        @php
                            $userOrderCount = $user->orders()->where('payment_status', 'paid')->count();
                        @endphp

                        @if($userOrderCount === 0)
                        <div class="reward-item">
                            <div class="reward-info">
                                <div class="reward-name">First Order Bonus</div>
                                <div class="reward-requirement">Place your first order</div>
                            </div>
                            <button class="btn-secondary" onclick="showMessage('Complete your first order to earn bonus points! 🎯')">+50 pts</button>
                        </div>
                        @else
                        <div class="empty-state" style="padding: 1rem; text-align: center; color: var(--text-2); font-size: 0.9rem;">
                            <p>No active challenges available</p>
                            <p style="font-size: 0.8rem; margin-top: 0.5rem;">Check back later for new bonus point opportunities!</p>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Achievements -->
    @if(isset($achievements) && $achievements->count() > 0)
    <div class="card">
        <h2>🏆 Achievements</h2>
        <div class="achievement-grid" id="achievementGrid">
            @foreach($achievements as $achievement)
            <div class="achievement-item">
                <div class="achievement-icon">🏅</div>
                <div class="achievement-details">
                    <h4>{{ $achievement->name }}</h4>
                    <p>{{ $achievement->description }}</p>
                    <div class="achievement-target">
                        Target: {{ $achievement->target_value }} {{ $achievement->target_type }}
                    </div>
                    <div class="achievement-reward">
                        Reward: +{{ $achievement->reward_points }} points
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- My Vouchers -->
    <div class="card">
        <h2>🎪 My Voucher Collection</h2>
        <div id="myVoucherList" class="voucher-grid" style="display: {{ (isset($userVouchers) && $userVouchers->count() > 0) ? 'grid' : 'none' }}">
            @if(isset($userVouchers) && $userVouchers->count() > 0)
                @foreach($userVouchers->take(6) as $voucher)
                <div class="voucher-card {{ $voucher->status }}">
                    <div class="voucher-header">
                        <span class="voucher-icon">🎁</span>
                        <span class="voucher-type">{{ $voucher->voucherTemplate->name ?? 'Voucher' }}</span>
                    </div>
                    <div class="voucher-body">
                        <h3>
                            @if($voucher->voucherTemplate->discount_type === 'free_item')
                                FREE ITEM
                            @elseif($voucher->voucherTemplate->discount_type === 'percentage')
                                {{ $voucher->voucherTemplate->discount_value }}% OFF
                            @else
                                RM{{ number_format($voucher->voucherTemplate->discount_value, 2) }} OFF
                            @endif
                        </h3>
                        <p>{{ $voucher->voucherTemplate->description ?? 'Exclusive discount' }}</p>
                        @if($voucher->expiry_date)
                        <div class="voucher-expiry">Expires: {{ $voucher->expiry_date->format('M j, Y') }}</div>
                        @endif
                    </div>
                    <div class="voucher-footer">
                        @if($voucher->voucherTemplate->discount_type === 'free_item')
                        <button class="btn-primary"
                            onclick="applyFreeItemVoucher(
                                {{ $voucher->id }},
                                '{{ addslashes($voucher->voucherTemplate->title ?? 'Free Item') }}',
                                {{ json_encode($voucher->voucherTemplate->applicable_menu_item_ids ?? []) }}
                            )">
                            USE NOW
                        </button>
                        @else
                        <button class="btn-primary" onclick="window.location.href='{{ route('customer.menu.index') }}'">USE NOW</button>
                        @endif
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        <div id="noVoucher" class="empty-state" style="display: {{ (isset($userVouchers) && $userVouchers->count() > 0) ? 'none' : 'block' }}">
            <div class="icon">🎫</div>
            <h3>No vouchers yet</h3>
            <p>Start collecting vouchers by spending more or completing challenges!</p>
        </div>

        @if(isset($userVouchers) && $userVouchers->count() > 6)
        <div id="seeAllVouchersBtn" style="text-align: center; margin-top: 16px;">
            <button class="see-all-btn" onclick="showAllVouchersModal()">
                See All ({{ $userVouchers->count() }}) <i class="fas fa-arrow-right"></i>
            </button>
        </div>
        @endif
    </div>

    <!-- My Rewards -->
    <div class="card">
        <h2>🎁 My Rewards</h2>
        <p style="color: var(--text-2); margin-bottom: 1.5rem;">Your redeemed rewards - ready to use!</p>
        <div id="redeemedRewardsList">
            @if(isset($redeemedRewards) && $redeemedRewards->count() > 0)
            @foreach($redeemedRewards as $redemption)
            <div class="redeemed-reward-item status-{{ $redemption->status ?? 'pending' }}">
                <div class="reward-info">
                    <div class="reward-details">
                        <h4>
                            {{ $redemption->reward->title ?? 'Reward' }}
                            <span class="status-badge {{ $redemption->status ?? 'active' }}">
                                @if(($redemption->status ?? 'active') === 'redeemed')
                                ✓ Used
                                @else
                                ⏳ Ready
                                @endif
                            </span>
                        </h4>
                        <p>{{ $redemption->reward->description ?? '' }}</p>
                        <div class="reward-meta">
                            <span class="points-spent">{{ number_format($redemption->points_spent) }} points</span>
                            <span class="redemption-date">{{ $redemption->created_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="reward-actions">
                    @if(($redemption->status ?? 'active') === 'redeemed')
                    <div class="staff-note" style="background: var(--success); color: white; padding: 8px 12px; border-radius: 8px; font-weight: 600;">
                        <small>✓ Already Used</small>
                    </div>
                    @else
                    @if($redemption->reward->voucher_template_id)
                    <!-- Reward with voucher - automatically issued, show info -->
                    <div style="text-align: center; padding: 8px;">
                        <div style="background: var(--success); color: white; padding: 8px 16px; border-radius: 8px; font-weight: 600; margin-bottom: 4px;">
                            <i class="fas fa-check-circle"></i> Voucher Issued
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-3);">
                            Check "My Vouchers" section
                        </div>
                    </div>
                    @else
                    <!-- Direct reward - apply to cart -->
                    <button class="btn-secondary"
                        onclick="applyRewardToCart(
                            {{ $redemption->id }},
                            '{{ addslashes($redemption->reward->title ?? 'Reward') }}',
                            '{{ $redemption->reward->reward_type ?? 'discount' }}',
                            {{ $redemption->reward->discount_type === 'percentage' ? $redemption->reward->discount_value : 0 }},
                            {{ $redemption->reward->discount_type === 'fixed' ? $redemption->reward->discount_value : 0 }},
                            {{ $redemption->reward->menu_item_id ?? 'null' }}
                        )"
                        style="width: 100%; padding: 10px;">
                        <i class="fas fa-shopping-cart"></i> Apply to Cart
                    </button>
                    @endif
                    @endif
                </div>
            </div>
            @endforeach
            @else
            <div class="empty-state">
                <div class="icon">🎁</div>
                <h3>No redeemed rewards yet</h3>
                <p>Start redeeming your points to see your rewards here!</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Loyalty Levels -->
    <div class="card">
        <h2>👑 Loyalty Status</h2>
        @php
            $currentTier = $user->loyaltyTier ?? null;
            $nextTier = null;
            $progress = 0;
            $amountNeeded = 0;

            if ($currentTier) {
                // Find next tier
                $nextTier = \App\Models\LoyaltyTier::where('order', '>', $currentTier->order)
                    ->where('is_active', true)
                    ->orderBy('order', 'asc')
                    ->first();

                if ($nextTier) {
                    $currentPoints = $user->points_balance ?? 0;
                    $progress = min(100, ($currentPoints / $nextTier->points_threshold) * 100);
                    $amountNeeded = max(0, $nextTier->points_threshold - $currentPoints);
                }
            }
        @endphp

        <style>
            #loyaltyStatus .loyalty-level {
                color: {{ $currentTier->color ?? '#CD7F32' }};
            }
            #loyaltyStatus .max-level-text {
                color: {{ $currentTier->color ?? '#CD7F32' }};
            }
            #loyaltyProgress {
                width: {{ $progress }}%;
            }
        </style>

        <div id="loyaltyStatus">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <div class="loyalty-level">
                    Current Level: {{ $currentTier->name ?? 'Bronze' }} Member {{ $currentTier->icon ?? '🥉' }}
                </div>
                @if($nextTier)
                <div style="color: var(--text-2); font-weight: 700;">Next Level: {{ $nextTier->name }} Member {{ $nextTier->icon ?? '🥈' }}</div>
                @else
                <div class="max-level-text" style="font-weight: 700;">Maximum Level Reached!</div>
                @endif
            </div>

            <div class="progress-container">
                <div class="progress-bar" id="loyaltyProgress"></div>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.9rem; color: var(--text-2); margin-top: 8px; font-weight: 700;">
                <span>{{ number_format($user->points_balance ?? 0) }} points</span>
                @if($nextTier)
                <span>{{ number_format($amountNeeded) }} points needed for {{ $nextTier->name }}</span>
                @else
                <span>Maximum level achieved! 🎉</span>
                @endif
            </div>
        </div>
    </div>
    @endif
</main>

<!-- Exchange Points Modal -->
@if(isset($allRewards) && $allRewards->count() > 0)
<div id="exchangePointsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>💳 All Exchange Points</h2>
            <button class="close-btn" onclick="closeExchangePointsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="reward-list">
                @foreach($allRewards as $reward)
                <div class="reward-item">
                    <div class="reward-info">
                        <div class="reward-name">
                            {{ $reward->title }}
                            @if($reward->requiredTier)
                            <span style="background: #E3F2FD; color: #2196F3; padding: 2px 8px; border-radius: 8px; font-size: 0.75rem; margin-left: 8px;">
                                <i class="fas fa-star"></i> {{ $reward->requiredTier->name }}
                            </span>
                            @endif
                        </div>
                        <div class="reward-cost">
                            {{ $reward->description ?? 'Exclusive reward' }} • {{ $reward->points_required }} points
                            @if($reward->expires_at)
                            <span class="expiry">• Expires: {{ $reward->expires_at->format('M j, Y') }}</span>
                            @endif
                        </div>
                    </div>
                    <button class="btn-secondary redeem-btn"
                        data-reward-id="{{ $reward->id }}"
                        data-points-required="{{ $reward->points_required }}"
                        onclick="redeemReward('{{ $reward->id }}', '{{ $reward->points_required }}')">
                        Redeem
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<!-- All Vouchers Modal -->
@if(isset($userVouchers) && $userVouchers->count() > 0)
<div id="allVouchersModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>🎪 All My Vouchers</h2>
            <button class="close-btn" onclick="closeAllVouchersModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="voucher-grid">
                @foreach($userVouchers as $voucher)
                <div class="voucher-card {{ $voucher->status }}">
                    <div class="voucher-header">
                        <span class="voucher-icon">🎁</span>
                        <span class="voucher-type">{{ $voucher->voucherTemplate->name ?? 'Voucher' }}</span>
                    </div>
                    <div class="voucher-body">
                        <h3>
                            @if($voucher->voucherTemplate->discount_type === 'free_item')
                                FREE ITEM
                            @elseif($voucher->voucherTemplate->discount_type === 'percentage')
                                {{ $voucher->voucherTemplate->discount_value }}% OFF
                            @else
                                RM{{ number_format($voucher->voucherTemplate->discount_value, 2) }} OFF
                            @endif
                        </h3>
                        <p>{{ $voucher->voucherTemplate->description ?? 'Exclusive discount' }}</p>
                        @if($voucher->expiry_date)
                        <div class="voucher-expiry">Expires: {{ $voucher->expiry_date->format('M j, Y') }}</div>
                        @endif
                    </div>
                    <div class="voucher-footer">
                        @if($voucher->voucherTemplate->discount_type === 'free_item')
                        <button class="btn-primary"
                            onclick="applyFreeItemVoucher(
                                {{ $voucher->id }},
                                '{{ addslashes($voucher->voucherTemplate->title ?? 'Free Item') }}',
                                {{ json_encode($voucher->voucherTemplate->applicable_menu_item_ids ?? []) }}
                            )">
                            USE NOW
                        </button>
                        @else
                        <button class="btn-primary" onclick="window.location.href='{{ route('customer.menu.index') }}'">USE NOW</button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
@php
    $rewardsData = [
        'checkinSettings' => $checkinSettings ?? null,
        'isAuthenticated' => auth()->check(),
        'user' => auth()->check() && isset($user) ? $user : null,
        'points' => auth()->check() && isset($user) ? ($user->points_balance ?? 0) : 0,
        'lastCheckinDate' => auth()->check() && isset($user) && $user->last_checkin_date ? $user->last_checkin_date->toDateString() : null,
        'checkinStreak' => auth()->check() && isset($user) ? ($user->checkin_streak ?? 0) : 0,
        'csrfToken' => csrf_token(),
        'redeemRoute' => route('customer.rewards.redeem'),
        'checkinRoute' => route('customer.rewards.checkin'),
        'collectVoucherRoute' => route('customer.rewards.collectVoucher')
    ];
@endphp
<script>
    // Pass all necessary data to JavaScript
    window.rewardsData = @json($rewardsData);

    // Collect Voucher Function
    function collectVoucher(collectionId, collectionName) {
        console.log('🎟️ collectVoucher() called', { collectionId, collectionName });

        if (!window.rewardsData.isAuthenticated) {
            showMessage('Please login to collect vouchers', 'warning');
            return;
        }

        console.log('📡 Sending API request to:', window.rewardsData.collectVoucherRoute);

        fetch(window.rewardsData.collectVoucherRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.rewardsData.csrfToken
            },
            body: JSON.stringify({ voucher_collection_id: collectionId })
        })
        .then(response => {
            console.log('📥 Response received:', response.status, response.statusText);
            return response.json();
        })
        .then(data => {
            console.log('✅ Response data:', data);
            if (data.success) {
                showMessage(data.message, 'success');
                setTimeout(() => window.location.reload(), 2000);
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('❌ Fetch error:', error);
            showMessage('Failed to collect voucher. Please try again.', 'error');
        });
    }

    // Claim Bonus Challenge Function
    function claimBonusChallenge(challengeId, challengeName, bonusPoints) {
        console.log('🎯 claimBonusChallenge() called', { challengeId, challengeName, bonusPoints });

        if (!window.rewardsData.isAuthenticated) {
            showMessage('Please login to claim bonus points', 'warning');
            return;
        }

        console.log('📡 Sending API request to claim bonus challenge');

        fetch('{{ route("customer.rewards.claimBonusChallenge") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.rewardsData.csrfToken
            },
            body: JSON.stringify({ challenge_id: challengeId })
        })
        .then(response => {
            console.log('📥 Response received:', response.status, response.statusText);
            return response.json();
        })
        .then(data => {
            console.log('✅ Response data:', data);
            if (data.success) {
                showMessage(data.message, 'success');
                // Update points display
                if (data.new_balance) {
                    const pointsEl = document.getElementById('points');
                    if (pointsEl) {
                        pointsEl.textContent = new Intl.NumberFormat().format(data.new_balance);
                    }
                }
                // Reload page after 2 seconds
                setTimeout(() => window.location.reload(), 2000);
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('❌ Fetch error:', error);
            showMessage('Failed to claim bonus points. Please try again.', 'error');
        });
    }

    // Show All Exchange Points Modal
    function showAllExchangePoints() {
        document.getElementById('exchangePointsModal').style.display = 'flex';
    }

    function closeExchangePointsModal() {
        document.getElementById('exchangePointsModal').style.display = 'none';
    }

    // Show All Vouchers Modal
    function showAllVouchersModal() {
        document.getElementById('allVouchersModal').style.display = 'flex';
    }

    function closeAllVouchersModal() {
        document.getElementById('allVouchersModal').style.display = 'none';
    }

    // Show Barcode Modal
    window.showRewardBarcode = function(redemptionId, rewardName, redemptionCode) {
        if (typeof JsBarcode === 'undefined') {
            console.error('JsBarcode library not loaded');
            showMessage('Error loading barcode library. Please refresh the page.', 'error');
            return;
        }

        const modalHTML = `
            <div id="barcodeModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 10000; display: flex; align-items: center; justify-content: center;" onclick="if(event.target.id === 'barcodeModal') closeBarcodeModal()">
                <div style="background: white; border-radius: 16px; padding: 32px; max-width: 500px; width: 90%; text-align: center;" onclick="event.stopPropagation()">
                    <h3 style="margin-bottom: 16px; color: #1e293b;">${rewardName}</h3>
                    <p style="color: #64748b; margin-bottom: 24px;">Show this code to staff</p>

                    <div style="background: white; padding: 24px; border-radius: 12px; margin-bottom: 16px;">
                        <svg id="barcode"></svg>
                    </div>

                    <div style="font-size: 1.2rem; font-weight: 600; color: #1e293b; margin-bottom: 24px; letter-spacing: 2px;">
                        ${redemptionCode}
                    </div>

                    <button onclick="closeBarcodeModal()" style="background: #ef4444; color: white; border: none; padding: 12px 32px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                        Close
                    </button>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);

        try {
            JsBarcode("#barcode", redemptionCode, {
                format: "CODE128",
                width: 2,
                height: 100,
                displayValue: false
            });
        } catch (error) {
            console.error('Error generating barcode:', error);
            document.getElementById('barcode').innerHTML = '<p style="color: #1e293b; font-size: 1.5rem; font-weight: 700;">' + redemptionCode + '</p>';
        }
    }

    window.closeBarcodeModal = function() {
        const modal = document.getElementById('barcodeModal');
        if (modal) {
            modal.remove();
        }
    }
</script>
<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script src="{{ asset('js/customer/rewards.js') }}?v={{ time() }}"></script>
@endsection
