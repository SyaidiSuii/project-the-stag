<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Order Tracking Performance Statistics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-900">Performance Analytics</h3>
                <div class="flex gap-2">
                    <a href="{{ route('order-trackings.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                        All Trackings
                    </a>
                    <a href="{{ route('order-trackings.stations.active-orders', ['station_name' => 'Kitchen']) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Kitchen Dashboard
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 bg-gray-50">
                    <form method="GET" action="{{ route('order-trackings.stats.performance') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700">Date From</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700">Date To</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="station_name" class="block text-sm font-medium text-gray-700">Station</label>
                            <select name="station_name" id="station_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Stations</option>
                                <option value="Kitchen" @if(request('station_name') == 'Kitchen') selected @endif>Kitchen</option>
                                <option value="Bar" @if(request('station_name') == 'Bar') selected @endif>Bar</option>
                                <option value="Grill" @if(request('station_name') == 'Grill') selected @endif>Grill</option>
                                <option value="Pastry" @if(request('station_name') == 'Pastry') selected @endif>Pastry</option>
                                <option value="Cold Station" @if(request('station_name') == 'Cold Station') selected @endif>Cold Station</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full px-4 py-2 bg-indigo-600 !text-white font-semibold 
                                    rounded-md hover:bg-indigo-700 
                                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Apply Filters
                            </button>
                        </div>
                    </form>

                    @if(request()->hasAny(['date_from', 'date_to', 'station_name']))
                        <div class="mt-3">
                            <a href="{{ route('order-trackings.stats.performance') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                Clear all filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            @if($stats->count() > 0)
            <!-- Overall Summary -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                @php
                    $totalOrders = $stats->sum('total_orders');
                    $overallAvgTime = $stats->avg('avg_time');
                    $fastestStation = $stats->sortBy('avg_time')->first();
                    $busiestStation = $stats->sortByDesc('total_orders')->first();
                @endphp

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ $totalOrders }}</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalOrders }}</dd>
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
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Avg Time</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($overallAvgTime, 1) }}m</dd>
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
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Fastest Station</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $fastestStation->station_name ?: 'No Station' }}</dd>
                                    <dd class="text-sm text-gray-500">{{ number_format($fastestStation->avg_time, 1) }}m avg</dd>
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
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Busiest Station</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $busiestStation->station_name ?: 'No Station' }}</dd>
                                    <dd class="text-sm text-gray-500">{{ $busiestStation->total_orders }} orders</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Station Performance Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 bg-gray-50 border-b">
                    <h4 class="font-semibold text-gray-800">Station Performance Breakdown</h4>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">Station</th>
                                    <th class="text-left py-2">Total Orders</th>
                                    <th class="text-left py-2">Average Time</th>
                                    <th class="text-left py-2">Fastest Order</th>
                                    <th class="text-left py-2">Slowest Order</th>
                                    <th class="text-left py-2">Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats->sortBy('avg_time') as $stat)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <span class="font-medium">{{ $stat->station_name ?: 'No Station Assigned' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-medium">{{ $stat->total_orders }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-medium">{{ number_format($stat->avg_time, 1) }} min</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-green-600 font-medium">{{ $stat->min_time }} min</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-red-600 font-medium">{{ $stat->max_time }} min</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $performance = 'excellent';
                                            $bgColor = 'bg-green-100 text-green-800';
                                            if ($stat->avg_time > 30) {
                                                $performance = 'needs improvement';
                                                $bgColor = 'bg-red-100 text-red-800';
                                            } elseif ($stat->avg_time > 20) {
                                                $performance = 'good';
                                                $bgColor = 'bg-yellow-100 text-yellow-800';
                                            }
                                        @endphp
                                        <span class="px-2 py-1 text-xs rounded {{ $bgColor }}">
                                            {{ ucfirst($performance) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Performance Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Average Time by Station Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Average Time by Station</h4>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($stats->sortBy('avg_time') as $stat)
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $stat->station_name ?: 'No Station' }}</span>
                                    <span class="text-sm text-gray-500">{{ number_format($stat->avg_time, 1) }}m</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    @php
                                        $maxTime = $stats->max('avg_time');
                                        $percentage = ($stat->avg_time / $maxTime) * 100;
                                        $color = $stat->avg_time <= 15 ? 'bg-green-500' : ($stat->avg_time <= 25 ? 'bg-yellow-500' : 'bg-red-500');
                                    @endphp
                                    <div class="{{ $color }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Order Volume by Station -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Order Volume by Station</h4>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($stats->sortByDesc('total_orders') as $stat)
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $stat->station_name ?: 'No Station' }}</span>
                                    <span class="text-sm text-gray-500">{{ $stat->total_orders }} orders</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    @php
                                        $maxOrders = $stats->max('total_orders');
                                        $percentage = ($stat->total_orders / $maxOrders) * 100;
                                    @endphp
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Performance Insights -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Performance Insights & Recommendations</h4>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Insights -->
                            <div>
                                <h5 class="font-medium text-gray-900 mb-3">Key Insights</h5>
                                <div class="space-y-3">
                                    @php
                                        $slowestStation = $stats->sortByDesc('avg_time')->first();
                                        $fastestStation = $stats->sortBy('avg_time')->first();
                                        $averageOverall = $stats->avg('avg_time');
                                    @endphp
                                    
                                    <div class="flex items-start space-x-2">
                                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-700">
                                                <strong>{{ $fastestStation->station_name ?: 'No Station' }}</strong> is the fastest station with an average of 
                                                <strong>{{ number_format($fastestStation->avg_time, 1) }} minutes</strong> per order.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start space-x-2">
                                        <svg class="w-5 h-5 text-orange-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-700">
                                                <strong>{{ $slowestStation->station_name ?: 'No Station' }}</strong> takes the longest with 
                                                <strong>{{ number_format($slowestStation->avg_time, 1) }} minutes</strong> average per order.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start space-x-2">
                                        <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-700">
                                                Overall average processing time is <strong>{{ number_format($averageOverall, 1) }} minutes</strong> 
                                                across all stations.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recommendations -->
                            <div>
                                <h5 class="font-medium text-gray-900 mb-3">Recommendations</h5>
                                <div class="space-y-3">
                                    @if($slowestStation->avg_time > 30)
                                    <div class="flex items-start space-x-2">
                                        <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-700">
                                                <strong>Urgent:</strong> {{ $slowestStation->station_name ?: 'Unassigned orders' }} needs immediate attention. 
                                                Consider additional staff or process optimization.
                                            </p>
                                        </div>
                                    </div>
                                    @endif

                                    @if($stats->where('avg_time', '>', 25)->count() > 1)
                                    <div class="flex items-start space-x-2">
                                        <svg class="w-5 h-5 text-yellow-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-700">
                                                Multiple stations are underperforming. Consider cross-training staff or redistributing workload.
                                            </p>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="flex items-start space-x-2">
                                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-700">
                                                Study {{ $fastestStation->station_name ?: 'the fastest station' }}'s processes and apply successful practices to other stations.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start space-x-2">
                                        <svg class="w-5 h-5 text-purple-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-700">
                                                Regular monitoring and staff feedback can help maintain consistent performance levels.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @else
            <!-- No Data State -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No performance data available</h3>
                    <p class="mt-1 text-sm text-gray-500">No completed order trackings found for the selected criteria.</p>
                    <div class="mt-6">
                        <a href="{{ route('order-trackings.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            View All Trackings
                        </a>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>