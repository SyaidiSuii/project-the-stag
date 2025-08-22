<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Order ETA Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Action Buttons and Stats -->
            <div class="pb-3 flex justify-between items-center">
                <a href="{{ route('order-etas.create') }}" class="items-center px-4 py-2 bg-gray-800 rounded font-semibold text-white hover:bg-gray-700">
                    Add New ETA
                </a>
                <div class="flex space-x-2">
                    <button onclick="loadStatistics()" class="px-4 py-2 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700">
                        View Statistics
                    </button>
                    <button onclick="loadDelayedOrders()" class="px-3 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600">
                        Delayed Orders
                    </button>
                    <button onclick="loadNeedingAttention()" class="px-3 py-1 bg-yellow-500 text-white rounded text-sm hover:bg-yellow-600">
                        Need Attention
                    </button>
                </div>
            </div>

            <!-- Quick Stats Cards -->
            <div id="stats-section" class="hidden grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Stats will be loaded here via AJAX -->
            </div>

            <!-- Alerts for Delayed Orders -->
            <div id="alerts-section" class="mb-4">
                <!-- Alerts will be loaded here -->
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4 bg-gray-50">
                    <form method="GET" action="{{ route('order-etas.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="Order ID, customer...">
                        </div>

                        <div>
                            <label for="is_delayed" class="block text-sm font-medium text-gray-700">Delay Status</label>
                            <select name="is_delayed" id="is_delayed" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Orders</option>
                                <option value="1" @if(request('is_delayed') == '1') selected @endif>Delayed Only</option>
                                <option value="0" @if(request('is_delayed') == '0') selected @endif>On Time Only</option>
                            </select>
                        </div>

                        <div>
                            <label for="customer_notified" class="block text-sm font-medium text-gray-700">Notification Status</label>
                            <select name="customer_notified" id="customer_notified" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All</option>
                                <option value="1" @if(request('customer_notified') == '1') selected @endif>Notified</option>
                                <option value="0" @if(request('customer_notified') == '0') selected @endif>Not Notified</option>
                            </select>
                        </div>

                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="date" id="date" value="{{ request('date') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full px-4 py-2 bg-indigo-600 !text-white font-semibold 
                                    rounded-md hover:bg-indigo-700 
                                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Filter
                            </button>
                        </div>
                    </form>

                    @if(request()->hasAny(['search', 'is_delayed', 'customer_notified', 'date']))
                        <div class="mt-3">
                            <a href="{{ route('order-etas.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                Clear all filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Order ETAs Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('message'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">#</th>
                                    <th class="text-left py-2">Order Details</th>
                                    <th class="text-left py-2">Customer</th>
                                    <th class="text-left py-2">Estimates (min)</th>
                                    <th class="text-left py-2">Delay Info</th>
                                    <th class="text-left py-2">Status</th>
                                    <th class="text-left py-2">Last Updated</th>
                                    <th class="text-left py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orderEtas as $eta)
                                <tr class="border-b hover:bg-gray-50 {{ $eta->is_delayed ? 'bg-red-50' : '' }}">
                                    <td class="px-6 py-4">{{ ($orderEtas->currentPage() - 1) * $orderEtas->perPage() + $loop->iteration }}</td>
                                    
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="font-medium">#{{ $eta->order->id }}</div>
                                            @if($eta->order->confirmation_code)
                                                <div class="text-sm font-mono text-gray-600">{{ $eta->order->confirmation_code }}</div>
                                            @endif
                                            <div class="text-sm text-gray-600">
                                                {{ $eta->order->order_time->format('M d, h:i A') }}
                                            </div>
                                            @if($eta->order->is_rush_order)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                    RUSH
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ $eta->order->user->name ?? 'Unknown' }}</div>
                                        @if($eta->order->table)
                                            <div class="text-sm text-gray-600">Table {{ $eta->order->table->table_number }}</div>
                                        @elseif($eta->order->table_number)
                                            <div class="text-sm text-gray-600">{{ $eta->order->table_number }}</div>
                                        @endif
                                        <div class="text-sm text-gray-600">RM {{ number_format($eta->order->total_amount, 2) }}</div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            <div>
                                                <span class="text-xs text-gray-500">Initial:</span>
                                                <span class="font-medium">{{ $eta->initial_estimate }} min</span>
                                            </div>
                                            <div>
                                                <span class="text-xs text-gray-500">Current:</span>
                                                <span class="font-medium {{ $eta->current_estimate > $eta->initial_estimate ? 'text-red-600' : 'text-green-600' }}">
                                                    {{ $eta->current_estimate }} min
                                                </span>
                                            </div>
                                            @if($eta->actual_completion_time)
                                                <div>
                                                    <span class="text-xs text-gray-500">Actual:</span>
                                                    <span class="font-medium text-blue-600">{{ $eta->actual_completion_time }} min</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($eta->is_delayed)
                                            <div class="space-y-1">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Delayed {{ $eta->delay_duration }} min
                                                </span>
                                                @if($eta->delay_reason)
                                                    <div class="text-xs text-gray-600">{{ $eta->delay_reason }}</div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                On Time
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            <span class="px-2 py-1 text-xs rounded capitalize
                                                @if($eta->order->order_status == 'confirmed') bg-green-100 text-green-800
                                                @elseif($eta->order->order_status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($eta->order->order_status == 'preparing') bg-blue-100 text-blue-800
                                                @elseif($eta->order->order_status == 'ready') bg-purple-100 text-purple-800
                                                @elseif($eta->order->order_status == 'served') bg-indigo-100 text-indigo-800
                                                @elseif($eta->order->order_status == 'completed') bg-green-100 text-green-800
                                                @elseif($eta->order->order_status == 'cancelled') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ str_replace('_', ' ', $eta->order->order_status) }}
                                            </span>
                                            
                                            @if($eta->is_delayed)
                                                <div>
                                                    @if($eta->customer_notified)
                                                        <span class="text-xs text-green-600">✓ Notified</span>
                                                    @else
                                                        <span class="text-xs text-red-600">⚠ Not Notified</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="text-sm">{{ $eta->last_updated->format('M d, Y') }}</div>
                                        <div class="text-sm text-gray-600">{{ $eta->last_updated->format('h:i A') }}</div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex flex-col space-y-1">
                                            <div class="flex space-x-1">
                                                <a href="{{ route('order-etas.show', $eta->id) }}" 
                                                   class="inline-flex items-center px-2 py-1 bg-blue-600 hover:bg-blue-700 
                                                    border border-transparent rounded text-xs text-white shadow">
                                                    View
                                                </a>
                                                <a href="{{ route('order-etas.edit', $eta->id) }}" 
                                                   class="inline-flex items-center px-2 py-1 bg-gray-800 border border-transparent rounded text-xs text-white hover:bg-gray-700">
                                                    Edit
                                                </a>
                                            </div>
                                            
                                            <div class="flex space-x-1">
                                                @if($eta->is_delayed && !$eta->customer_notified)
                                                    <button onclick="notifyCustomer({{ $eta->id }})" 
                                                            class="px-2 py-1 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700">
                                                        Notify
                                                    </button>
                                                @endif
                                                
                                                @if(!$eta->actual_completion_time && $eta->order->order_status !== 'completed')
                                                    <button onclick="markCompleted({{ $eta->id }})" 
                                                            class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                        Complete
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-lg font-medium">No ETA records found</p>
                                            <p class="text-sm">Try adjusting your search criteria or add new ETA tracking</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $orderEtas->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Quick Update Modal -->
    <div id="quick-update-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Update Estimate</h3>
                <form id="quick-update-form">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Estimate (minutes)</label>
                        <input type="number" id="current_estimate" min="1" max="480" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Delay Reason (if delayed)</label>
                        <input type="text" id="delay_reason" maxlength="255"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Kitchen busy, ingredient shortage, etc.">
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeQuickUpdateModal()" 
                                class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentEtaId = null;

        // Load statistics
        function loadStatistics() {
            fetch('{{ route("order-etas.getStatistics") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayStatistics(data.statistics);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function displayStatistics(stats) {
            const statsSection = document.getElementById('stats-section');
            statsSection.innerHTML = `
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">${stats.total_orders}</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                                    <dd class="text-lg font-medium text-gray-900">${stats.total_orders}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">${stats.delayed_orders}</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Delayed Orders</dt>
                                    <dd class="text-lg font-medium text-gray-900">${stats.delayed_orders} (${stats.delay_percentage}%)</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">${stats.completed_orders}</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                    <dd class="text-lg font-medium text-gray-900">${stats.completed_orders}</dd>
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
                                    <span class="text-white font-bold text-xs">${stats.average_estimate}</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Avg Estimate</dt>
                                    <dd class="text-lg font-medium text-gray-900">${stats.average_estimate} min</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            statsSection.classList.remove('hidden');
        }

        // Load delayed orders
        function loadDelayedOrders() {
            const url = new URL(window.location.href);
            url.searchParams.set('is_delayed', '1');
            window.location.href = url.toString();
        }

        // Load orders needing attention
        function loadNeedingAttention() {
            fetch('{{ route("order-etas.getNeedingAttention") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayNeedingAttention(data.orders, data.count);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function displayNeedingAttention(orders, count) {
            const alertsSection = document.getElementById('alerts-section');
            if (count > 0) {
                alertsSection.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <strong class="font-bold">⚠ Attention Required!</strong>
                        <span class="block sm:inline"> ${count} delayed order(s) need customer notification.</span>
                    </div>
                `;
            } else {
                alertsSection.innerHTML = `
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <strong class="font-bold">✓ All Good!</strong>
                        <span class="block sm:inline"> No orders requiring immediate attention.</span>
                    </div>
                `;
            }
        }

        // Notify customer
        function notifyCustomer(etaId) {
            if (!confirm('Are you sure you want to notify the customer about the delay?')) {
                return;
            }

            fetch(`/order-etas/${etaId}/notify-customer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error notifying customer');
            });
        }

        // Mark as completed
        function markCompleted(etaId) {
            if (!confirm('Are you sure you want to mark this order as completed?')) {
                return;
            }

            fetch(`/order-etas/${etaId}/mark-completed`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error marking as completed');
            });
        }

        // Quick update estimate
        function quickUpdateEstimate(etaId) {
            currentEtaId = etaId;
            document.getElementById('quick-update-modal').classList.remove('hidden');
        }

        function closeQuickUpdateModal() {
            document.getElementById('quick-update-modal').classList.add('hidden');
            currentEtaId = null;
        }

        // Handle quick update form submission
        document.getElementById('quick-update-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const currentEstimate = document.getElementById('current_estimate').value;
            const delayReason = document.getElementById('delay_reason').value;

            fetch(`/order-etas/${currentEtaId}/update-estimate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    current_estimate: currentEstimate,
                    delay_reason: delayReason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeQuickUpdateModal();
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating estimate');
            });
        });

        // Auto-load statistics and attention check on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadStatistics();
            loadNeedingAttention();
        });

        // Auto-refresh every 60 seconds
        setInterval(function() {
            loadNeedingAttention();
        }, 60000);
    </script>
</x-app-layout>