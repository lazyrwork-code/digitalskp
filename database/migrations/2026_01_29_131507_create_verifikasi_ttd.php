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
        Schema::create('verifikasi_ttd', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skp_id')->constrained('skp_pengajuan');
            $table->text('qr_text');
            $table->integer('qr_x');
            $table->integer('qr_y');
            $table->integer('qr_size');
            $table->foreignId('ditandatangani_oleh')->constrained('users');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi_ttd');
    }
};
