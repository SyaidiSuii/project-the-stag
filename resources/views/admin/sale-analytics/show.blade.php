<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sale Analytics Details') }} - {{ $saleAnalytics->date->format('M d, Y') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold">{{ $saleAnalytics->date->format('l, F d, Y') }}</h3>
                    <p class="text-sm text-gray-600">Sales Analytics Report</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.sale-analytics.edit', $saleAnalytics->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Edit Analytics
                    </a>
                    <button onclick="printReport()" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Print Report
                    </button>
                    <a href="{{ route('admin.sale-analytics.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-xs">RM</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Sales</dt>
                                    <dd class="text-lg font-medium text-gray-900">RM {{ number_format($saleAnalytics->total_sales, 2) }}</dd>
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
                                    <span class="text-white font-bold text-sm">#</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($saleAnalytics->total_orders) }}</dd>
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
                                    <span class="text-white font-bold text-xs">AVG</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Avg Order Value</dt>
                                    <dd class="text-lg font-medium text-gray-900">RM {{ number_format($saleAnalytics->average_order_value, 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
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
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($saleAnalytics->unique_customers) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Order Analysis -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Order Analysis</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Dine-in Orders:</span>
                                <p class="font-bold text-lg">{{ number_format($saleAnalytics->dine_in_orders) }}</p>
                                @if($saleAnalytics->total_revenue_dine_in > 0)
                                    <p class="text-sm text-green-600">RM {{ number_format($saleAnalytics->total_revenue_dine_in, 2) }}</p>
                                @endif
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Takeaway Orders:</span>
                                <p class="font-bold text-lg">{{ number_format($saleAnalytics->takeaway_orders) }}</p>
                                @if($saleAnalytics->total_revenue_takeaway > 0)
                                    <p class="text-sm text-green-600">RM {{ number_format($saleAnalytics->total_revenue_takeaway, 2) }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Delivery Orders:</span>
                                <p class="font-bold text-lg">{{ number_format($saleAnalytics->delivery_orders) }}</p>
                                @if($saleAnalytics->total_revenue_delivery > 0)
                                    <p class="text-sm text-green-600">RM {{ number_format($saleAnalytics->total_revenue_delivery, 2) }}</p>
                                @endif
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Mobile Orders:</span>
                                <p class="font-bold text-lg">{{ number_format($saleAnalytics->mobile_orders) }}</p>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <span class="text-sm text-gray-600">QR Code Orders:</span>
                            <p class="font-bold text-lg">{{ number_format($saleAnalytics->qr_orders) }}</p>
                        </div>

                        <!-- Order Type Distribution Chart -->
                        @php
                            $totalOrders = $saleAnalytics->total_orders;
                            $dineInPercent = $totalOrders > 0 ? ($saleAnalytics->dine_in_orders / $totalOrders) * 100 : 0;
                            $takeawayPercent = $totalOrders > 0 ? ($saleAnalytics->takeaway_orders / $totalOrders) * 100 : 0;
                            $deliveryPercent = $totalOrders > 0 ? ($saleAnalytics->delivery_orders / $totalOrders) * 100 : 0;
                        @endphp

                        <div class="border-t pt-4">
                            <h5 class="text-sm font-medium text-gray-700 mb-3">Order Type Distribution</h5>
                            
                            @if($dineInPercent > 0)
                            <div class="mb-2">
                                <div class="flex justify-between text-sm">
                                    <span>Dine-in</span>
                                    <span>{{ number_format($dineInPercent, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $dineInPercent }}%"></div>
                                </div>
                            </div>
                            @endif

                            @if($takeawayPercent > 0)
                            <div class="mb-2">
                                <div class="flex justify-between text-sm">
                                    <span>Takeaway</span>
                                    <span>{{ number_format($takeawayPercent, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $takeawayPercent }}%"></div>
                                </div>
                            </div>
                            @endif

                            @if($deliveryPercent > 0)
                            <div class="mb-2">
                                <div class="flex justify-between text-sm">
                                    <span>Delivery</span>
                                    <span>{{ number_format($deliveryPercent, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $deliveryPercent }}%"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Customer Analysis -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Customer Analysis</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Total Customers:</span>
                                <p class="font-bold text-lg">{{ number_format($saleAnalytics->unique_customers) }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">New Customers:</span>
                                <p class="font-bold text-lg">{{ number_format($saleAnalytics->new_customers) }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Returning Customers:</span>
                                <p class="font-bold text-lg">{{ number_format($saleAnalytics->returning_customers) }}</p>
                            </div>
                            @if($saleAnalytics->customer_satisfaction_avg)
                            <div>
                                <span class="text-sm text-gray-600">Satisfaction Avg:</span>
                                <p class="font-bold text-lg">{{ number_format($saleAnalytics->customer_satisfaction_avg, 2) }}/5</p>
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $saleAnalytics->customer_satisfaction_avg)
                                            <span class="text-yellow-400">★</span>
                                        @else
                                            <span class="text-gray-300">★</span>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Customer Distribution -->
                        @php
                            $totalCustomers = $saleAnalytics->unique_customers;
                            $newPercent = $totalCustomers > 0 ? ($saleAnalytics->new_customers / $totalCustomers) * 100 : 0;
                            $returningPercent = $totalCustomers > 0 ? ($saleAnalytics->returning_customers / $totalCustomers) * 100 : 0;
                        @endphp

                        @if($newPercent > 0 || $returningPercent > 0)
                        <div class="border-t pt-4">
                            <h5 class="text-sm font-medium text-gray-700 mb-3">Customer Type Distribution</h5>
                            
                            @if($newPercent > 0)
                            <div class="mb-2">
                                <div class="flex justify-between text-sm">
                                    <span>New Customers</span>
                                    <span>{{ number_format($newPercent, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $newPercent }}%"></div>
                                </div>
                            </div>
                            @endif

                            @if($returningPercent > 0)
                            <div class="mb-2">
                                <div class="flex justify-between text-sm">
                                    <span>Returning Customers</span>
                                    <span>{{ number_format($returningPercent, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $returningPercent }}%"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Peak Hours Analysis -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Peak Hours Analysis</h4>
                    </div>
                    <div class="p-6">
                        @if($saleAnalytics->peak_hours && is_array($saleAnalytics->peak_hours))
                            <div class="space-y-4">
                                @foreach($saleAnalytics->peak_hours as $period => $hour)
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <span class="font-medium capitalize">{{ $period }}</span>
                                            <p class="text-sm text-gray-600">
                                                @if($period == 'breakfast')
                                                    Morning period (6AM - 11AM)
                                                @elseif($period == 'lunch')
                                                    Afternoon period (11AM - 3PM)
                                                @elseif($period == 'dinner')
                                                    Evening period (5PM - 10PM)
                                                @endif
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-lg font-bold">{{ sprintf('%02d:00', $hour) }}</span>
                                            <p class="text-sm text-gray-600">Peak hour</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Visual Peak Hours Timeline -->
                            <div class="mt-6 border-t pt-4">
                                <h5 class="text-sm font-medium text-gray-700 mb-3">Daily Peak Hours Timeline</h5>
                                <div class="relative">
                                    <div class="flex justify-between text-xs text-gray-500 mb-2">
                                        <span>6AM</span>
                                        <span>12PM</span>
                                        <span>6PM</span>
                                        <span>11PM</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-4 relative">
                                        @foreach($saleAnalytics->peak_hours as $period => $hour)
                                            @php
                                                $position = (($hour - 6) / 17) * 100; // 6AM to 11PM = 17 hours
                                                $color = $period == 'breakfast' ? 'bg-yellow-500' : ($period == 'lunch' ? 'bg-orange-500' : 'bg-red-500');
                                            @endphp
                                            <div class="absolute top-0 w-2 h-4 rounded-full {{ $color }}" 
                                                 style="left: {{ $position }}%"
                                                 title="{{ ucfirst($period) }} peak at {{ sprintf('%02d:00', $hour) }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">No peak hours data available</p>
                        @endif
                    </div>
                </div>

                <!-- Popular Items -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Popular Items</h4>
                    </div>
                    <div class="p-6">
                        @if($saleAnalytics->popular_items && is_array($saleAnalytics->popular_items) && count($saleAnalytics->popular_items) > 0)
                            <div class="space-y-3">
                                @foreach($saleAnalytics->popular_items as $index => $item)
                                    @if(isset($item['name']) && isset($item['quantity']))
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center">
                                            <span class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold mr-3">
                                                {{ $index + 1 }}
                                            </span>
                                            <span class="font-medium">{{ $item['name'] }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-lg font-bold">{{ number_format($item['quantity']) }}</span>
                                            <p class="text-sm text-gray-600">sold</p>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Top Items Chart -->
                            @if(count($saleAnalytics->popular_items) > 1)
                            <div class="mt-6 border-t pt-4">
                                <h5 class="text-sm font-medium text-gray-700 mb-3">Popularity Distribution</h5>
                                @php
                                    $totalQuantity = array_sum(array_column($saleAnalytics->popular_items, 'quantity'));
                                @endphp
                                @foreach(array_slice($saleAnalytics->popular_items, 0, 5) as $item)
                                    @if(isset($item['name']) && isset($item['quantity']))
                                        @php
                                            $percentage = $totalQuantity > 0 ? ($item['quantity'] / $totalQuantity) * 100 : 0;
                                        @endphp
                                        <div class="mb-2">
                                            <div class="flex justify-between text-sm">
                                                <span>{{ $item['name'] }}</span>
                                                <span>{{ number_format($percentage, 1) }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @endif
                        @else
                            <p class="text-gray-500 text-center py-8">No popular items data available</p>
                        @endif
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Performance Metrics</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        @if($saleAnalytics->average_preparation_time)
                        <div>
                            <span class="text-sm text-gray-600">Average Preparation Time:</span>
                            <p class="font-bold text-lg">{{ number_format($saleAnalytics->average_preparation_time, 1) }} minutes</p>
                            @php
                                $prepTimeStatus = $saleAnalytics->average_preparation_time <= 15 ? 'excellent' : 
                                                ($saleAnalytics->average_preparation_time <= 25 ? 'good' : 'needs-improvement');
                            @endphp
                            <span class="text-xs px-2 py-1 rounded-full 
                                @if($prepTimeStatus == 'excellent') bg-green-100 text-green-800
                                @elseif($prepTimeStatus == 'good') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                @if($prepTimeStatus == 'excellent') Excellent
                                @elseif($prepTimeStatus == 'good') Good
                                @else Needs Improvement @endif
                            </span>
                        </div>
                        @endif

                        @if($saleAnalytics->customer_satisfaction_avg)
                        <div>
                            <span class="text-sm text-gray-600">Customer Satisfaction:</span>
                            <p class="font-bold text-lg">{{ number_format($saleAnalytics->customer_satisfaction_avg, 2) }}/5.00</p>
                            @php
                                $satisfactionStatus = $saleAnalytics->customer_satisfaction_avg >= 4.5 ? 'excellent' : 
                                                    ($saleAnalytics->customer_satisfaction_avg >= 3.5 ? 'good' : 'needs-improvement');
                            @endphp
                            <span class="text-xs px-2 py-1 rounded-full 
                                @if($satisfactionStatus == 'excellent') bg-green-100 text-green-800
                                @elseif($satisfactionStatus == 'good') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                @if($satisfactionStatus == 'excellent') Excellent
                                @elseif($satisfactionStatus == 'good') Good
                                @else Needs Improvement @endif
                            </span>
                        </div>
                        @endif

                        <!-- Key Performance Indicators -->
                        <div class="border-t pt-4">
                            <h5 class="text-sm font-medium text-gray-700 mb-3">Key Performance Indicators</h5>
                            
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="bg-gray-50 p-3 rounded">
                                    <span class="text-gray-600">Orders per Customer:</span>
                                    <p class="font-medium">
                                        {{ $saleAnalytics->unique_customers > 0 ? number_format($saleAnalytics->total_orders / $saleAnalytics->unique_customers, 2) : '0' }}
                                    </p>
                                </div>
                                
                                <div class="bg-gray-50 p-3 rounded">
                                    <span class="text-gray-600">Revenue per Customer:</span>
                                    <p class="font-medium">
                                        RM {{ $saleAnalytics->unique_customers > 0 ? number_format($saleAnalytics->total_sales / $saleAnalytics->unique_customers, 2) : '0.00' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if(!$saleAnalytics->average_preparation_time && !$saleAnalytics->customer_satisfaction_avg)
                        <p class="text-gray-500 text-center py-8">No performance metrics data available</p>
                        @endif
                    </div>
                </div>

                <!-- Record Information -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 bg-gray-50 border-b">
                            <h4 class="font-semibold text-gray-800">Record Information</h4>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Created:</span>
                                    <p class="font-medium">{{ $saleAnalytics->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                
                                @if($saleAnalytics->updated_at != $saleAnalytics->created_at)
                                <div>
                                    <span class="text-gray-600">Last Updated:</span>
                                    <p class="font-medium">{{ $saleAnalytics->updated_at->format('M d, Y h:i A') }}</p>
                                </div>
                                @endif
                                
                                <div>
                                    <span class="text-gray-600">Record ID:</span>
                                    <p class="font-medium">#{{ $saleAnalytics->id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        function printReport() {
            window.print();
        }

        // Print styles
        const style = document.createElement('style');
        style.textContent = `
            @media print {
                .no-print { display: none !important; }
                body { font-size: 12px; }
                .shadow { box-shadow: none !important; }
                .bg-gray-50 { background-color: #f9f9f9 !important; }
            }
        `;
        document.head.appendChild(style);

        // Add no-print class to action buttons
        document.addEventListener('DOMContentLoaded', function() {
            const actionButtons = document.querySelector('.flex.justify-between.items-center.mb-6');
            if (actionButtons) {
                actionButtons.classList.add('no-print');
            }
        });
    </script>
</x-app-layout>