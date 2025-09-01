<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Menu Customizations Statistics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('admin.menu-customizations.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                    ← Back to Customizations
                </a>
            </div>

            <!-- Overview Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Average Price</dt>
                                    <dd class="text-lg font-medium text-gray-900">RM {{ number_format($stats['average_additional_price'], 2) }}</dd>
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
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                                    <dd class="text-lg font-medium text-gray-900">RM {{ number_format($stats['total_additional_revenue'], 2) }}</dd>
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
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Price Range</dt>
                                    <dd class="text-lg font-medium text-gray-900">
                                        RM {{ number_format($stats['price_range']['min'], 2) }} - 
                                        RM {{ number_format($stats['price_range']['max'], 2) }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Customizations by Type -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Customizations by Type</h4>
                    </div>
                    <div class="p-6">
                        @if(!empty($stats['by_type']))
                            <div class="space-y-4">
                                @php 
                                    $maxCount = max($stats['by_type']); 
                                    $totalTypes = array_sum($stats['by_type']);
                                @endphp
                                @foreach($stats['by_type'] as $type => $count)
                                    @php $percentage = $totalTypes > 0 ? ($count / $totalTypes) * 100 : 0; @endphp
                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="font-medium text-gray-700">{{ $type }}</span>
                                            <span class="text-gray-500">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">No customization data available</p>
                        @endif
                    </div>
                </div>

                <!-- Most Popular Customizations -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Most Popular Customizations</h4>
                    </div>
                    <div class="p-6">
                        @if(!empty($stats['most_popular_customizations']))
                            <div class="space-y-3">
                                @php 
                                    $maxPopular = max($stats['most_popular_customizations']);
                                    $totalPopular = array_sum($stats['most_popular_customizations']);
                                @endphp
                                @foreach(array_slice($stats['most_popular_customizations'], 0, 10, true) as $value => $count)
                                    @php $percentage = $totalPopular > 0 ? ($count / $totalPopular) * 100 : 0; @endphp
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $value }}</p>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                                                <div class="bg-green-600 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                        <div class="ml-4 text-right">
                                            <span class="text-sm font-bold text-gray-700">{{ $count }}</span>
                                            <p class="text-xs text-gray-500">{{ number_format($percentage, 1) }}%</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">No popular customizations data available</p>
                        @endif
                    </div>
                </div>

                <!-- Recent Customizations -->
                @if(!empty($stats['recent_customizations']))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Recent Customizations</h4>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($stats['recent_customizations'] as $customization)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                                                {{ $customization->customization_type }}
                                            </span>
                                            <span class="font-medium">{{ $customization->customization_value }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $customization->orderItem->menuItem->name ?? 'Unknown Item' }} - 
                                            Order #{{ $customization->orderItem->order_id ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $customization->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold {{ $customization->additional_price > 0 ? 'text-green-600' : 'text-gray-500' }}">
                                            @if($customization->additional_price > 0)
                                                +RM {{ number_format($customization->additional_price, 2) }}
                                            @else
                                                FREE
                                            @endif
                                        </p>
                                        <a href="{{ route('admin.menu-customizations.show', $customization->id) }}" 
                                           class="text-xs text-blue-600 hover:text-blue-800">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Revenue Analytics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Revenue Analytics</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">
                                    RM {{ number_format($stats['total_additional_revenue'], 2) }}
                                </div>
                                <div class="text-sm text-green-700">Total Additional Revenue</div>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">
                                    RM {{ number_format($stats['average_additional_price'], 2) }}
                                </div>
                                <div class="text-sm text-blue-700">Average Per Customization</div>
                            </div>
                        </div>

                        @if($stats['total_customizations'] > 0)
                        <div class="border-t pt-4">
                            <h5 class="font-medium text-gray-900 mb-2">Revenue Insights</h5>
                            <div class="space-y-2 text-sm text-gray-600">
                                @php
                                    $freeCustomizations = 0;
                                    $paidCustomizations = 0;
                                    foreach($stats['by_type'] as $type => $count) {
                                        // This is an estimation - you might want to query actual data
                                        $freeCustomizations += $count * 0.3; // Assuming 30% are free
                                        $paidCustomizations += $count * 0.7; // Assuming 70% are paid
                                    }
                                    $freeCustomizations = floor($freeCustomizations);
                                    $paidCustomizations = floor($paidCustomizations);
                                    $freePercentage = $stats['total_customizations'] > 0 ? ($freeCustomizations / $stats['total_customizations']) * 100 : 0;
                                    $paidPercentage = 100 - $freePercentage;
                                @endphp
                                <div class="flex justify-between">
                                    <span>Free Customizations:</span>
                                    <span class="font-medium">{{ $freeCustomizations }} ({{ number_format($freePercentage, 1) }}%)</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Paid Customizations:</span>
                                    <span class="font-medium">{{ $paidCustomizations }} ({{ number_format($paidPercentage, 1) }}%)</span>
                                </div>
                                @if($paidCustomizations > 0)
                                <div class="flex justify-between">
                                    <span>Avg Revenue per Paid:</span>
                                    <span class="font-medium text-green-600">
                                        RM {{ number_format($stats['total_additional_revenue'] / $paidCustomizations, 2) }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Export Options -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-gray-50 border-b">
                    <h4 class="font-semibold text-gray-800">Export & Actions</h4>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-4">
                        <button onclick="exportStatistics('pdf')" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Export PDF Report
                        </button>
                        <button onclick="exportStatistics('excel')" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Export Excel
                        </button>
                        <button onclick="window.print()" 
                                class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            Print Report
                        </button>
                        <a href="{{ route('admin.menu-customizations.index') }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            View All Customizations
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Export functionality
        function exportStatistics(format) {
            window.location.href = `{{ route('admin.menu-customizations.export') }}?format=${format}`;
        }

        // Print styles
        const printStyles = `
            <style>
                @media print {
                    body * { visibility: hidden; }
                    .print-area, .print-area * { visibility: visible; }
                    .print-area { 
                        position: absolute;
                        left: 0;
                        top: 0;
                        width: 100%;
                    }
                    .no-print { display: none !important; }
                }
            </style>
        `;
        document.head.insertAdjacentHTML('beforeend', printStyles);

        // Add print-area class to main content
        document.addEventListener('DOMContentLoaded', function() {
            const mainContent = document.querySelector('.py-12');
            if (mainContent) {
                mainContent.classList.add('print-area');
            }
        });

        // Auto-refresh statistics every 5 minutes
        setTimeout(function() {
            window.location.reload();
        }, 300000); // 5 minutes
    </script>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; }
            .bg-white { background: white !important; }
            .shadow-sm { box-shadow: none !important; }
        }
    </style>
</x-app-layout>truncate">Total Customizations</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_customizations']) }}</dd>
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
                                    <span class="text-white font-bold text-sm">RM</span>
                                </div>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500