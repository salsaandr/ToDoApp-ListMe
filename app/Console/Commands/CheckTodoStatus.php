<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Todo;
use App\Http\Controllers\TelegramController; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; 

class CheckTodoStatus extends Command
{
    protected $signature = 'todos:check-status';
    protected $description = 'Update status todos (missed) & send Telegram reminders (upcoming).';

    public function handle(TelegramController $telegramController)
    {
        $now = Carbon::now();
        $this->info("Memulai pengecekan status dan pengingat pada waktu: {$now->format('Y-m-d H:i:s')}");

        // --- LOGIKA 1: UPCOMING REMINDER (Pengingat yang akan datang) ---
        $this->checkUpcomingReminders($now, $telegramController);

        // --- LOGIKA 2: MISSED STATUS UPDATE (Deadline sudah lewat) ---
        $this->updateMissedStatus($now, $telegramController);

        $this->info('Pengecekan selesai.');
        
        return self::SUCCESS;
    }

    protected function checkUpcomingReminders(Carbon $now, TelegramController $telegramController): void
    {
        // Debugging: Catat kondisi query
        Log::debug("Memeriksa Reminder: is_notified=false, reminder_at IS NOT NULL, reminder_at <= {$now->toDateTimeString()}");

        $reminders = Todo::where('is_notified', false)
                             ->whereNotNull('reminder_at')
                             ->where('reminder_at', '<=', $now)
                             ->with(['user', 'category']) 
                             ->get();

        $count = $reminders->count();
        $this->info("Ditemukan {$count} pengingat yang akan datang.");
        Log::info("[REMINDER COUNT] Ditemukan {$count} pengingat yang akan diproses.");


        foreach ($reminders as $todo) {
            // Log setiap TODO yang sedang diproses
            Log::info("[REMINDER PROCESS] Memproses Todo ID: {$todo->id}, Title: {$todo->title}, Reminder At: {$todo->reminder_at}");

            if (!$todo->user || !$todo->user->telegram_chat_id) {
                 // Log detail skip
                 Log::warning("[REMINDER SKIP] Todo ID {$todo->id} dilewati. User ID: {$todo->user_id}. Chat ID tidak ditemukan/kosong: " . ($todo->user->telegram_chat_id ?? 'NULL'));
                 continue; 
            }
            
            $chatId = $todo->user->telegram_chat_id;

            // Membangun pesan... (Logika pesan sama)
            $message = "🔔 PENGINGAT TUGAS!\n";
            $message .= "Tugas: {$todo->title}\n";
            $categoryName = $todo->category?->name ?: 'Tidak Ada'; 
            $message .= "Kategori: {$categoryName}\n";
            if ($todo->deadline) {
                $message .= "Jatuh Tempo: " . Carbon::parse($todo->deadline)->format('d M Y H:i');
            } else {
                $message .= "Jatuh Tempo: Tidak disetel";
            }
            
            $success = $telegramController->sendMessage($chatId, $message);

            if ($success) {
                $todo->is_notified = true;
                $todo->save();
                Log::info("[REMINDER SUCCESS] Pengingat UPCOMING berhasil dikirim & ditandai untuk Todo ID: {$todo->id}");
            } else {
                Log::error("[REMINDER FAIL] Gagal mengirim pengingat UPCOMING untuk Todo ID: {$todo->id}. Chat ID: {$chatId}");
            }
        }
    }
    
    // Logika updateMissedStatus sama, tidak perlu diubah.
    protected function updateMissedStatus(Carbon $now, TelegramController $telegramController): void
    {
        Log::debug("Memeriksa MISSED: status='pending', deadline IS NOT NULL, deadline < {$now->toDateTimeString()}");

        $missedTodos = Todo::where('status', 'pending')
                            ->whereNotNull('deadline')
                            ->where('deadline', '<', $now)
                            ->with('user') 
                            ->get();

        $count = $missedTodos->count();
        $this->info("Ditemukan {$count} tugas yang statusnya harus diupdate ke MISSED.");
        
        foreach ($missedTodos as $todo) {
            // Logika untuk status missed
            // ... (sama seperti sebelumnya)
            Log::info("[MISSED PROCESS] Memproses Todo ID: {$todo->id}, Title: {$todo->title}. Status diubah menjadi MISSED.");
            
            $todo->status = 'missed';
            $todo->is_notified = true; 
            $todo->save();

            $chatId = $todo->user ? $todo->user->telegram_chat_id : null;

            if ($chatId) {
                $message = "⚠️ Tugas {$todo->title} berstatus MISSED karena melewati deadline!";
                $telegramController->sendMessage($chatId, $message);
            }
        }
    }
}