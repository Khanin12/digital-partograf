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
        Schema::create('persalinan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')
                ->constrained('pasien')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->unsignedTinyInteger('gravida')->default(1);
            $table->unsignedTinyInteger('para')->default(0);
            $table->unsignedTinyInteger('abortus')->default(0);
            $table->unsignedTinyInteger('usia_kehamilan_minggu');
            $table->string('nama_fasilitas', 150);
            $table->dateTime('waktu_masuk');
            $table->dateTime('waktu_ketuban_pecah')->nullable();
            $table->dateTime('waktu_mules_mulai')->nullable();
            $table->string('nama_penolong', 120);
            $table->enum('tempat_persalinan', ['rumah_ibu', 'puskesmas', 'klinik_swasta', 'rumah_sakit', 'lainnya'])->default('puskesmas');
            $table->enum('status_persalinan', ['proses', 'selesai', 'rujuk', 'batal'])->default('proses');

            // Riwayat makan & minum terakhir (Footer PDF Lembar Depan Partograf)
            $table->dateTime('waktu_makan_terakhir')->nullable();
            $table->string('jenis_makan', 100)->nullable();
            $table->string('porsi_makan', 50)->nullable();
            $table->dateTime('waktu_minum_terakhir')->nullable();
            $table->string('jenis_minum', 100)->nullable();
            $table->string('porsi_minum', 50)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persalinan');
    }
};
