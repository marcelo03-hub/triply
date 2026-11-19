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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perjalanan');
            $table->string('destinasi');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->decimal('anggaran', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->string('status')->default('planned');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
