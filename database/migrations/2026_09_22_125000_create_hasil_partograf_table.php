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
        Schema::create('hasil_partograf', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persalinan_id')
                ->unique()
                ->constrained('persalinan')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->json('parto_json')->comment('Snapshot lengkap data partograf format JSON terstruktur ala form-io');
            $table->enum('status_sinkronisasi', ['draft', 'final', 'terkunci'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_partograf');
    }
};
