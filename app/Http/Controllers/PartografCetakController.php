<?php

namespace App\Http\Controllers;

use App\Services\PartografJsonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PartografCetakController extends Controller
{
    public function __construct(
        public PartografJsonService $partoService
    ) {}

    /**
     * Cetak lembar partograf resmi 2 halaman (Halaman 1 Lembar Depan & Halaman 2 Lembar Belakang).
     */
    public function cetak(string $persalinan_id): View
    {
        // Kompilasi dan simpan snapshot terbaru ke tabel hasil_partograf
        $parto = $this->partoService->saveSnapshot((int) $persalinan_id);

        return view('partograf.cetak', [
            'parto' => $parto,
            'id' => $persalinan_id,
        ]);
    }

    /**
     * Cetak lembar partograf berdasarkan ID pasien (shortcut dari master pasien).
     */
    public function cetakByPasien(string $pasien_id)
    {
        $persalinan = DB::table('persalinan')
            ->where('pasien_id', $pasien_id)
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->first();

        if (! $persalinan) {
            return redirect()->route('pasien.index')->with('error', 'Data persalinan untuk pasien ini belum terdaftar.');
        }

        return $this->cetak((string) $persalinan->id);
    }

    /**
     * Ekspor data snapshot partograf dalam format JSON murni (skema form-io).
     */
    public function apiJson(string $persalinan_id): JsonResponse
    {
        $parto = $this->partoService->saveSnapshot((int) $persalinan_id);

        return response()->json($parto);
    }
}
