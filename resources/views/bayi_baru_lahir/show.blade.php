@extends('layouts.master')

@section('title', 'Detail Rekam Medis Bayi Baru Lahir')

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
            font-size: 0.825rem;
            margin-bottom: 0.15rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .data-value {
            font-size: 0.925rem;
            color: #111827;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            padding: 0.45rem 0.75rem;
            min-height: 38px;
            display: flex;
            align-items: center;
        }
        .data-value.textarea-val {
            min-height: 70px;
            align-items: flex-start;
            white-space: pre-line;
        }

        /* APGAR Score Card Highlights */
        .apgar-box {
            text-align: center;
            border-radius: 0.5rem;
            padding: 0.75rem 0.5rem;
            border: 2px solid #e5e7eb;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .apgar-box.active {
            border-color: #059669;
            background-color: #f0fdf4;
        }
        .apgar-num {
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1.2;
        }

        /* Print Styling */
        @media print {
            #wrapper #content-wrapper {
                background-color: #fff !important;
            }
            #accordionSidebar, .navbar, .btn-group, footer, .btn, .modal {
                display: none !important;
            }
            .card {
                border: 1px solid #ccc !important;
                box-shadow: none !important;
                margin-bottom: 1rem !important;
                page-break-inside: avoid;
            }
            .card-header {
                background-color: #f3f4f6 !important;
                color: #000 !important;
            }
            .data-value {
                background-color: #fff !important;
                border-color: #ddd !important;
            }
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
        title="Detail Rekam Medis Bayi Baru Lahir"
        subtitle="Dokumentasi asuhan segera, evaluasi nilai APGAR, antropometri, dan penatalaksanaan pasca lahir"
        icon="fas fa-baby"
    >
        <div class="btn-group shadow-sm">
            <a href="{{ route('bayi-baru-lahir.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
            </a>
            <button type="button" class="btn btn-sm btn-warning text-dark font-weight-bold" data-toggle="modal" data-target="#modalEditBayiShow">
                <i class="fas fa-edit mr-1"></i> Edit Data Bayi
            </button>
            <button type="button" class="btn btn-sm btn-primary" onclick="window.print();">
                <i class="fas fa-print mr-1"></i> Cetak Rekam Medis
            </button>
        </div>
    </x-page-header>

    @php
        $rawNama = $pasien->nama_ibu ?? 'Pasien #' . $bayi->persalinan_id;
        $namaClean = preg_match('/^ny\.?\s*/i', $rawNama) ? $rawNama : 'Ny. ' . $rawNama;
        $resArray = array_map('trim', explode(',', $bayi->tindakan_resusitasi ?? ''));
    @endphp

    <!-- Card 1: Identitas Ibu & Persalinan -->
    <div class="card shadow mb-4 border-left-primary">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-female mr-2"></i> Identitas Ibu & Data Persalinan Terkait
            </h6>
            <span class="badge badge-primary px-3 py-2 font-weight-bold">
                ID Bayi #{{ $bayi->id }} | Persalinan #{{ $bayi->persalinan_id }}
            </span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="data-label">Nomor Register Ibu</div>
                    <div class="data-value font-weight-bold text-primary">
                        {{ $pasien->nomor_register ?? '-' }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">Nama Ibu Kandung</div>
                    <div class="data-value font-weight-bold">
                        {{ $namaClean }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">Usia Ibu / Suami</div>
                    <div class="data-value">
                        {{ $pasien->usia_ibu ?? '-' }} tahun
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
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="data-label">Paritas (G - P - A)</div>
                    <div class="data-value font-weight-bold">
                        G{{ $persalinan->gravida ?? '-' }} P{{ $persalinan->para ?? '-' }} A{{ $persalinan->abortus ?? '-' }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">Fasilitas Kesehatan</div>
                    <div class="data-value">
                        {{ $persalinan->nama_fasilitas ?? '-' }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">Bidan / Penolong</div>
                    <div class="data-value">
                        {{ $persalinan->nama_penolong ?? '-' }}
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">Waktu Masuk / Persalinan</div>
                    <div class="data-value">
                        {{ $persalinan->waktu_masuk ? \Carbon\Carbon::parse($persalinan->waktu_masuk)->format('d/m/Y H:i') . ' WIB' : '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Antropometri & Nilai APGAR -->
    <div class="card shadow mb-4 border-left-success">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-weight mr-2"></i> Antropometri & Evaluasi Nilai APGAR
            </h6>
            @if($bayi->urutan_kelahiran == 1)
                <span class="badge badge-success px-3 py-1 font-weight-bold">Kelahiran Tunggal / Bayi Ke-1</span>
            @else
                <span class="badge badge-info px-3 py-1 font-weight-bold">Gemelli (Bayi Ke-{{ $bayi->urutan_kelahiran }})</span>
            @endif
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="data-label">Jenis Kelamin</div>
                    <div class="data-value">
                        @if($bayi->jenis_kelamin == 'L')
                            <span class="badge badge-primary px-3 py-2"><i class="fas fa-mars mr-1"></i> Laki-laki</span>
                        @else
                            <span class="badge badge-danger px-3 py-2" style="background-color: #ec4899;"><i class="fas fa-venus mr-1"></i> Perempuan</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">Berat Badan Lahir</div>
                    <div class="data-value font-weight-bold text-primary" style="font-size: 1.1rem;">
                        <i class="fas fa-weight mr-2 text-muted"></i> {{ number_format($bayi->berat_badan_gram, 0, ',', '.') }} gram
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">Panjang Badan</div>
                    <div class="data-value font-weight-bold text-info" style="font-size: 1.1rem;">
                        <i class="fas fa-ruler-vertical mr-2 text-muted"></i> {{ $bayi->panjang_badan_cm }} cm
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="data-label">Kondisi Bayi Saat Lahir</div>
                    <div class="data-value">
                        @if($bayi->kondisi_bayi == 'normal')
                            <span class="badge badge-success px-3 py-2"><i class="fas fa-check-circle mr-1"></i> Normal / Bugar</span>
                        @else
                            <span class="badge badge-danger px-3 py-2"><i class="fas fa-exclamation-triangle mr-1"></i> Asfiksia</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Visual APGAR Score --}}
            <h6 class="font-weight-bold text-gray-800 mb-3 section-header">
                <i class="fas fa-chart-line mr-1"></i> Perkembangan Skor APGAR (Appearance, Pulse, Grimace, Activity, Respiration)
            </h6>
            <div class="row text-center mb-3">
                <div class="col-md-4 mb-3">
                    <div class="apgar-box {{ ($bayi->apgar_menit_1 >= 7) ? 'active' : '' }}">
                        <div class="text-muted small text-uppercase font-weight-bold mb-1">Menit Ke-1</div>
                        <div class="apgar-num {{ ($bayi->apgar_menit_1 >= 7) ? 'text-success' : 'text-danger' }}">
                            {{ $bayi->apgar_menit_1 ?? '-' }}
                        </div>
                        <div class="small mt-1 text-muted">
                            @if($bayi->apgar_menit_1 !== null)
                                @if($bayi->apgar_menit_1 >= 7)
                                    <span class="text-success font-weight-bold">Bugar (Normal)</span>
                                @elseif($bayi->apgar_menit_1 >= 4)
                                    <span class="text-warning font-weight-bold">Asfiksia Sedang</span>
                                @else
                                    <span class="text-danger font-weight-bold">Asfiksia Berat</span>
                                @endif
                            @else
                                Belum dinilai
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="apgar-box {{ ($bayi->apgar_menit_5 >= 7) ? 'active' : '' }}">
                        <div class="text-muted small text-uppercase font-weight-bold mb-1">Menit Ke-5</div>
                        <div class="apgar-num {{ ($bayi->apgar_menit_5 >= 7) ? 'text-success' : 'text-danger' }}">
                            {{ $bayi->apgar_menit_5 ?? '-' }}
                        </div>
                        <div class="small mt-1 text-muted">
                            @if($bayi->apgar_menit_5 !== null)
                                @if($bayi->apgar_menit_5 >= 7)
                                    <span class="text-success font-weight-bold">Bugar (Normal)</span>
                                @elseif($bayi->apgar_menit_5 >= 4)
                                    <span class="text-warning font-weight-bold">Asfiksia Sedang</span>
                                @else
                                    <span class="text-danger font-weight-bold">Asfiksia Berat</span>
                                @endif
                            @else
                                Belum dinilai
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="apgar-box {{ ($bayi->apgar_menit_10 >= 7) ? 'active' : '' }}">
                        <div class="text-muted small text-uppercase font-weight-bold mb-1">Menit Ke-10</div>
                        <div class="apgar-num {{ ($bayi->apgar_menit_10 >= 7) ? 'text-success' : 'text-danger' }}">
                            {{ $bayi->apgar_menit_10 ?? '-' }}
                        </div>
                        <div class="small mt-1 text-muted">
                            @if($bayi->apgar_menit_10 !== null)
                                @if($bayi->apgar_menit_10 >= 7)
                                    <span class="text-success font-weight-bold">Bugar (Normal)</span>
                                @elseif($bayi->apgar_menit_10 >= 4)
                                    <span class="text-warning font-weight-bold">Asfiksia Sedang</span>
                                @else
                                    <span class="text-danger font-weight-bold">Asfiksia Berat</span>
                                @endif
                            @else
                                Belum dinilai
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="alert alert-light border py-2 small text-muted">
                <i class="fas fa-info-circle mr-1 text-primary"></i>
                <strong>Pedoman Klinis APGAR:</strong> Skor 7-10 = Bayi Normal/Bugar; Skor 4-6 = Asfiksia Ringan-Sedang; Skor 0-3 = Asfiksia Berat (Memerlukan tindakan resusitasi aktif segera).
            </div>
        </div>
    </div>

    <!-- Card 3: Asuhan Segera, Resusitasi, & IMD -->
    <div class="card shadow mb-4 border-left-info">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-info">
                <i class="fas fa-hand-holding-heart mr-2"></i> Inisiasi Menyusu Dini (IMD) & Asuhan Resusitasi
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="data-label">Inisiasi Menyusu Dini (IMD &lt; 1 Jam)</div>
                    <div class="data-value">
                        @if($bayi->pemberian_asi_kurang_1jam)
                            <span class="text-success font-weight-bold">
                                <i class="fas fa-check-circle mr-2"></i> Ya, Dilakukan dalam waktu kurang dari 1 jam
                            </span>
                        @else
                            <span class="text-danger font-weight-bold">
                                <i class="fas fa-times-circle mr-2"></i> Tidak Dilakukan
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="data-label">Alasan Tidak Dilakukan IMD</div>
                    <div class="data-value">
                        {{ $bayi->alasan_tidak_asi ?: 'Tidak ada (IMD terlaksana dengan baik)' }}
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="data-label">Tindakan Resusitasi yang Diberikan</div>
                    <div class="data-value">
                        @if(!empty($bayi->tindakan_resusitasi))
                            <div class="d-flex flex-wrap gap-2 py-1">
                                @foreach($resArray as $resItem)
                                    <span class="badge badge-light border text-dark mr-2 px-2 py-1">
                                        <i class="fas fa-check text-success mr-1"></i> {{ ucfirst(str_replace('_', ' ', $resItem)) }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted">Tidak ada resusitasi khusus (asuhan rutin normal)</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Catatan Medis, Penatalaksanaan, & Evaluasi -->
    <div class="card shadow mb-4 border-left-warning">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-notes-medical mr-2"></i> Catatan Medis Khusus, Profilaksis, & Hasil Evaluasi
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="data-label">Cacat Bawaan Lahir</div>
                    <div class="data-value">
                        {{ $bayi->cacat_bawaan ?: 'Tidak ada kelainan kongenital / cacat bawaan' }}
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="data-label">Masalah Lain Pada Bayi</div>
                    <div class="data-value">
                        {{ $bayi->masalah_lain ?: 'Tidak ada masalah klinis lain' }}
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="data-label">Penatalaksanaan Segera (Profilaksis & Terapi)</div>
                    <div class="data-value textarea-val">
                        {{ $bayi->penatalaksanaan ?: 'Asuhan neonatal esensial standar.' }}
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="data-label">Hasil Evaluasi Akhir Kondisi Bayi</div>
                    <div class="data-value textarea-val">
                        {{ $bayi->hasil_evaluasi ?: 'Tanda vital stabil, bayi bugar.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Kelahiran Saudara Kembar (Jika Gemelli) --}}
    @if(isset($saudara_kembar) && $saudara_kembar->isNotEmpty())
        <div class="card shadow mb-4 border-left-info">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-users mr-2"></i> Saudara Kembar (Gemelli / Triplet) Terkait
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Urutan</th>
                                <th>Jenis Kelamin</th>
                                <th>Berat Badan</th>
                                <th>Panjang Badan</th>
                                <th>APGAR (1'/5'/10')</th>
                                <th>Kondisi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($saudara_kembar as $kembar)
                                <tr>
                                    <td class="font-weight-bold">Bayi Ke-{{ $kembar->urutan_kelahiran }}</td>
                                    <td>{{ $kembar->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                    <td>{{ number_format($kembar->berat_badan_gram, 0, ',', '.') }} gram</td>
                                    <td>{{ $kembar->panjang_badan_cm }} cm</td>
                                    <td>{{ $kembar->apgar_menit_1 ?? '-' }} / {{ $kembar->apgar_menit_5 ?? '-' }} / {{ $kembar->apgar_menit_10 ?? '-' }}</td>
                                    <td>{{ ucfirst($kembar->kondisi_bayi) }}</td>
                                    <td>
                                        <a href="{{ route('bayi-baru-lahir.show', $kembar->id) }}" class="btn btn-xs btn-primary">
                                            Lihat Lembar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif


    {{-- ========================================================================= --}}
    {{-- MODAL EDIT DATA BAYI (DARI HALAMAN SHOW)                                   --}}
    {{-- ========================================================================= --}}
    <div class="modal fade text-left" id="modalEditBayiShow" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered" role="document">
            <form action="{{ route('bayi-baru-lahir.update', $bayi->id) }}" method="POST" class="modal-content shadow border-0">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning text-dark py-3">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-edit mr-2"></i> Edit Data Rekam Medis Bayi #{{ $bayi->id }}
                    </h5>
                    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <input type="hidden" name="persalinan_id" value="{{ $bayi->persalinan_id }}">

                    <h6 class="font-weight-bold text-primary mb-3 section-header">
                        <i class="fas fa-info-circle mr-1"></i> Data Kelahiran & Antropometri
                    </h6>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Urutan Kelahiran</label>
                            <select name="urutan_kelahiran" class="form-control" required>
                                <option value="1" {{ old('urutan_kelahiran', $bayi->urutan_kelahiran) == '1' ? 'selected' : '' }}>Bayi Tunggal / Ke-1</option>
                                <option value="2" {{ old('urutan_kelahiran', $bayi->urutan_kelahiran) == '2' ? 'selected' : '' }}>Bayi Ke-2 (Gemelli)</option>
                                <option value="3" {{ old('urutan_kelahiran', $bayi->urutan_kelahiran) == '3' ? 'selected' : '' }}>Bayi Ke-3 (Triplet)</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control" required>
                                <option value="L" {{ old('jenis_kelamin', $bayi->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $bayi->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Berat Badan (gram)</label>
                            <input type="number" name="berat_badan_gram" class="form-control" min="300" max="9000" value="{{ old('berat_badan_gram', $bayi->berat_badan_gram) }}" required>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Panjang Badan (cm)</label>
                            <input type="number" name="panjang_badan_cm" class="form-control" min="20" max="75" value="{{ old('panjang_badan_cm', $bayi->panjang_badan_cm) }}" required>
                        </div>
                    </div>

                    <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                        <i class="fas fa-heartbeat mr-1"></i> Nilai APGAR & IMD
                    </h6>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">APGAR Menit 1</label>
                            <input type="number" name="apgar_menit_1" class="form-control" min="0" max="10" value="{{ old('apgar_menit_1', $bayi->apgar_menit_1) }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">APGAR Menit 5</label>
                            <input type="number" name="apgar_menit_5" class="form-control" min="0" max="10" value="{{ old('apgar_menit_5', $bayi->apgar_menit_5) }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">APGAR Menit 10</label>
                            <input type="number" name="apgar_menit_10" class="form-control" min="0" max="10" value="{{ old('apgar_menit_10', $bayi->apgar_menit_10) }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Pemberian ASI &lt; 1 Jam (IMD)</label>
                            <select name="pemberian_asi_kurang_1jam" class="form-control" required>
                                <option value="1" {{ old('pemberian_asi_kurang_1jam', $bayi->pemberian_asi_kurang_1jam ? '1' : '0') == '1' ? 'selected' : '' }}>Ya (Berhasil IMD)</option>
                                <option value="0" {{ old('pemberian_asi_kurang_1jam', $bayi->pemberian_asi_kurang_1jam ? '1' : '0') == '0' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Alasan Tidak ASI</label>
                            <input type="text" name="alasan_tidak_asi" class="form-control" value="{{ old('alasan_tidak_asi', $bayi->alasan_tidak_asi) }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Kondisi Bayi</label>
                            <select name="kondisi_bayi" class="form-control" required>
                                <option value="normal" {{ old('kondisi_bayi', $bayi->kondisi_bayi) == 'normal' ? 'selected' : '' }}>Normal / Bugar</option>
                                <option value="asfiksia_pucat_biru_lemas" {{ old('kondisi_bayi', $bayi->kondisi_bayi) == 'asfiksia_pucat_biru_lemas' ? 'selected' : '' }}>Asfiksia (Pucat / Biru / Lemas)</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                        <i class="fas fa-first-aid mr-1"></i> Resusitasi & Catatan Klinis
                    </h6>
                    <div class="row">
                        <div class="col-12 form-group">
                            <label class="font-weight-bold text-gray-700 mb-2">Tindakan Resusitasi:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="custom-control custom-checkbox mr-3 mb-2">
                                    <input type="checkbox" name="tindakan_resusitasi[]" value="mengeringkan" class="custom-control-input" id="show_edit_kering" {{ in_array('mengeringkan', $resArray) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="show_edit_kering">Mengeringkan</label>
                                </div>
                                <div class="custom-control custom-checkbox mr-3 mb-2">
                                    <input type="checkbox" name="tindakan_resusitasi[]" value="menghangatkan" class="custom-control-input" id="show_edit_hangat" {{ in_array('menghangatkan', $resArray) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="show_edit_hangat">Menghangatkan</label>
                                </div>
                                <div class="custom-control custom-checkbox mr-3 mb-2">
                                    <input type="checkbox" name="tindakan_resusitasi[]" value="rangsang_aktif" class="custom-control-input" id="show_edit_rangsang" {{ in_array('rangsang_aktif', $resArray) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="show_edit_rangsang">Rangsang Aktif / Taktil</label>
                                </div>
                                <div class="custom-control custom-checkbox mr-3 mb-2">
                                    <input type="checkbox" name="tindakan_resusitasi[]" value="bebaskan_jalan_napas" class="custom-control-input" id="show_edit_napas" {{ in_array('bebaskan_jalan_napas', $resArray) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="show_edit_napas">Bebaskan Jalan Napas</label>
                                </div>
                                <div class="custom-control custom-checkbox mr-3 mb-2">
                                    <input type="checkbox" name="tindakan_resusitasi[]" value="ventilasi_tekanan_positif" class="custom-control-input" id="show_edit_vtp" {{ in_array('ventilasi_tekanan_positif', $resArray) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="show_edit_vtp">VTP</label>
                                </div>
                                <div class="custom-control custom-checkbox mr-3 mb-2">
                                    <input type="checkbox" name="tindakan_resusitasi[]" value="kompresi_dada" class="custom-control-input" id="show_edit_kompresi" {{ in_array('kompresi_dada', $resArray) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="show_edit_kompresi">Kompresi Dada</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Cacat Bawaan</label>
                            <input type="text" name="cacat_bawaan" class="form-control" value="{{ old('cacat_bawaan', $bayi->cacat_bawaan) }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Masalah Lain</label>
                            <input type="text" name="masalah_lain" class="form-control" value="{{ old('masalah_lain', $bayi->masalah_lain) }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Penatalaksanaan Segera</label>
                            <textarea name="penatalaksanaan" class="form-control" rows="3">{{ old('penatalaksanaan', $bayi->penatalaksanaan) }}</textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Hasil Evaluasi</label>
                            <textarea name="hasil_evaluasi" class="form-control" rows="3">{{ old('hasil_evaluasi', $bayi->hasil_evaluasi) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-warning shadow-sm font-weight-bold text-dark">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
