<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersalinanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data_persalinan = DB::table('persalinan')
            ->leftJoin('pasien', 'persalinan.pasien_id', '=', 'pasien.id')
            ->whereNull('persalinan.deleted_at')
            ->select(
                'persalinan.*',
                'pasien.nama_ibu as nama_pasien',
                'pasien.nama_ibu',
                'pasien.nama_suami',
                'pasien.nomor_register as nomer_registerer',
                'pasien.nomor_register'
            )
            ->orderBy('persalinan.created_at', 'desc')
            ->get();

        $data_pasien = DB::table('pasien')
            ->select('id', 'nama_ibu', 'nama_suami', 'nomor_register as nomer_registerer', 'nomor_register')
            ->whereNull('deleted_at')
            ->orderBy('nama_ibu', 'asc')
            ->get();

        return view('persalinan.index', compact('data_persalinan', 'data_pasien'));
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
            'pasien_id' => 'required|exists:pasien,id',
            'gravida' => 'required|integer|min:1|max:30',
            'para' => 'required|integer|min:0|max:30',
            'abortus' => 'required|integer|min:0|max:30',
            'usia_kehamilan_minggu' => 'required|integer|min:10|max:50',
            'nama_fasilitas' => 'required|string|max:150',
            'waktu_masuk' => 'required|date',
            'waktu_ketuban_pecah' => 'nullable|date',
            'waktu_mules_mulai' => 'nullable|date',
            'nama_penolong' => 'required|string|max:120',
            'tempat_persalinan' => 'required|in:rumah_ibu,puskesmas,klinik_swasta,rumah_sakit,lainnya',
            'status_persalinan' => 'required|in:proses,selesai,rujuk,batal',
            'waktu_makan_terakhir' => 'nullable|date',
            'jenis_makan' => 'nullable|string|max:100',
            'porsi_makan' => 'nullable|string|max:50',
            'waktu_minum_terakhir' => 'nullable|date',
            'jenis_minum' => 'nullable|string|max:100',
            'porsi_minum' => 'nullable|string|max:50',
        ]);

        $validasi['created_at'] = now();
        $validasi['updated_at'] = now();

        DB::table('persalinan')->insert($validasi);

        if ($request->input('redirect_to') === 'input_terpadu') {
            return redirect()->route('pasien.input_terpadu', [
                'pasien_id' => $validasi['pasien_id'],
                'tab' => $request->input('tab_target', 'observasi'),
            ])->with('success', 'Data admisi persalinan berhasil disimpan!');
        }

        return redirect()->route('persalinan.index')->with('success', 'Data persalinan berhasil ditambahkan!');
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
            'pasien_id' => 'required|exists:pasien,id',
            'gravida' => 'required|integer|min:1|max:30',
            'para' => 'required|integer|min:0|max:30',
            'abortus' => 'required|integer|min:0|max:30',
            'usia_kehamilan_minggu' => 'required|integer|min:10|max:50',
            'nama_fasilitas' => 'required|string|max:150',
            'waktu_masuk' => 'required|date',
            'waktu_ketuban_pecah' => 'nullable|date',
            'waktu_mules_mulai' => 'nullable|date',
            'nama_penolong' => 'required|string|max:120',
            'tempat_persalinan' => 'required|in:rumah_ibu,puskesmas,klinik_swasta,rumah_sakit,lainnya',
            'status_persalinan' => 'required|in:proses,selesai,rujuk,batal',
            'waktu_makan_terakhir' => 'nullable|date',
            'jenis_makan' => 'nullable|string|max:100',
            'porsi_makan' => 'nullable|string|max:50',
            'waktu_minum_terakhir' => 'nullable|date',
            'jenis_minum' => 'nullable|string|max:100',
            'porsi_minum' => 'nullable|string|max:50',
        ]);

        $validasi['updated_at'] = now();

        DB::table('persalinan')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update($validasi);

        if ($request->input('redirect_to') === 'input_terpadu') {
            return redirect()->route('pasien.input_terpadu', [
                'pasien_id' => $validasi['pasien_id'],
                'tab' => $request->input('tab_target', 'persalinan'),
            ])->with('success', 'Data admisi persalinan berhasil diperbarui!');
        }

        return redirect()->route('persalinan.index')->with('success', 'Data persalinan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::table('persalinan')->where('id', $id)->update(['deleted_at' => now()]);

        return redirect()->route('persalinan.index')->with('success', 'Data persalinan berhasil dihapus!');
    }
}
