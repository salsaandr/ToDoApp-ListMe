<div class="bg-white dark:bg-gray-800 h-full">

    @php
    $primaryColor = $primaryColor ?? 'ungu-kustom'; 
    $secondaryColor = $secondaryColor ?? 'pink-kustom';
    @endphp
{{-- ================================================= --}}
{{-- BLOK TELEGRAM STATUS --}}
{{-- ================================================= --}}
@auth
    <div class="px-6 pt-4 pb-4 mb-4 border-b border-gray-200 dark:border-gray-700">
        @if (!Auth::user()->telegram_chat_id)
            {{-- Tombol Connect Telegram - Menggunakan primaryColor --}}
            <a href="{{ route('profile.edit') }}#connect-telegram"
                class="block w-full text-center px-4 py-2 bg-{{ $secondaryColor }} text-white rounded-lg text-sm font-bold hover:bg-{{ $primaryColor }} transition duration-150 shadow-md">
                Connect Telegram
            </a>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">Hubungkan untuk notifikasi tugas.</p>
        @else
            {{-- Status Connected - Menggunakan warna hijau standar (umum untuk sukses) --}}
            <span class="block w-full text-center px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-bold shadow-md">
                <svg class="w-4 h-4 inline-block me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                    </path>
                </svg> Telegram Connected
            </span>
            <p class="text-xs text-green-600 dark:text-green-400 mt-2 text-center">Notifikasi aktif.</p>
        @endif
    </div>
@endauth

{{-- ================================================= --}}
{{-- NAVIGASI UTAMA & TODOS --}}
{{-- ================================================= --}}
<div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700 px-2">
    
    @php
        // --- Variabel Kelas Aktif/Tidak Aktif ---
        $activeClassesOnly = 'bg-' . $primaryColor . ' dark:bg-' . $secondaryColor . ' text-white dark:text-gray-800'; 
        $inactiveClassesOnly = 'text-gray-700 dark:text-gray-200 hover:bg-' . $primaryColor . ' hover:bg-opacity-10 dark:hover:bg-' . $secondaryColor . ' dark:hover:bg-opacity-10'; 
        
        $baseClasses = 'flex items-center w-full px-4 py-3 text-sm font-semibold rounded-lg transition duration-150 ease-in-out';

        $isActive = fn($name) => request()->routeIs($name) ? $activeClassesOnly : $inactiveClassesOnly;

        // ------------------------------------------------------------------------------------------------
        // LOGIKA STATUS AKTIF BARU UNTUK MEMBEDAKAN DASHBOARD DAN DAFTAR TUGAS
        // ------------------------------------------------------------------------------------------------

        $hasFilters = request('category') || request('search');

        // Dashboard aktif HANYA jika berada di route 'dashboard' dan TIDAK ada filter/search
        $isDashboard = request()->routeIs('dashboard') && !$hasFilters;

        // Daftar Tugas aktif jika berada di route 'todos.list' ATAU jika berada di 'dashboard' TAPI ada filter/search
        $isTodosList = request()->routeIs('todos.list') || (request()->routeIs('dashboard') && $hasFilters);

    @endphp

    {{-- 1. Dashboard Murni --}}
    <a href="{{ route('dashboard') }}" 
        class="{{ $baseClasses }} {{ $isDashboard ? $activeClassesOnly : $inactiveClassesOnly }} mt-1">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-14 0v10a1 1 0 001 1h10a1 1 0 001-1v-10"></path></svg>
        Dashboard
    </a>

    {{-- 2. Tambah Tugas --}}
    <a href="{{ route('todos.create') }}" class="{{ $baseClasses }} {{ $isActive('todos.create') }} mt-1">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Tambah Tugas
    </a>
    
    {{-- 3. Kelola Kategori --}}
    <a href="{{ route('categories.index') }}" class="{{ $baseClasses }} {{ $isActive('categories.index') }} mt-1">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        Kelola Kategori
    </a>

</div>

