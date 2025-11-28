@extends('layouts.app')

@section('title', 'Pomodoro Timer')

@section('content')

<!-- Pastikan warna kustom Tailwind Anda telah didefinisikan (misalnya di tailwind.config.js) -->
<!-- Mengganti bg-putih-kustom dan dark:bg-coklat dengan default jika tidak terdefinisi -->
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-10 flex flex-col items-center">

            <h3 class="text-3xl font-extrabold text-gray-800 dark:text-white mb-8">
                POMODORO TIMER
            </h3>

            {{-- Timer Display --}}
            <div id="timer-display" 
                 class="relative w-72 h-72 rounded-full flex flex-col items-center justify-center 
                        bg-gray-100 dark:bg-gray-700 shadow-2xl p-4 border-8 border-{{ $primaryColor }} dark:border-{{ $secondaryColor }}">
                
                {{-- Status Text (Menggantikan alert()) --}}
                <span id="timer-status" class="text-sm font-medium mb-2 text-gray-500 dark:text-gray-300">
                    Waktu Fokus
                </span>

                <span id="time-left" 
                      class="text-6xl font-extrabold text-gray-800 dark:text-white">
                      25:00
                </span>
                
                {{-- Play/Pause Button --}}
                <button id="start-pause-btn" 
                        class="mt-4 bg-{{ $secondaryColor }} hover:bg-{{ $primaryColor }} p-4 rounded-full text-white shadow-xl transition duration-300 transform hover:scale-105 active:scale-95 focus:outline-none focus:ring-4 focus:ring-{{ $secondaryColor }} focus:ring-opacity-50">
                    {{-- Ikon Play --}}
                    <svg id="start-icon" class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                    {{-- Ikon Pause --}}
                    <svg id="pause-icon" class="w-8 h-8 hidden" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                    </svg>
                </button>
            </div>
            
            
            {{-- Control Buttons --}}
            <div class="mt-10 flex space-x-4 p-3 border-4 border-{{ $primaryColor }} dark:border-{{ $secondaryColor }} rounded-xl">
                {{-- SHORT BREAK --}}
                <button id="short-break-btn" 
                        class="px-6 py-2 rounded-lg text-sm font-semibold transition duration-300 shadow-md">
                    Short Break (5:00)
                </button>
                
                {{-- FOCUS (Default Aktif) --}}
                <button id="focus-btn" 
                        class="px-6 py-2 rounded-lg text-sm font-semibold transition duration-300 shadow-md">
                    Focus (25:00)
                </button>
                
                {{-- LONG BREAK --}}
                <button id="long-break-btn" 
                        class="px-6 py-2 rounded-lg text-sm font-semibold transition duration-300 shadow-md">
                    Long Break (15:00)
                </button>
            </div>

            {{-- Button Reset (Tambahan untuk kontrol yang lebih baik) --}}
            <button id="reset-btn"
                    class="mt-6 px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition duration-300">
                    Reset
            </button>

        </div>
    </div>
</div>

