<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Wajib diimport untuk mendapatkan user yang login
use App\Models\Todo; 
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Menampilkan kalender mingguan dengan tugas yang difilter berdasarkan user yang login.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Mendapatkan ID user yang sedang login
        // Jika user belum login, ini akan mengembalikan null, tetapi biasanya controller ini 
        // dilindungi oleh middleware 'auth'.
        $userId = Auth::id();

        // 1. Tentukan rentang waktu untuk minggu ini
        // Menggunakan startOfWeek(Carbon::MONDAY) untuk memastikan minggu dimulai pada hari Senin
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $startOfWeek->clone()->addDays(6)->endOfDay(); // Akhir hari Minggu

        // 2. Buat koleksi tanggal untuk header kalender (Senin - Minggu)
        $dates = collect(range(0, 6))->map(function ($day) use ($startOfWeek) {
            return $startOfWeek->clone()->addDays($day);
        });

        // 3. Modifikasi Query Eloquent:
        //    a. Filter berdasarkan 'user_id' user yang sedang login.
        //    b. Filter berdasarkan rentang deadline minggu ini.
        $todos = Todo::where('user_id', $userId) // <--- PENTING: Filter user yang login
                     ->whereDate('deadline', '>=', $startOfWeek)
                     ->whereDate('deadline', '<=', $endOfWeek)
                     ->orderBy('deadline')
                     ->get();

        // Data untuk tampilan (Anda bisa menyesuaikan warna ini di tempat lain jika perlu)
        $primaryColor = 'ungu-kustom';
        $secondaryColor = 'pink-kustom';

        return view('calendar.index', [
            'dates' => $dates,
            'todos' => $todos,
            'primaryColor' => $primaryColor,
            'secondaryColor' => $secondaryColor,
        ]);
    }
}