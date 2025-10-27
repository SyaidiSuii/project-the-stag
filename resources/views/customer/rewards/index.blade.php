@extends('layouts.customer')

@section('title', 'Rewards - The Stag SmartDine')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/rewards.css') }}">
@endsection

@section('content')
<!-- Main Content -->
<main class="main-content">
    @if(isset($guest) && $guest)
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
        <h1 class="category-title" id="categoryTitle"></h1>
    </div>

    <!-- Top Row: Points & Check-in -->
    <div class="grid-two">
        <!-- Points Display & Check-in -->
        <div class="card points-card">
            <div class="points-display">
                <div class="points-label" id="points-label">
                    @if(Auth::check())
                    Your Points
                    @else
                    Guest Points
                    @endif
                </div>
                <div class="points-number" id="points">
                    @if(Auth::check())
                    {{ number_format($user->points_balance ?? 0) }}
                    @else
                    0
                    @endif
                </div>
                <div class="points-label">Keep collecting! 🌟</div>
            </div>

            <h3 id="checkin-header">
                @if(isset($checkinSettings))
                📅 Daily Check-In Streak
                @else
                📅 Daily Check-In Streak
                @endif
            </h3>
            <p id="checkin-desc" style="color: rgba(255,255,255,0.9); font-weight: 600;">
                @if(isset($checkinSettings))
                Check in daily to earn bonus points!
                @else
                Check in daily to earn bonus points!
                @endif
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
                @foreach($availableRewards as $reward)
                <div class="reward-item {{ $reward->is_limit_reached ? 'reward-disabled' : '' }}">
                    <div class="reward-info">
                        <div class="reward-name">{{ $reward->name }}</div>
                        <div class="reward-cost">
                            {{ $reward->description ?? 'Exclusive reward' }} • {{ $reward->points_required }} points
                            @if($reward->expires_at)
                            <span class="expiry">• Expires: {{ $reward->expires_at->format('M j, Y') }}</span>
                            @endif
                            @if($reward->is_limit_reached)
                            <div class="limit-reached">✋ Usage limit reached ({{ $reward->user_redemptions_count }}/{{ $reward->usage_limit ?? 1 }})</div>
                            @endif
                            @if($reward->terms_conditions)
                            <div class="reward-terms">{{ $reward->terms_conditions }}</div>
                            @endif
                        </div>
                    </div>
                    @if($reward->is_limit_reached)
                    <button class="btn-disabled" disabled>
                        Limit Reached
                    </button>
                    @else
                    <button class="btn-secondary redeem-btn"
                        data-reward-id="{{ $reward->id }}"
                        data-points-required="{{ $reward->points_required }}"
                        onclick="redeemReward('{{ $reward->id }}', '{{ $reward->points_required }}')">
                        Redeem
                    </button>
                    @endif
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
    <div class="card special-events">
        <h2 style="color: #92400e;">🎪 Special Events</h2>
        <div class="event-list" id="event-list">
            @if(isset($specialEvents) && $specialEvents->count() > 0)
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
            @else
                <div class="empty-state" style="padding: 2rem; text-align: center; color: var(--text-2);">
                    <div class="icon" style="font-size: 3rem; margin-bottom: 1rem;">🎪</div>
                    <p>No special events at the moment. Check back soon!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Spending Rewards -->
    <div class="card">
        <h2>💰 Spend & Earn More</h2>
        <div class="grid-two">
            <div>
                <h3>🎟️ Collect Vouchers</h3>
                <p>Spend more to unlock exclusive vouchers!</p>
                <div class="reward-list" id="voucherList">
                    @if(isset($voucherCollections) && $voucherCollections->count() > 0)
                        @foreach($voucherCollections as $voucher)
                        <div class="reward-item">
                            <div class="reward-info">
                                <div class="reward-name">
                                    @if($voucher->voucher_type === 'percentage')
                                        🎫 {{ $voucher->voucher_value }}% OFF
                                    @else
                                        💵 RM{{ number_format($voucher->voucher_value, 2) }} OFF
                                    @endif
                                </div>
                                <div class="reward-requirement">Spend RM{{ number_format($voucher->spending_requirement, 2) }} or more</div>
                            </div>
                            <button class="btn-secondary" onclick="collectVoucher({{ $voucher->id }}, '{{ $voucher->name }}')">Collect</button>
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
                        @foreach($bonusChallenges as $challenge)
                        <div class="reward-item">
                            <div class="reward-info">
                                <div class="reward-name">{{ $challenge->name }}</div>
                                <div class="reward-requirement">{{ $challenge->description ?? $challenge->condition }}</div>
                                @if($challenge->end_date)
                                <div style="font-size: 0.8rem; color: var(--text-2); margin-top: 4px;">
                                    Ends: {{ $challenge->end_date->format('M j, Y') }}
                                </div>
                                @endif
                            </div>
                            <button class="btn-secondary" onclick="showMessage('{{ $challenge->description ?? "Complete this challenge to earn bonus points!" }} 🎯')">+{{ $challenge->bonus_points }} pts</button>
                        </div>
                        @endforeach
                    @else
                        <!-- Default/fallback challenges -->
                        <div class="reward-item">
                            <div class="reward-info">
                                <div class="reward-name">First Order Bonus</div>
                                <div class="reward-requirement">Place your first order</div>
                            </div>
                            <button class="btn-secondary" onclick="showMessage('Complete your first order to earn bonus points! 🎯')">+50 pts</button>
                        </div>
                        <div class="reward-item">
                            <div class="reward-info">
                                <div class="reward-name">Review & Rate</div>
                                <div class="reward-requirement">Leave a 5-star review</div>
                            </div>
                            <button class="btn-secondary" onclick="showMessage('Leave a review after your meal to earn points! ⭐')">+25 pts</button>
                        </div>
                        <div class="reward-item">
                            <div class="reward-info">
                                <div class="reward-name">Social Share</div>
                                <div class="reward-requirement">Share us on social media</div>
                            </div>
                            <button class="btn-secondary" onclick="showMessage('Share your experience on social media! 📱')">+15 pts</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Achievements -->
    <div class="card">
        <h2>🏆 Achievements</h2>
        <div class="achievement-grid" id="achievementGrid">
            @if(isset($achievements) && $achievements->count() > 0)
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
            @else
                <div class="empty-state" style="padding: 2rem; text-align: center; color: var(--text-2);">
                    <div class="icon" style="font-size: 3rem; margin-bottom: 1rem;">🏆</div>
                    <p>No achievements available yet. Keep ordering to unlock achievements!</p>
                </div>
            @endif
        </div>
    </div>

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
                        <h3>{{ $voucher->voucherTemplate->title ?? 'Special Voucher' }}</h3>
                        <p>{{ $voucher->voucherTemplate->description ?? 'Exclusive discount' }}</p>
                        @if($voucher->expiry_date)
                        <div class="voucher-expiry">Expires: {{ $voucher->expiry_date->format('M j, Y') }}</div>
                        @endif
                    </div>
                    <div class="voucher-footer">
                        <button class="btn-primary" onclick="window.location.href='{{ route('customer.menu.index') }}'">USE NOW</button>
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
        @else
        <div id="seeAllVouchersBtn" style="text-align: center; margin-top: 16px; display: none;"></div>
        @endif
    </div>

    <!-- My Rewards -->
    <div class="card">
        <h2>🎁 My Rewards</h2>
        <p style="color: var(--text-2); margin-bottom: 1.5rem;">Your redeemed rewards - pending rewards can be shown to staff for claiming</p>
        <div id="redeemedRewardsList">
            @if(isset($redeemedRewards) && $redeemedRewards->count() > 0)
            @foreach($redeemedRewards as $redemption)
            <div class="redeemed-reward-item status-{{ $redemption->status ?? 'pending' }}">
                <div class="reward-info">
                    <div class="reward-details">
                        <h4>
                            {{ $redemption->exchangePoint->name }}
                            <span class="status-badge {{ $redemption->status ?? 'pending' }}">
                                @if(($redemption->status ?? 'pending') === 'redeemed')
                                ✓ Claimed
                                @else
                                ⏳ Pending
                                @endif
                            </span>
                        </h4>
                        <p>{{ $redemption->exchangePoint->description }}</p>
                        <div class="reward-meta">
                            <span class="points-spent">{{ number_format($redemption->points_spent) }} points</span>
                            <span class="redemption-date">{{ $redemption->created_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="reward-actions">
                    @if(($redemption->status ?? 'pending') === 'redeemed')
                    <div class="staff-note" style="background: var(--success); color: white; padding: 8px 12px; border-radius: 8px; font-weight: 600;">
                        <small>✓ Already Claimed</small>
                    </div>
                    @else
                    <div class="qr-code" onclick="showRewardQR('{{ $redemption->id }}', '{{ $redemption->exchangePoint->name }}', '{{ $redemption->redemption_code }}')">
                        📱 Show QR
                    </div>
                    @if($redemption->exchangePoint->redemption_method === 'show_to_staff')
                    <div class="staff-note">
                        <small>Show this to staff</small>
                    </div>
                    @elseif($redemption->exchangePoint->redemption_method === 'qr_code_scan')
                    <div class="staff-note">
                        <small>Staff will scan QR code</small>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
            @endforeach
            @if($hasMoreRedeemed)
            <div style="text-align: center; margin-top: 1.5rem;">
                <button class="btn-secondary" onclick="openMyRewardsModal()"
                    style="background: var(--muted); color: var(--text); border: 1px solid var(--border); padding: 12px 24px; border-radius: 25px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                    🔍 See All My Rewards ({{ $allRedeemedRewards->count() }})
                </button>
            </div>
            @endif
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
        <style>
            #loyaltyStatus .loyalty-level {
                color: {{ $userTierInfo['current']->color ?? '#CD7F32' }};
            }
            #loyaltyStatus .max-level-text {
                color: {{ $userTierInfo['current']->color ?? '#CD7F32' }};
            }
            #loyaltyProgress {
                width: {{ $userTierInfo['progress'] }}%;
            }
        </style>
        <div id="loyaltyStatus">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <div class="loyalty-level">
                    Current Level: {{ $userTierInfo['current']->name ?? 'Bronze' }} Member {{ $userTierInfo['current']->icon ?? '🥉' }}
                </div>
                @if($userTierInfo['next'])
                <div style="color: var(--text-2); font-weight: 700;">Next Level: {{ $userTierInfo['next']->name }} Member {{ $userTierInfo['next']->icon }}</div>
                @else
                <div class="max-level-text" style="font-weight: 700;">Maximum Level Reached!</div>
                @endif
            </div>

            <div class="progress-container">
                <div class="progress-bar" id="loyaltyProgress"></div>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.9rem; color: var(--text-2); margin-top: 8px; font-weight: 700;">
                <span>RM {{ number_format($userTierInfo['spending'], 2) }} spent</span>
                @if($userTierInfo['next'])
                <span>RM {{ number_format($userTierInfo['amount_needed'], 2) }} needed for {{ $userTierInfo['next']->name }}</span>
                @else
                <span>Maximum level achieved! 🎉</span>
                @endif
            </div>
        </div>
    </div>
    @endif
