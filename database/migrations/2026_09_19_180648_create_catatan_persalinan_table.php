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
        Schema::create('catatan_persalinan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persalinan_id')
                ->unique()
                ->constrained('persalinan')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // KALA I
            $table->boolean('kala1_melewati_garis_waspada')->default(false);
            $table->text('kala1_masalah_lain')->nullable();
            $table->text('kala1_penatalaksanaan')->nullable();
            $table->text('kala1_hasil_evaluasi')->nullable();

            // KALA II
            $table->unsignedSmallInteger('kala2_lama_menit')->nullable();
            $table->boolean('kala2_episiotomi')->default(false);
            $table->string('kala2_indikasi_episiotomi', 255)->nullable();
            $table->enum('kala2_pendamping', ['suami', 'keluarga', 'teman', 'dukun', 'tidak_ada'])->nullable();
            $table->string('kala2_gawat_janin', 255)->nullable()->comment('Opsi multi-centang: miring_kiri, tarik_napas, episiotomi, tidak_ada');
            $table->string('kala2_distosia_bahu', 255)->nullable()->comment('Opsi multi-centang: mc_robert, merangkang, lainnya, tidak_ada');
            $table->text('kala2_penatalaksanaan')->nullable();
            $table->text('kala2_hasil_evaluasi')->nullable();

            // KALA III
            $table->unsignedSmallInteger('kala3_lama_menit')->nullable();
            $table->unsignedSmallInteger('kala3_jumlah_perdarahan_ml')->nullable();
            $table->boolean('kala3_oksitosin_10u_2menit')->default(true);
            $table->string('kala3_alasan_tidak_oksitosin', 255)->nullable();
            $table->boolean('kala3_oksitosin_ulang')->default(false);
            $table->string('kala3_alasan_oksitosin_ulang', 255)->nullable();
            $table->boolean('kala3_penegangan_tali_pusat')->default(true);
            $table->string('kala3_alasan_tidak_ptp', 255)->nullable();
            $table->boolean('kala3_masase_fundus')->default(true);
            $table->string('kala3_alasan_tidak_masase', 255)->nullable();
            $table->enum('kala3_derajat_laserasi', ['tidak_ada', '1', '2', '3', '4'])->default('tidak_ada');
            $table->string('kala3_tindakan_laserasi', 255)->nullable()->comment('manual, rujuk, penjahitan, lainnya');
            $table->string('kala3_atonia_uteri', 255)->nullable()->comment('Opsi multi-centang: kompresi_bimanual, ergometrin, oksitosin_drip, tidak_ada');
            $table->text('kala3_penatalaksanaan')->nullable();
            $table->text('kala3_hasil_evaluasi')->nullable();

            // KALA IV (Evaluasi Ringkasan Akhir)
            $table->text('kala4_masalah')->nullable();
            $table->text('kala4_penatalaksanaan')->nullable();
            $table->text('kala4_hasil_evaluasi')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_persalinan');
    }
};
