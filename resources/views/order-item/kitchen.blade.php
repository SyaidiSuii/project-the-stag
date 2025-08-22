<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kitchen Display - Order Items') }} - {{ now()->format('M d, Y h:i A') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Kitchen Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                @php
                    $pendingCount = $orderItems->get('pending', collect())->count();
                    $preparingCount = $orderItems->get('preparing', collect())->count();
                    $totalActiveItems = $pendingCount + $preparingCount;
                    $avgPrepTime = 15; // Default prep time in minutes
                @endphp

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ $totalActiveItems }}</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Items</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalActiveItems }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ $pendingCount }}</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $pendingCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ $preparingCount }}</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Preparing</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $preparingCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">~{{ $avgPrepTime }}</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Avg Prep (min)</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $avgPrepTime }} min</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-900">Kitchen Queue</h3>
                <div class="flex gap-2">
                    <button onclick="refreshKitchen()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        🔄 Refresh
                    </button>
                    <a href="{{ route('order-item.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        All Items
                    </a>
                    <button onclick="toggleAutoRefresh()" id="autoRefreshBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Auto-Refresh: ON
                    </button>
                </div>
            </div>

            <!-- Kitchen Display -->
            <div class="space-y-6">
                
                <!-- Pending Items (High Priority) -->
                @if($orderItems->has('pending') && $orderItems->get('pending')->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-yellow-500">
                    <div class="p-4 bg-yellow-50 border-b flex justify-between items-center">
                        <h4 class="font-semibold text-gray-800">
                            🕐 PENDING ITEMS 
                            <span class="text-sm font-normal text-gray-600">({{ $orderItems->get('pending')->count() }} items)</span>
                        </h4>
                        <div class="flex space-x-2">
                            <button onclick="bulkUpdateStatus('pending', 'preparing')" 
                                    class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
                                Start All
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($orderItems->get('pending')->sortBy('created_at') as $item)
                            <div class="border-2 border-yellow-400 rounded-lg p-4 bg-yellow-50 hover:shadow-md transition-shadow">
                                
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h5 class="font-bold text-lg text-yellow-800">
                                            {{ $item->menuItem->name ?? 'Unknown Item' }}
                                        </h5>
                                        <p class="text-sm text-gray-600">Order #{{ $item->order_id }}</p>
                                        @if($item->order->user)
                                            <p class="text-sm text-gray-600">{{ $item->order->user->name }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-yellow-800">{{ $item->quantity }}x</span>
                                        @if($item->order->is_rush_order ?? false)
                                            <div class="mt-1">
                                                <span class="text-xs font-bold bg-red-100 text-red-800 px-2 py-1 rounded animate-pulse">🚨 RUSH</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="space-y-2 text-sm text-gray-600 mb-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $item->created_at->format('h:i A') }}
                                        </div>
                                        @if($item->order->table || $item->order->table_number)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            @if($item->order->table)
                                                Table {{ $item->order->table->table_number }}
                                            @else
                                                {{ $item->order->table_number }}
                                            @endif
                                        </div>
                                        @endif
                                    </div>

                                    @php
                                        $waitTime = now()->diffInMinutes($item->created_at);
                                    @endphp
                                    <div class="flex items-center {{ $waitTime > 10 ? 'text-red-600 font-bold' : '' }}">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Waiting: {{ $waitTime }} min
                                        @if($waitTime > 15)
                                            <span class="ml-2 text-xs bg-red-100 px-1 rounded animate-pulse">URGENT!</span>
                                        @endif
                                    </div>
                                </div>

                                @if($item->special_note)
                                <div class="mb-3">
                                    <div class="text-xs text-orange-700 bg-orange-50 p-2 rounded border border-orange-200">
                                        <strong>⚠️ Special Note:</strong>
                                        <div>{{ $item->special_note }}</div>
                                    </div>
                                </div>
                                @endif

                                <div class="flex justify-between items-center">
                                    <button onclick="updateItemStatus({{ $item->id }}, 'preparing')" 
                                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-semibold">
                                        🔥 START COOKING
                                    </button>
                                    <div class="text-right text-xs text-gray-500">
                                        <div>Item #{{ $item->id }}</div>
                                        @if($item->order->confirmation_code)
                                            <div class="font-mono">{{ $item->order->confirmation_code }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Preparing Items (In Progress) -->
                @if($orderItems->has('preparing') && $orderItems->get('preparing')->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500">
                    <div class="p-4 bg-blue-50 border-b flex justify-between items-center">
                        <h4 class="font-semibold text-gray-800">
                            🔥 PREPARING NOW 
                            <span class="text-sm font-normal text-gray-600">({{ $orderItems->get('preparing')->count() }} items)</span>
                        </h4>
                        <div class="flex space-x-2">
                            <button onclick="bulkUpdateStatus('preparing', 'ready')" 
                                    class="px-3 py-1 bg-purple-500 text-white text-sm rounded hover:bg-purple-600">
                                Mark All Ready
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($orderItems->get('preparing')->sortBy('updated_at') as $item)
                            <div class="border-2 border-blue-400 rounded-lg p-4 bg-blue-50 hover:shadow-md transition-shadow">
                                
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h5 class="font-bold text-lg text-blue-800">
                                            {{ $item->menuItem->name ?? 'Unknown Item' }}
                                        </h5>
                                        <p class="text-sm text-gray-600">Order #{{ $item->order_id }}</p>
                                        @if($item->order->user)
                                            <p class="text-sm text-gray-600">{{ $item->order->user->name }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-blue-800">{{ $item->quantity }}x</span>
                                        @if($item->order->is_rush_order ?? false)
                                            <div class="mt-1">
                                                <span class="text-xs font-bold bg-red-100 text-red-800 px-2 py-1 rounded animate-pulse">🚨 RUSH</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="space-y-2 text-sm text-gray-600 mb-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Started: {{ $item->updated_at->format('h:i A') }}
                                        </div>
                                        @if($item->order->table || $item->order->table_number)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            @if($item->order->table)
                                                Table {{ $item->order->table->table_number }}
                                            @else
                                                {{ $item->order->table_number }}
                                            @endif
                                        </div>
                                        @endif
                                    </div>

                                    @php
                                        $prepTime = now()->diffInMinutes($item->updated_at);
                                        $totalTime = now()->diffInMinutes($item->created_at);
                                    @endphp
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Cooking: {{ $prepTime }} min | Total: {{ $totalTime }} min
                                    </div>
                                </div>

                                @if($item->special_note)
                                <div class="mb-3">
                                    <div class="text-xs text-orange-700 bg-orange-50 p-2 rounded border border-orange-200">
                                        <strong>⚠️ Special Note:</strong>
                                        <div>{{ $item->special_note }}</div>
                                    </div>
                                </div>
                                @endif

                                <div class="flex justify-between items-center">
                                    <button onclick="updateItemStatus({{ $item->id }}, 'ready')" 
                                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 font-semibold">
                                        ✅ READY
                                    </button>
                                    <div class="text-right text-xs text-gray-500">
                                        <div>Item #{{ $item->id }}</div>
                                        @if($item->order->confirmation_code)
                                            <div class="font-mono">{{ $item->order->confirmation_code }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Empty Kitchen State -->
                @if($totalActiveItems == 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <div class="text-6xl mb-4">👨‍🍳</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Kitchen All Clear!</h3>
                        <p class="text-gray-600 mb-4">No pending or preparing items at the moment.</p>
                        <div class="text-sm text-gray-500">
                            <p>Last refreshed: {{ now()->format('h:i:s A') }}</p>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    <!-- Audio Alert -->
    <audio id="newOrderAlert" preload="auto">
        <source src="/sounds/kitchen-bell.mp3" type="audio/mpeg">
        <source src="/sounds/kitchen-bell.wav" type="audio/wav">
    </audio>

    <script>
        let autoRefreshEnabled = true;
        let refreshInterval;
        let lastItemCount = {{ $totalActiveItems }};

        function updateItemStatus(itemId, status) {
            if (!confirm(`Mark this item as '${status}'?`)) {
                return;
            }

            fetch(`/order-item/${itemId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    item_status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add visual feedback before refresh
                    showStatusUpdate(status);
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    alert('Error: ' + (data.message || 'Failed to update status'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating item status');
            });
        }

        function bulkUpdateStatus(currentStatus, newStatus) {
            const confirmMessage = `Mark all ${currentStatus} items as '${newStatus}'?`;
            if (!confirm(confirmMessage)) {
                return;
            }

            // Get all items with current status
            const itemElements = document.querySelectorAll(`[data-status="${currentStatus}"]`);
            const itemIds = Array.from(itemElements).map(el => el.dataset.itemId).filter(id => id);

            if (itemIds.length === 0) {
                alert('No items found to update');
                return;
            }

            fetch('/order-item/bulk-update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    item_ids: itemIds,
                    item_status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatusUpdate(newStatus, itemIds.length);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert('Error: ' + (data.message || 'Failed to update status'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating items');
            });
        }

        function showStatusUpdate(status, count = 1) {
            const message = count > 1 ? `${count} items marked as ${status}` : `Item marked as ${status}`;
            
            // Create temporary notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            notification.textContent = `✅ ${message}`;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        function refreshKitchen() {
            window.location.reload();
        }

        function toggleAutoRefresh() {
            autoRefreshEnabled = !autoRefreshEnabled;
            const btn = document.getElementById('autoRefreshBtn');
            
            if (autoRefreshEnabled) {
                btn.textContent = 'Auto-Refresh: ON';
                btn.className = 'px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700';
                startAutoRefresh();
            } else {
                btn.textContent = 'Auto-Refresh: OFF';
                btn.className = 'px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700';
                stopAutoRefresh();
            }
        }

        function startAutoRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
            
            refreshInterval = setInterval(() => {
                if (autoRefreshEnabled) {
                    // Check for new items before refresh
                    fetch('/order-item/get-by-status?status=pending')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.orderItems.length > lastItemCount) {
                                // Play alert sound for new items
                                playNewOrderAlert();
                            }
                            lastItemCount = data.orderItems.length;
                            window.location.reload();
                        })
                        .catch(() => {
                            // Fallback to regular refresh
                            window.location.reload();
                        });
                }
            }, 30000); // 30 seconds
        }

        function stopAutoRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
        }

        function playNewOrderAlert() {
            const audio = document.getElementById('newOrderAlert');
            if (audio) {
                audio.play().catch(e => console.log('Audio play failed:', e));
            }
        }

        // Initialize auto-refresh
        document.addEventListener('DOMContentLoaded', function() {
            startAutoRefresh();
            
            // Add data attributes for bulk operations
            document.querySelectorAll('[data-item-status]').forEach(item => {
                const status = item.getAttribute('data-item-status');
                item.setAttribute('data-status', status);
            });
        });

        // Handle page visibility change
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopAutoRefresh();
            } else if (autoRefreshEnabled) {
                startAutoRefresh();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch(e.key) {
                    case 'r':
                        e.preventDefault();
                        refreshKitchen();
                        break;
                    case ' ':
                        e.preventDefault();
                        toggleAutoRefresh();
                        break;
                }
            }
        });

        // Visual timer updates
        function updateTimers() {
            document.querySelectorAll('[data-created-at]').forEach(element => {
                const createdAt = new Date(element.dataset.createdAt);
                const now = new Date();
                const minutes = Math.floor((now - createdAt) / 60000);
                
                const timerElement = element.querySelector('.timer');
                if (timerElement) {
                    timerElement.textContent = `${minutes} min`;
                    
                    // Add visual urgency
                    if (minutes > 15) {
                        timerElement.className = 'timer text-red-600 font-bold animate-pulse';
                    } else if (minutes > 10) {
                        timerElement.className = 'timer text-orange-600 font-semibold';
                    }
                }
            });
        }

        // Update timers every minute
        setInterval(updateTimers, 60000);
        updateTimers(); // Initial call
    </script>

    <style>
        /* Custom animations for kitchen urgency */
        @keyframes urgent-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .animate-urgent {
            animation: urgent-pulse 1s ease-in-out infinite;
        }
        
        /* Print styles for kitchen tickets */
        @media print {
            .no-print { display: none !important; }
            .kitchen-item { 
                break-inside: avoid; 
                border: 2px solid #000; 
                margin-bottom: 10px; 
                padding: 10px; 
            }
        }
    </style>
</x-app-layout>