</main>

<!-- Exchange Points Modal -->
@if(isset($allRewards))
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
                <div class="reward-item {{ $reward->is_limit_reached ? 'reward-disabled' : '' }}">
                    <div class="reward-info">
                        <div class="reward-name">{{ $reward->name }}</div>
                        <div class="reward-cost">
                            {{ $reward->description ?? 'Exclusive reward' }} • {{ $reward->points_required }} points
                            @if($reward->expires_at)
                            <span class="expiry">• Expires: {{ $reward->expires_at->format('M j, Y') }}</span>
                            @endif
                            @if($reward->is_limit_reached)
                            <div class="limit-reached">✋ Usage limit reached ({{ $reward->user_redemptions_count }}/{{ $reward->usage_limit ?? 1 }})</div>
                            @endif
                            @if($reward->terms_conditions)
                            <div class="reward-terms">{{ $reward->terms_conditions }}</div>
                            @endif
                        </div>
                    </div>
                    @if($reward->is_limit_reached)
                    <button class="btn-disabled" disabled>
                        Limit Reached
                    </button>
                    @else
                    <button class="btn-secondary redeem-btn"
                        data-reward-id="{{ $reward->id }}"
                        data-points-required="{{ $reward->points_required }}"
                        onclick="redeemReward('{{ $reward->id }}', '{{ $reward->points_required }}')">
                        Redeem
                    </button>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<!-- My Rewards Modal -->
