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
        Schema::create('skp_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skp_id')->constrained('skp_pengajuan');
            $table->string('dari_status', 20);
            $table->string('ke_status', 20);
            $table->text('keterangan')->nullable();
            $table->foreignId('dibuat_oleh')->constrained('users');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skp_log');
    }
};
