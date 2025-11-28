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
        // Migrasi ini akan berjalan PALING AKHIR
        // Pada saat ini, tabel 'categories' dan 'todos' sudah dijamin ada.
        Schema::table('todos', function (Blueprint $table) {
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            // Drop foreign key sebelum menghapus kolom atau tabel
            $table->dropForeign(['category_id']);
        });
    }
};