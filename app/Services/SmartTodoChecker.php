<?php

namespace App\Services;

use App\Models\Todo;
use App\Http\Controllers\TelegramController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SmartTodoChecker
{
    protected $telegramController;
    protected $cacheKey = 'last_todo_check';

    public function __construct(TelegramController $telegramController)
    {
        $this->telegramController = $telegramController;
    }

    public function run()
    {
        $now = Carbon::now();
        $lastCheck = Cache::get($this->cacheKey);

        // Cek apakah sudah lewat 1 menit dari pengecekan terakhir
        if ($lastCheck && $now->diffInSeconds($lastCheck) < 60) {
            return; // skip pengecekan, belum waktunya
        }

        // Update timestamp terakhir
        Cache::put($this->cacheKey, $now, 3600); // simpan 1 jam cukup

        // Jalankan pengecekan
        $this->checkUpcomingReminders($now);
        $this->updateMissedStatus($now);
    }

    protected function checkUpcomingReminders($now)
    {
        $todos = Todo::where('is_notified', false)
                     ->whereNotNull('reminder_at')
                     ->where('reminder_at', '<=', $now)
                     ->with('user', 'category')
                     ->get();

        foreach ($todos as $todo) {
            $chatId = $todo->user?->telegram_chat_id;
            if (!$chatId) continue;

            $message = "🔔 PENGINGAT TUGAS!\n";
            $message .= "Tugas: {$todo->title}\n";
            $message .= "Kategori: " . ($todo->category?->name ?? 'Tidak Ada') . "\n";
            $message .= "Jatuh Tempo: " . ($todo->deadline ? $todo->deadline->format('d M Y H:i') : 'Tidak disetel');

            if ($this->telegramController->sendMessage($chatId, $message)) {
                $todo->is_notified = true;
                $todo->save();
            }
        }
    }

    protected function updateMissedStatus($now)
    {
        $todos = Todo::where('status', 'pending')
                     ->whereNotNull('deadline')
                     ->where('deadline', '<', $now)
                     ->with('user')
                     ->get();

        foreach ($todos as $todo) {
            $todo->status = 'missed';
            $todo->save();

            $chatId = $todo->user?->telegram_chat_id;
            if ($chatId) {
                $message = "⚠️ Tugas {$todo->title} berstatus MISSED karena melewati deadline!";
                $this->telegramController->sendMessage($chatId, $message);
            }
        }
    }
}
