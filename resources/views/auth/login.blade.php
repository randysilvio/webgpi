{{-- Ambil data setting --}}
@php
    $setting = \App\Models\Setting::firstOrCreate(
        ['id' => 1],
        ['site_name' => 'Sinode GPI Papua', 'logo_path' => null]
    );

    // (Baru) Query gambar berita terakhir yang published dan punya gambar
    $latestPostWithImage = \App\Models\Post::whereNotNull('published_at')
                            ->where('published_at', '<=', now())
                            ->whereNotNull('image_path') // Pastikan ada gambar
                            ->latest('published_at') // Urutkan terbaru
                            ->first();

    $backgroundImageUrl = null;
    if ($latestPostWithImage && Storage::disk('public')->exists($latestPostWithImage->image_path)) {
        $backgroundImageUrl = Storage::url($latestPostWithImage->image_path);
    }
@endphp

{{-- Kirim URL gambar background ke layout --}}
<x-guest-layout :background="$backgroundImageUrl"> {{-- <<< Tambahkan :background --}}

    {{-- Slot Logo (Kode tetap sama) --}}
    <x-slot name="logo">
        <a href="/" class="flex flex-col items-center space-y-3">
             @if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path))
                 <img src="{{ Storage::url($setting->logo_path) }}" alt="{{ $setting->site_name ?? 'Logo GPI Papua' }}" class="w-24 h-24 object-contain fill-current text-gray-500">
             @else
                 <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-200 rounded-full flex items-center justify-center text-primary shadow-inner">
                    <svg class="w-16 h-16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"> <path d="M12 2L3 7v13h18V7L12 2zm0 2.236L18.99 8H5.01L12 4.236zM5 18V9.618l7 4.118 7-4.118V18H5z"/> </svg>
                 </div>
             @endif
             <h1 class="text-gray-800 text-2xl font-semibold text-center">{{ $setting->site_name ?? 'Sinode GPI Papua' }}</h1>
             <p class="text-gray-500 text-sm">Silakan login untuk mengakses area admin</p>
        </a>
    </x-slot>

    {{-- Card Form Login --}}
    {{-- Tambahkan bg-opacity agar form sedikit transparan di atas background --}}
    <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white bg-opacity-95 dark:bg-gray-800 dark:bg-opacity-95 shadow-xl rounded-lg overflow-hidden border border-gray-200">

        <x-auth-session-status class="mb-5 text-sm font-medium text-green-600 bg-green-50 p-3 rounded-md border border-green-200" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            {{-- Input Email --}}
            <div>
                <x-input-label for="email" value="Alamat Email" class="font-medium" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com"/>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
            </div>

            {{-- Input Password --}}
            <div class="mt-4">
                <x-input-label for="password" value="Password" class="font-medium"/>
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" placeholder="********"/>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs" />
            </div>

            {{-- Remember Me --}}
            <div class="block">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary focus:ring-opacity-50" name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('Ingat Saya') }}</span>
                </label>
            </div>

            {{-- Forgot Password & Tombol Login --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-primary rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @else <span></span> @endif

                <x-primary-button class="ml-3 bg-primary hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:ring-blue-500 inline-flex items-center">
                     <svg class="w-4 h-4 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    {{ __('Login') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>