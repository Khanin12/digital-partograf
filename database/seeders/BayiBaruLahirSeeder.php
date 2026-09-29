<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BayiBaruLahirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Cari data pasien Ny. Siti Aminah (REG-2026-001)
        $pasien = DB::table('pasien')
            ->where('nomor_register', 'REG-2026-001')
            ->orWhere('nama_ibu', 'like', '%Siti Aminah%')
            ->first();

        if (! $pasien) {
            $this->command->warn('Pasien Ny. Siti Aminah (REG-2026-001) tidak ditemukan. Seeder dibatalkan.');

            return;
        }

        // 2. Cari data persalinan dari pasien tersebut
        $persalinan = DB::table('persalinan')
            ->where('pasien_id', $pasien->id)
            ->whereNull('deleted_at')
            ->orderBy('id', 'asc')
            ->first();

        if (! $persalinan) {
            $this->command->warn("Data persalinan untuk pasien {$pasien->nama_ibu} (ID: {$pasien->id}) tidak ditemukan. Seeder dibatalkan.");

            return;
        }

        // 3. Masukkan data Bayi Baru Lahir
        DB::table('bayi_baru_lahir')->updateOrInsert(
            [
                'persalinan_id' => $persalinan->id,
                'urutan_kelahiran' => 1,
            ],
            [
                'jenis_kelamin' => 'P',
                'berat_badan_gram' => 3100,
                'panjang_badan_cm' => 49,
                'apgar_menit_1' => 8,
                'apgar_menit_5' => 9,
                'apgar_menit_10' => 10,
                'pemberian_asi_kurang_1jam' => true,
                'alasan_tidak_asi' => null,
                'kondisi_bayi' => 'normal',
                'tindakan_resusitasi' => 'mengeringkan, menghangatkan, rangsang_aktif',
                'cacat_bawaan' => null,
                'masalah_lain' => null,
                'penatalaksanaan' => 'Inisiasi Menyusu Dini (IMD) < 1 jam, profilaksis salep mata antibiotik, injeksi Vitamin K1 1 mg IM pada paha kiri anterolateral, dan imunisasi Hepatitis B (HB0) pada paha kanan setelah 1-2 jam pasca lahir.',
                'hasil_evaluasi' => 'Bayi bugar, menangis kuat, gerakan aktif, warna kulit kemerahan, refleks hisap dan menelan baik, tanda-tanda vital stabil (DJJ/HR: 138 x/m, Pernapasan: 44 x/m, Suhu: 36.7 °C).',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info("Data Bayi Baru Lahir berhasil di-seed untuk pasien {$pasien->nama_ibu} ({$pasien->nomor_register}), Persalinan ID: {$persalinan->id}!");
    }
}
