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
        Schema::create('skp_pengajuan', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('unit'); 
        $table->string('bulan', 20);
        $table->integer('tahun');
        $table->date('tanggal_pengajuan');
        $table->enum('status', ['verifikasi','perbaikan','menungguttd','selesai'])->default('verifikasi');
        
        // Gunakan text atau string tanpa limit 100 untuk link & judul
        $table->string('judul_laporan')->nullable();
        $table->string('link_bukti_dukung')->nullable();
        
        $table->string('catatan_perbaikan')->nullable();
        $table->string('catatan_kepala_rm')->nullable();
        $table->string('pdf_file')->nullable();
        $table->string('pdf_ttdfinal')->nullable();
        $table->timestamps();
    });   
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skp_pengajuan');
    }
};
