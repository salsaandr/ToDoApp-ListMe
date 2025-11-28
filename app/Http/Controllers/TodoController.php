<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class TodoController extends Controller
{
    /**
     * Tampilkan form untuk membuat tugas baru.
     */
    public function create()
    {
        $userId = Auth::id();

        // Ambil semua kategori milik user atau kategori global
        $categories = Category::where('user_id', $userId)
                             ->orWhereNull('user_id')
                             ->get();
                             
        // Akan merender view di resources/views/todos/create.blade.php
        return view('todos.create', compact('categories'));
    }
    
    /**
     * Tampilkan daftar tugas (todos) di dashboard.
     * Sudah termasuk fitur Pencarian (search) dan Filter Kategori (category).
     */
    public function index(Request $request) // Tambahkan Request $request
    {
        $userId = Auth::id();
        $now = Carbon::now();

        // Ambil parameter pencarian dan kategori dari Request
        $searchQuery = $request->get('search');
        $categoryId = $request->get('category'); 
        
        // Ambil semua kategori
        $categories = Category::where('user_id', $userId)
                             ->orWhereNull('user_id')
                             ->get();
        
        // ---------------------------------------------
        // A. LOGIKA AUTO-UPDATE STATUS MISSED 
        // ---------------------------------------------
        // Update tugas yang sudah lewat deadline dan masih 'pending' menjadi 'missed'.
        // Catatan: Ini harus dilakukan SEBELUM query progres dan paginasi.
        Todo::where('user_id', $userId)
            ->whereIn('status', ['pending']) // Hanya cek yang masih pending
            ->whereNotNull('deadline')
            ->where('deadline', '<', $now)
            ->update(['status' => 'missed']);

        // ---------------------------------------------
        // B. LOGIKA PENGHITUNGAN PROGRESS GLOBAL
        // ---------------------------------------------
        // Query untuk menghitung PROGRESS tidak boleh terpengaruh oleh SEARCH.
        // HANYA dipengaruhi oleh Filter Kategori.
        
        $baseProgressQuery = Todo::where('user_id', $userId);
        
        if ($categoryId) {
            $baseProgressQuery->where('category_id', $categoryId);
        }

        // Ambil SELURUH DATA untuk menghitung PROGRES GLOBAL
        $allTodos = $baseProgressQuery->get();

        // Hitung Statistik Global
        $totalTasks = $allTodos->count();
        // Menggunakan filter collection (sudah di-get) untuk menghitung status
        $completedTasks = $allTodos->where('status', 'completed')->count();
        $missedTasks = $allTodos->where('status', 'missed')->count();
        $pendingTasks = $allTodos->where('status', 'pending')->count(); // Hanya yang pending
        
        // Hitung persentase
        $progressPercentage = ($totalTasks > 0) ? round(($completedTasks / $totalTasks) * 100) : 0;
        
        // Persentase untuk Not Completed (Missed + Pending)
        $notCompletedTasks = $totalTasks - $completedTasks;
        $pendingPercentage = ($totalTasks > 0) ? round(($pendingTasks / $totalTasks) * 100) : 0;
        $missedPercentage = ($totalTasks > 0) ? round(($missedTasks / $totalTasks) * 100) : 0;

        // ---------------------------------------------
        // C. LOGIKA QUERY DAFTAR TUGAS (PAGINATION + FILTER)
        // ---------------------------------------------
        // Query ini harus terpengaruh oleh SEARCH dan KATEGORI.
        
        $paginatedQuery = Todo::where('user_id', $userId)
                             ->latest()
                             ->with('category');
        
        // 1. Terapkan Filter Kategori (Jika ada)
        if ($categoryId) {
            $paginatedQuery->where('category_id', $categoryId);
        }

        // 2. Terapkan Filter Pencarian (Jika ada)
        if ($searchQuery) {
            // WHERE LIKE untuk pencarian judul yang mengandung kata kunci
            $paginatedQuery->where('title', 'like', '%' . $searchQuery . '%');
        }

        // 3. Ambil data dengan Paginasi
        $todos = $paginatedQuery->paginate(10);
        
        // Ambil 2 tugas terakhir yang diselesaikan dari $allTodos
        $recentCompleted = $allTodos->where('status', 'completed')->sortByDesc('updated_at')->take(2);

        return view('dashboard', [
            'todos' => $todos, 
            'categories' => $categories,

            // Kirim variabel progress global ke view
            'totalTasks' => $totalTasks,
            'completedTasks' => $completedTasks,
            'missedTasks' => $missedTasks, // Tambah variabel missed
            'pendingTasks' => $pendingTasks, // Tambah variabel pending
            
            'progressPercentage' => $progressPercentage,
            'pendingPercentage' => $pendingPercentage, // Kirim persentase pending
            'missedPercentage' => $missedPercentage, // Kirim persentase missed
            'recentCompleted' => $recentCompleted, // Kirim tugas yang baru diselesaikan
            
            // Variabel ini sudah diambil secara implisit di view melalui request('search'),
            // tetapi pastikan Request $request di-inject.
        ]);
    }

    /**
     * Simpan tugas baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'deadline' => 'nullable|date', 
            'description' => 'nullable|string',
        ]);

        // --- LOGIKA REMINDER ---
        $reminderAt = null;
        if ($request->deadline) {
            $dueDate = Carbon::parse($request->deadline);
            // Atur pengingat 1 jam sebelum deadline
            $reminderAt = $dueDate->copy()->subHour(); 
        }

        Todo::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description, 
            'category_id' => $request->category_id,
            'deadline' => $request->deadline,
            'status' => 'pending', 
            
            'reminder_at' => $reminderAt, 
            'is_notified' => false,
        ]);

        return redirect()->route('dashboard')->with('status', 'Tugas berhasil ditambahkan!');
    }

    /**
     * Perbarui tugas yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        $todo = Todo::where('id', $id)
                     ->where('user_id', Auth::id())
                     ->firstOrFail();

        $request->validate([
            'title' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            // Pastikan 'status' hanya bisa 'pending', 'completed', atau 'missed'
            'status' => 'nullable|string|in:pending,completed,missed', 
            'deadline' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        // --- LOGIKA REMINDER & STATUS UPDATE ---

        $reminderAt = $todo->reminder_at;
        $isNotified = $todo->is_notified;
        
        $deadlineChanged = $request->has('deadline') && 
                            Carbon::parse($request->deadline)->timestamp !== Carbon::parse($todo->deadline)->timestamp;

        // Jika status di-update manual di form, kita harus memastikan status yang masuk valid.
        $newStatus = $request->input('status', $todo->status);

        if ($deadlineChanged) {
            if ($request->deadline) {
                $dueDate = Carbon::parse($request->deadline);
                $reminderAt = $dueDate->copy()->subHour(); 
                $isNotified = false; // Reset notifikasi jika deadline berubah
            } else {
                $reminderAt = null;
                $isNotified = false;
            }
        }
        
        // Jika status berubah
        if ($newStatus !== $todo->status) {
            if ($newStatus === 'completed') {
                $isNotified = true; // Tidak perlu diingatkan jika sudah selesai
            } else {
                $isNotified = false; // Reset notifikasi jika kembali ke pending/missed
            }
            // Juga, jika diupdate menjadi 'completed', kita harus memastikan statusnya tetap 'completed'
            // meskipun deadline sudah terlewat.
        }
        
        // Pastikan status 'missed' hanya bisa di set otomatis oleh sistem, 
        // atau jika user mengubahnya manual (walaupun lebih baik di-set otomatis).
        // Biarkan validasi di atas yang menangani input status.

        // 3. Persiapkan data update
        $updateData = $request->only('title', 'category_id', 'status', 'deadline', 'description'); 
        $updateData['reminder_at'] = $reminderAt;
        $updateData['is_notified'] = $isNotified;
        $updateData['category_id'] = $request->category_id; 

        $todo->update($updateData);

        return back()->with('status', 'Tugas berhasil diperbarui!');
    }

    /**
     * Hapus tugas.
     */
    public function destroy($id)
    {
        $todo = Todo::where('id', $id)
                     ->where('user_id', Auth::id())
                     ->firstOrFail();

        $todo->delete();

        return back()->with('status', 'Tugas berhasil dihapus!');
    }

    // =========================================================
    // BARU: LOGIKA NOTIFIKASI UNTUK FRONTEND
    // =========================================================
    /**
     * Ambil data notifikasi tugas (deadline 1 jam lagi & terlewat).
     */
    public function getNotifications()
    {
        $userId = Auth::id();
        $now = Carbon::now();
        $oneHourLater = $now->copy()->addHour();

        // 1. Tugas yang Mendekati Deadline (1 jam menuju)
        $impendingTodos = Todo::where('user_id', $userId)
            ->where('status', 'pending') // Hanya tugas yang masih pending
            ->whereNotNull('deadline')
            ->where('deadline', '>', $now)
            ->where('deadline', '<=', $oneHourLater)
            ->orderBy('deadline')
            ->get(['id', 'title', 'deadline']);

        // 2. Tugas yang Terlewat (Status 'missed' adalah hasil auto-update di index())
        $missedTodos = Todo::where('user_id', $userId)
            ->where('status', 'missed') // Ambil yang statusnya sudah diubah ke 'missed' oleh sistem
            ->orderByDesc('deadline')
            ->get(['id', 'title', 'deadline']);

        $notifications = [];

        foreach ($impendingTodos as $todo) {
            // Menghitung sisa waktu dalam format yang mudah dibaca
            $diff = $todo->deadline->diffForHumans($now, [
                'syntax' => Carbon::DIFF_ABSOLUTE,
                'parts' => 2,
                'join' => true,
            ]);
            
            $notifications[] = [
                'id' => $todo->id,
                'title' => $todo->title,
                'type' => 'reminder',
                'message' => 'Deadline ' . $diff . ' lagi!', 
                'time_diff' => $diff,
                'deadline' => $todo->deadline->format('Y-m-d H:i'),
            ];
        }

        foreach ($missedTodos as $todo) {
            $notifications[] = [
                'id' => $todo->id,
                'title' => $todo->title,
                'type' => 'missed',
                'message' => 'Telah terlewat!',
                'time_diff' => $todo->deadline->diffForHumans(),
                'deadline' => $todo->deadline->format('Y-m-d H:i'),
            ];
        }

        // Urutkan notifikasi: Missed (terbaru) di atas, Reminder di bawah
        usort($notifications, function ($a, $b) {
            if ($a['type'] === 'missed' && $b['type'] !== 'missed') {
                return -1;
            }
            if ($a['type'] !== 'missed' && $b['type'] === 'missed') {
                return 1;
            }
            // Jika keduanya missed atau keduanya reminder, diurutkan berdasarkan deadline
            return strtotime($b['deadline']) - strtotime($a['deadline']);
        });

        return response()->json([
            'count' => count($notifications),
            'notifications' => $notifications,
        ]);
    }
}