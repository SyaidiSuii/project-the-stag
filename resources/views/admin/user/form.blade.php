<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Information') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-200">
                        {{ __('User Information') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600 ">
                        {{ __("Your user information.") }}
                    </p>
                </header>

                @if($user->id)
                    @php($route = route('admin.user.update', $user->id))
                    @php($method = 'PUT')
                @else
                    @php($route = route('admin.user.store'))
                    @php($method = 'POST')
                @endif

                <form method="post" action="{{ $route }}" class="mt-6 space-y-6">
                    <input type="hidden" name="_method" value="{{ $method }}">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)"/>
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="text" class="mt-1 block w-full" :value="old('email', $user->email)"/>
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div>
                        <x-input-label for="phone_number" :value="__('Phone Number')" />
                        <x-text-input id="phone_number" name="phone_number" type="text" placeholder="012345678910" class="mt-1 block w-full" :value="old('phone_number', $user->phone_number)"/>
                        <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Roles') }}</label>
                        <div class="mt-2 space-y-2">
                            @foreach($roles as $role)
                                <div class="flex items-center">
                                    <input type="checkbox" name="roles[]"
                                        value="{{ $role->id }}" id="role_{{ $role->id }}"
                                        @checked(in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())))
                                        class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                                    <label for="role_{{ $role->id }}"
                                        class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $role->name }}</label>
                                </div>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                    </div>


                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>

                        <a href="{{ route('admin.user.index', ['cancel' => 'true']) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                    </div>
                </form>
</section>
    
        </div>
    </div>
</x-app-layout>

