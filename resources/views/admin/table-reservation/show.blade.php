<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reservation Details') }} - {{ $tableReservation->confirmation_code }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Action Buttons -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold">Reservation {{ $tableReservation->confirmation_code }}</h3>
                    <p class="text-sm text-gray-600">{{ $tableReservation->guest_name }} - {{ $tableReservation->reservation_date->format('M d, Y') }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.table-reservation.edit', $tableReservation->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Edit Reservation
                    </a>
                    <a href="{{ route('admin.table-reservation.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Quick Status Update -->
            @if(!in_array($tableReservation->status, ['completed', 'cancelled', 'no_show']))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 bg-gray-50 border-b">
                    <h4 class="font-semibold text-gray-800">Quick Status Update</h4>
                </div>
                <div class="p-4">
                    <form method="POST" action="{{ route('admin.table-reservation.update-status', $tableReservation->id) }}" class="flex items-end gap-4">
                        @csrf
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">New Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="pending" @if($tableReservation->status == 'pending') selected @endif>Pending</option>
                                <option value="confirmed" @if($tableReservation->status == 'confirmed') selected @endif>Confirmed</option>
                                <option value="seated" @if($tableReservation->status == 'seated') selected @endif>Seated</option>
                                <option value="completed" @if($tableReservation->status == 'completed') selected @endif>Completed</option>
                                <option value="cancelled" @if($tableReservation->status == 'cancelled') selected @endif>Cancelled</option>
                                <option value="no_show" @if($tableReservation->status == 'no_show') selected @endif>No Show</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                            <input type="text" name="notes" id="notes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Add a note about this status change...">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Update Status
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Reservation Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Reservation Details</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Confirmation Code:</span>
                                <p class="font-mono text-lg font-bold">{{ $tableReservation->confirmation_code }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Status:</span>
                                <p>
                                    <span class="px-3 py-1 text-sm rounded-full capitalize
                                        @if($tableReservation->status == 'confirmed') bg-green-100 text-green-800
                                        @elseif($tableReservation->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($tableReservation->status == 'seated') bg-blue-100 text-blue-800
                                        @elseif($tableReservation->status == 'completed') bg-indigo-100 text-indigo-800
                                        @elseif($tableReservation->status == 'cancelled') bg-red-100 text-red-800
                                        @elseif($tableReservation->status == 'no_show') bg-gray-100 text-gray-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ str_replace('_', ' ', $tableReservation->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Date:</span>
                                <p class="font-medium">{{ $tableReservation->reservation_date->format('l, M d, Y') }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Time:</span>
                                <p class="font-medium">{{ \Carbon\Carbon::parse($tableReservation->reservation_time)->format('h:i A') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Number of Guests:</span>
                                <p class="font-medium text-lg">{{ $tableReservation->number_of_guests }} people</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Table:</span>
                                @if($tableReservation->table)
                                    <p class="font-medium">{{ $tableReservation->table->table_number }}</p>
                                    <p class="text-sm text-gray-500">{{ ucfirst($tableReservation->table->table_type) }} ({{ $tableReservation->table->capacity }} capacity)</p>
                                @else
                                    <p class="text-gray-500">No table assigned</p>
                                @endif
                            </div>
                        </div>

                        @if($tableReservation->auto_release_time)
                        <div>
                            <span class="text-sm text-gray-600">Auto Release Time:</span>
                            <p class="font-medium">{{ $tableReservation->auto_release_time->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Guest Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Guest Information</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <span class="text-sm text-gray-600">Name:</span>
                            <p class="font-medium text-lg">{{ $tableReservation->guest_name }}</p>
                        </div>

                        <div>
                            <span class="text-sm text-gray-600">Phone:</span>
                            <p class="font-medium">
                                <a href="tel:{{ $tableReservation->guest_phone }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $tableReservation->guest_phone }}
                                </a>
                            </p>
                        </div>

                        @if($tableReservation->guest_email)
                        <div>
                            <span class="text-sm text-gray-600">Email:</span>
                            <p class="font-medium">
                                <a href="mailto:{{ $tableReservation->guest_email }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $tableReservation->guest_email }}
                                </a>
                            </p>
                        </div>
                        @endif

                        @if($tableReservation->special_requests)
                        <div>
                            <span class="text-sm text-gray-600">Special Requests:</span>
                            <p class="font-medium bg-yellow-50 p-3 rounded-md border border-yellow-200">{{ $tableReservation->special_requests }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- System Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">System Information</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <span class="text-sm text-gray-600">Created By:</span>
                            <p class="font-medium">{{ $tableReservation->user->name ?? 'Unknown User' }}</p>
                        </div>

                        <div>
                            <span class="text-sm text-gray-600">Created At:</span>
                            <p class="font-medium">{{ $tableReservation->created_at->format('M d, Y h:i A') }}</p>
                        </div>

                        @if($tableReservation->updated_at != $tableReservation->created_at)
                        <div>
                            <span class="text-sm text-gray-600">Last Updated:</span>
                            <p class="font-medium">{{ $tableReservation->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif

                        @if($tableReservation->confirmed_at)
                        <div>
                            <span class="text-sm text-gray-600">Confirmed At:</span>
                            <p class="font-medium">{{ $tableReservation->confirmed_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif

                        @if($tableReservation->seated_at)
                        <div>
                            <span class="text-sm text-gray-600">Seated At:</span>
                            <p class="font-medium">{{ $tableReservation->seated_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif

                        @if($tableReservation->completed_at)
                        <div>
                            <span class="text-sm text-gray-600">Completed At:</span>
                            <p class="font-medium">{{ $tableReservation->completed_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @endif
                        
                        @if($tableReservation->status === 'seated' && $tableReservation->hasActiveSession())
                        <div>
                            <span class="text-sm text-gray-600">QR Code:</span>
                            <div class="mt-2">
                                <div class="bg-green-50 border border-green-200 rounded-md p-4">
                                    <p class="text-sm font-medium text-green-800 mb-3">Customer QR Code Active!</p>
                                    
                                    @if($tableReservation->tableSession && $tableReservation->tableSession->qr_code_png)
                                        <div class="flex items-start gap-4">
                                            <!-- QR Code Image -->
                                            <div class="flex-shrink-0">
                                                <img src="{{ asset('storage/' . $tableReservation->tableSession->qr_code_png) }}" 
                                                     alt="QR Code for Table {{ $tableReservation->table->table_number }}"
                                                     class="w-32 h-32 border border-green-300 rounded-md">
                                            </div>
                                            
                                            <!-- Actions -->
                                            <div class="flex-1">
                                                <p class="text-xs text-green-700 mb-3 break-all">{{ $tableReservation->getQRCodeUrl() }}</p>
                                                
                                                <div class="grid grid-cols-2 gap-2">
                                                    <button onclick="copyToClipboard('{{ $tableReservation->getQRCodeUrl() }}')"
                                                            class="px-3 py-2 bg-green-600 text-white text-xs rounded hover:bg-green-700 w-full">
                                                        Copy URL
                                                    </button>
                                                    <a href="{{ $tableReservation->getQRCodeUrl() }}" target="_blank"
                                                       class="px-3 py-2 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 text-center">
                                                        Test QR
                                                    </a>
                                                    <a href="{{ route('admin.table-sessions.qr-download', [$tableReservation->tableSession, 'png']) }}"
                                                       class="px-3 py-2 bg-purple-600 text-white text-xs rounded hover:bg-purple-700 text-center">
                                                        Download PNG
                                                    </a>
                                                    <a href="{{ route('admin.table-sessions.qr-download', [$tableReservation->tableSession, 'svg']) }}"
                                                       class="px-3 py-2 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700 text-center">
                                                        Download SVG
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Fallback if QR image not generated -->
                                        <p class="text-xs text-green-700 break-all mb-2">{{ $tableReservation->getQRCodeUrl() }}</p>
                                        <div class="flex gap-2">
                                            <button onclick="copyToClipboard('{{ $tableReservation->getQRCodeUrl() }}')"
                                                    class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                Copy URL
                                            </button>
                                            <a href="{{ $tableReservation->getQRCodeUrl() }}" target="_blank"
                                               class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                                Test QR
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <div>
                            <span class="text-sm text-gray-600">Reminder Sent:</span>
                            <p class="font-medium">
                                <span class="px-2 py-1 text-xs rounded {{ $tableReservation->reminder_sent ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $tableReservation->reminder_sent ? 'Yes' : 'No' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($tableReservation->notes)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 bg-gray-50 border-b">
                        <h4 class="font-semibold text-gray-800">Internal Notes</h4>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $tableReservation->notes }}</p>
                    </div>
                </div>
                @endif

            </div>

            <!-- Status Timeline -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-gray-50 border-b">
                    <h4 class="font-semibold text-gray-800">Status Timeline</h4>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Reservation created</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $tableReservation->created_at->format('M d, h:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            @if($tableReservation->confirmed_at)
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Reservation confirmed</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $tableReservation->confirmed_at->format('M d, h:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif

                            @if($tableReservation->seated_at)
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Guest seated</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $tableReservation->seated_at->format('M d, h:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif

                            @if($tableReservation->completed_at)
                            <li>
                                <div class="relative">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Reservation completed</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $tableReservation->completed_at->format('M d, h:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Copied!';
                button.classList.remove('bg-green-600', 'hover:bg-green-700');
                button.classList.add('bg-green-800');
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('bg-green-800');
                    button.classList.add('bg-green-600', 'hover:bg-green-700');
                }, 2000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy URL to clipboard');
            });
        }
    </script>
</x-app-layout>
