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
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->string('lokasi');
            $table->string('wilayah')->default('domestik'); // domestik, internasional
            $table->string('kategori')->default('alam'); // pantai, alam, budaya, kota, religi
            $table->text('deskripsi');
            $table->string('gambar')->nullable();
            $table->string('estimasi_biaya')->nullable();
            $table->string('rekomendasi_durasi')->nullable();
            $table->decimal('rating', 3, 1)->default(4.8);
            $table->text('highlight')->nullable();
            $table->text('tips')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};
