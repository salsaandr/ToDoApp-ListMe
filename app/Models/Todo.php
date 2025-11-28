<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category_id',
        'status',
        'deadline',
        'reminder_at',
        'is_notified',
    ];

    // Otomatis set reminder_at 1 jam sebelum deadline jika belum diisi
    protected static function booted()
    {
        static::creating(function ($todo) {
            if ($todo->deadline && !$todo->reminder_at) {
                $todo->reminder_at = Carbon::parse($todo->deadline)->subHour();
            }
        });

        static::updating(function ($todo) {
            if ($todo->deadline && !$todo->reminder_at) {
                $todo->reminder_at = Carbon::parse($todo->deadline)->subHour();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
