<!DOCTYPE html>
<html lang="en" class="{{ Auth::user() && Auth::user()->theme === 'dark' ? 'dark' : '' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'List Me')</title>
    {{-- Menghubungkan file CSS dan JS melalui Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

{{-- Kelas pada body memastikan penggunaan font, anti-aliasing, dan pengaturan warna latar/teks yang responsif terhadap dark/light mode --}}
<body class="font-sans antialiased bg-pink-pucat dark:bg-gray-900 text-gray-800 dark:text-gray-100">

    <!-- 
        Div pembungkus utama yang mengelola state Alpine 'sidebarOpen'. 
        Nilai awal: true jika lebar >= 1024px (desktop), false jika mobile.
    -->
    <div x-data="{ sidebarOpen: window.innerWidth >= 1024 }" 
        x-init="
            document.addEventListener('sidebar-toggle', (e) => {
                sidebarOpen = e.detail.open;
            });
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    sidebarOpen = true; 
                } else if(window.innerWidth < 1024 && sidebarOpen) {
                }
            });
          "
          class="min-h-screen">
        
        <!-- Navigation bar -->
        @include('layouts.navigation')

        {{-- Main content --}}
        <main 
            class="max-w-7xl mx-auto px-4 pt-24 transition-all duration-300"
            :class="{
                'lg:ml-72': sidebarOpen,
                'lg:ml-0': !sidebarOpen
            }">
            @yield('content')
        </main>
    </div>

</body>

</html>