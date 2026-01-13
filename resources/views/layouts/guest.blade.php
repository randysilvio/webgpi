<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Ambil nama situs dari config atau fallback --}}
        <title>{{ config('app.name', 'Sinode GPI Papua') }} - Login</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{-- Pastikan @vite memuat CSS yang benar --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Tambahkan style untuk background jika perlu --}}
        <style>
            body {
                /* Contoh background gradient */
                /* background: linear-gradient(to bottom right, #eff6ff, #e0e7ff); */
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        {{-- Container utama --}}
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900"> {{-- Sesuaikan background jika perlu --}}

            {{-- == BAGIAN YANG DIUBAH == --}}
            {{-- Tampilkan slot 'logo' yang diisi dari login.blade.php --}}
            @if (isset($logo))
                <div>
                    {{ $logo }} {{-- Ini akan merender konten <x-slot name="logo"> dari login.blade.php --}}
                </div>
            @endif
            {{-- == AKHIR BAGIAN YANG DIUBAH == --}}

            {{-- Card container untuk form --}}
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }} {{-- Ini akan merender konten utama dari login.blade.php (formnya) --}}
            </div>
        </div>
    </body>
</html>