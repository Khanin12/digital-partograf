<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BayiBaruLahirController extends Controller
{
    /**
     * Tampilkan data daftar bayi baru lahir.
     */
    public function index()
    {
        $data_bayi_baru_lahir = DB::table('bayi_baru_lahir')
            ->leftJoin('persalinan', 'bayi_baru_lahir.persalinan_id', '=', 'persalinan.id')
            ->leftJoin('pasien', 'persalinan.pasien_id', '=', 'pasien.id')
            ->whereNull('bayi_baru_lahir.deleted_at')
            ->select(
                'bayi_baru_lahir.*',
                'persalinan.nama_penolong',
                'persalinan.nama_fasilitas',
                'persalinan.gravida',
                'persalinan.para',
                'persalinan.abortus',
                'pasien.nama_ibu as nama_pasien',
                'pasien.nomor_register',
                'pasien.nama_suami',
                'pasien.usia_ibu'
            )
            ->orderByDesc('bayi_baru_lahir.id')
            ->get();

        $data_persalinan = DB::table('persalinan')
            ->join('pasien', 'persalinan.pasien_id', '=', 'pasien.id')
            ->whereNull('persalinan.deleted_at')
            ->select(
                'persalinan.id',
                'persalinan.gravida',
                'persalinan.para',
                'persalinan.abortus',
                'persalinan.waktu_masuk',
                'persalinan.status_persalinan',
                'pasien.nama_ibu as ibu_pasien',
                'pasien.nomor_register as no_register'
            )
            ->orderByDesc('persalinan.id')
            ->get();

        return view('bayi_baru_lahir.index', compact('data_bayi_baru_lahir', 'data_persalinan'));
    }

    /**
     * Form tambah diarahkan ke index (menggunakan modal).
     */
    public function create()
    {
        return redirect()->route('bayi-baru-lahir.index');
    }

    /**
     * Simpan data bayi baru lahir ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'persalinan_id' => 'required',
            'urutan_kelahiran' => 'required|numeric',
            'jenis_kelamin' => 'required|in:L,P',
            'berat_badan_gram' => 'required|numeric',
            'panjang_badan_cm' => 'required|numeric',
            'kondisi_bayi' => 'required',
        ]);

        // Satukan opsi checkbox tindakan resusitasi jika berupa array
        $tindakan = is_array($request->tindakan_resusitasi)
            ? implode(', ', $request->tindakan_resusitasi)
            : $request->tindakan_resusitasi;

        DB::table('bayi_baru_lahir')->insert([
            'persalinan_id' => $request->persalinan_id,
            'urutan_kelahiran' => $request->urutan_kelahiran,
            'jenis_kelamin' => $request->jenis_kelamin,
            'berat_badan_gram' => $request->berat_badan_gram,
            'panjang_badan_cm' => $request->panjang_badan_cm,
            'apgar_menit_1' => $request->apgar_menit_1,
            'apgar_menit_5' => $request->apgar_menit_5,
            'apgar_menit_10' => $request->apgar_menit_10,
            'pemberian_asi_kurang_1jam' => $request->pemberian_asi_kurang_1jam ?? 1,
            'alasan_tidak_asi' => $request->alasan_tidak_asi,
            'kondisi_bayi' => $request->kondisi_bayi,
            'tindakan_resusitasi' => $tindakan,
            'cacat_bawaan' => $request->cacat_bawaan,
            'masalah_lain' => $request->masalah_lain,
            'penatalaksanaan' => $request->penatalaksanaan,
            'hasil_evaluasi' => $request->hasil_evaluasi,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->input('redirect_to') === 'input_terpadu') {
            $pasienId = $request->input('pasien_id') ?: DB::table('persalinan')->where('id', $request->persalinan_id)->value('pasien_id');

            return redirect()->route('pasien.input_terpadu', [
                'pasien_id' => $pasienId,
                'tab' => 'bayi',
            ])->with('success', 'Data bayi baru lahir berhasil ditambahkan!');
        }

        return redirect()->route('bayi-baru-lahir.index')->with('success', 'Data bayi baru lahir berhasil ditambahkan!');
    }

    /**
     * Tampilkan detail rekam medis bayi baru lahir.
     */
    public function show(string $id)
    {
        $bayi = DB::table('bayi_baru_lahir')->where('id', $id)->first();

        if (! $bayi) {
            return redirect()->route('bayi-baru-lahir.index')->with('error', 'Data bayi tidak ditemukan.');
        }

        $persalinan = DB::table('persalinan')->where('id', $bayi->persalinan_id)->first();
        $pasien = $persalinan ? DB::table('pasien')->where('id', $persalinan->pasien_id)->first() : null;

        $saudara_kembar = DB::table('bayi_baru_lahir')
            ->where('persalinan_id', $bayi->persalinan_id)
            ->where('id', '!=', $bayi->id)
            ->whereNull('deleted_at')
            ->get();

        return view('bayi_baru_lahir.show', compact('bayi', 'persalinan', 'pasien', 'saudara_kembar'));
    }

    /**
     * Form edit diarahkan ke halaman show atau modal.
     */
    public function edit(string $id)
    {
        return redirect()->route('bayi-baru-lahir.show', $id);
    }

    /**
     * Perbarui data bayi baru lahir di database.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'persalinan_id' => 'required',
            'urutan_kelahiran' => 'required|numeric',
            'jenis_kelamin' => 'required|in:L,P',
            'berat_badan_gram' => 'required|numeric',
            'panjang_badan_cm' => 'required|numeric',
            'kondisi_bayi' => 'required',
        ]);

        $tindakan = is_array($request->tindakan_resusitasi)
            ? implode(', ', $request->tindakan_resusitasi)
            : $request->tindakan_resusitasi;

        DB::table('bayi_baru_lahir')->where('id', $id)->update([
            'persalinan_id' => $request->persalinan_id,
            'urutan_kelahiran' => $request->urutan_kelahiran,
            'jenis_kelamin' => $request->jenis_kelamin,
            'berat_badan_gram' => $request->berat_badan_gram,
            'panjang_badan_cm' => $request->panjang_badan_cm,
            'apgar_menit_1' => $request->apgar_menit_1,
            'apgar_menit_5' => $request->apgar_menit_5,
            'apgar_menit_10' => $request->apgar_menit_10,
            'pemberian_asi_kurang_1jam' => $request->pemberian_asi_kurang_1jam ?? 1,
            'alasan_tidak_asi' => $request->alasan_tidak_asi,
            'kondisi_bayi' => $request->kondisi_bayi,
            'tindakan_resusitasi' => $tindakan,
            'cacat_bawaan' => $request->cacat_bawaan,
            'masalah_lain' => $request->masalah_lain,
            'penatalaksanaan' => $request->penatalaksanaan,
            'hasil_evaluasi' => $request->hasil_evaluasi,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Data bayi baru lahir berhasil diperbarui!');
    }

    /**
     * Hapus data bayi baru lahir (soft delete).
     */
    public function destroy(string $id)
    {
        $bayi = DB::table('bayi_baru_lahir')->where('id', $id)->first();
        $persalinan_id = $bayi ? $bayi->persalinan_id : null;

        DB::table('bayi_baru_lahir')->where('id', $id)->update(['deleted_at' => now()]);

        if (request('redirect_to') === 'input_terpadu') {
            $pasienId = request('pasien_id') ?: ($persalinan_id ? DB::table('persalinan')->where('id', $persalinan_id)->value('pasien_id') : null);
            if ($pasienId) {
                return redirect()->route('pasien.input_terpadu', [
                    'pasien_id' => $pasienId,
                    'tab' => 'bayi',
                ])->with('success', 'Data bayi baru lahir berhasil dihapus!');
            }
        }

        return redirect()->route('bayi-baru-lahir.index')->with('success', 'Data bayi baru lahir berhasil dihapus!');
    }
}
