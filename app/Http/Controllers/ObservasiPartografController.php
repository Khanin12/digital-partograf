<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObservasiPartografController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $selected_persalinan_id = $request->query('persalinan_id');

        $query = DB::table('observasi_partograf')
            ->leftJoin('persalinan', 'observasi_partograf.persalinan_id', '=', 'persalinan.id')
            ->leftJoin('pasien', 'persalinan.pasien_id', '=', 'pasien.id')
            ->whereNull('observasi_partograf.deleted_at')
            ->select(
                'observasi_partograf.*',
                'persalinan.pasien_id',
                'persalinan.nama_fasilitas',
                'persalinan.waktu_masuk',
                'persalinan.gravida',
                'persalinan.para',
                'persalinan.abortus',
                'persalinan.usia_kehamilan_minggu',
                'pasien.nama_ibu as nama_pasien',
                'pasien.nomor_register as nomer_registerer',
                'pasien.nomor_register',
                'pasien.usia_ibu'
            );

        if ($selected_persalinan_id) {
            $query->where('observasi_partograf.persalinan_id', $selected_persalinan_id)
                ->orderBy('observasi_partograf.kolom_partograf_ke', 'asc')
                ->orderBy('observasi_partograf.waktu_pemeriksaan', 'asc');
        } else {
            $query->orderBy('observasi_partograf.waktu_pemeriksaan', 'desc');
        }

        $data_observasi = $query->get();

        $data_persalinan = DB::table('persalinan')
            ->leftJoin('pasien', 'persalinan.pasien_id', '=', 'pasien.id')
            ->whereNull('persalinan.deleted_at')
            ->select(
                'persalinan.id',
                'persalinan.waktu_masuk',
                'persalinan.waktu_ketuban_pecah',
                'persalinan.waktu_mules_mulai',
                'persalinan.gravida',
                'persalinan.para',
                'persalinan.abortus',
                'persalinan.usia_kehamilan_minggu',
                'persalinan.status_persalinan',
                'persalinan.nama_penolong',
                'persalinan.nama_fasilitas',
                'pasien.nama_ibu',
                'pasien.nama_ibu as nama_pasien',
                'pasien.nomor_register as nomer_registerer',
                'pasien.nomor_register',
                'pasien.usia_ibu'
            )
            ->orderBy('persalinan.created_at', 'desc')
            ->get();

        $selected_persalinan = $selected_persalinan_id
            ? $data_persalinan->firstWhere('id', (int) $selected_persalinan_id)
            : null;

        // Auto suggestion for next partograf column (1 - 16)
        $next_kolom = 1;
        if ($selected_persalinan_id && $data_observasi->isNotEmpty()) {
            $max_kolom = $data_observasi->max('kolom_partograf_ke');
            $next_kolom = $max_kolom ? min(16, $max_kolom + 1) : 1;
        }

        return view('observasi_partograf.index', compact(
            'data_observasi',
            'data_persalinan',
            'selected_persalinan_id',
            'selected_persalinan',
            'next_kolom'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validasi = $request->validate([
            'persalinan_id' => 'required|exists:persalinan,id',
            'waktu_pemeriksaan' => 'required|date',
            'kolom_partograf_ke' => 'nullable|integer|min:1|max:16',
            'djj_denyut_per_menit' => 'nullable|integer|min:0|max:300',
            'air_ketuban' => 'nullable|in:U,J,M,D,K',
            'penyusupan_kepala' => 'nullable|in:0,1,2,3',
            'pembukaan_serviks' => 'nullable|numeric|min:0|max:10',
            'turunnya_kepala' => 'nullable|integer|min:0|max:5',
            'frekuensi_kontraksi' => 'nullable|integer|min:0|max:10',
            'durasi_kontraksi' => 'nullable|in:kurang_20,antara_20_40,lebih_40',
            'oksitosin_unit' => 'nullable|numeric|min:0|max:99.99',
            'oksitosin_tetes_per_menit' => 'nullable|integer|min:0|max:300',
            'obat_dan_cairan_iv' => 'nullable|string',
            'tekanan_darah_sistolik' => 'nullable|integer|min:0|max:300',
            'tekanan_darah_diastolik' => 'nullable|integer|min:0|max:200',
            'nadi_per_menit' => 'nullable|integer|min:0|max:300',
            'suhu_tubuh' => 'nullable|numeric|min:30|max:45',
            'urin_protein' => 'nullable|string|max:50',
            'urin_aseton' => 'nullable|string|max:50',
            'urin_volume_ml' => 'nullable|integer|min:0|max:5000',
        ]);

        $validasi['created_at'] = now();
        $validasi['updated_at'] = now();

        DB::table('observasi_partograf')->insert($validasi);

        if ($request->input('redirect_to') === 'input_terpadu') {
            $pasienId = $request->input('pasien_id') ?: DB::table('persalinan')->where('id', $request->persalinan_id)->value('pasien_id');

            return redirect()->route('pasien.input_terpadu', [
                'pasien_id' => $pasienId,
                'tab' => 'observasi',
            ])->with('success', 'Data observasi partograf berhasil ditambahkan!');
        }

        $redirectUrl = route('observasi-partograf.index', ['persalinan_id' => $request->persalinan_id]);

        return redirect($redirectUrl)->with('success', 'Data observasi partograf berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validasi = $request->validate([
            'persalinan_id' => 'required|exists:persalinan,id',
            'waktu_pemeriksaan' => 'required|date',
            'kolom_partograf_ke' => 'nullable|integer|min:1|max:16',
            'djj_denyut_per_menit' => 'nullable|integer|min:0|max:300',
            'air_ketuban' => 'nullable|in:U,J,M,D,K',
            'penyusupan_kepala' => 'nullable|in:0,1,2,3',
            'pembukaan_serviks' => 'nullable|numeric|min:0|max:10',
            'turunnya_kepala' => 'nullable|integer|min:0|max:5',
            'frekuensi_kontraksi' => 'nullable|integer|min:0|max:10',
            'durasi_kontraksi' => 'nullable|in:kurang_20,antara_20_40,lebih_40',
            'oksitosin_unit' => 'nullable|numeric|min:0|max:99.99',
            'oksitosin_tetes_per_menit' => 'nullable|integer|min:0|max:300',
            'obat_dan_cairan_iv' => 'nullable|string',
            'tekanan_darah_sistolik' => 'nullable|integer|min:0|max:300',
            'tekanan_darah_diastolik' => 'nullable|integer|min:0|max:200',
            'nadi_per_menit' => 'nullable|integer|min:0|max:300',
            'suhu_tubuh' => 'nullable|numeric|min:30|max:45',
            'urin_protein' => 'nullable|string|max:50',
            'urin_aseton' => 'nullable|string|max:50',
            'urin_volume_ml' => 'nullable|integer|min:0|max:5000',
        ]);

        $validasi['updated_at'] = now();

        DB::table('observasi_partograf')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update($validasi);

        if ($request->input('redirect_to') === 'input_terpadu') {
            $pasienId = $request->input('pasien_id') ?: DB::table('persalinan')->where('id', $validasi['persalinan_id'])->value('pasien_id');

            return redirect()->route('pasien.input_terpadu', [
                'pasien_id' => $pasienId,
                'tab' => 'observasi',
            ])->with('success', 'Data observasi partograf berhasil diperbarui!');
        }

        $redirectUrl = route('observasi-partograf.index', ['persalinan_id' => $request->persalinan_id]);

        return redirect($redirectUrl)->with('success', 'Data observasi partograf berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $observasi = DB::table('observasi_partograf')->where('id', $id)->first();
        $persalinan_id = $observasi ? $observasi->persalinan_id : null;

        DB::table('observasi_partograf')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);

        if (request('redirect_to') === 'input_terpadu') {
            $pasienId = request('pasien_id') ?: ($persalinan_id ? DB::table('persalinan')->where('id', $persalinan_id)->value('pasien_id') : null);
            if ($pasienId) {
                return redirect()->route('pasien.input_terpadu', [
                    'pasien_id' => $pasienId,
                    'tab' => 'observasi',
                ])->with('success', 'Data observasi partograf berhasil dihapus!');
            }
        }

        $redirect = $persalinan_id
            ? route('observasi-partograf.index', ['persalinan_id' => $persalinan_id])
            : route('observasi-partograf.index');

        return redirect($redirect)->with('success', 'Data observasi partograf berhasil dihapus!');
    }
}
