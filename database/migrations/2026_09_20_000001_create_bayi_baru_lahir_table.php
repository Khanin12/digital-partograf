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
        Schema::create('bayi_baru_lahir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persalinan_id')
                ->constrained('persalinan')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedTinyInteger('urutan_kelahiran')->default(1)->comment('1 untuk tunggal, 2 dst untuk kembar/gemelli');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->unsignedSmallInteger('berat_badan_gram');
            $table->unsignedTinyInteger('panjang_badan_cm');
            $table->unsignedTinyInteger('apgar_menit_1')->nullable();
            $table->unsignedTinyInteger('apgar_menit_5')->nullable();
            $table->unsignedTinyInteger('apgar_menit_10')->nullable();
            $table->boolean('pemberian_asi_kurang_1jam')->default(true)->comment('Inisiasi Menyusu Dini / IMD < 1 jam');
            $table->string('alasan_tidak_asi', 255)->nullable();
            $table->enum('kondisi_bayi', ['normal', 'asfiksia_pucat_biru_lemas'])->default('normal');
            $table->string('tindakan_resusitasi', 255)->nullable()->comment('mengeringkan, menghangatkan, bebaskan_jalan_napas, rangsang_aktif, lainnya');
            $table->text('cacat_bawaan')->nullable();
            $table->text('masalah_lain')->nullable();
            $table->text('penatalaksanaan')->nullable();
            $table->text('hasil_evaluasi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bayi_baru_lahir');
    }
};
