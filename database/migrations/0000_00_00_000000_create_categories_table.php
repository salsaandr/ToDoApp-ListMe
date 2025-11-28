<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Nama kategori harus unik
            
            // PERBAIKAN KRITIS: Hapus ->constrained(). Hanya definisikan kolom user_id.
            // Constraint akan ditambahkan di migrasi terpisah (Langkah 2).
            $table->foreignId('user_id')->nullable(); 
            
            $table->timestamps();
        });
        
        // Opsional: Tambahkan beberapa kategori default setelah tabel dibuat
        // Anda bisa mengisi data ini di Seeder jika tidak ingin di Migrasi.
        \Illuminate\Support\Facades\DB::table('categories')->insert([
            ['name' => 'Organisasi', 'user_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pekerjaan', 'user_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kuliah', 'user_id' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};