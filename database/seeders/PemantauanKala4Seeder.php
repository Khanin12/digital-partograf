<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PemantauanKala4Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari data persalinan pertama aktif (Ny. Siti Aminah)
        $persalinan = DB::table('persalinan')
            ->whereNull('deleted_at')
            ->orderBy('id', 'asc')
            ->first();

        if (! $persalinan) {
            $this->command->warn('Data persalinan tidak ditemukan. Seeder Pemantauan Kala IV dibatalkan.');

            return;
        }

        // Standar APN: 6 kali pemantauan Kala IV (4 kali di Jam ke-1, 2 kali di Jam ke-2)
        $observasi = [
            // Jam ke-1 (tiap 15 menit)
            [
                'persalinan_id' => $persalinan->id,
                'jam_ke' => 1,
                'pemantauan_ke' => 1,
                'waktu_pemantauan' => '12:15:00',
                'tekanan_darah_sistolik' => 120,
                'tekanan_darah_diastolik' => 80,
                'nadi_per_menit' => 82,
                'suhu_tubuh' => 36.8, // 1x per jam
                'tinggi_fundus_uteri' => '2 jari di bawah pusat',
                'kontraksi_uterus' => 'Keras',
                'kandung_kemih' => 'Kosong',
                'perdarahan_ml' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'persalinan_id' => $persalinan->id,
                'jam_ke' => 1,
                'pemantauan_ke' => 2,
                'waktu_pemantauan' => '12:30:00',
                'tekanan_darah_sistolik' => 120,
                'tekanan_darah_diastolik' => 80,
                'nadi_per_menit' => 80,
                'suhu_tubuh' => null, // tidak diukur tiap 15 menit
                'tinggi_fundus_uteri' => '2 jari di bawah pusat',
                'kontraksi_uterus' => 'Keras',
                'kandung_kemih' => 'Kosong',
                'perdarahan_ml' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'persalinan_id' => $persalinan->id,
                'jam_ke' => 1,
                'pemantauan_ke' => 3,
                'waktu_pemantauan' => '12:45:00',
                'tekanan_darah_sistolik' => 118,
                'tekanan_darah_diastolik' => 78,
                'nadi_per_menit' => 78,
                'suhu_tubuh' => null,
                'tinggi_fundus_uteri' => '2 jari di bawah pusat',
                'kontraksi_uterus' => 'Keras',
                'kandung_kemih' => 'Kosong',
                'perdarahan_ml' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'persalinan_id' => $persalinan->id,
                'jam_ke' => 1,
                'pemantauan_ke' => 4,
                'waktu_pemantauan' => '13:00:00',
                'tekanan_darah_sistolik' => 120,
                'tekanan_darah_diastolik' => 80,
                'nadi_per_menit' => 80,
                'suhu_tubuh' => null,
                'tinggi_fundus_uteri' => '2 jari di bawah pusat',
                'kontraksi_uterus' => 'Keras',
                'kandung_kemih' => 'Kosong',
                'perdarahan_ml' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Jam ke-2 (tiap 30 menit)
            [
                'persalinan_id' => $persalinan->id,
                'jam_ke' => 2,
                'pemantauan_ke' => 1,
                'waktu_pemantauan' => '13:30:00',
                'tekanan_darah_sistolik' => 120,
                'tekanan_darah_diastolik' => 80,
                'nadi_per_menit' => 78,
                'suhu_tubuh' => 36.7, // 1x pada jam ke-2
                'tinggi_fundus_uteri' => '2 jari di bawah pusat',
                'kontraksi_uterus' => 'Keras',
                'kandung_kemih' => 'Kosong',
                'perdarahan_ml' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'persalinan_id' => $persalinan->id,
                'jam_ke' => 2,
                'pemantauan_ke' => 2,
                'waktu_pemantauan' => '14:00:00',
                'tekanan_darah_sistolik' => 120,
                'tekanan_darah_diastolik' => 80,
                'nadi_per_menit' => 76,
                'suhu_tubuh' => null,
                'tinggi_fundus_uteri' => '2 jari di bawah pusat',
                'kontraksi_uterus' => 'Keras',
                'kandung_kemih' => 'Kosong',
                'perdarahan_ml' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($observasi as $obs) {
            DB::table('pemantauan_kala_4')->updateOrInsert(
                [
                    'persalinan_id' => $obs['persalinan_id'],
                    'jam_ke' => $obs['jam_ke'],
                    'pemantauan_ke' => $obs['pemantauan_ke'],
                ],
                $obs
            );
        }

        $this->command->info('Data Seeder Pemantauan Kala IV berhasil dimasukkan!');
    }
}
