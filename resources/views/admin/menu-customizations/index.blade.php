<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Menu Customizations Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Action Buttons -->
            <div class="pb-3 flex justify-between items-center">
                <a href="{{ route('admin.menu-customizations.create') }}" class="items-center px-4 py-2 bg-gray-800 rounded font-semibold text-white hover:bg-gray-700">
                    Add New Customization
                </a>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.menu-customizations.getStatistics') }}" class="px-4 py-2 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700">
                        View Statistics
                    </a>
                    <button onclick="showBulkActions()" class="px-3 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600">
                        Bulk Actions
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4 bg-gray-50">
                    <form method="GET" action="{{ route('admin.menu-customizations.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="Search customization value...">
                        </div>

                        <div>
                            <label for="customization_type" class="block text-sm font-medium text-gray-700">Customization Type</label>
                            <select name="customization_type" id="customization_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">All Types</option>
                                @foreach($customizationTypes as $type)
                                    <option value="{{ $type }}" @if(request('customization_type') == $type) selected @endif>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="min_price" class="block text-sm font-medium text-gray-700">Min Price (RM)</label>
                            <input type="number" step="0.01" name="min_price" id="min_price" value="{{ request('min_price') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="0.00">
                        </div>

                        <div>
                            <label for="max_price" class="block text-sm font-medium text-gray-700">Max Price (RM)</label>
                            <input type="number" step="0.01" name="max_price" id="max_price" value="{{ request('max_price') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="999.99">
                        </div>

                        <div>
                            <label for="sort_by" class="block text-sm font-medium text-gray-700">Sort By</label>
                            <select name="sort_by" id="sort_by" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="created_at" @if(request('sort_by') == 'created_at') selected @endif>Created Date</option>
                                <option value="customization_type" @if(request('sort_by') == 'customization_type') selected @endif>Type</option>
                                <option value="customization_value" @if(request('sort_by') == 'customization_value') selected @endif>Value</option>
                                <option value="additional_price" @if(request('sort_by') == 'additional_price') selected @endif>Price</option>
                            </select>
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

                    @if(request()->hasAny(['search', 'customization_type', 'min_price', 'max_price', 'sort_by']))
                        <div class="mt-3">
                            <a href="{{ route('admin.menu-customizations.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                Clear all filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Customizations Table -->
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

                    <!-- Bulk Actions (Hidden by default) -->
                    <div id="bulk-actions" class="hidden mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="checkbox" id="select-all" class="rounded border-gray-300">
                                    <span class="ml-2 text-sm text-gray-600">Select All</span>
                                </label>
                                <span id="selected-count" class="text-sm text-gray-600">0 selected</span>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="bulkDelete()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                    Delete Selected
                                </button>
                                <button onclick="hideBulkActions()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2" id="bulk-header" style="display: none;">
                                        <input type="checkbox" id="header-checkbox" class="rounded border-gray-300">
                                    </th>
                                    <th class="text-left py-2">#</th>
                                    <th class="text-left py-2">Order Item</th>
                                    <th class="text-left py-2">Customization Type</th>
                                    <th class="text-left py-2">Customization Value</th>
                                    <th class="text-left py-2">Additional Price</th>
                                    <th class="text-left py-2">Created</th>
                                    <th class="text-left py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customizations as $customization)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 bulk-checkbox" style="display: none;">
                                        <input type="checkbox" name="customization_ids[]" value="{{ $customization->id }}" 
                                               class="rounded border-gray-300 customization-checkbox">
                                    </td>
                                    <td class="px-6 py-4">{{ ($customizations->currentPage() - 1) * $customizations->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="font-medium">Order #{{ $customization->orderItem->order_id ?? 'N/A' }}</div>
                                            @if($customization->orderItem && $customization->orderItem->menuItem)
                                                <div class="text-sm text-gray-600">{{ $customization->orderItem->menuItem->name }}</div>
                                            @endif
                                            <div class="text-xs text-gray-500">Qty: {{ $customization->orderItem->quantity ?? 'N/A' }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                                            {{ $customization->customization_type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ $customization->customization_value }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium {{ $customization->additional_price > 0 ? 'text-green-600' : 'text-gray-500' }}">
                                            @if($customization->additional_price > 0)
                                                +RM {{ number_format($customization->additional_price, 2) }}
                                            @else
                                                FREE
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm">{{ $customization->created_at->format('M d, Y') }}</div>
                                        <div class="text-sm text-gray-600">{{ $customization->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.menu-customizations.show', $customization->id) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 
                                                border border-transparent rounded-lg font-medium text-sm text-white shadow">
                                                View
                                            </a>
                                            <a href="{{ route('admin.menu-customizations.edit', $customization->id) }}" 
                                               class="inline-flex items-center px-2 py-1 bg-gray-800 border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.menu-customizations.destroy', $customization->id) }}" 
                                                  onsubmit="return confirm('Are you sure to delete this customization?');" class="inline">
                                                <input type="hidden" name="_method" value="DELETE">
                                                @csrf
                                               <x-danger-button class="text-xs">
                                                    Delete
                                                </x-danger-button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                            </svg>
                                            <p class="text-lg font-medium">No customizations found</p>
                                            <p class="text-sm">Try adjusting your search criteria or create a new customization</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $customizations->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Bulk Actions
        function showBulkActions() {
            document.getElementById('bulk-actions').classList.remove('hidden');
            document.getElementById('bulk-header').style.display = 'table-cell';
            document.querySelectorAll('.bulk-checkbox').forEach(cell => {
                cell.style.display = 'table-cell';
            });
        }

        function hideBulkActions() {
            document.getElementById('bulk-actions').classList.add('hidden');
            document.getElementById('bulk-header').style.display = 'none';
            document.querySelectorAll('.bulk-checkbox').forEach(cell => {
                cell.style.display = 'none';
            });
            // Uncheck all checkboxes
            document.querySelectorAll('.customization-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('select-all').checked = false;
            updateSelectedCount();
        }

        // Select All functionality
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.customization-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });

        // Update selected count
        function updateSelectedCount() {
            const selected = document.querySelectorAll('.customization-checkbox:checked').length;
            document.getElementById('selected-count').textContent = `${selected} selected`;
        }

        // Add event listeners to individual checkboxes
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.customization-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });
        });

        // Bulk delete
        function bulkDelete() {
            const selectedCheckboxes = document.querySelectorAll('.customization-checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                alert('Please select at least one customization to delete.');
                return;
            }

            if (!confirm(`Are you sure you want to delete ${selectedCheckboxes.length} customization(s)?`)) {
                return;
            }

            const customizationIds = Array.from(selectedCheckboxes).map(cb => cb.value);
            
            fetch('{{ route("admin.menu-customizations.bulkDelete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    customization_ids: customizationIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error deleting customizations: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting customizations');
            });
        }
    </script>
</x-app-layout>