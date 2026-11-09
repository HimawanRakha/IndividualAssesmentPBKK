<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    {{-- Login instan akun uji untuk demo kelas; disembunyikan bila fitur dimatikan atau di production. --}}
    @if (config('demo.enabled') && ! app()->isProduction())
        <div class="mt-6 border-t border-gray-200 pt-5">
            <p class="text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                Login instan akun uji (demo)
            </p>

            <div class="mt-3 grid gap-2">
                @foreach (config('demo.accounts') as $kunci => $akun)
                    <form method="POST" action="{{ route('demo-login', $kunci) }}">
                        @csrf
                        <button type="submit" @class([
                            'w-full rounded-md border px-4 py-2 text-left text-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2',
                            'border-red-200 bg-red-50 hover:bg-red-100 focus:ring-red-500' => $akun['role'] === 'Admin',
                            'border-amber-200 bg-amber-50 hover:bg-amber-100 focus:ring-amber-500' => $akun['role'] === 'Staf',
                            'border-gray-200 bg-gray-50 hover:bg-gray-100 focus:ring-gray-400' => $akun['role'] === null,
                        ])>
                            <span class="block font-semibold text-gray-800">
                                Masuk sebagai {{ $akun['role'] ?? 'Mahasiswa' }}
                                <span class="font-normal text-gray-500">· {{ $akun['email'] }}</span>
                            </span>
                            <span class="block text-xs text-gray-600">{{ $akun['description'] }}</span>
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    @endif
</x-guest-layout>
