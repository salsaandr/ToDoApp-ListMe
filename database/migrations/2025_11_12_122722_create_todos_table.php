<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // STEP 1: Buat tabel 'todos' dengan kolom category_id, TAPI TANPA foreign key
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke tabel users (Tabel users dijamin ada duluan)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            
            // Definisikan kolom category_id sebagai foreignId, TAPI JANGAN DIBUAT constraint-nya dulu
            $table->foreignId('category_id')->nullable(); 
            
            $table->enum('status', ['pending', 'completed', 'missed'])->default('pending');
            $table->dateTime('deadline')->nullable();
            
            // Menambahkan field reminder & notification
            $table->timestamp('reminder_at')->nullable();
            $table->boolean('is_notified')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};