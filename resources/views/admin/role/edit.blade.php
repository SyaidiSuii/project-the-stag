<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                {{ __('Edit Role') }}
            </h2>
        </div>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6">
<form method="POST" action="{{ route('admin.role.update', $role) }}" class="space-y-6">
    @csrf
    @method('PUT')


    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Role Name') }}</label>
        <x-text-input id="name" name="name" type="text"
            class="mt-1 block w-full"
            :value="old('name', $role->name)"
            required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>


    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Description') }}</label>
        <x-text-input id="description" name="description" type="text"
            class="mt-1 block w-full"
            :value="old('description', $role->description)" />
         <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>


    <div class="flex gap-4">
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">{{ __('Update Role') }}</button>
        <a href="{{ route('admin.role.index') }}"
            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">{{ __('Cancel') }}</a>
    </div>
</form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>