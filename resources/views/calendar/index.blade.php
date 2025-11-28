@php
    use Carbon\Carbon;
@endphp

@extends('layouts.app')

@section('title', 'Calendar View')

@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Menggunakan kelas Tailwind default, ganti 'bg-putih-kustom' dan 'dark:bg-coklat' jika tidak didefinisikan
            --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                <h3 class="text-2xl font-bold text-coklat-800 dark:text-white mb-6 text-center">
                    JADWAL MINGGU INI
                </h3>

                {{-- Header Hari: Tampilkan nama hari dan tanggal --}}
                <div class="grid grid-cols-7 gap-1 border-b border-gray-300 dark:border-gray-600 mb-4">
                    @foreach ($dates as $date)
                        <div class="text-center pb-2">
                            <p class="text-sm font-semibold uppercase text-gray-700 dark:text-white">
                                {{ $date->isoFormat('ddd') }}
                            </p>
                            {{-- Menggunakan warna kustom yang dilewatkan dari Controller --}}
                            <p class="text-3xl font-bold text-{{ $primaryColor }} dark:text-{{ $secondaryColor }}">
                                {{ $date->isoFormat('D') }}
                            </p>
                        </div>
                    @endforeach
                </div>


                {{-- Body Kalender: Tampilkan Tugas --}}
                <div class="grid grid-cols-7 gap-1 min-h-[500px]">
                    @foreach ($dates as $date)
                        <div class="p-2 border-r border-gray-200 dark:border-gray-700 last:border-r-0 space-y-2">
                            {{-- Filter tugas untuk hari ini --}}
                            @php
                                // Catatan: $todos yang masuk ke sini sudah HANYA milik user yang login (difilter di Controller)
                                $dailyTodos = $todos->filter(fn($todo) => $todo->deadline && $date->isSameDay(new Carbon($todo->deadline)));
                            @endphp

                            @forelse ($dailyTodos as $todo)
                                {{-- Menggunakan warna kustom untuk kotak tugas --}}
                                <div
                                    class="p-2 rounded-lg text-xs shadow-md 
                                        bg-{{ $secondaryColor }} bg-opacity-80 text-gray-800 dark:text-white dark:bg-{{ $primaryColor }}">
                                    <p class="font-bold">{{ $todo->title }}</p>
                                    @if ($todo->deadline)
                                        {{-- Menggunakan kelas kustom 'text-xxs' untuk font super kecil,
                                        jika tidak ada di Tailwind, itu akan diabaikan atau perlu ditambahkan --}}
                                        <p class="text-xs opacity-80">{{ (new Carbon($todo->deadline))->isoFormat('HH:mm') }}</p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 dark:text-gray-500 italic">Tidak ada tugas</p>
                            @endforelse
                        </div>
                    @endforeach
                </div>

            </div>
        </div>


    </div>

@endsection