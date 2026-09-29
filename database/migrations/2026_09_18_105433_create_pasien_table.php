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
        Schema::create('pasien', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_register', 50)->unique();
            $table->string('nama_ibu', 120);
            $table->string('nama_suami', 120)->nullable();
            $table->unsignedTinyInteger('usia_ibu');
            $table->unsignedTinyInteger('usia_suami')->nullable();
            $table->text('alamat')->nullable();
            $table->string('nomor_telepon', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasien');
    }
};