{{-- ================================================= --}}
{{-- FILTER KATEGORI --}}
{{-- ================================================= --}}
<p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 px-6">FILTER KATEGORI</p>

<div class="flex flex-wrap gap-2 mb-4 pb-4 border-b border-gray-200 dark:border-gray-700 px-6">
    @php
        $activeCategoryId = (int)request('category');
        $currentSearch = request('search');
        
        // Cek status aktif untuk tombol "Semua Tugas" (menggunakan route todos.list sebagai basis filter)
        $isAllActive = empty($activeCategoryId) && (request()->routeIs('dashboard') || request()->routeIs('todos.list'));
        
        // Warna Aktif: primaryColor, Warna Tidak Aktif: secondaryColor (dengan hover primaryColor)
        $allClasses = $isAllActive 
            ? 'bg-' . $primaryColor . ' text-white' 
            : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-' . $secondaryColor . ' hover:text-white';
    @endphp

    {{-- Tombol "Semua Tugas" --}}
    {{-- Arahkan ke todos.list untuk mempertahankan tampilan list --}}
    <a href="{{ route('todos.list', ['search' => $currentSearch]) }}" 
        class="text-xs px-3 py-1 rounded-full transition duration-150 ease-in-out font-semibold {{ $allClasses }}">
        Semua Tugas
    </a>

    {{-- Loop Filter Kategori --}}
    @foreach ($categories ?? [] as $cat)
        @php
            $isCatActive = ($activeCategoryId === $cat->id);
            // Warna Aktif: primaryColor, Warna Tidak Aktif: secondaryColor (dengan hover primaryColor)
            $catClasses = $isCatActive 
                ? 'bg-' . $primaryColor . ' text-white' 
                : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-' . $secondaryColor . ' hover:text-white';
            
            // Menggunakan todos.list untuk filter
            $filterUrl = route('todos.list', ['category' => $cat->id, 'search' => $currentSearch]);
        @endphp
        <a href="{{ $filterUrl }}" 
            class="text-xs px-3 py-1 rounded-full transition duration-150 ease-in-out font-semibold {{ $catClasses }}">
            {{ $cat->name }}
        </a>
    @endforeach
</div>

{{-- ================================================= --}}
{{-- NAVIGASI FITUR LAIN --}}
{{-- ================================================= --}}
<div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700 px-2">
    
    {{-- 4. Calendar View --}}
    <a href="{{ route('calendar') }}" class="{{ $baseClasses }} {{ $isActive('calendar') }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-4 4V3m-4 12v-6m0 0h.01M16 15v-6m0 0h.01M12 21h.01M3 17a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2z"></path></svg>
        Calendar View
    </a>

    {{-- 5.  Timer (Pomodoro) --}}
    <a href="{{ route('timer') }}" class="{{ $baseClasses }} {{ $isActive('timer') }} mt-1">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Timer (Pomodoro)
    </a>
</div>

{{-- ================================================= --}}
{{-- SETTINGS & LOGOUT --}}
{{-- ================================================= --}}
<div class="px-2">
    {{-- Settings (Profile Edit) --}}
    <a href="{{ route('profile.edit') }}" class="{{ $baseClasses }} {{ $isActive('profile.edit') }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        Settings
    </a>

    {{-- Help --}}
    <a href="{{ route('help') }}" class="{{ $baseClasses }} {{ $isActive('help') }} mt-1">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9.924a4.002 4.002 0 004.34-4.34M12 21a9 9 0 110-18 9 9 0 010 18zm0-10a1 1 0 100-2 1 1 0 000 2z"></path></svg>
        Help
    </a>

    {{-- Logout (Tetap warna merah untuk tindakan bahaya) --}}
    <form method="POST" action="{{ route('logout') }}" class="mt-1">
        @csrf
        <a href="{{ route('logout') }}" 
            onclick="event.preventDefault(); this.closest('form').submit();"
            class="{{ $baseClasses }} text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            Log Out
        </a>
    </form>
</div>


</div>