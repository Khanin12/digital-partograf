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
        Schema::create('edukasi_kie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persalinan_id')
                ->constrained('persalinan')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->date('tanggal_edukasi');
            $table->enum('materi_edukasi', [
                'semua_nifas',
                'breast_care',
                'asi',
                'perawatan_tali_pusat',
                'kb',
                'gizi',
                'imunisasi',
            ])->comment('7 materi edukasi nifas resmi partograf');
            $table->string('pelaksana', 120);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['persalinan_id', 'materi_edukasi'], 'idx_persalinan_materi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edukasi_kie');
    }
};
