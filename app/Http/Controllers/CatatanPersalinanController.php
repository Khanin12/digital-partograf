<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatatanPersalinanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data_catatan = DB::table('catatan_persalinan')
            ->leftJoin('persalinan', 'catatan_persalinan.persalinan_id', '=', 'persalinan.id')
            ->leftJoin('pasien', 'persalinan.pasien_id', '=', 'pasien.id')
            ->whereNull('catatan_persalinan.deleted_at')
            ->select(
                'catatan_persalinan.*',
                // Relasi Persalinan Lengkap
                'persalinan.id as persalinan_id',
                'persalinan.pasien_id',
                'persalinan.gravida',
                'persalinan.para',
                'persalinan.abortus',
                'persalinan.usia_kehamilan_minggu',
                'persalinan.nama_fasilitas',
                'persalinan.waktu_masuk',
                'persalinan.waktu_ketuban_pecah',
                'persalinan.waktu_mules_mulai',
                'persalinan.nama_penolong',
                'persalinan.tempat_persalinan',
                'persalinan.status_persalinan',
                'persalinan.waktu_makan_terakhir',
                'persalinan.jenis_makan',
                'persalinan.porsi_makan',
                'persalinan.waktu_minum_terakhir',
                'persalinan.jenis_minum',
                'persalinan.porsi_minum',
                // Relasi Pasien Lengkap
                'pasien.nomor_register',
                'pasien.nomor_register as no_register',
                'pasien.nama_ibu',
                'pasien.nama_ibu as ibu_pasien',
                'pasien.nama_ibu as nama_pasien',
                'pasien.nama_suami',
                'pasien.usia_ibu',
                'pasien.usia_suami',
                'pasien.alamat',
                'pasien.nomor_telepon'
            )
            ->orderBy('catatan_persalinan.created_at', 'desc')
            ->get();

        $tercatat_ids = DB::table('catatan_persalinan')
            ->whereNull('deleted_at')
            ->pluck('persalinan_id');

        $data_persalinan = DB::table('persalinan')
            ->join('pasien', 'persalinan.pasien_id', '=', 'pasien.id')
            ->whereNull('persalinan.deleted_at')
            ->whereNotIn('persalinan.id', $tercatat_ids)
            ->select(
                'persalinan.id',
                'persalinan.status_persalinan',
                'persalinan.waktu_masuk',
                'persalinan.gravida',
                'persalinan.para',
                'persalinan.abortus',
                'persalinan.nama_penolong',
                'pasien.nama_ibu as ibu_pasien',
                'pasien.nomor_register as no_register',
                'pasien.usia_ibu'
            )
            ->orderBy('persalinan.created_at', 'desc')
            ->get();

        return view('catatan_persalinan.index', compact('data_catatan', 'data_persalinan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'persalinan_id' => 'required|exists:persalinan,id|unique:catatan_persalinan,persalinan_id,NULL,id,deleted_at,NULL',

            // KALA I
            'kala1_melewati_garis_waspada' => 'required|in:0,1',
            'kala1_masalah_lain' => 'nullable|string',
            'kala1_penatalaksanaan' => 'nullable|string',
            'kala1_hasil_evaluasi' => 'nullable|string',

            // KALA II
            'kala2_lama_menit' => 'nullable|integer|min:0|max:600',
            'kala2_episiotomi' => 'required|in:0,1',
            'kala2_indikasi_episiotomi' => 'nullable|string|max:255',
            'kala2_pendamping' => 'nullable|in:suami,keluarga,teman,dukun,tidak_ada',
            'kala2_gawat_janin' => 'nullable|string|max:255',
            'kala2_distosia_bahu' => 'nullable|string|max:255',
            'kala2_penatalaksanaan' => 'nullable|string',
            'kala2_hasil_evaluasi' => 'nullable|string',

            // KALA III
            'kala3_lama_menit' => 'nullable|integer|min:0|max:300',
            'kala3_jumlah_perdarahan_ml' => 'nullable|integer|min:0|max:5000',
            'kala3_oksitosin_10u_2menit' => 'required|in:0,1',
            'kala3_alasan_tidak_oksitosin' => 'nullable|string|max:255',
            'kala3_oksitosin_ulang' => 'required|in:0,1',
            'kala3_alasan_oksitosin_ulang' => 'nullable|string|max:255',
            'kala3_penegangan_tali_pusat' => 'required|in:0,1',
            'kala3_alasan_tidak_ptp' => 'nullable|string|max:255',
            'kala3_masase_fundus' => 'required|in:0,1',
            'kala3_alasan_tidak_masase' => 'nullable|string|max:255',
            'kala3_derajat_laserasi' => 'required|in:tidak_ada,1,2,3,4',
            'kala3_tindakan_laserasi' => 'nullable|string|max:255',
            'kala3_atonia_uteri' => 'nullable|string|max:255',
            'kala3_penatalaksanaan' => 'nullable|string',
            'kala3_hasil_evaluasi' => 'nullable|string',

            // KALA IV
            'kala4_masalah' => 'nullable|string',
            'kala4_penatalaksanaan' => 'nullable|string',
            'kala4_hasil_evaluasi' => 'nullable|string',
        ]);

        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        DB::table('catatan_persalinan')->insert($validated);

        if ($request->input('redirect_to') === 'input_terpadu') {
            $pasienId = $request->input('pasien_id') ?: DB::table('persalinan')->where('id', $validated['persalinan_id'])->value('pasien_id');

            return redirect()->route('pasien.input_terpadu', [
                'pasien_id' => $pasienId,
                'tab' => 'catatan',
            ])->with('success', 'Data catatan persalinan berhasil ditambahkan!');
        }

        return redirect()->route('catatan-persalinan.index')->with('success', 'Data catatan persalinan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $catatan = DB::table('catatan_persalinan')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (! $catatan) {
            return redirect()->route('catatan-persalinan.index')
                ->with('error', 'Catatan persalinan tidak ditemukan.');
        }

        // Relasi Persalinan
        $persalinan = DB::table('persalinan')
            ->where('id', $catatan->persalinan_id)
            ->first();

        // Relasi Pasien
        $pasien = $persalinan
            ? DB::table('pasien')->where('id', $persalinan->pasien_id)->first()
            : null;

        // Relasi Tambahan Terkait Persalinan
        $bayi = $persalinan
            ? DB::table('bayi_baru_lahir')->where('persalinan_id', $persalinan->id)->whereNull('deleted_at')->get()
            : collect();

        $pemantauan_kala_4 = $persalinan
            ? DB::table('pemantauan_kala_4')->where('persalinan_id', $persalinan->id)->whereNull('deleted_at')->orderBy('jam_ke')->orderBy('pemantauan_ke')->get()
            : collect();

        $edukasi = $persalinan
            ? DB::table('edukasi_kie')->where('persalinan_id', $persalinan->id)->whereNull('deleted_at')->get()
            : collect();

        $klasifikasi = $persalinan
            ? DB::table('hasil_klasifikasi')->where('persalinan_id', $persalinan->id)->whereNull('deleted_at')->first()
            : null;

        return view('catatan_persalinan.show', compact(
            'catatan',
            'persalinan',
            'pasien',
            'bayi',
            'pemantauan_kala_4',
            'edukasi',
            'klasifikasi'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'persalinan_id' => 'required|exists:persalinan,id|unique:catatan_persalinan,persalinan_id,'.$id.',id,deleted_at,NULL',

            // KALA I
            'kala1_melewati_garis_waspada' => 'required|in:0,1',
            'kala1_masalah_lain' => 'nullable|string',
            'kala1_penatalaksanaan' => 'nullable|string',
            'kala1_hasil_evaluasi' => 'nullable|string',

            // KALA II
            'kala2_lama_menit' => 'nullable|integer|min:0|max:600',
            'kala2_episiotomi' => 'required|in:0,1',
            'kala2_indikasi_episiotomi' => 'nullable|string|max:255',
            'kala2_pendamping' => 'nullable|in:suami,keluarga,teman,dukun,tidak_ada',
            'kala2_gawat_janin' => 'nullable|string|max:255',
            'kala2_distosia_bahu' => 'nullable|string|max:255',
            'kala2_penatalaksanaan' => 'nullable|string',
            'kala2_hasil_evaluasi' => 'nullable|string',

            // KALA III
            'kala3_lama_menit' => 'nullable|integer|min:0|max:300',
            'kala3_jumlah_perdarahan_ml' => 'nullable|integer|min:0|max:5000',
            'kala3_oksitosin_10u_2menit' => 'required|in:0,1',
            'kala3_alasan_tidak_oksitosin' => 'nullable|string|max:255',
            'kala3_oksitosin_ulang' => 'required|in:0,1',
            'kala3_alasan_oksitosin_ulang' => 'nullable|string|max:255',
            'kala3_penegangan_tali_pusat' => 'required|in:0,1',
            'kala3_alasan_tidak_ptp' => 'nullable|string|max:255',
            'kala3_masase_fundus' => 'required|in:0,1',
            'kala3_alasan_tidak_masase' => 'nullable|string|max:255',
            'kala3_derajat_laserasi' => 'required|in:tidak_ada,1,2,3,4',
            'kala3_tindakan_laserasi' => 'nullable|string|max:255',
            'kala3_atonia_uteri' => 'nullable|string|max:255',
            'kala3_penatalaksanaan' => 'nullable|string',
            'kala3_hasil_evaluasi' => 'nullable|string',

            // KALA IV
            'kala4_masalah' => 'nullable|string',
            'kala4_penatalaksanaan' => 'nullable|string',
            'kala4_hasil_evaluasi' => 'nullable|string',
        ]);

        $validated['updated_at'] = now();

        DB::table('catatan_persalinan')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update($validated);

        if ($request->input('redirect_to') === 'input_terpadu') {
            $pasienId = $request->input('pasien_id') ?: DB::table('persalinan')->where('id', $validated['persalinan_id'])->value('pasien_id');

            return redirect()->route('pasien.input_terpadu', [
                'pasien_id' => $pasienId,
                'tab' => 'catatan',
            ])->with('success', 'Data catatan persalinan berhasil diperbarui!');
        }

        return redirect()->route('catatan-persalinan.index')->with('success', 'Data catatan persalinan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $catatan = DB::table('catatan_persalinan')->where('id', $id)->first();
        $persalinan_id = $catatan ? $catatan->persalinan_id : null;

        DB::table('catatan_persalinan')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);

        if (request('redirect_to') === 'input_terpadu') {
            $pasienId = request('pasien_id') ?: ($persalinan_id ? DB::table('persalinan')->where('id', $persalinan_id)->value('pasien_id') : null);
            if ($pasienId) {
                return redirect()->route('pasien.input_terpadu', [
                    'pasien_id' => $pasienId,
                    'tab' => 'catatan',
                ])->with('success', 'Data catatan persalinan berhasil dihapus!');
            }
        }

        return redirect()->route('catatan-persalinan.index')->with('success', 'Data catatan persalinan berhasil dihapus!');
    }
}
