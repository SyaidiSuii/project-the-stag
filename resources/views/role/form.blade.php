<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($role->id)
                {{ __('Edit Role') }}
            @else
                {{ __('Create Role') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        @if($role->id)
                            {{ __('Edit Role Information') }}
                        @else
                            {{ __('Role Information') }}
                        @endif
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __("Role information and permissions.") }}
                    </p>
                </header>

                @if($role->id)
                    <form method="post" action="{{ route('role.update', $role->id) }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="PUT">
                @else
                    <form method="post" action="{{ route('role.store') }}" class="mt-6 space-y-6">
                        <input type="hidden" name="_method" value="POST">
                @endif
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Role Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $role->name)" placeholder="e.g. Manager, Cashier, Chef"/>
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="3" 
                                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                  placeholder="Describe the role responsibilities...">{{ old('description', $role->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div>
                        <x-input-label for="permissions" :value="__('Permissions')" />
                        <div class="mt-2 space-y-2">
                            @php
                                $availablePermissions = [
                                    'users.view' => 'View Users',
                                    'users.create' => 'Create Users',
                                    'users.edit' => 'Edit Users',
                                    'users.delete' => 'Delete Users',
                                    'orders.view' => 'View Orders',
                                    'orders.create' => 'Create Orders',
                                    'orders.edit' => 'Edit Orders',
                                    'orders.delete' => 'Delete Orders',
                                    'products.view' => 'View Products',
                                    'products.create' => 'Create Products',
                                    'products.edit' => 'Edit Products',
                                    'products.delete' => 'Delete Products',
                                    'reports.view' => 'View Reports',
                                    'settings.view' => 'View Settings',
                                    'settings.edit' => 'Edit Settings',
                                ];
                                
                                $rolePermissions = [];
                                if (old('permissions')) {
                                    $rolePermissions = old('permissions');
                                } elseif ($role->permissions) {
                                    if (is_array($role->permissions)) {
                                        $rolePermissions = $role->permissions;
                                    } else {
                                        $rolePermissions = json_decode($role->permissions, true) ?: [];
                                    }
                                }
                            @endphp
                            
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($availablePermissions as $permission => $label)
                                <label class="flex items-center">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission }}" 
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                           @if(in_array($permission, $rolePermissions)) checked @endif>
                                    <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('permissions')" />
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                               @if(old('is_active', $role->is_active ?? true)) checked @endif>
                        <label for="is_active" class="ml-2 text-sm text-gray-700">{{ __('Active Role') }}</label>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>

                        <a href="{{ route('role.index', ['cancel' => 'true']) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
