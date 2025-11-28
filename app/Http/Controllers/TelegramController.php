<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // Diperlukan untuk mengirim pesan
use Illuminate\Support\Facades\Log;  // Diperlukan untuk debugging

class TelegramController extends Controller
{

    public function connect(Request $request)
    {
        // Validasi input dari formulir di browser (Profile Edit)
        $request->validate([
            'chat_id' => 'required|numeric|unique:users,telegram_chat_id',
        ], [
            'chat_id.unique' => 'Chat ID ini sudah digunakan oleh akun lain.',
        ]);

        $user = Auth::user();
        $user->telegram_chat_id = $request->chat_id;
        $user->save();

        return redirect()->route('profile.edit')
                            ->with('status', 'Telegram berhasil dihubungkan!');
    }

    public function disconnect()
    {
        $user = Auth::user();
        $user->telegram_chat_id = null; // Setel kembali ke NULL
        $user->save();

        return redirect()->route('profile.edit')
                            ->with('status', 'Koneksi Telegram berhasil diputuskan.');
    }
    
    public function webhook(Request $request)
    {
        // Log ini akan muncul di storage/logs/laravel.log jika CSRF berhasil dilewati
        Log::info('1. [TELEGRAM WEBHOOK] Request diterima.'); 
        
        $update = $request->all();

        // Pastikan ada pesan dan chat id
        $chatId = $update['message']['chat']['id'] ?? null;
        $text = $update['message']['text'] ?? '';

        Log::info('2. [TELEGRAM WEBHOOK] Data Diterima:', ['chat_id' => $chatId, 'text' => $text]); 

        if (!$chatId) {
            return response()->json(['status' => 'ok']);
        }

        // --- Logika Utama ---

        // 1. Jika pengguna hanya mengirim '/start'
        if ($text === '/start') {
            Log::info('3. [TELEGRAM WEBHOOK] Memproses perintah /start.'); 
            
            // Mengirim Chat ID kembali ke pengguna
            $message = "Halo! Chat ID unik Telegram Anda adalah:\n\n**{$chatId}**\n\nMasukkan angka ini di halaman Profil List Me untuk menghubungkan akun Anda.";
            $this->sendMessage($chatId, $message);

            Log::info('4. [TELEGRAM WEBHOOK] Perintah sendMessage dipanggil untuk /start.'); 
        } 
        // 2. Jika pengguna mengirim '/start <user_id>'
        else if (str_starts_with($text, '/start ')) { 
            // Ambil parameter USER_ID setelah /start
            $parts = explode(' ', $text);
            $userId = $parts[1] ?? null;

            if ($userId) {
                $user = User::find($userId);

                if ($user) {
                    $user->telegram_chat_id = $chatId;
                    $user->save();

                    $this->sendMessage($chatId, 'Telegram kamu berhasil terhubung ke ListMe!');
                } else {
                    $this->sendMessage($chatId, 'Error: User ID tidak ditemukan.');
                }
            }
        }
        
        // --- Akhir Logika ---

        return response()->json(['status' => 'ok']);
    }

    /**
     * FUNGSI BARU UNTUK DEMO/TESTING NOTIFIKASI MANUAL
     * Dipanggil melalui Route GET: /telegram/test-notif
     */
    public function sendTestNotification()
    {
        $chatId = env('TELEGRAM_OWNER_CHAT_ID');
        $text = "Halo! Ini adalah notifikasi uji coba manual dari sistem List Me Laravel. Notifikasi terjadwal (seperti pengingat tugas) akan menggunakan fungsi ini.";
        
        if (!$chatId) {
             return "ERROR: Tambahkan TELEGRAM_OWNER_CHAT_ID di .env dan jalankan php artisan config:clear";
        }

        // Pastikan Chat ID adalah numerik sebelum mengirim
        if (!is_numeric($chatId)) {
            return "ERROR: TELEGRAM_OWNER_CHAT_ID di .env harus berupa angka (Chat ID), bukan string kosong atau teks.";
        }

        $success = $this->sendMessage($chatId, $text);

        if ($success) {
            return "Notifikasi uji coba berhasil dikirim ke Chat ID: {$chatId}";
        } else {
            return "Notifikasi uji coba GAGAL dikirim. Cek log laravel.log untuk detail API Error.";
        }
    }


    /**
     * Mengirim pesan ke chat ID Telegram tertentu menggunakan HTTP Client Laravel.
     */
    public function sendMessage($chatId, $text)
    {
        // --- TAMBAHAN LOG UNTUK MENGUJI TOKEN ---
        $token = env('TELEGRAM_BOT_TOKEN');
        // Log ini akan menunjukkan apakah token berhasil dimuat dari .env
        Log::info('5. [TELEGRAM BOT TOKEN] Token Value:', ['token' => $token ? 'Token Ditemukan' : 'Token TIDAK DITEMUKAN']);
        // --- AKHIR TAMBAHAN LOG ---

        if (!$token) {
            Log::error('TELEGRAM_BOT_TOKEN tidak ditemukan di .env!');
            return false;
        }

        try {
            // Menggunakan Laravel HTTP Client dengan metode POST
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown', // Menggunakan Markdown untuk format teks
            ]);

            if ($response->failed()) {
                // Log kegagalan API
                Log::error('Gagal mengirim pesan Telegram (API Error).', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'chat_id' => $chatId
                ]);
            }

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Exception saat mengirim pesan Telegram: ' . $e->getMessage(), ['chat_id' => $chatId]);
            return false;
        }
    }
}