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
        Schema::create('hasil_klasifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persalinan_id')
                ->unique()
                ->constrained('persalinan')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->enum('label_klasifikasi', [
                'partus_normal',
                'partus_lama',
                'partus_kasep',
                'gawat_janin',
                'perdarahan_postpartum',
                'rujuk_seksio',
            ]);
            $table->decimal('skor_probabilitas', 5, 4)->nullable();
            $table->string('algoritma_digunakan', 50);
            $table->json('parameter_masukan_json')->nullable();
            $table->text('rekomendasi_tindakan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_klasifikasi');
    }
};
