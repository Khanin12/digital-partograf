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
        Schema::create('pemantauan_kala_4', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persalinan_id')
                ->constrained('persalinan')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedTinyInteger('jam_ke')->comment('Jam ke-1 atau Jam ke-2');
            $table->unsignedTinyInteger('pemantauan_ke')->comment('Interval ke-1 s/d 4 (jam 1) atau 1 s/d 2 (jam 2)');
            $table->time('waktu_pemantauan');
            $table->unsignedSmallInteger('tekanan_darah_sistolik');
            $table->unsignedSmallInteger('tekanan_darah_diastolik');
            $table->unsignedSmallInteger('nadi_per_menit');

            // Suhu tubuh diukur 1x tiap jam pada lembar partograf (APN)
            $table->decimal('suhu_tubuh', 4, 1)->nullable()->comment('Biasanya hanya diukur 1x tiap jam');

            $table->string('tinggi_fundus_uteri', 60);
            $table->string('kontraksi_uterus', 60);
            $table->string('kandung_kemih', 60);

            // Default 0 jika tidak ada estimasi angka perdarahan eksak
            $table->unsignedSmallInteger('perdarahan_ml')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['persalinan_id', 'jam_ke', 'pemantauan_ke'], 'idx_persalinan_kala4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemantauan_kala_4');
    }
};
