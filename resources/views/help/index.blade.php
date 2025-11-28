@extends('layouts.app')

@section('title', 'Pusat Bantuan')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8">
            
            <h3 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Pertanyaan yang Sering Diajukan (FAQ)</h3>

            {{-- ================================================= --}}
            {{-- SEKSI 1: UMUM --}}
            {{-- ================================================= --}}
            <div class="mb-8">
                <h4 class="text-xl font-semibold mb-3 text-indigo-600 dark:text-indigo-400 border-b pb-1">
                    1. Penggunaan Umum
                </h4>

                <div class="space-y-4">

                    {{-- FAQ ITEM 1 --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <p class="font-bold text-gray-900 dark:text-gray-100">
                            Apa itu 'Dashboard' dan apa bedanya dengan 'Daftar Tugas'?
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            <strong>Dashboard</strong> (Dasbor) umumnya memberikan ringkasan visual seperti statistik, grafik,
                            atau tugas-tugas yang paling mendesak. <strong>Daftar Tugas</strong> adalah tampilan daftar penuh dari
                            semua tugas Anda, biasanya dengan opsi filter dan pencarian yang lengkap.
                        </p>
                    </div>

                    {{-- FAQ ITEM 2 --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <p class="font-bold text-gray-900 dark:text-gray-100">
                            Bagaimana cara menghubungkan akun Telegram saya?
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Anda dapat menghubungkan Telegram melalui menu <strong>Settings</strong> > <strong>Profile</strong>.
                            Cari bagian "Connect Telegram", ikuti instruksi bot, dan masukkan kode verifikasi untuk mengaktifkan
                            notifikasi tugas.
                        </p>
                    </div>

                </div>
            </div>

            {{-- ================================================= --}}
            {{-- SEKSI 2: PENGELOLAAN TUGAS --}}
            {{-- ================================================= --}}
            <div class="mb-8">
                <h4 class="text-xl font-semibold mb-3 text-indigo-600 dark:text-indigo-400 border-b pb-1">
                    2. Pengelolaan Tugas & Kategori
                </h4>

                <div class="space-y-4">

                    {{-- FAQ ITEM 3 --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <p class="font-bold text-gray-900 dark:text-gray-100">Bagaimana cara membuat tugas baru?</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Klik tautan <strong>Tambah Tugas</strong> di sidebar atau button <strong>+</strong> melayang di pojok kanan bawah. Isi Judul, Kategori dan Tanggal Deadline tugas.
                            Memilih <strong>Kategori</strong> sangat disarankan agar tugas mudah dikelola.
                        </p>
                    </div>

                    {{-- FAQ ITEM 4 --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <p class="font-bold text-gray-900 dark:text-gray-100">Apa fungsi 'Kelola Kategori'?</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Menu ini memungkinkan Anda membuat, mengedit, atau menghapus label seperti "Pekerjaan" atau "Pribadi".
                            Fungsinya untuk memfilter tugas dengan cepat menggunakan chip di sidebar.
                        </p>
                    </div>

                </div>
            </div>

            {{-- ================================================= --}}
            {{-- SEKSI 3: FITUR --}}
            {{-- ================================================= --}}
            <div class="mb-8">
                <h4 class="text-xl font-semibold mb-3 text-indigo-600 dark:text-indigo-400 border-b pb-1">
                    3. Fitur Khusus
                </h4>

                <div class="space-y-4">

                    {{-- FAQ ITEM 5 --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <p class="font-bold text-gray-900 dark:text-gray-100">Apa yang saya dapatkan dari 'Calendar View'?</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            <strong>Calendar View</strong> memberikan gambaran besar tentang jadwal tugas mingguan Anda. Semua tugas di minggu ini akan
                            tampil dalam format kalender mingguan, sangat membantu untuk pengecekan tugas secara berkala.
                        </p>
                    </div>

                    {{-- FAQ ITEM 6 --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <p class="font-bold text-gray-900 dark:text-gray-100">Bagaimana cara kerja 'Timer (Pomodoro)'?</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Fitur ini menggunakan teknik <strong>Pomodoro</strong>: kerja fokus (misalnya 25 menit) diikuti istirahat
                            (misalnya 5 menit). Cocok untuk meningkatkan fokus dan produktivitas.
                        </p>
                    </div>

                </div>
            </div>

            {{-- ================================================= --}}
            {{-- KONTAK --}}
            {{-- ================================================= --}}
            <div>
                <h4 class="text-xl font-semibold mb-3 text-indigo-600 dark:text-indigo-400 border-b pb-1">
                    4. Kontak & Dukungan
                </h4>

                <p class="text-gray-600 dark:text-gray-400">
                    Jika Anda membutuhkan bantuan lebih lanjut, silakan hubungi tim dukungan kami atau laporkan masalah:
                </p>

                <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 mt-3 ml-4">
                    <li>
                        <strong>Email Dukungan:</strong>
                        <a href="mailto:support@taskmanager.com" class="text-indigo-500 hover:underline">
                            SalsaDanShifa@taskmanager.com
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>
@endsection
