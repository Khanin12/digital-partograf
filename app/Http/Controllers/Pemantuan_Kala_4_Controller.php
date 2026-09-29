<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Pemantuan_Kala_4_Controller extends Controller
{
    /**
     * Tampilkan daftar pemantauan Kala IV.
     */
    public function index()
    {
        $data_pemantauan = DB::table('pemantauan_kala_4')
            ->leftJoin('persalinan', 'pemantauan_kala_4.persalinan_id', '=', 'persalinan.id')
            ->leftJoin('pasien', 'persalinan.pasien_id', '=', 'pasien.id')
            ->whereNull('pemantauan_kala_4.deleted_at')
            ->select(
                'pemantauan_kala_4.*',
                'persalinan.nama_penolong',
                'persalinan.nama_fasilitas',
                'pasien.nama_ibu as nama_pasien',
                'pasien.nomor_register',
                'pasien.usia_ibu'
            )
            ->orderByDesc('pemantauan_kala_4.id')
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

        return view('pemantuan_kala_4.index', compact('data_pemantauan', 'data_persalinan'));
    }

    public function create()
    {
        return redirect()->route('pemantauan-kala-4.index');
    }

    /**
     * Simpan data pemantauan Kala IV.
     */
    public function store(Request $request)
    {
        $request->validate([
            'persalinan_id' => 'required',
            'jam_ke' => 'required|numeric|in:1,2',
            'pemantauan_ke' => 'required|numeric|min:1|max:4',
            'waktu_pemantauan' => 'required',
            'tekanan_darah_sistolik' => 'required|numeric',
            'tekanan_darah_diastolik' => 'required|numeric',
            'nadi_per_menit' => 'required|numeric',
            'tinggi_fundus_uteri' => 'required',
            'kontraksi_uterus' => 'required',
            'kandung_kemih' => 'required',
        ]);

        DB::table('pemantauan_kala_4')->insert([
            'persalinan_id' => $request->persalinan_id,
            'jam_ke' => $request->jam_ke,
            'pemantauan_ke' => $request->pemantauan_ke,
            'waktu_pemantauan' => $request->waktu_pemantauan,
            'tekanan_darah_sistolik' => $request->tekanan_darah_sistolik,
            'tekanan_darah_diastolik' => $request->tekanan_darah_diastolik,
            'nadi_per_menit' => $request->nadi_per_menit,
            'suhu_tubuh' => $request->suhu_tubuh ?: null,
            'tinggi_fundus_uteri' => $request->tinggi_fundus_uteri,
            'kontraksi_uterus' => $request->kontraksi_uterus,
            'kandung_kemih' => $request->kandung_kemih,
            'perdarahan_ml' => $request->perdarahan_ml ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->input('redirect_to') === 'input_terpadu') {
            $pasienId = $request->input('pasien_id') ?: DB::table('persalinan')->where('id', $request->persalinan_id)->value('pasien_id');

            return redirect()->route('pasien.input_terpadu', [
                'pasien_id' => $pasienId,
                'tab' => 'kala4',
            ])->with('success', 'Data pemantauan Kala IV berhasil ditambahkan!');
        }

        return redirect()->route('pemantauan-kala-4.index')->with('success', 'Data pemantauan Kala IV berhasil ditambahkan!');
    }

    public function show(string $id)
    {
        return redirect()->route('pemantauan-kala-4.index');
    }

    public function edit(string $id)
    {
        return redirect()->route('pemantauan-kala-4.index');
    }

    /**
     * Update data pemantauan Kala IV.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'persalinan_id' => 'required',
            'jam_ke' => 'required|numeric|in:1,2',
            'pemantauan_ke' => 'required|numeric|min:1|max:4',
            'waktu_pemantauan' => 'required',
            'tekanan_darah_sistolik' => 'required|numeric',
            'tekanan_darah_diastolik' => 'required|numeric',
            'nadi_per_menit' => 'required|numeric',
            'tinggi_fundus_uteri' => 'required',
            'kontraksi_uterus' => 'required',
            'kandung_kemih' => 'required',
        ]);

        DB::table('pemantauan_kala_4')->where('id', $id)->update([
            'persalinan_id' => $request->persalinan_id,
            'jam_ke' => $request->jam_ke,
            'pemantauan_ke' => $request->pemantauan_ke,
            'waktu_pemantauan' => $request->waktu_pemantauan,
            'tekanan_darah_sistolik' => $request->tekanan_darah_sistolik,
            'tekanan_darah_diastolik' => $request->tekanan_darah_diastolik,
            'nadi_per_menit' => $request->nadi_per_menit,
            'suhu_tubuh' => $request->suhu_tubuh ?: null,
            'tinggi_fundus_uteri' => $request->tinggi_fundus_uteri,
            'kontraksi_uterus' => $request->kontraksi_uterus,
            'kandung_kemih' => $request->kandung_kemih,
            'perdarahan_ml' => $request->perdarahan_ml ?? 0,
            'updated_at' => now(),
        ]);

        if ($request->input('redirect_to') === 'input_terpadu') {
            $pasienId = $request->input('pasien_id') ?: DB::table('persalinan')->where('id', $request->persalinan_id)->value('pasien_id');

            return redirect()->route('pasien.input_terpadu', [
                'pasien_id' => $pasienId,
                'tab' => 'kala4',
            ])->with('success', 'Data pemantauan Kala IV berhasil diperbarui!');
        }

        return redirect()->route('pemantauan-kala-4.index')->with('success', 'Data pemantauan Kala IV berhasil diperbarui!');
    }

    /**
     * Hapus data pemantauan Kala IV (soft delete).
     */
    public function destroy(string $id)
    {
        $pemantauan = DB::table('pemantauan_kala_4')->where('id', $id)->first();
        $persalinan_id = $pemantauan ? $pemantauan->persalinan_id : null;

        DB::table('pemantauan_kala_4')->where('id', $id)->update(['deleted_at' => now()]);

        if (request('redirect_to') === 'input_terpadu') {
            $pasienId = request('pasien_id') ?: ($persalinan_id ? DB::table('persalinan')->where('id', $persalinan_id)->value('pasien_id') : null);
            if ($pasienId) {
                return redirect()->route('pasien.input_terpadu', [
                    'pasien_id' => $pasienId,
                    'tab' => 'kala4',
                ])->with('success', 'Data pemantauan Kala IV berhasil dihapus!');
            }
        }

        return redirect()->route('pemantauan-kala-4.index')->with('success', 'Data pemantauan Kala IV berhasil dihapus!');
    }
}
