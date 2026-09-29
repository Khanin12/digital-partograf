@extends('layouts.master')

@section('title', 'Detail Catatan Persalinan (Lembar Belakang Partograf)')

@push('styles')
    <style>
        .section-header {
            background-color: #f8fafc;
            color: #1f2937 !important;
            border-left: 4px solid #059669;
            padding: 0.5rem 0.85rem;
            font-size: 0.95rem;
            border-radius: 0 0.35rem 0.35rem 0;
            font-weight: 700;
        }
        .data-label {
            font-weight: 600;
            color: #4b5563;
            font-size: 0.85rem;
            margin-bottom: 0.15rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .data-value {
            font-size: 0.95rem;
            color: #111827;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            min-height: 38px;
            display: flex;
            align-items: center;
        }
        .data-value.textarea-val {
            min-height: 70px;
            align-items: flex-start;
            white-space: pre-line;
        }
        .table-custom-header th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
        }
    </style>
@endpush

@section('content')

    <!-- Flash Message -->
    @if(session('success'))
        <x-alert type="success" icon="fas fa-check-circle" dismissible>
            {{ session('success') }}
        </x-alert>
    @endif

    <!-- Page Header -->
    <x-page-header
        title="Detail Catatan Persalinan"
        subtitle="Dokumentasi lengkap Lembar Belakang Partograf: Kala I, Kala II, Kala III, dan ringkasan Kala IV"
        icon="fas fa-file-medical-alt"
    >
        <div class="btn-group shadow-sm">
            <a href="{{ route('catatan-persalinan.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
            </a>
            <button type="button" class="btn btn-sm btn-warning text-dark font-weight-bold" data-toggle="modal" data-target="#modalEditCatatanShow">
                <i class="fas fa-edit mr-1"></i> Edit Catatan
            </button>
            <button type="button" class="btn btn-sm btn-primary" onclick="window.print();">
                <i class="fas fa-print mr-1"></i> Cetak Lembar
            </button>
        </div>
    </x-page-header>

    @php
        $rawNama = $pasien->nama_ibu ?? ($catatan->nama_pasien ?? 'Pasien #' . $catatan->persalinan_id);
        $namaClean = preg_match('/^ny\.?\s*/i', $rawNama) ? $rawNama : 'Ny. ' . $rawNama;
    @endphp

    <!-- Card 1: Identitas Pasien & Admisi Persalinan -->
    <div class="card shadow mb-4 border-left-primary">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-user-injured mr-2"></i> Identitas Pasien & Admisi Persalinan
            </h6>
            <span class="badge badge-primary px-3 py-2 font-weight-bold">
                ID Catatan #{{ $catatan->id }} | Persalinan #{{ $catatan->persalinan_id }}
            </span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="data-label">Nomor Register</div>
                    <div class="data-value font-weight-bold text-primary">
                        {{ $pasien->nomor_register ?? '-' }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">Nama Pasien (Ibu)</div>
                    <div class="data-value font-weight-bold">
                        {{ $namaClean }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">Usia Ibu / Suami</div>
                    <div class="data-value">
                        {{ $pasien->usia_ibu ?? '-' }} th
                        @if(!empty($pasien->usia_suami))
                            / {{ $pasien->usia_suami }} th
                        @endif
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">Nama Suami</div>
                    <div class="data-value">
                        {{ $pasien->nama_suami ?? '-' }}
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="data-label">Riwayat Obstetri (GPA)</div>
                    <div class="data-value font-weight-bold text-info">
                        G{{ $persalinan->gravida ?? 1 }} P{{ $persalinan->para ?? 0 }} A{{ $persalinan->abortus ?? 0 }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">Usia Kehamilan</div>
                    <div class="data-value font-weight-bold">
                        {{ $persalinan->usia_kehamilan_minggu ?? '-' }} Minggu
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">Fasilitas Persalinan</div>
                    <div class="data-value">
                        {{ $persalinan->nama_fasilitas ?? '-' }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">Nama Penolong</div>
                    <div class="data-value">
                        {{ $persalinan->nama_penolong ?? '-' }}
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="data-label">Waktu Masuk Admisi</div>
                    <div class="data-value small">
                        {{ $persalinan->waktu_masuk ? \Carbon\Carbon::parse($persalinan->waktu_masuk)->format('d/m/Y H:i') : '-' }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">Waktu Ketuban Pecah</div>
                    <div class="data-value small">
                        {{ $persalinan->waktu_ketuban_pecah ? \Carbon\Carbon::parse($persalinan->waktu_ketuban_pecah)->format('d/m/Y H:i') : '-' }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">Waktu Mules Mulai</div>
                    <div class="data-value small">
                        {{ $persalinan->waktu_mules_mulai ? \Carbon\Carbon::parse($persalinan->waktu_mules_mulai)->format('d/m/Y H:i') : '-' }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">Status Persalinan</div>
                    <div class="data-value">
                        <span class="badge badge-success px-2 py-1 text-uppercase">{{ $persalinan->status_persalinan ?? 'selesai' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: SELURUH DATA CATATAN PERSALINAN (KALA I - KALA IV LENGKAP) -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-gray-800">
                <i class="fas fa-clipboard-list mr-2 text-primary"></i> Data Lengkap Tabel `catatan_persalinan`
            </h6>
        </div>
        <div class="card-body p-4">

            {{-- ======================== KALA I ======================== --}}
            <div class="section-header mb-3">
                <i class="fas fa-clock mr-2 text-primary"></i> KALA I (Fase Persalinan)
            </div>
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="data-label">kala1_melewati_garis_waspada</div>
                    <div class="data-value">
                        @if($catatan->kala1_melewati_garis_waspada)
                            <span class="badge badge-danger px-3 py-1 font-weight-bold">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Ya (Melewati Garis Waspada)
                            </span>
                        @else
                            <span class="badge badge-success px-3 py-1 font-weight-bold">
                                <i class="fas fa-check mr-1"></i> Tidak (Aman / Normal)
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-9 mb-3">
                    <div class="data-label">kala1_masalah_lain</div>
                    <div class="data-value">
                        {{ $catatan->kala1_masalah_lain ?: 'Tidak ada masalah' }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="data-label">kala1_penatalaksanaan</div>
                    <div class="data-value textarea-val">
                        {{ $catatan->kala1_penatalaksanaan ?: '-' }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="data-label">kala1_hasil_evaluasi</div>
                    <div class="data-value textarea-val">
                        {{ $catatan->kala1_hasil_evaluasi ?: '-' }}
                    </div>
                </div>
            </div>

            {{-- ======================== KALA II ======================== --}}
            <div class="section-header mb-3">
                <i class="fas fa-baby mr-2 text-primary"></i> KALA II (Pengeluaran Janin)
            </div>
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="data-label">kala2_lama_menit</div>
                    <div class="data-value font-weight-bold">
                        <i class="fas fa-stopwatch text-muted mr-2"></i>{{ $catatan->kala2_lama_menit ? $catatan->kala2_lama_menit . ' Menit' : '-' }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">kala2_episiotomi</div>
                    <div class="data-value">
                        @if($catatan->kala2_episiotomi)
                            <span class="badge badge-warning px-3 py-1 font-weight-bold">Ya (Dilakukan)</span>
                        @else
                            <span class="badge badge-secondary px-3 py-1 font-weight-bold">Tidak (Spontan/Utuh)</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="data-label">kala2_indikasi_episiotomi</div>
                    <div class="data-value">
                        {{ $catatan->kala2_indikasi_episiotomi ?: '-' }}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="data-label">kala2_pendamping</div>
                    <div class="data-value text-capitalize font-weight-bold">
                        <i class="fas fa-user-friends text-gray-400 mr-2"></i>{{ str_replace('_', ' ', $catatan->kala2_pendamping ?: '-') }}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="data-label">kala2_gawat_janin</div>
                    <div class="data-value">
                        {{ $catatan->kala2_gawat_janin ?: 'tidak_ada' }}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="data-label">kala2_distosia_bahu</div>
                    <div class="data-value">
                        {{ $catatan->kala2_distosia_bahu ?: 'tidak_ada' }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="data-label">kala2_penatalaksanaan</div>
                    <div class="data-value textarea-val">
                        {{ $catatan->kala2_penatalaksanaan ?: '-' }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="data-label">kala2_hasil_evaluasi</div>
                    <div class="data-value textarea-val">
                        {{ $catatan->kala2_hasil_evaluasi ?: '-' }}
                    </div>
                </div>
            </div>

            {{-- ======================== KALA III ======================== --}}
            <div class="section-header mb-3">
                <i class="fas fa-heartbeat mr-2 text-primary"></i> KALA III (Pengeluaran Plasenta & Manajemen Aktif Kala III)
            </div>
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="data-label">kala3_lama_menit</div>
                    <div class="data-value font-weight-bold">
                        <i class="fas fa-stopwatch text-muted mr-2"></i>{{ $catatan->kala3_lama_menit ? $catatan->kala3_lama_menit . ' Menit' : '-' }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">kala3_jumlah_perdarahan_ml</div>
                    <div class="data-value font-weight-bold text-danger">
                        <i class="fas fa-tint mr-2"></i>{{ $catatan->kala3_jumlah_perdarahan_ml ? $catatan->kala3_jumlah_perdarahan_ml . ' ml' : '0 ml' }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">kala3_oksitosin_10u_2menit</div>
                    <div class="data-value">
                        @if($catatan->kala3_oksitosin_10u_2menit)
                            <span class="badge badge-success px-3 py-1 font-weight-bold"><i class="fas fa-check mr-1"></i> Ya (Diberikan)</span>
                        @else
                            <span class="badge badge-danger px-3 py-1 font-weight-bold"><i class="fas fa-times mr-1"></i> Tidak</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">kala3_alasan_tidak_oksitosin</div>
                    <div class="data-value">
                        {{ $catatan->kala3_alasan_tidak_oksitosin ?: '-' }}
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="data-label">kala3_oksitosin_ulang</div>
                    <div class="data-value">
                        @if($catatan->kala3_oksitosin_ulang)
                            <span class="badge badge-warning px-3 py-1 font-weight-bold">Ya</span>
                        @else
                            <span class="badge badge-secondary px-3 py-1 font-weight-bold">Tidak</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">kala3_alasan_oksitosin_ulang</div>
                    <div class="data-value">
                        {{ $catatan->kala3_alasan_oksitosin_ulang ?: '-' }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">kala3_penegangan_tali_pusat</div>
                    <div class="data-value">
                        @if($catatan->kala3_penegangan_tali_pusat)
                            <span class="badge badge-success px-3 py-1 font-weight-bold"><i class="fas fa-check mr-1"></i> Ya</span>
                        @else
                            <span class="badge badge-danger px-3 py-1 font-weight-bold"><i class="fas fa-times mr-1"></i> Tidak</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">kala3_alasan_tidak_ptp</div>
                    <div class="data-value">
                        {{ $catatan->kala3_alasan_tidak_ptp ?: '-' }}
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="data-label">kala3_masase_fundus</div>
                    <div class="data-value">
                        @if($catatan->kala3_masase_fundus)
                            <span class="badge badge-success px-3 py-1 font-weight-bold"><i class="fas fa-check mr-1"></i> Ya</span>
                        @else
                            <span class="badge badge-danger px-3 py-1 font-weight-bold"><i class="fas fa-times mr-1"></i> Tidak</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">kala3_alasan_tidak_masase</div>
                    <div class="data-value">
                        {{ $catatan->kala3_alasan_tidak_masase ?: '-' }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">kala3_derajat_laserasi</div>
                    <div class="data-value">
                        @if($catatan->kala3_derajat_laserasi == 'tidak_ada')
                            <span class="badge badge-light border text-success font-weight-bold px-3 py-1">Tidak Ada Laserasi</span>
                        @else
                            <span class="badge badge-info px-3 py-1 font-weight-bold">Derajat {{ $catatan->kala3_derajat_laserasi }}</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">kala3_tindakan_laserasi</div>
                    <div class="data-value text-capitalize font-weight-bold">
                        {{ $catatan->kala3_tindakan_laserasi ?: '-' }}
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="data-label">kala3_atonia_uteri</div>
                    <div class="data-value">
                        {{ $catatan->kala3_atonia_uteri ?: 'tidak_ada' }}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="data-label">kala3_penatalaksanaan</div>
                    <div class="data-value textarea-val">
                        {{ $catatan->kala3_penatalaksanaan ?: '-' }}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="data-label">kala3_hasil_evaluasi</div>
                    <div class="data-value textarea-val">
                        {{ $catatan->kala3_hasil_evaluasi ?: '-' }}
                    </div>
                </div>
            </div>

            {{-- ======================== KALA IV ======================== --}}
            <div class="section-header mb-3">
                <i class="fas fa-procedures mr-2 text-primary"></i> KALA IV (Pengawasan 2 Jam Post-Partum)
            </div>
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="data-label">kala4_masalah</div>
                    <div class="data-value textarea-val">
                        {{ $catatan->kala4_masalah ?: 'Tidak ada masalah' }}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="data-label">kala4_penatalaksanaan</div>
                    <div class="data-value textarea-val">
                        {{ $catatan->kala4_penatalaksanaan ?: '-' }}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="data-label">kala4_hasil_evaluasi</div>
                    <div class="data-value textarea-val">
                        {{ $catatan->kala4_hasil_evaluasi ?: '-' }}
                    </div>
                </div>
            </div>

            {{-- ======================== TIMESTAMP ======================== --}}
            <div class="section-header mb-3">
                <i class="fas fa-history mr-2 text-primary"></i> METADATA REKAM MEDIS
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="data-label">created_at (Waktu Pembuatan)</div>
                    <div class="data-value small">
                        {{ $catatan->created_at ? \Carbon\Carbon::parse($catatan->created_at)->format('d F Y - H:i:s') : '-' }}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="data-label">updated_at (Terakhir Diperbarui)</div>
                    <div class="data-value small">
                        {{ $catatan->updated_at ? \Carbon\Carbon::parse($catatan->updated_at)->format('d F Y - H:i:s') : '-' }}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="data-label">deleted_at (Status Arsip)</div>
                    <div class="data-value small">
                        {{ $catatan->deleted_at ?: 'Aktif (Tidak dihapus)' }}
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Card 3: Relasi Terkait Lainnya (Bayi Baru Lahir, Pemantauan Kala 4, Edukasi KIE) -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-network-wired mr-2"></i> Relasi Tambahan Terkait Persalinan Ini
            </h6>
        </div>
        <div class="card-body">
            <!-- Nav Tabs -->
            <ul class="nav nav-pills mb-3" id="relasiTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="bayi-tab" data-toggle="pill" href="#bayi-content" role="tab">
                        <i class="fas fa-baby mr-1"></i> Bayi Baru Lahir ({{ $bayi->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="kala4-tab" data-toggle="pill" href="#kala4-content" role="tab">
                        <i class="fas fa-stethoscope mr-1"></i> TTV Observasi Kala IV ({{ $pemantauan_kala_4->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="edukasi-tab" data-toggle="pill" href="#edukasi-content" role="tab">
                        <i class="fas fa-book-reader mr-1"></i> Edukasi KIE ({{ $edukasi->count() }})
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="relasiTabContent">
                <!-- Tab Bayi -->
                <div class="tab-pane fade show active" id="bayi-content" role="tabpanel">
                    @if($bayi->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-custom-header">
                                <thead>
                                    <tr>
                                        <th>Kelahiran Ke</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Berat / Panjang</th>
                                        <th>APGAR (1/5/10)</th>
                                        <th>IMD < 1 Jam</th>
                                        <th>Kondisi Bayi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bayi as $b)
                                        <tr>
                                            <td>Bayi Ke-{{ $b->urutan_kelahiran }}</td>
                                            <td>{{ $b->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                            <td>{{ $b->berat_badan_gram }} gram / {{ $b->panjang_badan_cm }} cm</td>
                                            <td>{{ $b->apgar_menit_1 ?? '-' }} / {{ $b->apgar_menit_5 ?? '-' }} / {{ $b->apgar_menit_10 ?? '-' }}</td>
                                            <td>{{ $b->pemberian_asi_kurang_1jam ? 'Ya (IMD Berhasil)' : 'Tidak' }}</td>
                                            <td><span class="badge badge-success text-capitalize">{{ str_replace('_', ' ', $b->kondisi_bayi) }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-light border text-muted py-2 small mb-0">
                            <i class="fas fa-info-circle mr-1"></i> Belum ada rekaman bayi baru lahir untuk sesi persalinan ini.
                        </div>
                    @endif
                </div>

                <!-- Tab Pemantauan Kala 4 -->
                <div class="tab-pane fade" id="kala4-content" role="tabpanel">
                    @if($pemantauan_kala_4->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-custom-header">
                                <thead>
                                    <tr>
                                        <th>Jam Ke</th>
                                        <th>Ke-</th>
                                        <th>Waktu</th>
                                        <th>TD (mmHg)</th>
                                        <th>Nadi</th>
                                        <th>Suhu</th>
                                        <th>Tinggi Fundus</th>
                                        <th>Kontraksi</th>
                                        <th>Kandung Kemih</th>
                                        <th>Darah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pemantauan_kala_4 as $pk)
                                        <tr>
                                            <td>Jam Ke-{{ $pk->jam_ke }}</td>
                                            <td>Interval {{ $pk->pemantauan_ke }}</td>
                                            <td>{{ $pk->waktu_pemantauan }}</td>
                                            <td>{{ $pk->tekanan_darah_sistolik }}/{{ $pk->tekanan_darah_diastolik }}</td>
                                            <td>{{ $pk->nadi_per_menit }} x/m</td>
                                            <td>{{ $pk->suhu_tubuh }} °C</td>
                                            <td>{{ $pk->tinggi_fundus_uteri }}</td>
                                            <td>{{ $pk->kontraksi_uterus }}</td>
                                            <td>{{ $pk->kandung_kemih }}</td>
                                            <td class="text-danger font-weight-bold">{{ $pk->perdarahan_ml }} ml</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-light border text-muted py-2 small mb-0">
                            <i class="fas fa-info-circle mr-1"></i> Belum ada tabel pemantauan berkala Kala IV untuk sesi persalinan ini.
                        </div>
                    @endif
                </div>

                <!-- Tab Edukasi KIE -->
                <div class="tab-pane fade" id="edukasi-content" role="tabpanel">
                    @if($edukasi->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-custom-header">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Materi Edukasi</th>
                                        <th>Pelaksana</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($edukasi as $e)
                                        <tr>
                                            <td>{{ $e->tanggal_edukasi }}</td>
                                            <td class="text-capitalize">{{ str_replace('_', ' ', $e->materi_edukasi) }}</td>
                                            <td>{{ $e->pelaksana }}</td>
                                            <td>{{ $e->keterangan ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-light border text-muted py-2 small mb-0">
                            <i class="fas fa-info-circle mr-1"></i> Belum ada catatan edukasi KIE nifas yang diisi.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    {{-- MODAL EDIT DI HALAMAN SHOW --}}
    <div class="modal fade text-left" id="modalEditCatatanShow" tabindex="-1" role="dialog" aria-labelledby="modalEditShowLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered" role="document">
            <form action="{{ route('catatan-persalinan.update', $catatan->id) }}" method="POST" class="modal-content shadow border-0">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning text-dark py-3">
                    <h5 class="modal-title font-weight-bold" id="modalEditShowLabel">
                        <i class="fas fa-edit mr-2"></i> Edit Catatan Persalinan: {{ $namaClean }}
                    </h5>
                    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <input type="hidden" name="persalinan_id" value="{{ $catatan->persalinan_id }}">

                    {{-- 2. KALA I --}}
                    <h6 class="font-weight-bold text-primary mb-3 section-header">
                        <i class="fas fa-clock mr-1"></i> 2. Catatan Kala I (Fase Persalinan)
                    </h6>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Melewati Garis Waspada? <span class="text-danger">*</span></label>
                            <select name="kala1_melewati_garis_waspada" class="form-control" required>
                                <option value="0" {{ (string)$catatan->kala1_melewati_garis_waspada === '0' ? 'selected' : '' }}>Tidak (Aman)</option>
                                <option value="1" {{ (string)$catatan->kala1_melewati_garis_waspada === '1' ? 'selected' : '' }}>Ya (Melewati Garis Waspada)</option>
                            </select>
                        </div>
                        <div class="col-md-8 form-group">
                            <label class="font-weight-bold text-gray-700">Masalah Lain Kala I</label>
                            <input type="text" name="kala1_masalah_lain" class="form-control" value="{{ $catatan->kala1_masalah_lain }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Penatalaksanaan Kala I</label>
                            <textarea name="kala1_penatalaksanaan" class="form-control" rows="2">{{ $catatan->kala1_penatalaksanaan }}</textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Hasil Evaluasi Kala I</label>
                            <textarea name="kala1_hasil_evaluasi" class="form-control" rows="2">{{ $catatan->kala1_hasil_evaluasi }}</textarea>
                        </div>
                    </div>

                    {{-- 3. KALA II --}}
                    <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                        <i class="fas fa-baby mr-1"></i> 3. Catatan Kala II (Pengeluaran Janin)
                    </h6>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Lama Kala II (Menit)</label>
                            <input type="number" name="kala2_lama_menit" class="form-control" min="0" max="600" value="{{ $catatan->kala2_lama_menit }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Episiotomi <span class="text-danger">*</span></label>
                            <select name="kala2_episiotomi" class="form-control" required>
                                <option value="0" {{ (string)$catatan->kala2_episiotomi === '0' ? 'selected' : '' }}>Tidak</option>
                                <option value="1" {{ (string)$catatan->kala2_episiotomi === '1' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Indikasi Episiotomi (Jika Ya)</label>
                            <input type="text" name="kala2_indikasi_episiotomi" class="form-control" value="{{ $catatan->kala2_indikasi_episiotomi }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Pendamping Persalinan</label>
                            <select name="kala2_pendamping" class="form-control">
                                <option value="suami" {{ $catatan->kala2_pendamping == 'suami' ? 'selected' : '' }}>Suami</option>
                                <option value="keluarga" {{ $catatan->kala2_pendamping == 'keluarga' ? 'selected' : '' }}>Keluarga</option>
                                <option value="teman" {{ $catatan->kala2_pendamping == 'teman' ? 'selected' : '' }}>Teman</option>
                                <option value="dukun" {{ $catatan->kala2_pendamping == 'dukun' ? 'selected' : '' }}>Dukun</option>
                                <option value="tidak_ada" {{ $catatan->kala2_pendamping == 'tidak_ada' ? 'selected' : '' }}>Tidak Ada</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Gawat Janin (Tindakan)</label>
                            <input type="text" name="kala2_gawat_janin" class="form-control" value="{{ $catatan->kala2_gawat_janin }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Distosia Bahu (Tindakan)</label>
                            <input type="text" name="kala2_distosia_bahu" class="form-control" value="{{ $catatan->kala2_distosia_bahu }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Penatalaksanaan Kala II</label>
                            <textarea name="kala2_penatalaksanaan" class="form-control" rows="2">{{ $catatan->kala2_penatalaksanaan }}</textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Hasil Evaluasi Kala II</label>
                            <textarea name="kala2_hasil_evaluasi" class="form-control" rows="2">{{ $catatan->kala2_hasil_evaluasi }}</textarea>
                        </div>
                    </div>

                    {{-- 4. KALA III --}}
                    <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                        <i class="fas fa-heartbeat mr-1"></i> 4. Catatan Kala III (Pengeluaran Plasenta & MAK III)
                    </h6>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Lama Kala III (Menit)</label>
                            <input type="number" name="kala3_lama_menit" class="form-control" min="0" max="300" value="{{ $catatan->kala3_lama_menit }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Jumlah Darah (ml)</label>
                            <input type="number" name="kala3_jumlah_perdarahan_ml" class="form-control" min="0" max="5000" value="{{ $catatan->kala3_jumlah_perdarahan_ml }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Oksitosin 10U dlm 2 mnt? <span class="text-danger">*</span></label>
                            <select name="kala3_oksitosin_10u_2menit" class="form-control" required>
                                <option value="1" {{ (string)$catatan->kala3_oksitosin_10u_2menit === '1' ? 'selected' : '' }}>Ya</option>
                                <option value="0" {{ (string)$catatan->kala3_oksitosin_10u_2menit === '0' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Alasan Tdk Oksitosin</label>
                            <input type="text" name="kala3_alasan_tidak_oksitosin" class="form-control" value="{{ $catatan->kala3_alasan_tidak_oksitosin }}">
                        </div>

                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Oksitosin Ulang? <span class="text-danger">*</span></label>
                            <select name="kala3_oksitosin_ulang" class="form-control" required>
                                <option value="0" {{ (string)$catatan->kala3_oksitosin_ulang === '0' ? 'selected' : '' }}>Tidak</option>
                                <option value="1" {{ (string)$catatan->kala3_oksitosin_ulang === '1' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Alasan Oksitosin Ulang</label>
                            <input type="text" name="kala3_alasan_oksitosin_ulang" class="form-control" value="{{ $catatan->kala3_alasan_oksitosin_ulang }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Penegangan Tali Pusat? <span class="text-danger">*</span></label>
                            <select name="kala3_penegangan_tali_pusat" class="form-control" required>
                                <option value="1" {{ (string)$catatan->kala3_penegangan_tali_pusat === '1' ? 'selected' : '' }}>Ya</option>
                                <option value="0" {{ (string)$catatan->kala3_penegangan_tali_pusat === '0' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Alasan Tidak PTP</label>
                            <input type="text" name="kala3_alasan_tidak_ptp" class="form-control" value="{{ $catatan->kala3_alasan_tidak_ptp }}">
                        </div>

                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Masase Fundus Uteri? <span class="text-danger">*</span></label>
                            <select name="kala3_masase_fundus" class="form-control" required>
                                <option value="1" {{ (string)$catatan->kala3_masase_fundus === '1' ? 'selected' : '' }}>Ya</option>
                                <option value="0" {{ (string)$catatan->kala3_masase_fundus === '0' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Alasan Tidak Masase</label>
                            <input type="text" name="kala3_alasan_tidak_masase" class="form-control" value="{{ $catatan->kala3_alasan_tidak_masase }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Derajat Laserasi <span class="text-danger">*</span></label>
                            <select name="kala3_derajat_laserasi" class="form-control" required>
                                <option value="tidak_ada" {{ $catatan->kala3_derajat_laserasi == 'tidak_ada' ? 'selected' : '' }}>Tidak Ada</option>
                                <option value="1" {{ $catatan->kala3_derajat_laserasi == '1' ? 'selected' : '' }}>Derajat 1</option>
                                <option value="2" {{ $catatan->kala3_derajat_laserasi == '2' ? 'selected' : '' }}>Derajat 2</option>
                                <option value="3" {{ $catatan->kala3_derajat_laserasi == '3' ? 'selected' : '' }}>Derajat 3</option>
                                <option value="4" {{ $catatan->kala3_derajat_laserasi == '4' ? 'selected' : '' }}>Derajat 4</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Tindakan Laserasi</label>
                            <input type="text" name="kala3_tindakan_laserasi" class="form-control" value="{{ $catatan->kala3_tindakan_laserasi }}">
                        </div>

                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Atonia Uteri (Tindakan)</label>
                            <input type="text" name="kala3_atonia_uteri" class="form-control" value="{{ $catatan->kala3_atonia_uteri }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Penatalaksanaan Kala III</label>
                            <textarea name="kala3_penatalaksanaan" class="form-control" rows="2">{{ $catatan->kala3_penatalaksanaan }}</textarea>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Hasil Evaluasi Kala III</label>
                            <textarea name="kala3_hasil_evaluasi" class="form-control" rows="2">{{ $catatan->kala3_hasil_evaluasi }}</textarea>
                        </div>
                    </div>

                    {{-- 5. KALA IV --}}
                    <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                        <i class="fas fa-procedures mr-1"></i> 5. Catatan Ringkasan Kala IV (Pengawasan Post-Partum)
                    </h6>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Masalah Kala IV</label>
                            <textarea name="kala4_masalah" class="form-control" rows="2">{{ $catatan->kala4_masalah }}</textarea>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Penatalaksanaan Kala IV</label>
                            <textarea name="kala4_penatalaksanaan" class="form-control" rows="2">{{ $catatan->kala4_penatalaksanaan }}</textarea>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Hasil Evaluasi Kala IV</label>
                            <textarea name="kala4_hasil_evaluasi" class="form-control" rows="2">{{ $catatan->kala4_hasil_evaluasi }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning shadow-sm font-weight-bold text-dark">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
