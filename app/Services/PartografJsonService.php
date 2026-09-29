<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PartografJsonService
{
    /**
     * Kompilasi seluruh relasi data persalinan menjadi skema JSON partograf terstruktur.
     */
    public function compile(int $persalinanId): array
    {
        // 1. Data Pasien & Persalinan
        $persalinan = DB::table('persalinan')
            ->leftJoin('pasien', 'persalinan.pasien_id', '=', 'pasien.id')
            ->where('persalinan.id', $persalinanId)
            ->whereNull('persalinan.deleted_at')
            ->select(
                'persalinan.*',
                'pasien.nomor_register',
                'pasien.nama_ibu',
                'pasien.nama_suami',
                'pasien.usia_ibu',
                'pasien.usia_suami',
                'pasien.alamat',
                'pasien.nomor_telepon'
            )
            ->first();

        if (! $persalinan) {
            abort(404, 'Data persalinan tidak ditemukan.');
        }

        // Siapkan 10 digit register kotak
        $registerStr = str_pad(preg_replace('/[^0-9A-Za-z]/', '', (string) ($persalinan->nomor_register ?? '')), 10, ' ', STR_PAD_RIGHT);
        $registerDigits = array_slice(str_split(substr($registerStr, 0, 10)), 0, 10);

        // 2. Data Observasi Matriks (Kolom 1 s/d 16)
        $observasiRaw = DB::table('observasi_partograf')
            ->where('persalinan_id', $persalinanId)
            ->whereNull('deleted_at')
            ->orderBy('kolom_partograf_ke', 'asc')
            ->get();

        $kolomMatrix = [];
        $trendDjj = [];
        $trendPembukaan = [];
        $trendPenurunan = [];

        for ($i = 1; $i <= 16; $i++) {
            $item = $observasiRaw->firstWhere('kolom_partograf_ke', $i);
            if ($item) {
                $kolomMatrix[$i] = [
                    'kolom_ke' => $i,
                    'waktu_pemeriksaan' => $item->waktu_pemeriksaan ? Carbon::parse($item->waktu_pemeriksaan)->format('H:i') : null,
                    'djj' => $item->djj_denyut_per_menit,
                    'air_ketuban' => $item->air_ketuban,
                    'penyusupan' => $item->penyusupan_kepala,
                    'pembukaan' => $item->pembukaan_serviks !== null ? (float) $item->pembukaan_serviks : null,
                    'penurunan' => $item->turunnya_kepala !== null ? (int) $item->turunnya_kepala : null,
                    'kontraksi_frekuensi' => $item->frekuensi_kontraksi,
                    'kontraksi_durasi' => $item->durasi_kontraksi,
                    'oksitosin_unit' => $item->oksitosin_unit,
                    'oksitosin_tpm' => $item->oksitosin_tetes_per_menit,
                    'obat_cairan_iv' => $item->obat_dan_cairan_iv,
                    'tekanan_darah_sistolik' => $item->tekanan_darah_sistolik,
                    'tekanan_darah_diastolik' => $item->tekanan_darah_diastolik,
                    'nadi' => $item->nadi_per_menit,
                    'suhu' => $item->suhu_tubuh,
                    'urin_protein' => $item->urin_protein,
                    'urin_aseton' => $item->urin_aseton,
                    'urin_volume_ml' => $item->urin_volume_ml,
                ];

                if ($item->djj_denyut_per_menit !== null) {
                    $trendDjj[] = ['kolom' => $i, 'djj' => (int) $item->djj_denyut_per_menit];
                }
                if ($item->pembukaan_serviks !== null) {
                    $trendPembukaan[] = ['kolom' => $i, 'pembukaan' => (float) $item->pembukaan_serviks];
                }
                if ($item->turunnya_kepala !== null) {
                    $trendPenurunan[] = ['kolom' => $i, 'penurunan' => (int) $item->turunnya_kepala];
                }
            } else {
                $kolomMatrix[$i] = [
                    'kolom_ke' => $i,
                    'waktu_pemeriksaan' => null,
                    'djj' => null,
                    'air_ketuban' => null,
                    'penyusupan' => null,
                    'pembukaan' => null,
                    'penurunan' => null,
                    'kontraksi_frekuensi' => null,
                    'kontraksi_durasi' => null,
                    'oksitosin_unit' => null,
                    'oksitosin_tpm' => null,
                    'obat_cairan_iv' => null,
                    'tekanan_darah_sistolik' => null,
                    'tekanan_darah_diastolik' => null,
                    'nadi' => null,
                    'suhu' => null,
                    'urin_protein' => null,
                    'urin_aseton' => null,
                    'urin_volume_ml' => null,
                ];
            }
        }

        // 3. Catatan Persalinan Kala I - IV
        $catatanRaw = DB::table('catatan_persalinan')
            ->where('persalinan_id', $persalinanId)
            ->whereNull('deleted_at')
            ->first();

        // 4. Bayi Baru Lahir
        $bayiList = DB::table('bayi_baru_lahir')
            ->where('persalinan_id', $persalinanId)
            ->whereNull('deleted_at')
            ->orderBy('urutan_kelahiran', 'asc')
            ->get()
            ->map(fn ($i) => (array) $i)
            ->toArray();
        $bayiUtama = ! empty($bayiList) ? $bayiList[0] : [];

        // 5. Pemantauan Kala IV
        $kala4Raw = DB::table('pemantauan_kala_4')
            ->where('persalinan_id', $persalinanId)
            ->whereNull('deleted_at')
            ->orderBy('jam_ke', 'asc')
            ->orderBy('pemantauan_ke', 'asc')
            ->get()
            ->map(fn ($i) => (array) $i)
            ->toArray();

        // 6. Edukasi KIE
        $kieRaw = DB::table('edukasi_kie')
            ->where('persalinan_id', $persalinanId)
            ->whereNull('deleted_at')
            ->orderBy('tanggal_edukasi', 'asc')
            ->get()
            ->map(fn ($i) => (array) $i)
            ->toArray();

        return [
            'metadata' => [
                'persalinan_id' => $persalinan->id,
                'pasien_id' => $persalinan->pasien_id,
                'generated_at' => now()->toIso8601String(),
                'version' => '1.0-APN-KEMENKES',
            ],
            'identitas' => [
                'nomor_register' => $persalinan->nomor_register,
                'register_digits' => $registerDigits,
                'nama_ibu' => $persalinan->nama_ibu,
                'nama_suami' => $persalinan->nama_suami,
                'usia_ibu' => $persalinan->usia_ibu,
                'usia_suami' => $persalinan->usia_suami,
                'gravida' => $persalinan->gravida,
                'para' => $persalinan->para,
                'abortus' => $persalinan->abortus,
                'usia_kehamilan_minggu' => $persalinan->usia_kehamilan_minggu,
                'nama_fasilitas' => $persalinan->nama_fasilitas,
                'waktu_masuk' => $persalinan->waktu_masuk,
                'tanggal_masuk_formatted' => $persalinan->waktu_masuk ? Carbon::parse($persalinan->waktu_masuk)->translatedFormat('d F Y') : null,
                'jam_masuk_formatted' => $persalinan->waktu_masuk ? Carbon::parse($persalinan->waktu_masuk)->format('H:i') : null,
                'waktu_ketuban_pecah' => $persalinan->waktu_ketuban_pecah,
                'jam_ketuban_pecah_formatted' => $persalinan->waktu_ketuban_pecah ? Carbon::parse($persalinan->waktu_ketuban_pecah)->format('H:i') : null,
                'waktu_mules_mulai' => $persalinan->waktu_mules_mulai,
                'jam_mules_mulai_formatted' => $persalinan->waktu_mules_mulai ? Carbon::parse($persalinan->waktu_mules_mulai)->format('H:i') : null,
                'alamat' => $persalinan->alamat,
                'nomor_telepon' => $persalinan->nomor_telepon,
                'tempat_persalinan' => $persalinan->tempat_persalinan,
                'nama_penolong' => $persalinan->nama_penolong,
                'status_persalinan' => $persalinan->status_persalinan,
                'waktu_makan_terakhir' => $persalinan->waktu_makan_terakhir,
                'jam_makan_terakhir_formatted' => $persalinan->waktu_makan_terakhir ? Carbon::parse($persalinan->waktu_makan_terakhir)->format('H:i') : null,
                'jenis_makan' => $persalinan->jenis_makan,
                'porsi_makan' => $persalinan->porsi_makan,
                'waktu_minum_terakhir' => $persalinan->waktu_minum_terakhir,
                'jam_minum_terakhir_formatted' => $persalinan->waktu_minum_terakhir ? Carbon::parse($persalinan->waktu_minum_terakhir)->format('H:i') : null,
                'jenis_minum' => $persalinan->jenis_minum,
                'porsi_minum' => $persalinan->porsi_minum,
            ],
            'observasi' => [
                'kolom' => $kolomMatrix,
                'trend_djj' => $trendDjj,
                'trend_pembukaan' => $trendPembukaan,
                'trend_penurunan' => $trendPenurunan,
            ],
            'catatan' => (array) $catatanRaw,
            'bayi' => (array) $bayiUtama,
            'bayi_list' => $bayiList,
            'kala_4' => $kala4Raw,
            'kie' => $kieRaw,
        ];
    }

    /**
     * Simpan atau perbarui snapshot parto_json ke tabel hasil_partograf.
     */
    public function saveSnapshot(int $persalinanId): array
    {
        $payload = $this->compile($persalinanId);

        DB::table('hasil_partograf')->updateOrInsert(
            ['persalinan_id' => $persalinanId],
            [
                'parto_json' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'status_sinkronisasi' => 'final',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return $payload;
    }

    /**
     * Dapatkan snapshot parto_json jika ada, atau kompilasi langsung.
     */
    public function getOrCompile(int $persalinanId): array
    {
        $existing = DB::table('hasil_partograf')
            ->where('persalinan_id', $persalinanId)
            ->whereNull('deleted_at')
            ->first();

        if ($existing && $existing->parto_json) {
            $data = json_decode($existing->parto_json, true);
            if (is_array($data) && ! empty($data)) {
                return $data;
            }
        }

        return $this->saveSnapshot($persalinanId);
    }
}
