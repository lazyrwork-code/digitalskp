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
        Schema::create('skp_dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skp_id')->constrained('skp_pengajuan')->cascadeOnDelete();
            $table->string('nama_file');
            $table->enum('tipe', ['pdf']);
            $table->string('link_pendukung');
            $table->string('url');
            $table->string('url_signed');
            $table->string('catatan')->nullable;
            $table->boolean('isttd')->default(0);;
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skp_dokumen');
    }
};
