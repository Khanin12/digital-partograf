<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatatanPersalinanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data persalinan pertama yang ada di database
        $persalinan = DB::table('persalinan')->orderBy('id', 'asc')->first();

        if (! $persalinan) {
            $this->command->warn('Data persalinan tidak ditemukan. Silakan isi data persalinan terlebih dahulu.');

            return;
        }

        DB::table('catatan_persalinan')->updateOrInsert(
            ['persalinan_id' => $persalinan->id],
            [
                // KALA I
                'kala1_melewati_garis_waspada' => false,
                'kala1_masalah_lain' => 'Tidak ada masalah',
                'kala1_penatalaksanaan' => 'Observasi kemajuan persalinan dan DJJ berkala sesuai partograf',
                'kala1_hasil_evaluasi' => 'Pembukaan serviks maju normal, kondisi ibu dan janin baik',

                // KALA II
                'kala2_lama_menit' => 30,
                'kala2_episiotomi' => false,
                'kala2_indikasi_episiotomi' => null,
                'kala2_pendamping' => 'suami',
                'kala2_gawat_janin' => 'tidak_ada',
                'kala2_distosia_bahu' => 'tidak_ada',
                'kala2_penatalaksanaan' => 'Pimpin meneran saat his, bimbing posisi meneran yang nyaman',
                'kala2_hasil_evaluasi' => 'Bayi lahir spontan menangis kuat, jenis kelamin perempuan, BB 3100 gr',

                // KALA III
                'kala3_lama_menit' => 10,
                'kala3_jumlah_perdarahan_ml' => 150,
                'kala3_oksitosin_10u_2menit' => true,
                'kala3_alasan_tidak_oksitosin' => null,
                'kala3_oksitosin_ulang' => false,
                'kala3_alasan_oksitosin_ulang' => null,
                'kala3_penegangan_tali_pusat' => true,
                'kala3_alasan_tidak_ptp' => null,
                'kala3_masase_fundus' => true,
                'kala3_alasan_tidak_masase' => null,
                'kala3_derajat_laserasi' => '1',
                'kala3_tindakan_laserasi' => 'penjahitan',
                'kala3_atonia_uteri' => 'tidak_ada',
                'kala3_penatalaksanaan' => 'Manajemen Aktif Kala III (MAK III), penjahitan luka robekan jalan lahir',
                'kala3_hasil_evaluasi' => 'Plasenta lahir lengkap (kotiledon dan selaput utuh), kontraksi uterus baik/keras',

                // KALA IV
                'kala4_masalah' => 'Tidak ada masalah',
                'kala4_penatalaksanaan' => 'Pemantauan tanda-tanda vital, TFU, kontraksi, dan perdarahan berkala tiap 15 menit pada 1 jam pertama dan tiap 30 menit pada 1 jam kedua',
                'kala4_hasil_evaluasi' => 'Kondisi ibu stabil, TTV normal, kontraksi kuat, lokia rubra normal',

                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info("Data catatan persalinan berhasil di-seed untuk persalinan_id: {$persalinan->id}!");
    }
}
