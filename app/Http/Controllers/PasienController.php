<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PasienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data_pasien = DB::table('pasien')
            ->select('pasien.*', 'nomor_register as nomer_registerer', 'nama_suami as nama_ayah')
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $pasienIds = $data_pasien->pluck('id');

        // Ambil persalinan terbaru untuk setiap pasien
        $persalinanList = DB::table('persalinan')
            ->whereIn('pasien_id', $pasienIds)
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('pasien_id');

        $persalinanIds = $persalinanList->flatten()->pluck('id');

        // Ringkasan status data anak per persalinan
        $observasiSummary = DB::table('observasi_partograf')
            ->whereIn('persalinan_id', $persalinanIds)
            ->whereNull('deleted_at')
            ->select('persalinan_id', DB::raw('count(*) as total'), DB::raw('max(created_at) as last_created'))
            ->groupBy('persalinan_id')
            ->get()
            ->keyBy('persalinan_id');

        $catatanSummary = DB::table('catatan_persalinan')
            ->whereIn('persalinan_id', $persalinanIds)
            ->whereNull('deleted_at')
            ->select('persalinan_id', DB::raw('count(*) as total'), DB::raw('max(created_at) as last_created'))
            ->groupBy('persalinan_id')
            ->get()
            ->keyBy('persalinan_id');

        $bayiSummary = DB::table('bayi_baru_lahir')
            ->whereIn('persalinan_id', $persalinanIds)
            ->whereNull('deleted_at')
            ->select('persalinan_id', DB::raw('count(*) as total'), DB::raw('max(created_at) as last_created'))
            ->groupBy('persalinan_id')
            ->get()
            ->keyBy('persalinan_id');

        $kala4Summary = DB::table('pemantauan_kala_4')
            ->whereIn('persalinan_id', $persalinanIds)
            ->whereNull('deleted_at')
            ->select('persalinan_id', DB::raw('count(*) as total'), DB::raw('max(created_at) as last_created'))
            ->groupBy('persalinan_id')
            ->get()
            ->keyBy('persalinan_id');

        $edukasiSummary = DB::table('edukasi_kie')
            ->whereIn('persalinan_id', $persalinanIds)
            ->whereNull('deleted_at')
            ->select('persalinan_id', DB::raw('count(*) as total'), DB::raw('max(created_at) as last_created'))
            ->groupBy('persalinan_id')
            ->get()
            ->keyBy('persalinan_id');

        foreach ($data_pasien as $p) {
            $latestPersalinan = $persalinanList->get($p->id)?->first();
            $pid = $latestPersalinan?->id;
            $p->active_persalinan_id = $pid;
            $p->active_persalinan = $latestPersalinan;

            // 1. Admisi Persalinan
            if (! $latestPersalinan) {
                $p->persalinan_status = [
                    'state' => 'belum',
                    'label' => 'Belum Diisi',
                    'badge' => 'secondary',
                    'icon_status' => 'fas fa-circle text-muted',
                    'icon_modul' => 'fas fa-procedures',
                    'time' => null,
                ];
            } elseif ($latestPersalinan->status_persalinan === 'proses') {
                $p->persalinan_status = [
                    'state' => 'proses',
                    'label' => 'Dalam Proses',
                    'badge' => 'warning',
                    'icon_status' => 'fas fa-hourglass-half text-warning',
                    'icon_modul' => 'fas fa-procedures',
                    'time' => $latestPersalinan->created_at,
                ];
            } else {
                $p->persalinan_status = [
                    'state' => 'selesai',
                    'label' => 'Selesai ('.ucfirst($latestPersalinan->status_persalinan).')',
                    'badge' => 'success',
                    'icon_status' => 'fas fa-check-circle text-success',
                    'icon_modul' => 'fas fa-procedures',
                    'time' => $latestPersalinan->created_at,
                ];
            }

            // 2. Observasi Partograf
            $obs = $pid ? ($observasiSummary->get($pid) ?? null) : null;
            if (! $obs || $obs->total == 0) {
                $p->observasi_status = [
                    'state' => 'belum',
                    'label' => 'Belum Diisi',
                    'badge' => 'secondary',
                    'icon_status' => 'fas fa-circle text-muted',
                    'icon_modul' => 'fas fa-chart-line',
                    'time' => null,
                    'count' => 0,
                ];
            } else {
                $isSelesai = ($latestPersalinan && in_array($latestPersalinan->status_persalinan, ['selesai', 'rujuk']));
                $p->observasi_status = [
                    'state' => $isSelesai ? 'selesai' : 'proses',
                    'label' => $obs->total.' Titik Catatan',
                    'badge' => $isSelesai ? 'success' : 'warning',
                    'icon_status' => $isSelesai ? 'fas fa-check-circle text-success' : 'fas fa-chart-line text-warning',
                    'icon_modul' => 'fas fa-chart-line',
                    'time' => $obs->last_created,
                    'count' => $obs->total,
                ];
            }

            // 3. Catatan Persalinan
            $cat = $pid ? ($catatanSummary->get($pid) ?? null) : null;
            if (! $cat || $cat->total == 0) {
                $p->catatan_status = [
                    'state' => 'belum',
                    'label' => 'Belum Diisi',
                    'badge' => 'secondary',
                    'icon_status' => 'fas fa-circle text-muted',
                    'icon_modul' => 'fas fa-clipboard-list',
                    'time' => null,
                ];
            } else {
                $p->catatan_status = [
                    'state' => 'selesai',
                    'label' => 'Catatan Lengkap',
                    'badge' => 'success',
                    'icon_status' => 'fas fa-check-circle text-success',
                    'icon_modul' => 'fas fa-clipboard-list',
                    'time' => $cat->last_created,
                ];
            }

            // 4. Bayi Baru Lahir
            $bayi = $pid ? ($bayiSummary->get($pid) ?? null) : null;
            if (! $bayi || $bayi->total == 0) {
                $p->bayi_status = [
                    'state' => 'belum',
                    'label' => 'Belum Diisi',
                    'badge' => 'secondary',
                    'icon_status' => 'fas fa-circle text-muted',
                    'icon_modul' => 'fas fa-baby',
                    'time' => null,
                ];
            } else {
                $p->bayi_status = [
                    'state' => 'selesai',
                    'label' => $bayi->total.' Bayi Lahir',
                    'badge' => 'success',
                    'icon_status' => 'fas fa-check-circle text-success',
                    'icon_modul' => 'fas fa-baby',
                    'time' => $bayi->last_created,
                ];
            }

            // 5. Pemantauan Kala IV
            $k4 = $pid ? ($kala4Summary->get($pid) ?? null) : null;
            if (! $k4 || $k4->total == 0) {
                $p->kala4_status = [
                    'state' => 'belum',
                    'label' => 'Belum Diisi',
                    'badge' => 'secondary',
                    'icon_status' => 'fas fa-circle text-muted',
                    'icon_modul' => 'fas fa-clock',
                    'time' => null,
                    'count' => 0,
                ];
            } elseif ($k4->total < 4) {
                $p->kala4_status = [
                    'state' => 'proses',
                    'label' => $k4->total.'x Cek (Proses)',
                    'badge' => 'warning',
                    'icon_status' => 'fas fa-hourglass-half text-warning',
                    'icon_modul' => 'fas fa-clock',
                    'time' => $k4->last_created,
                    'count' => $k4->total,
                ];
            } else {
                $p->kala4_status = [
                    'state' => 'selesai',
                    'label' => $k4->total.'x Cek (Lengkap)',
                    'badge' => 'success',
                    'icon_status' => 'fas fa-check-circle text-success',
                    'icon_modul' => 'fas fa-clock',
                    'time' => $k4->last_created,
                    'count' => $k4->total,
                ];
            }

            // 6. Edukasi KIE
            $edu = $pid ? ($edukasiSummary->get($pid) ?? null) : null;
            if (! $edu || $edu->total == 0) {
                $p->edukasi_status = [
                    'state' => 'belum',
                    'label' => 'Belum Diisi',
                    'badge' => 'secondary',
                    'icon_status' => 'fas fa-circle text-muted',
                    'icon_modul' => 'fas fa-comments',
                    'time' => null,
                ];
            } else {
                $p->edukasi_status = [
                    'state' => 'selesai',
                    'label' => $edu->total.' Sesi KIE',
                    'badge' => 'success',
                    'icon_status' => 'fas fa-check-circle text-success',
                    'icon_modul' => 'fas fa-comments',
                    'time' => $edu->last_created,
                ];
            }
        }

        return view('pasien.index', compact('data_pasien'));
    }

    /**
     * Tampilkan formulir terpadu pengisian rekam medis partograf pasien (Opsi A - Tab Bar).
     */
    public function inputTerpadu($pasien_id, Request $request)
    {
        $pasien = DB::table('pasien')
            ->where('id', $pasien_id)
            ->whereNull('deleted_at')
            ->first();

        if (! $pasien) {
            return redirect()->route('pasien.index')->with('error', 'Data pasien tidak ditemukan.');
        }

        // Cari data persalinan aktif / terbaru untuk pasien ini
        $persalinan = DB::table('persalinan')
            ->where('pasien_id', $pasien_id)
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->first();

        $pid = $persalinan?->id;

        // Ambil data-data anak jika sudah ada persalinan
        $data_observasi = $pid
            ? DB::table('observasi_partograf')
                ->where('persalinan_id', $pid)
                ->whereNull('deleted_at')
                ->orderBy('kolom_partograf_ke', 'asc')
                ->orderBy('waktu_pemeriksaan', 'asc')
                ->get()
            : collect();

        $catatan_persalinan = $pid
            ? DB::table('catatan_persalinan')
                ->where('persalinan_id', $pid)
                ->whereNull('deleted_at')
                ->first()
            : null;

        $data_bayi = $pid
            ? DB::table('bayi_baru_lahir')
                ->where('persalinan_id', $pid)
                ->whereNull('deleted_at')
                ->orderByDesc('id')
                ->get()
            : collect();

        $data_kala4 = $pid
            ? DB::table('pemantauan_kala_4')
                ->where('persalinan_id', $pid)
                ->whereNull('deleted_at')
                ->orderBy('jam_ke')
                ->orderBy('pemantauan_ke')
                ->get()
            : collect();

        $data_edukasi = $pid
            ? DB::table('edukasi_kie')
                ->where('persalinan_id', $pid)
                ->whereNull('deleted_at')
                ->orderByDesc('tanggal_edukasi')
                ->get()
            : collect();

        // Status per form untuk tampilan header tabs
        $statuses = [
            'persalinan' => [
                'state' => ! $persalinan ? 'belum' : ($persalinan->status_persalinan === 'proses' ? 'proses' : 'selesai'),
                'label' => ! $persalinan ? 'Belum Diisi' : ($persalinan->status_persalinan === 'proses' ? 'Dalam Proses' : 'Selesai'),
                'badge' => ! $persalinan ? 'secondary' : ($persalinan->status_persalinan === 'proses' ? 'warning' : 'success'),
                'icon_modul' => 'fas fa-procedures',
                'time' => $persalinan?->created_at,
            ],
            'observasi' => [
                'state' => $data_observasi->isEmpty() ? 'belum' : (($persalinan && in_array($persalinan->status_persalinan, ['selesai', 'rujuk'])) ? 'selesai' : 'proses'),
                'label' => $data_observasi->isEmpty() ? 'Belum Diisi' : $data_observasi->count().' Titik Observasi',
                'badge' => $data_observasi->isEmpty() ? 'secondary' : (($persalinan && in_array($persalinan->status_persalinan, ['selesai', 'rujuk'])) ? 'success' : 'warning'),
                'icon_modul' => 'fas fa-chart-line',
                'time' => $data_observasi->max('created_at'),
            ],
            'catatan' => [
                'state' => ! $catatan_persalinan ? 'belum' : 'selesai',
                'label' => ! $catatan_persalinan ? 'Belum Diisi' : 'Catatan Lengkap',
                'badge' => ! $catatan_persalinan ? 'secondary' : 'success',
                'icon_modul' => 'fas fa-clipboard-list',
                'time' => $catatan_persalinan?->created_at,
            ],
            'bayi' => [
                'state' => $data_bayi->isEmpty() ? 'belum' : 'selesai',
                'label' => $data_bayi->isEmpty() ? 'Belum Diisi' : $data_bayi->count().' Bayi Lahir',
                'badge' => $data_bayi->isEmpty() ? 'secondary' : 'success',
                'icon_modul' => 'fas fa-baby',
                'time' => $data_bayi->max('created_at'),
            ],
            'kala4' => [
                'state' => $data_kala4->isEmpty() ? 'belum' : ($data_kala4->count() < 4 ? 'proses' : 'selesai'),
                'label' => $data_kala4->isEmpty() ? 'Belum Diisi' : $data_kala4->count().'x Pemantauan',
                'badge' => $data_kala4->isEmpty() ? 'secondary' : ($data_kala4->count() < 4 ? 'warning' : 'success'),
                'icon_modul' => 'fas fa-clock',
                'time' => $data_kala4->max('created_at'),
            ],
            'edukasi' => [
                'state' => $data_edukasi->isEmpty() ? 'belum' : 'selesai',
                'label' => $data_edukasi->isEmpty() ? 'Belum Diisi' : $data_edukasi->count().' Sesi KIE',
                'badge' => $data_edukasi->isEmpty() ? 'secondary' : 'success',
                'icon_modul' => 'fas fa-comments',
                'time' => $data_edukasi->max('created_at'),
            ],
        ];

        $active_tab = $request->query('tab', 'persalinan');
        // Jika persalinan belum diisi dan mencoba buka tab lain, sarankan isi persalinan dulu atau izinkan lihat
        if (! $persalinan && ! in_array($active_tab, ['persalinan'])) {
            $active_tab = 'persalinan';
        }

        return view('pasien.input_terpadu', compact(
            'pasien',
            'persalinan',
            'data_observasi',
            'catatan_persalinan',
            'data_bayi',
            'data_kala4',
            'data_edukasi',
            'statuses',
            'active_tab'
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
        $nomorRegister = $request->input('nomor_register', $request->input('nomer_registerer'));
        $namaSuami = $request->input('nama_suami', $request->input('nama_ayah'));

        $request->merge([
            'nomor_register' => $nomorRegister,
            'nama_suami' => $namaSuami,
        ]);

        $validated = $request->validate([
            'nomor_register' => 'required|string|max:50|unique:pasien,nomor_register',
            'nama_ibu' => 'required|string|max:120',
            'nama_suami' => 'nullable|string|max:120',
            'usia_ibu' => 'required|numeric|min:10|max:100',
            'usia_suami' => 'nullable|numeric|min:10|max:100',
            'alamat' => 'nullable|string',
            'nomor_telepon' => 'nullable|string|max:20',
        ]);

        DB::table('pasien')->insert([
            'nomor_register' => $validated['nomor_register'],
            'nama_ibu' => $validated['nama_ibu'],
            'nama_suami' => $validated['nama_suami'] ?? null,
            'usia_ibu' => (int) $validated['usia_ibu'],
            'usia_suami' => isset($validated['usia_suami']) ? (int) $validated['usia_suami'] : null,
            'alamat' => $validated['alamat'] ?? null,
            'nomor_telepon' => $validated['nomor_telepon'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('pasien.index')->with('success', 'Data pasien berhasil ditambahkan');
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
        $nomorRegister = $request->input('nomor_register', $request->input('nomer_registerer'));
        $namaSuami = $request->input('nama_suami', $request->input('nama_ayah'));

        $request->merge([
            'nomor_register' => $nomorRegister,
            'nama_suami' => $namaSuami,
        ]);

        $validated = $request->validate([
            'nomor_register' => 'required|string|max:50|unique:pasien,nomor_register,'.$id,
            'nama_ibu' => 'required|string|max:120',
            'nama_suami' => 'nullable|string|max:120',
            'usia_ibu' => 'required|numeric|min:10|max:100',
            'usia_suami' => 'nullable|numeric|min:10|max:100',
            'alamat' => 'nullable|string',
            'nomor_telepon' => 'nullable|string|max:20',
        ]);

        DB::table('pasien')->where('id', $id)->update([
            'nomor_register' => $validated['nomor_register'],
            'nama_ibu' => $validated['nama_ibu'],
            'nama_suami' => $validated['nama_suami'] ?? null,
            'usia_ibu' => (int) $validated['usia_ibu'],
            'usia_suami' => isset($validated['usia_suami']) ? (int) $validated['usia_suami'] : null,
            'alamat' => $validated['alamat'] ?? null,
            'nomor_telepon' => $validated['nomor_telepon'] ?? null,
            'updated_at' => now(),
        ]);

        return redirect()->route('pasien.index')->with('success', 'Data pasien berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::table('pasien')->where('id', $id)->update(['deleted_at' => now()]);

        return redirect()->route('pasien.index')->with('success', 'Data pasien berhasil dihapus');
    }
}
