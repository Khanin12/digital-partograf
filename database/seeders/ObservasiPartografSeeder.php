<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObservasiPartografSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $persalinan = DB::table('persalinan')->orderBy('id', 'asc')->first();

        if (! $persalinan) {
            $this->command->warn('Data persalinan tidak ditemukan.');

            return;
        }

        // 1. Lengkapi data riwayat makan, minum, mules, dan ketuban di tabel persalinan
        DB::table('persalinan')->where('id', $persalinan->id)->update([
            'waktu_mules_mulai' => '2026-09-20 05:00:00',
            'waktu_ketuban_pecah' => '2026-09-20 09:15:00',
            'waktu_makan_terakhir' => '2026-09-20 07:00:00',
            'jenis_makan' => 'Nasi tim + sup sayur',
            'porsi_makan' => '1 porsi',
            'waktu_minum_terakhir' => '2026-09-20 08:30:00',
            'jenis_minum' => 'Teh manis hangat',
            'porsi_minum' => '1 gelas',
            'status_persalinan' => 'selesai',
            'updated_at' => now(),
        ]);

        // 2. Isi data Observasi Partograf Lengkap (Kolom 1 s/d 7, kemajuan persalinan fase aktif)
        $observasiData = [
            [
                'persalinan_id' => $persalinan->id,
                'kolom_partograf_ke' => 1,
                'waktu_pemeriksaan' => '2026-09-20 09:43:00',
                'djj_denyut_per_menit' => 140,
                'air_ketuban' => 'J',
                'penyusupan_kepala' => '0',
                'pembukaan_serviks' => 4.0,
                'turunnya_kepala' => 4,
                'frekuensi_kontraksi' => 3,
                'durasi_kontraksi' => 'antara_20_40',
                'oksitosin_unit' => null,
                'oksitosin_tetes_per_menit' => null,
                'obat_dan_cairan_iv' => 'RL 500ml',
                'tekanan_darah_sistolik' => 120,
                'tekanan_darah_diastolik' => 80,
                'nadi_per_menit' => 82,
                'suhu_tubuh' => 36.8,
                'urin_protein' => '-',
                'urin_aseton' => '-',
                'urin_volume_ml' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'persalinan_id' => $persalinan->id,
                'kolom_partograf_ke' => 2,
                'waktu_pemeriksaan' => '2026-09-20 10:13:00',
                'djj_denyut_per_menit' => 138,
                'air_ketuban' => 'J',
                'penyusupan_kepala' => '0',
                'pembukaan_serviks' => null,
                'turunnya_kepala' => 4,
                'frekuensi_kontraksi' => 3,
                'durasi_kontraksi' => 'antara_20_40',
                'oksitosin_unit' => null,
                'oksitosin_tetes_per_menit' => null,
                'obat_dan_cairan_iv' => null,
                'tekanan_darah_sistolik' => null,
                'tekanan_darah_diastolik' => null,
                'nadi_per_menit' => 80,
                'suhu_tubuh' => null,
                'urin_protein' => null,
                'urin_aseton' => null,
                'urin_volume_ml' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'persalinan_id' => $persalinan->id,
                'kolom_partograf_ke' => 3,
                'waktu_pemeriksaan' => '2026-09-20 10:43:00',
                'djj_denyut_per_menit' => 142,
                'air_ketuban' => 'J',
                'penyusupan_kepala' => '0',
                'pembukaan_serviks' => null,
                'turunnya_kepala' => 3,
                'frekuensi_kontraksi' => 4,
                'durasi_kontraksi' => 'antara_20_40',
                'oksitosin_unit' => null,
                'oksitosin_tetes_per_menit' => null,
                'obat_dan_cairan_iv' => null,
                'tekanan_darah_sistolik' => null,
                'tekanan_darah_diastolik' => null,
                'nadi_per_menit' => 84,
                'suhu_tubuh' => null,
                'urin_protein' => null,
                'urin_aseton' => null,
                'urin_volume_ml' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'persalinan_id' => $persalinan->id,
                'kolom_partograf_ke' => 4,
                'waktu_pemeriksaan' => '2026-09-20 11:13:00',
                'djj_denyut_per_menit' => 140,
                'air_ketuban' => 'J',
                'penyusupan_kepala' => '0',
                'pembukaan_serviks' => null,
                'turunnya_kepala' => 3,
                'frekuensi_kontraksi' => 4,
                'durasi_kontraksi' => 'antara_20_40',
                'oksitosin_unit' => null,
                'oksitosin_tetes_per_menit' => null,
                'obat_dan_cairan_iv' => null,
                'tekanan_darah_sistolik' => null,
                'tekanan_darah_diastolik' => null,
                'nadi_per_menit' => 82,
                'suhu_tubuh' => null,
                'urin_protein' => null,
                'urin_aseton' => null,
                'urin_volume_ml' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'persalinan_id' => $persalinan->id,
                'kolom_partograf_ke' => 5,
                'waktu_pemeriksaan' => '2026-09-20 11:43:00',
                'djj_denyut_per_menit' => 144,
                'air_ketuban' => 'J',
                'penyusupan_kepala' => '0',
                'pembukaan_serviks' => 7.0,
                'turunnya_kepala' => 2,
                'frekuensi_kontraksi' => 4,
                'durasi_kontraksi' => 'lebih_40',
                'oksitosin_unit' => null,
                'oksitosin_tetes_per_menit' => null,
                'obat_dan_cairan_iv' => 'RL 500ml',
                'tekanan_darah_sistolik' => 120,
                'tekanan_darah_diastolik' => 80,
                'nadi_per_menit' => 84,
                'suhu_tubuh' => 36.9,
                'urin_protein' => '-',
                'urin_aseton' => '-',
                'urin_volume_ml' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'persalinan_id' => $persalinan->id,
                'kolom_partograf_ke' => 6,
                'waktu_pemeriksaan' => '2026-09-20 12:13:00',
                'djj_denyut_per_menit' => 140,
                'air_ketuban' => 'J',
                'penyusupan_kepala' => '0',
                'pembukaan_serviks' => null,
                'turunnya_kepala' => 2,
                'frekuensi_kontraksi' => 5,
                'durasi_kontraksi' => 'lebih_40',
                'oksitosin_unit' => null,
                'oksitosin_tetes_per_menit' => null,
                'obat_dan_cairan_iv' => null,
                'tekanan_darah_sistolik' => null,
                'tekanan_darah_diastolik' => null,
                'nadi_per_menit' => 86,
                'suhu_tubuh' => null,
                'urin_protein' => null,
                'urin_aseton' => null,
                'urin_volume_ml' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'persalinan_id' => $persalinan->id,
                'kolom_partograf_ke' => 7,
                'waktu_pemeriksaan' => '2026-09-20 12:43:00',
                'djj_denyut_per_menit' => 142,
                'air_ketuban' => 'J',
                'penyusupan_kepala' => '0',
                'pembukaan_serviks' => 10.0,
                'turunnya_kepala' => 1,
                'frekuensi_kontraksi' => 5,
                'durasi_kontraksi' => 'lebih_40',
                'oksitosin_unit' => null,
                'oksitosin_tetes_per_menit' => null,
                'obat_dan_cairan_iv' => null,
                'tekanan_darah_sistolik' => null,
                'tekanan_darah_diastolik' => null,
                'nadi_per_menit' => 88,
                'suhu_tubuh' => null,
                'urin_protein' => null,
                'urin_aseton' => null,
                'urin_volume_ml' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Hapus data observasi lama untuk persalinan ini lalu masukkan data baru yang lengkap
        DB::table('observasi_partograf')->where('persalinan_id', $persalinan->id)->delete();
        foreach ($observasiData as $row) {
            DB::table('observasi_partograf')->insert($row);
        }

        // 3. Lengkapi juga seluruh 7 materi Edukasi KIE
        $kieMateriLengkap = [
            [
                'persalinan_id' => $persalinan->id,
                'tanggal_edukasi' => '2026-09-20',
                'materi_edukasi' => 'semua_nifas',
                'pelaksana' => 'Bidan Siti Nurhaliza, S.Tr.Keb',
                'keterangan' => 'Penjelasan menyeluruh tentang tahapan pemulihan masa nifas, tanda bahaya nifas, kebersihan diri, dan istirahat cukup.',
            ],
            [
                'persalinan_id' => $persalinan->id,
                'tanggal_edukasi' => '2026-09-20',
                'materi_edukasi' => 'breast_care',
                'pelaksana' => 'Bidan Siti Nurhaliza, S.Tr.Keb',
                'keterangan' => 'Edukasi perawatan payudara, pemijatan oksitosin untuk merangsang pengeluaran ASI, dan penanganan bendungan ASI.',
            ],
            [
                'persalinan_id' => $persalinan->id,
                'tanggal_edukasi' => '2026-09-20',
                'materi_edukasi' => 'asi',
                'pelaksana' => 'Bidan Siti Nurhaliza, S.Tr.Keb',
                'keterangan' => 'Konseling manajemen laktasi dan pemberian ASI eksklusif 6 bulan tanpa makanan tambahan. Ibu memahami perlekatan mulut bayi.',
            ],
            [
                'persalinan_id' => $persalinan->id,
                'tanggal_edukasi' => '2026-09-20',
                'materi_edukasi' => 'perawatan_tali_pusat',
                'pelaksana' => 'Bidan Siti Nurhaliza, S.Tr.Keb',
                'keterangan' => 'Edukasi perawatan tali pusat terbuka, bersih, dan kering tanpa membubuhi ramuan/alkohol/betadine.',
            ],
            [
                'persalinan_id' => $persalinan->id,
                'tanggal_edukasi' => '2026-09-20',
                'materi_edukasi' => 'kb',
                'pelaksana' => 'Bidan Siti Nurhaliza, S.Tr.Keb',
                'keterangan' => 'Konseling KB pascasalin dengan metode MAL (Metode Amenore Laktasi) dan rencana pemasangan IUD pascaplasenta.',
            ],
            [
                'persalinan_id' => $persalinan->id,
                'tanggal_edukasi' => '2026-09-20',
                'materi_edukasi' => 'gizi',
                'pelaksana' => 'Bidan Siti Nurhaliza, S.Tr.Keb',
                'keterangan' => 'Edukasi pola gizi seimbang bagi ibu menyusui (tinggi kalori & protein, sayuran hijau, buah-buahan, dan minum air minimal 3 liter/hari).',
            ],
            [
                'persalinan_id' => $persalinan->id,
                'tanggal_edukasi' => '2026-09-20',
                'materi_edukasi' => 'imunisasi',
                'pelaksana' => 'Bidan Siti Nurhaliza, S.Tr.Keb',
                'keterangan' => 'Pemberian edukasi jadwal imunisasi dasar lengkap bayi (Hb0, BCG, Polio, DPT-Hb-Hib, PCV, Rotavirus, dan Campak/Rubella).',
            ],
        ];

        foreach ($kieMateriLengkap as $kie) {
            DB::table('edukasi_kie')->updateOrInsert(
                [
                    'persalinan_id' => $kie['persalinan_id'],
                    'materi_edukasi' => $kie['materi_edukasi'],
                ],
                [
                    'tanggal_edukasi' => $kie['tanggal_edukasi'],
                    'pelaksana' => $kie['pelaksana'],
                    'keterangan' => $kie['keterangan'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->command->info('Data Observasi Partograf (7 kolom pemeriksaan lengkap), riwayat persalinan, dan 7 KIE berhasil di-seed!');
    }
}
