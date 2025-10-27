<x-guest-layout>
    {{-- Ambil data setting (asumsi ID setting adalah 1) --}}
    @php
        $setting = \App\Models\Setting::first(); // Ambil data setting
    @endphp

    {{-- Slot Logo di Guest Layout --}}
    <x-slot name="logo">
        <a href="/" class="flex flex-col items-center">
             {{-- Tampilkan logo jika ada path di setting --}}
             @if ($setting && $setting->logo_path && Storage::disk('public')->exists($setting->logo_path))
                 <img src="{{ Storage::url($setting->logo_path) }}" alt="{{ $setting->site_name ?? 'Logo GPI Papua' }}" class="w-20 h-20 fill-current text-gray-500 mb-2">
             @else
                 {{-- Fallback jika logo tidak ada: Tampilkan ikon/SVG default Breeze atau ikon gereja --}}
                 <svg class="w-20 h-20 text-gray-500 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"> <path d="M12 2L3 7v13h18V7L12 2zm0 2.236L18.99 8H5.01L12 4.236zM5 18V9.618l7 4.118 7-4.118V18H5z"/> </svg>
             @endif
             {{-- Tampilkan Nama Situs jika ada --}}
             @if ($setting && $setting->site_name)
                 <span class="text-gray-700 text-lg font-semibold mt-1">{{ $setting->site_name }}</span>
             @else
                 <span class="text-gray-700 text-lg font-semibold mt-1">Sinode GPI Papua</span>
             @endif
              <span class="text-gray-500 text-sm">Admin Login</span>
        </a>
    </x-slot>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="********"/>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary focus:ring-opacity-50" name="remember"> {{-- Ubah warna focus --}}
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat Saya') }}</span> {{-- Terjemahkan --}}
            </label>
        </div>

        <div class="flex items-center justify-between mt-6"> {{-- Ubah justify-end ke justify-between --}}
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" href="{{ route('password.request') }}"> {{-- Ubah warna focus --}}
                    {{ __('Lupa password?') }} {{-- Terjemahkan --}}
                </a>
            @else
                <span></span> {{-- Placeholder agar tombol tetap di kanan --}}
            @endif

             {{-- Ganti warna button --}}
            <x-primary-button class="ms-3 bg-primary hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:ring-blue-500">
                {{ __('Login') }} {{-- Terjemahkan --}}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>