@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')

<div class="max-w-xl mx-auto py-10">
    <h1 class="text-3xl font-bold mb-8 text-gray-800 dark:text-gray-100">⚙️ Pengaturan Profil</h1>

    {{-- Notifikasi Status (Success/Error) --}}
    @if (session('status'))
        <div class="bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 p-4 rounded-lg mb-6 border border-green-200 dark:border-green-800 font-medium">
            {{ session('status') }}
        </div>
    @endif
    
    {{-- Notifikasi Error Umum --}}
    @if ($errors->any())
        <div class="bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 p-4 rounded-lg mb-6 border border-red-200 dark:border-red-800 font-medium">
            Terdapat beberapa kesalahan dalam input Anda. Mohon periksa kembali.
        </div>
    @endif

    {{-- 1. BAGIAN UPDATE PROFIL (Nama & Email) --}}
    <div class="p-6 bg-white dark:bg-gray-800 shadow-xl rounded-lg mb-8">
        <h2 class="text-2xl font-semibold mb-4 border-b pb-2 text-gray-700 dark:text-gray-200">Informasi Dasar</h2>
        <p class="mb-4 text-gray-500 dark:text-gray-400">Perbarui informasi akun Anda.</p>
        
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            {{-- Menggunakan method PATCH (atau PUT) disarankan untuk update resource --}}
            @method('PATCH')

            <div class="mb-4">
                <label for="name" class="block mb-1 font-medium text-gray-600 dark:text-gray-300">Nama</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" 
                       class="w-full border rounded-lg p-3 bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 text-gray-800 dark:text-gray-100 @error('name') border-red-500 @enderror">
                @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label for="email" class="block mb-1 font-medium text-gray-600 dark:text-gray-300">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" 
                       class="w-full border rounded-lg p-3 bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 text-gray-800 dark:text-gray-100 @error('email') border-red-500 @enderror">
                @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="bg-indigo-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-indigo-700 transition duration-150 shadow-md">
                Simpan Perubahan
            </button>
        </form>
    </div>

    <hr class="border-gray-300 dark:border-gray-700 my-8">

    {{-- 2. BAGIAN UPDATE PASSWORD --}}
    <div class="p-6 bg-white dark:bg-gray-800 shadow-xl rounded-lg mb-8">
        <h2 class="text-2xl font-semibold mb-4 border-b pb-2 text-gray-700 dark:text-gray-200">Ganti Password</h2>
        <p class="mb-4 text-gray-500 dark:text-gray-400">Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.</p>
        
        {{-- ASUMSI ROUTE ADALAH 'password.update' --}}
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT') 

            <div class="mb-4">
                <label for="current_password" class="block mb-1 font-medium text-gray-600 dark:text-gray-300">Password Saat Ini</label>
                <input id="current_password" type="password" name="current_password" 
                       class="w-full border rounded-lg p-3 bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 text-gray-800 dark:text-gray-100 @error('current_password') border-red-500 @enderror">
                @error('current_password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block mb-1 font-medium text-gray-600 dark:text-gray-300">Password Baru</label>
                <input id="password" type="password" name="password" 
                       class="w-full border rounded-lg p-3 bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 text-gray-800 dark:text-gray-100 @error('password') border-red-500 @enderror">
                @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-6">
                <label for="password_confirmation" class="block mb-1 font-medium text-gray-600 dark:text-gray-300">Konfirmasi Password Baru</label>
                <input id="password_confirmation" type="password" name="password_confirmation" 
                       class="w-full border rounded-lg p-3 bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 text-gray-800 dark:text-gray-100">
            </div>

            <button type="submit" class="bg-indigo-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-indigo-700 transition duration-150 shadow-md">
                Ganti Password
            </button>
        </form>
    </div>

    <hr class="border-gray-300 dark:border-gray-700 my-8">

    {{-- 3. BAGIAN CONNECT TELEGRAM --}}
    <div id="connect-telegram" class="p-6 bg-white dark:bg-gray-800 shadow-xl rounded-lg">
        <h2 class="text-2xl font-semibold mb-4 border-b pb-2 text-gray-700 dark:text-gray-200">Koneksi Telegram</h2>
        <p class="mb-4 text-gray-500 dark:text-gray-400">Hubungkan Telegram Anda untuk menerima notifikasi tugas langsung di ponsel Anda.</p>
        
        {{-- ASUMSI PARTIAL INI ADA DAN MENAMPILKAN FORMULIR KONEKSI --}}
        {{-- Perhatikan: Saya menggunakan komponen @include() yang Anda sebutkan di kode awal --}}
        @include('profile.partials.connect-telegram-section')
    </div>

</div>
@endsection