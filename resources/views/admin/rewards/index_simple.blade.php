@extends('layouts.admin')

@section('title', 'Rewards Management')
@section('page-title', 'Rewards Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/rewards.css') }}">
@endsection

@section('content')

<!-- Stats Cards -->
<div class="admin-cards">
  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">Active Rewards</div>
      <div class="admin-card-icon icon-blue"><i class="fas fa-gift"></i></div>
    </div>
    <div class="admin-card-value">{{ $rewards->where('is_active', true)->count() }}</div>
    <div class="admin-card-desc">Total configurable rewards</div>
  </div>

  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">Rewards Redeemed</div>
      <div class="admin-card-icon icon-green"><i class="fas fa-ticket-alt"></i></div>
    </div>
    <div class="admin-card-value">{{ $redemptions->count() }}</div>
    <div class="admin-card-desc">Across all rewards</div>
  </div>

  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">Total Members</div>
      <div class="admin-card-icon icon-orange"><i class="fas fa-users"></i></div>
    </div>
    <div class="admin-card-value">{{ $members->count() }}</div>
    <div class="admin-card-desc">Registered members</div>
  </div>

  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">Active Special Events</div>
      <div class="admin-card-icon icon-red"><i class="fas fa-calendar-check"></i></div>
    </div>
    <div class="admin-card-value">{{ $specialEvents->where('is_active', true)->count() }}</div>
    <div class="admin-card-desc">Currently running</div>
  </div>
</div>

<!-- Navigation Tabs -->
<div class="admin-tabs">
  <a href="{{ route('admin.rewards.rewards.index') }}" class="admin-tab">Rewards</a>
  <a href="{{ route('admin.rewards.voucher-templates.index') }}" class="admin-tab">Voucher Templates</a>
  <a href="#" class="admin-tab">Check-in Settings</a>
  <a href="{{ route('admin.rewards.special-events.index') }}" class="admin-tab">Special Events</a>
  <a href="{{ route('admin.rewards.loyalty-tiers.index') }}" class="admin-tab">Tiers & Levels</a>
  <a href="#" class="admin-tab">Redemptions</a>
  <a href="#" class="admin-tab">Members</a>
  <a href="{{ route('admin.rewards.achievements.index') }}" class="admin-tab">Achievements</a>
  <a href="{{ route('admin.rewards.voucher-collections.index') }}" class="admin-tab">Voucher Collection</a>
  <a href="{{ route('admin.rewards.bonus-challenges.index') }}" class="admin-tab">Bonus Points</a>
</div>

<!-- Quick Access Section -->
<div class="admin-section" style="margin-top: 30px;">
  <div class="section-header">
    <h2 class="section-title">Quick Access</h2>
  </div>
  <div class="section-content">
    <p style="color: var(--text-2); margin-bottom: 20px;">Select a section from the tabs above to manage your rewards system.</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
      <a href="{{ route('admin.rewards.rewards.index') }}" class="admin-card" style="text-decoration: none; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
        <div class="admin-card-header">
          <div class="admin-card-icon icon-blue" style="font-size: 32px;"><i class="fas fa-gift"></i></div>
        </div>
        <div class="admin-card-title" style="font-size: 18px; margin-top: 12px;">Manage Rewards</div>
        <div class="admin-card-desc">Create and manage reward catalog</div>
      </a>

      <a href="{{ route('admin.rewards.loyalty-tiers.index') }}" class="admin-card" style="text-decoration: none; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
        <div class="admin-card-header">
          <div class="admin-card-icon icon-orange" style="font-size: 32px;"><i class="fas fa-layer-group"></i></div>
        </div>
        <div class="admin-card-title" style="font-size: 18px; margin-top: 12px;">Loyalty Tiers</div>
        <div class="admin-card-desc">Configure membership tiers</div>
      </a>

      <a href="{{ route('admin.rewards.voucher-templates.index') }}" class="admin-card" style="text-decoration: none; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
        <div class="admin-card-header">
          <div class="admin-card-icon icon-green" style="font-size: 32px;"><i class="fas fa-ticket-alt"></i></div>
        </div>
        <div class="admin-card-title" style="font-size: 18px; margin-top: 12px;">Voucher Templates</div>
        <div class="admin-card-desc">Create voucher templates</div>
      </a>

      <a href="{{ route('admin.rewards.achievements.index') }}" class="admin-card" style="text-decoration: none; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
        <div class="admin-card-header">
          <div class="admin-card-icon icon-red" style="font-size: 32px;"><i class="fas fa-trophy"></i></div>
        </div>
        <div class="admin-card-title" style="font-size: 18px; margin-top: 12px;">Achievements</div>
        <div class="admin-card-desc">Manage achievements</div>
      </a>
    </div>
  </div>
</div>

@endsection
