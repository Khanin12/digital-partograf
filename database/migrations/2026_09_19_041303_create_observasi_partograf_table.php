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
        Schema::create('observasi_partograf', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persalinan_id')
                ->constrained('persalinan')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedTinyInteger('kolom_partograf_ke')->nullable()->comment('Kolom ke-1 s/d 16 pada matriks grafik partograf');
            $table->dateTime('waktu_pemeriksaan');
            $table->unsignedSmallInteger('djj_denyut_per_menit')->nullable();
            $table->enum('air_ketuban', ['U', 'J', 'M', 'D', 'K'])->nullable();
            $table->enum('penyusupan_kepala', ['0', '1', '2', '3'])->nullable();
            $table->decimal('pembukaan_serviks', 3, 1)->nullable()->comment('Simbol X pada grafik (0-10)');
            $table->unsignedTinyInteger('turunnya_kepala')->nullable()->comment('Simbol O pada grafik (0-5)');
            $table->unsignedTinyInteger('frekuensi_kontraksi')->nullable()->comment('Jumlah kontraksi per 10 menit (1-5)');
            $table->enum('durasi_kontraksi', ['kurang_20', 'antara_20_40', 'lebih_40'])->nullable()->comment('Pola arsir kontraksi');
            $table->decimal('oksitosin_unit', 4, 2)->nullable();
            $table->unsignedSmallInteger('oksitosin_tetes_per_menit')->nullable();
            $table->text('obat_dan_cairan_iv')->nullable();
            $table->unsignedSmallInteger('tekanan_darah_sistolik')->nullable();
            $table->unsignedSmallInteger('tekanan_darah_diastolik')->nullable();
            $table->unsignedSmallInteger('nadi_per_menit')->nullable();
            $table->decimal('suhu_tubuh', 4, 1)->nullable();
            $table->string('urin_protein', 50)->nullable();
            $table->string('urin_aseton', 50)->nullable();
            $table->unsignedSmallInteger('urin_volume_ml')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['persalinan_id', 'waktu_pemeriksaan'], 'idx_persalinan_waktu');
            $table->index(['persalinan_id', 'kolom_partograf_ke'], 'idx_persalinan_kolom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observasi_partograf');
    }
};
