<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Edukasi_KIE_Controller extends Controller
{
    /**
     * Tampilkan daftar rekam edukasi & konseling KIE.
     */
    public function index()
    {
        $data_edukasi = DB::table('edukasi_kie')
            ->leftJoin('persalinan', 'edukasi_kie.persalinan_id', '=', 'persalinan.id')
            ->leftJoin('pasien', 'persalinan.pasien_id', '=', 'pasien.id')
            ->whereNull('edukasi_kie.deleted_at')
            ->select(
                'edukasi_kie.*',
                'persalinan.nama_penolong',
                'persalinan.nama_fasilitas',
                'pasien.nama_ibu as nama_pasien',
                'pasien.nomor_register',
                'pasien.usia_ibu'
            )
            ->orderByDesc('edukasi_kie.id')
            ->get();

        $data_persalinan = DB::table('persalinan')
            ->join('pasien', 'persalinan.pasien_id', '=', 'pasien.id')
            ->whereNull('persalinan.deleted_at')
            ->select(
                'persalinan.id',
                'persalinan.waktu_masuk',
                'persalinan.nama_penolong',
                'persalinan.status_persalinan',
                'pasien.nama_ibu as ibu_pasien',
                'pasien.nomor_register as no_register'
            )
            ->orderByDesc('persalinan.id')
            ->get();

        return view('edukasi_kie.index', compact('data_edukasi', 'data_persalinan'));
    }

    public function create()
    {
        return redirect()->route('edukasi-kie.index');
    }

    /**
     * Simpan data sesi edukasi KIE baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'persalinan_id' => 'required',
            'tanggal_edukasi' => 'required|date',
            'materi_edukasi' => 'required',
            'pelaksana' => 'required|string|max:120',
        ]);

        DB::table('edukasi_kie')->insert([
            'persalinan_id' => $request->persalinan_id,
            'tanggal_edukasi' => $request->tanggal_edukasi,
            'materi_edukasi' => $request->materi_edukasi,
            'pelaksana' => $request->pelaksana,
            'keterangan' => $request->keterangan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->input('redirect_to') === 'input_terpadu') {
            $pasienId = $request->input('pasien_id') ?: DB::table('persalinan')->where('id', $request->persalinan_id)->value('pasien_id');

            return redirect()->route('pasien.input_terpadu', [
                'pasien_id' => $pasienId,
                'tab' => 'edukasi',
            ])->with('success', 'Data edukasi KIE berhasil ditambahkan!');
        }

        return redirect()->route('edukasi-kie.index')->with('success', 'Data edukasi KIE berhasil ditambahkan!');
    }

    public function show(string $id)
    {
        return redirect()->route('edukasi-kie.index');
    }

    public function edit(string $id)
    {
        return redirect()->route('edukasi-kie.index');
    }

    /**
     * Update data edukasi KIE.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'persalinan_id' => 'required',
            'tanggal_edukasi' => 'required|date',
            'materi_edukasi' => 'required',
            'pelaksana' => 'required|string|max:120',
        ]);

        DB::table('edukasi_kie')->where('id', $id)->update([
            'persalinan_id' => $request->persalinan_id,
            'tanggal_edukasi' => $request->tanggal_edukasi,
            'materi_edukasi' => $request->materi_edukasi,
            'pelaksana' => $request->pelaksana,
            'keterangan' => $request->keterangan,
            'updated_at' => now(),
        ]);

        if ($request->input('redirect_to') === 'input_terpadu') {
            $pasienId = $request->input('pasien_id') ?: DB::table('persalinan')->where('id', $request->persalinan_id)->value('pasien_id');

            return redirect()->route('pasien.input_terpadu', [
                'pasien_id' => $pasienId,
                'tab' => 'edukasi',
            ])->with('success', 'Data edukasi KIE berhasil diperbarui!');
        }

        return redirect()->route('edukasi-kie.index')->with('success', 'Data edukasi KIE berhasil diperbarui!');
    }

    /**
     * Hapus data edukasi KIE (soft delete).
     */
    public function destroy(string $id)
    {
        $edukasi = DB::table('edukasi_kie')->where('id', $id)->first();
        $persalinan_id = $edukasi ? $edukasi->persalinan_id : null;

        DB::table('edukasi_kie')->where('id', $id)->update(['deleted_at' => now()]);

        if (request('redirect_to') === 'input_terpadu') {
            $pasienId = request('pasien_id') ?: ($persalinan_id ? DB::table('persalinan')->where('id', $persalinan_id)->value('pasien_id') : null);
            if ($pasienId) {
                return redirect()->route('pasien.input_terpadu', [
                    'pasien_id' => $pasienId,
                    'tab' => 'edukasi',
                ])->with('success', 'Data edukasi KIE berhasil dihapus!');
            }
        }

        return redirect()->route('edukasi-kie.index')->with('success', 'Data edukasi KIE berhasil dihapus!');
    }
}
