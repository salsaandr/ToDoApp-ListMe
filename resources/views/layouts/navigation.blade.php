<div x-data="{ 
    open: false, // State untuk responsive menu navbar mobile (hamburger)
    sidebarOpen: window.innerWidth >= 1024, // State untuk sidebar (true jika desktop)
    // --- BARU: Alpine.js untuk Notifikasi ---
    notificationOpen: false,
    notifications: [],
    notificationCount: 0,
    loading: true,
    
    // Fungsi untuk ambil notifikasi
    fetchNotifications() {
        // HANYA ambil data jika user sudah login (handle jika ada case tertentu)
        if (!'{{ Auth::check() }}') return; 
        
        fetch('{{ route('notifications.get') }}')
            .then(response => {
                // Tambahkan check response 401/403 jika diperlukan, tapi route sudah di-middleware('auth')
                if (!response.ok) {
                    throw new Error('Gagal mengambil notifikasi');
                }
                return response.json();
            })
            .then(data => {
                this.notifications = data.notifications;
                this.notificationCount = data.count;
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
                this.notifications = [];
                this.notificationCount = 0;
            })
            .finally(() => this.loading = false);
    },
    // Fungsi untuk menandai notifikasi sudah dilihat (opsional)
    markAsSeen() {
        // Saat dropdown ditutup, kita anggap notifikasi sudah dilihat.
        this.notificationCount = 0; 
    }
    // ----------------------------------------------------
}" x-init="
    // PENTING: Dispatch event saat halaman dimuat untuk menyinkronkan 
    // status awal sidebar ke app.blade.php
    $dispatch('sidebar-toggle', { open: sidebarOpen });

    // Listener untuk mengunci/membuka sidebar saat resize
    window.addEventListener('resize', () => {
        // Pada desktop (>1024), kita memastikan sidebar harus terbuka secara default.
        if (window.innerWidth >= 1024) {
            if (!sidebarOpen) {
                sidebarOpen = true; 
                $dispatch('sidebar-toggle', { open: true });
            }
        } 
    });
    
    // --- BARU: Ambil notifikasi saat inisialisasi dan setiap 60 detik ---
    fetchNotifications();
    setInterval(() => fetchNotifications(), 60000); // Poll setiap 1 menit
    // ------------------------------------------------------------------
">
    {{-- NAVIGASI UTAMA (FIXED TOP) --}}
    <nav class="bg-ungu-kustom border-b border-ungu-kustom shadow-xl fixed top-0 left-0 right-0 z-40">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">

                    <div class="shrink-0 flex items-center me-4">
                        <button @click.prevent="
                                // 1. Toggle state lokal
                                sidebarOpen = ! sidebarOpen;
                                // 2. KIRIM EVENT KE app.blade.php UNTUK MENGGESER KONTEN UTAMA
                                $dispatch('sidebar-toggle', { open: sidebarOpen });
                            "
                            class="flex items-center space-x-2 p-1.5 rounded-lg text-putih-kustom hover:opacity-80 transition duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-ungu-kustom focus:ring-putih-kustom"
                            aria-controls="off-canvas-sidebar" aria-expanded="sidebarOpen ? 'true' : 'false'">

                            <div x-show="sidebarOpen"
                                class="flex items-center justify-center h-10 w-10 bg-white/20 rounded-lg">
                                <svg class="h-6 w-6 text-putih-kustom" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>

                            <div x-show="!sidebarOpen">
                                <img src="{{ asset('images/logo-nav.png') }}" alt="ListMeApp Logo"
                                    class="block h-10 w-auto rounded-lg">
                            </div>
                        </button>
                    </div>
                </div>

                {{-- PERBAIKAN h1 (Lebih Estetik) --}}
                <h1 class="text-4xl font-extrabold text-white self-center 
                           tracking-tight
                           hover:text-pink-pucat transition duration-300 ease-in-out"
                    style="font-family: 'Poppins', sans-serif;">
                    ListMe
                </h1>
                
                
                {{-- TANGGAL --}}
                <div class="flex items-center space-x-4">
                    <div class="hidden sm:flex flex-col items-end text-sm text-white/90">
                        <span class="font-semibold">{{ \Carbon\Carbon::now()->format('l') }}</span>
                        <span class="text-xs font-light text-putih-kustom/70">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</span>
                    </div>
                    
                    <div class="-me-2 flex items-center sm:hidden">
                        <button @click="open = ! open"
                            class="inline-flex items-center justify-center p-2 rounded-md text-putih-kustom hover:text-pink-pucat hover:bg-ungu-kustom focus:outline-none focus:bg-ungu-kustom focus:text-pink-pucat transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white">
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            </div>

            <div class="pt-4 pb-1 border-t border-putih-kustom">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    @if (!Auth::user()->telegram_chat_id)
                        <x-responsive-nav-link :href="route('profile.edit') . '#connect-telegram'"
                            class="text-blue-600 hover:text-blue-800">
                            Connect Telegram
                        </x-responsive-nav-link>
                    @else
                        <span class="px-4 py-2 text-green-600 block font-medium">
                            ✓ Telegram Connected
                        </span>
                    @endif

                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- ================================================= --}}
    {{-- CONTAINER SIDEBAR (Fixed / Off-Canvas) --}}
    {{-- ================================================= --}}

    <div x-show="sidebarOpen && window.innerWidth < 1024" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="
            sidebarOpen = false;
            // Kirim event penutup ke app.blade.php (walaupun di mobile tidak memengaruhi margin, tapi bagus untuk konsistensi)
            $dispatch('sidebar-toggle', { open: false });
          " class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden">
    </div>

    <aside id="main-sidebar" x-show="sidebarOpen" {{-- Menggunakan Tailwind fixed untuk off-canvas --}}
        class="fixed top-16 left-0 h-[calc(100vh-4rem)] w-72 bg-white dark:bg-gray-800 z-30 shadow-xl overflow-y-auto border-r border-gray-200 dark:border-gray-700"
        :class="{
            // Transisi untuk Off-Canvas (Mobile/Terkunci)
            'transform -translate-x-full transition duration-300 ease-in-out': !sidebarOpen && window.innerWidth < 1024,
            'transform translate-x-0 transition duration-300 ease-in-out': sidebarOpen && window.innerWidth < 1024,
            // Mode Desktop (Terbuka Default)
            'transform translate-x-0 transition duration-0': sidebarOpen && window.innerWidth >= 1024,
        }" x-cloak>

        {{-- PENTING: INCLUDE FILE SIDEBAR DI SINI --}}
        @include('layouts.sidebar')
    </aside>

    {{-- Catatan: Logika pemindahan konten sudah ada di app.blade.php. --}}
</div>