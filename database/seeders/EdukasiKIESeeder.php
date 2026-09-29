<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EdukasiKIESeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari persalinan pertama (Ny. Siti Aminah)
        $persalinan = DB::table('persalinan')
            ->whereNull('deleted_at')
            ->orderBy('id', 'asc')
            ->first();

        if (! $persalinan) {
            $this->command->warn('Data persalinan tidak ditemukan. Seeder Edukasi KIE dibatalkan.');

            return;
        }

        $edukasiList = [
            [
                'persalinan_id' => $persalinan->id,
                'tanggal_edukasi' => now()->toDateString(),
                'materi_edukasi' => 'asi',
                'pelaksana' => 'Bidan Siti Nurhaliza, S.Tr.Keb',
                'keterangan' => 'Konseling manajemen laktasi dan pemberian ASI eksklusif 6 bulan tanpa makanan tambahan. Ibu memahami perlekatan mulut bayi pada areola.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'persalinan_id' => $persalinan->id,
                'tanggal_edukasi' => now()->toDateString(),
                'materi_edukasi' => 'breast_care',
                'pelaksana' => 'Bidan Siti Nurhaliza, S.Tr.Keb',
                'keterangan' => 'Edukasi perawatan payudara, pemijatan oksitosin untuk merangsang pengeluaran ASI, dan cara mengatasi bendungan ASI.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'persalinan_id' => $persalinan->id,
                'tanggal_edukasi' => now()->toDateString(),
                'materi_edukasi' => 'perawatan_tali_pusat',
                'pelaksana' => 'Bidan Siti Nurhaliza, S.Tr.Keb',
                'keterangan' => 'Edukasi perawatan tali pusat terbuka, bersih, dan kering tanpa membubuhi ramuan/alkohol/betadine.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'persalinan_id' => $persalinan->id,
                'tanggal_edukasi' => now()->toDateString(),
                'materi_edukasi' => 'kb',
                'pelaksana' => 'Bidan Siti Nurhaliza, S.Tr.Keb',
                'keterangan' => 'Konseling KB pascasalin dengan metode MAL (Metode Amenore Laktasi) dan rencana pemasangan IUD pascaplasenta atau suntik progestin.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($edukasiList as $edukasi) {
            DB::table('edukasi_kie')->updateOrInsert(
                [
                    'persalinan_id' => $edukasi['persalinan_id'],
                    'materi_edukasi' => $edukasi['materi_edukasi'],
                ],
                $edukasi
            );
        }

        $this->command->info('Data Seeder Edukasi KIE berhasil dimasukkan!');
    }
}