{{-- Script untuk Logika Timer --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- Konfigurasi Timer ---
        const TIMER_DURATIONS = {
            'focus': 25 * 60,
            'shortBreak': 5 * 60,
            'longBreak': 15 * 60
        };

        // --- Variabel State ---
        let timer;
        let currentMode = 'focus';
        let timeRemaining = TIMER_DURATIONS[currentMode];
        let isRunning = false;

        // --- Elemen DOM ---
        const timeLeftDisplay = document.getElementById('time-left');
        const timerStatusDisplay = document.getElementById('timer-status');
        const startPauseBtn = document.getElementById('start-pause-btn');
        const startIcon = document.getElementById('start-icon');
        const pauseIcon = document.getElementById('pause-icon');
        const focusBtn = document.getElementById('focus-btn');
        const shortBreakBtn = document.getElementById('short-break-btn');
        const longBreakBtn = document.getElementById('long-break-btn');
        const resetBtn = document.getElementById('reset-btn');
        
        // Warna dari PHP Blade
        const primaryColor = '{{ $primaryColor }}';
        const secondaryColor = '{{ $secondaryColor }}';

        // --- Fungsi Helper ---
        
        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }

        function updateDisplay() {
            timeLeftDisplay.textContent = formatTime(timeRemaining);
        }
        
        function setActiveMode(newMode) {
            currentMode = newMode;
            const buttons = {
                'focus': focusBtn,
                'shortBreak': shortBreakBtn,
                'longBreak': longBreakBtn
            };
            
            // Set teks status
            const statusMap = {
                'focus': 'Waktu Fokus',
                'shortBreak': 'Waktu Istirahat Pendek',
                'longBreak': 'Waktu Istirahat Panjang'
            };
            timerStatusDisplay.textContent = statusMap[newMode];

            // Update Class Tombol
            Object.keys(buttons).forEach(mode => {
                const btn = buttons[mode];
                const isActive = (mode === newMode);

                // Kelas default (inactive)
                btn.className = `px-6 py-2 rounded-lg text-sm font-semibold transition duration-300 shadow-md`;
                btn.classList.add('bg-gray-300', 'dark:bg-gray-600', 'text-gray-800', 'dark:text-white', `hover:bg-${secondaryColor}`, `dark:hover:bg-${primaryColor}`);


                if (isActive) {
                    // Kelas aktif (kebalikan dari hover)
                    btn.className = `px-6 py-2 rounded-lg text-sm font-semibold transition duration-300 shadow-lg`;
                    btn.classList.remove('bg-gray-300', 'dark:bg-gray-600', 'text-gray-800', 'dark:text-white');
                    btn.classList.add(`bg-${primaryColor}`, `dark:bg-${secondaryColor}`, 'text-white', 'dark:text-gray-800');
                    btn.classList.remove(`hover:bg-${secondaryColor}`, `dark:hover:bg-${primaryColor}`); // Hapus hover inactive
                }
            });
        }

        function startTimer() {
            if (isRunning) return;

            isRunning = true;
            startIcon.classList.add('hidden');
            pauseIcon.classList.remove('hidden');

            timer = setInterval(() => {
                timeRemaining--;
                updateDisplay();

                if (timeRemaining <= 0) {
                    clearInterval(timer);
                    isRunning = false;
                    
                    // Ganti alert() dengan notifikasi visual di UI
                    timerStatusDisplay.textContent = 'WAKTU HABIS! Mulai sesi baru.';
                    
                    // Secara otomatis beralih ke mode berikutnya (Misal: dari focus ke shortBreak)
                    const nextMode = currentMode === 'focus' 
                        ? 'shortBreak' 
                        : (currentMode === 'shortBreak' ? 'focus' : 'focus'); // Sederhana: kembali ke focus setelah break

                    setTimer(TIMER_DURATIONS[nextMode], nextMode);
                    pauseIcon.classList.add('hidden');
                    startIcon.classList.remove('hidden');
                }
            }, 1000);
        }

        function pauseTimer() {
            clearInterval(timer);
            isRunning = false;
            startIcon.classList.remove('hidden');
            pauseIcon.classList.add('hidden');
        }

        function setTimer(duration, mode) {
            pauseTimer();
            timeRemaining = duration;
            updateDisplay();
            setActiveMode(mode);
        }
        
        function resetTimer() {
            setTimer(TIMER_DURATIONS[currentMode], currentMode);
        }


        // --- Event Listeners ---
        startPauseBtn.addEventListener('click', () => {
            if (isRunning) pauseTimer();
            else startTimer();
        });

        focusBtn.addEventListener('click', () => setTimer(TIMER_DURATIONS.focus, 'focus'));
        shortBreakBtn.addEventListener('click', () => setTimer(TIMER_DURATIONS.shortBreak, 'shortBreak'));
        longBreakBtn.addEventListener('click', () => setTimer(TIMER_DURATIONS.longBreak, 'longBreak'));
        resetBtn.addEventListener('click', resetTimer);

        // Inisialisasi tampilan awal
        setActiveMode('focus');
        updateDisplay();
    });
</script>

@endsection