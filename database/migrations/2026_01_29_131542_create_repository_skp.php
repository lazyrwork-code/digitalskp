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
        Schema::create('repository_skp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skp_id')->constrained('skp_pengajuan');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('tahun');
            $table->string('bulan', 20);
            $table->string('kategori', 100);
            $table->string('file_pdf');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repository_skp');
    }
};