@if(isset($allRedeemedRewards) && $allRedeemedRewards->count() > 0)
<div id="myRewardsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>🎁 All My Rewards</h2>
            <button class="close-btn" onclick="closeMyRewardsModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="reward-list">
                @foreach($allRedeemedRewards as $redemption)
                <div class="redeemed-reward-item status-{{ $redemption->status ?? 'pending' }}">
                    <div class="reward-info">
                        <div class="reward-details">
                            <h4>
                                {{ $redemption->exchangePoint->name }}
                                <span class="status-badge {{ $redemption->status ?? 'pending' }}">
                                    @if(($redemption->status ?? 'pending') === 'redeemed')
                                    ✓ Claimed
                                    @else
                                    ⏳ Pending
                                    @endif
                                </span>
                            </h4>
                            <p>{{ $redemption->exchangePoint->description }}</p>
                            <div class="reward-meta">
                                <span class="points-spent">{{ number_format($redemption->points_spent) }} points</span>
                                <span class="redemption-date">{{ $redemption->created_at->format('M j, Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="reward-actions">
                        @if(($redemption->status ?? 'pending') === 'redeemed')
                        <div class="staff-note" style="background: var(--success); color: white; padding: 8px 12px; border-radius: 8px; font-weight: 600;">
                            <small>✓ Already Claimed</small>
                        </div>
                        @else
                        <div class="qr-code" onclick="showRewardQR('{{ $redemption->id }}', '{{ $redemption->exchangePoint->name }}', '{{ $redemption->redemption_code }}')">
                            📱 Show QR
                        </div>
                        @if($redemption->exchangePoint->redemption_method === 'show_to_staff')
                        <div class="staff-note">
                            <small>Show this to staff</small>
                        </div>
                        @elseif($redemption->exchangePoint->redemption_method === 'qr_code_scan')
                        <div class="staff-note">
                            <small>Staff will scan QR code</small>
                        </div>
                        @endif
                        @endif
                    </div>
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
                        <h3>{{ $voucher->voucherTemplate->title ?? 'Special Voucher' }}</h3>
                        <p>{{ $voucher->voucherTemplate->description ?? 'Exclusive discount' }}</p>
                        @if($voucher->expiry_date)
                        <div class="voucher-expiry">Expires: {{ $voucher->expiry_date->format('M j, Y') }}</div>
                        @endif
                    </div>
                    <div class="voucher-footer">
                        <button class="btn-primary" onclick="window.location.href='{{ route('customer.menu.index') }}'">USE NOW</button>
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
    function collectVoucher(voucherId, voucherName) {
        if (!window.rewardsData.isAuthenticated) {
            showMessage('Please login to collect vouchers', 'warning');
            return;
        }

        fetch(window.rewardsData.collectVoucherRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.rewardsData.csrfToken
            },
            body: JSON.stringify({ voucher_collection_id: voucherId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                // Reload page after 2 seconds to show new voucher
                setTimeout(() => window.location.reload(), 2000);
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Failed to collect voucher. Please try again.', 'error');
        });
    }
</script>
<script src="{{ asset('js/customer/rewards.js') }}"></script>
@endsection