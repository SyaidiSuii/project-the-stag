<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sale Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Action Buttons -->
            <div class="pb-3 flex justify-between items-center">
                <a href="{{ route('sale-analytics.create') }}" class="items-center px-4 py-2 bg-gray-800 rounded font-semibold text-white hover:bg-gray-700">
                    Create New Analytics Record
                </a>
                <div class="flex space-x-2">
                    <button onclick="generateAnalytics()" class="px-4 py-2 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700">
                        Generate Today's Analytics
                    </button>
                    <button onclick="openTrendsModal()" class="px-4 py-2 bg-green-600 text-white rounded font-semibold hover:bg-green-700">
                        View Trends
                    </button>
                    <button onclick="exportData()" class="px-4 py-2 bg-purple-600 text-white rounded font-semibold hover:bg-purple-700">
                        Export Data
                    </button>
                </div>
            </div>

            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg" id="total-sales-card">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-xs">RM</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Sales (This Month)</dt>
                                    <dd class="text-lg font-medium text-gray-900" id="total-sales">Loading...</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg" id="total-orders-card">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">#</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                                    <dd class="text-lg font-medium text-gray-900" id="total-orders">Loading...</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg" id="avg-order-value-card">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-xs">AVG</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Avg Order Value</dt>
                                    <dd class="text-lg font-medium text-gray-900" id="avg-order-value">Loading...</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg" id="unique-customers-card">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-xs">👥</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Unique Customers</dt>
                                    <dd class="text-lg font-medium text-gray-900" id="unique-customers">Loading...</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4 bg-gray-50">
                    <form method="GET" action="{{ route('sale-analytics.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="period" class="block text-sm font-medium text-gray-700">Quick Period</label>
                            <select name="period" id="period" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="setQuickPeriod(this.value)">
                                <option value="">Custom Range</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="quarter">This Quarter</option>
                                <option value="year">This Year</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full px-4 py-2 bg-indigo-600 text-white font-semibold 
                                    rounded-md hover:bg-indigo-700 
                                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Filter
                            </button>
                        </div>
                    </form>

                    @if(request()->hasAny(['start_date', 'end_date']))
                        <div class="mt-3">
                            <a href="{{ route('sale-analytics.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                Clear all filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Analytics Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
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
                                    <th class="text-left py-2">Date</th>
                                    <th class="text-left py-2">Total Sales</th>
                                    <th class="text-left py-2">Orders</th>
                                    <th class="text-left py-2">Avg Order Value</th>
                                    <th class="text-left py-2">Customers</th>
                                    <th class="text-left py-2">Order Types</th>
                                    <th class="text-left py-2">Peak Hours</th>
                                    <th class="text-left py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($analytics as $analytic)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ ($analytics->currentPage() - 1) * $analytics->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ $analytic->date->format('M d, Y') }}</div>
                                        <div class="text-sm text-gray-600">{{ $analytic->date->format('l') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-green-600">RM {{ number_format($analytic->total_sales, 2) }}</div>
                                        @if($analytic->total_revenue_dine_in > 0 || $analytic->total_revenue_takeaway > 0 || $analytic->total_revenue_delivery > 0)
                                            <div class="text-xs text-gray-500">
                                                @if($analytic->total_revenue_dine_in > 0)
                                                    Dine: RM{{ number_format($analytic->total_revenue_dine_in, 0) }}
                                                @endif
                                                @if($analytic->total_revenue_takeaway > 0)
                                                    Take: RM{{ number_format($analytic->total_revenue_takeaway, 0) }}
                                                @endif
                                                @if($analytic->total_revenue_delivery > 0)
                                                    Del: RM{{ number_format($analytic->total_revenue_delivery, 0) }}
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ number_format($analytic->total_orders) }}</div>
                                        <div class="text-xs text-gray-500">
                                            @if($analytic->dine_in_orders + $analytic->takeaway_orders + $analytic->delivery_orders > 0)
                                                D:{{ $analytic->dine_in_orders }} T:{{ $analytic->takeaway_orders }} Del:{{ $analytic->delivery_orders }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">RM {{ number_format($analytic->average_order_value, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ number_format($analytic->unique_customers) }}</div>
                                        @if($analytic->new_customers > 0 || $analytic->returning_customers > 0)
                                            <div class="text-xs text-gray-500">
                                                New: {{ $analytic->new_customers }} | Return: {{ $analytic->returning_customers }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-1">
                                            @if($analytic->mobile_orders > 0)
                                                <span class="px-1 py-0.5 bg-blue-100 text-blue-800 text-xs rounded">
                                                    M:{{ $analytic->mobile_orders }}
                                                </span>
                                            @endif
                                            @if($analytic->qr_orders > 0)
                                                <span class="px-1 py-0.5 bg-purple-100 text-purple-800 text-xs rounded">
                                                    QR:{{ $analytic->qr_orders }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($analytic->peak_hours && is_array($analytic->peak_hours))
                                            <div class="text-xs space-y-1">
                                                @foreach($analytic->peak_hours as $period => $hour)
                                                    <div>{{ ucfirst($period) }}: {{ $hour }}:00</div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-500 text-sm">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('sale-analytics.show', $analytic->id) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 
                                                border border-transparent rounded text-xs text-white shadow">
                                                View
                                            </a>
                                            <a href="{{ route('sale-analytics.edit', $analytic->id) }}" 
                                               class="inline-flex items-center px-2 py-1 bg-gray-800 border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('sale-analytics.destroy', $analytic->id) }}" 
                                                  onsubmit="return confirm('Are you sure to delete this analytics record?');" class="inline">
                                                <input type="hidden" name="_method" value="DELETE">
                                                @csrf
                                               <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-600 border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                            <p class="text-lg font-medium">No analytics data found</p>
                                            <p class="text-sm">Try adjusting your search criteria or generate analytics from existing orders</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $analytics->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Trends Modal -->
    <div id="trendsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Sales Trends</h3>
                    <button onclick="closeTrendsModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-4">
                    <label for="trendsPeriod" class="block text-sm font-medium text-gray-700 mb-2">Compare Period</label>
                    <select id="trendsPeriod" onchange="loadTrends()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="week">This Week vs Last Week</option>
                        <option value="month">This Month vs Last Month</option>
                        <option value="quarter">This Quarter vs Last Quarter</option>
                        <option value="year">This Year vs Last Year</option>
                    </select>
                </div>
                
                <div id="trendsContent" class="space-y-4">
                    <p class="text-gray-500">Loading trends data...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load dashboard stats on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardStats();
        });

        function loadDashboardStats() {
            fetch('{{ route("sale-analytics.dashboard-stats") }}?period=month')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('total-sales').textContent = 'RM ' + (data.total_sales ? parseFloat(data.total_sales).toLocaleString('en-MY', {minimumFractionDigits: 2}) : '0.00');
                    document.getElementById('total-orders').textContent = data.total_orders ? parseInt(data.total_orders).toLocaleString() : '0';
                    document.getElementById('avg-order-value').textContent = 'RM ' + (data.avg_order_value ? parseFloat(data.avg_order_value).toFixed(2) : '0.00');
                    document.getElementById('unique-customers').textContent = data.total_customers ? parseInt(data.total_customers).toLocaleString() : '0';
                })
                .catch(error => {
                    console.error('Error loading dashboard stats:', error);
                    document.getElementById('total-sales').textContent = 'Error';
                    document.getElementById('total-orders').textContent = 'Error';
                    document.getElementById('avg-order-value').textContent = 'Error';
                    document.getElementById('unique-customers').textContent = 'Error';
                });
        }

        function generateAnalytics() {
            if (!confirm('Generate analytics for today? This will create a new analytics record.')) {
                return;
            }

            const today = new Date().toISOString().split('T')[0];
            
            fetch(`{{ route('sale-analytics.generate', '') }}/${today}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                    if (data.data) {
                        location.reload(); // Reload to show new analytics
                    }
                } else {
                    alert('Error generating analytics: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error generating analytics');
            });
        }

        function setQuickPeriod(period) {
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');
            const today = new Date();
            
            switch(period) {
                case 'today':
                    const todayStr = today.toISOString().split('T')[0];
                    startDate.value = todayStr;
                    endDate.value = todayStr;
                    break;
                case 'week':
                    const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
                    const endOfWeek = new Date(today.setDate(today.getDate() - today.getDay() + 6));
                    startDate.value = startOfWeek.toISOString().split('T')[0];
                    endDate.value = endOfWeek.toISOString().split('T')[0];
                    break;
                case 'month':
                    const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                    const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    startDate.value = startOfMonth.toISOString().split('T')[0];
                    endDate.value = endOfMonth.toISOString().split('T')[0];
                    break;
                case 'quarter':
                    const quarter = Math.floor(today.getMonth() / 3);
                    const startOfQuarter = new Date(today.getFullYear(), quarter * 3, 1);
                    const endOfQuarter = new Date(today.getFullYear(), quarter * 3 + 3, 0);
                    startDate.value = startOfQuarter.toISOString().split('T')[0];
                    endDate.value = endOfQuarter.toISOString().split('T')[0];
                    break;
                case 'year':
                    const startOfYear = new Date(today.getFullYear(), 0, 1);
                    const endOfYear = new Date(today.getFullYear(), 11, 31);
                    startDate.value = startOfYear.toISOString().split('T')[0];
                    endDate.value = endOfYear.toISOString().split('T')[0];
                    break;
                default:
                    startDate.value = '';
                    endDate.value = '';
            }
        }

        function openTrendsModal() {
            document.getElementById('trendsModal').classList.remove('hidden');
            loadTrends();
        }

        function closeTrendsModal() {
            document.getElementById('trendsModal').classList.add('hidden');
        }

        function loadTrends() {
            const period = document.getElementById('trendsPeriod').value;
            const content = document.getElementById('trendsContent');
            
            content.innerHTML = '<p class="text-gray-500">Loading trends data...</p>';
            
            fetch(`{{ route('sale-analytics.trends') }}?period=${period}&compare=true`)
                .then(response => response.json())
                .then(data => {
                    let html = '<div class="grid grid-cols-2 gap-4">';
                    
                    if (data.current && data.previous && data.comparison) {
                        const metrics = [
                            {key: 'total_sales', label: 'Total Sales', prefix: 'RM ', growth: data.comparison.sales_growth},
                            {key: 'total_orders', label: 'Total Orders', prefix: '', growth: data.comparison.orders_growth},
                            {key: 'avg_order_value', label: 'Avg Order Value', prefix: 'RM ', growth: data.comparison.aov_growth},
                            {key: 'total_customers', label: 'Total Customers', prefix: '', growth: data.comparison.customers_growth}
                        ];
                        
                        metrics.forEach(metric => {
                            const current = data.current[metric.key] || 0;
                            const previous = data.previous[metric.key] || 0;
                            const growth = metric.growth || 0;
                            const growthClass = growth >= 0 ? 'text-green-600' : 'text-red-600';
                            const growthIcon = growth >= 0 ? '▲' : '▼';
                            
                            html += `
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-700">${metric.label}</h4>
                                    <div class="mt-2">
                                        <div class="text-lg font-bold">${metric.prefix}${parseFloat(current).toLocaleString()}</div>
                                        <div class="text-sm text-gray-600">Previous: ${metric.prefix}${parseFloat(previous).toLocaleString()}</div>
                                        <div class="text-sm ${growthClass} font-medium">
                                            ${growthIcon} ${Math.abs(growth)}%
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        html += '<div class="col-span-2 text-center text-gray-500">No comparison data available</div>';
                    }
                    
                    html += '</div>';
                    content.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error loading trends:', error);
                    content.innerHTML = '<p class="text-red-500">Error loading trends data</p>';
                });
        }

        function exportData() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            let url = '{{ route("sale-analytics.index") }}?export=csv';
            if (startDate) url += `&start_date=${startDate}`;
            if (endDate) url += `&end_date=${endDate}`;
            
            window.open(url, '_blank');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('trendsModal');
            if (event.target === modal) {
                closeTrendsModal();
            }
        }
    </script>
</x-app-layout>