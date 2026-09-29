@extends('layouts.master')

@section('title', 'Data Catatan Persalinan')

@push('styles')
    <style>
        /* Section headers for modals */
        .section-header {
            background-color: #f8fafc;
            color: #1f2937 !important;
            border-left: 4px solid #059669;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0 0.35rem 0.35rem 0;
        }

        /* Modal styling */
        .modal-dialog-scrollable {
            max-height: calc(100vh - 2rem) !important;
        }
        .modal-dialog-scrollable form.modal-content,
        .modal-dialog-scrollable .modal-content {
            border-radius: 0.5rem !important;
            overflow: hidden !important;
        }

        /* Table custom styling */
        #dataTable {
            width: 100% !important;
            margin-bottom: 0;
        }
        #dataTable th {
            background-color: #f9fafb !important;
            color: #4b5563 !important;
            font-weight: 700 !important;
            font-size: 0.775rem !important;
            letter-spacing: 0.04em !important;
            border-bottom: 2px solid #e5e7eb !important;
            vertical-align: middle !important;
            padding: 0.75rem 0.6rem !important;
        }
        #dataTable td {
            padding: 0.75rem 0.6rem !important;
            vertical-align: middle !important;
            font-size: 0.875rem !important;
        }

        /* DataTables Length & Search Layout Fix */
        .dataTables_wrapper .row:first-child {
            align-items: center;
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_length {
            display: flex;
            align-items: center;
        }
        .dataTables_wrapper .dataTables_length label {
            display: inline-flex !important;
            align-items: center !important;
            margin-bottom: 0 !important;
            font-size: 0.875rem;
            color: #4b5563;
        }
        .dataTables_wrapper .dataTables_length select {
            width: auto !important;
            display: inline-block !important;
            margin: 0 0.5rem !important;
            padding: 0.35rem 1.75rem 0.35rem 0.75rem !important;
            border-radius: 0.35rem !important;
            border: 1px solid #d1d5db !important;
            font-size: 0.875rem !important;
            height: calc(1.5em + 0.5rem + 2px) !important;
            background-color: #fff !important;
        }

        .dataTables_wrapper .dataTables_filter {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        .dataTables_wrapper .dataTables_filter label {
            display: inline-flex !important;
            align-items: center !important;
            position: relative;
            margin-bottom: 0 !important;
        }
        .dataTables_wrapper .dataTables_filter label::before {
            font-family: "Font Awesome 5 Free";
            content: "\f002";
            font-weight: 900;
            position: absolute;
            left: 0.75rem;
            color: #9ca3af;
            font-size: 0.85rem;
            pointer-events: none;
            z-index: 2;
        }
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0 !important;
            padding: 0.4rem 1rem 0.4rem 2.25rem !important;
            border-radius: 20px !important;
            border: 1px solid #d1d5db !important;
            font-size: 0.875rem !important;
            width: 230px;
            background-color: #fff !important;
            transition: all 0.2s ease-in-out;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            width: 270px;
            border-color: #059669 !important;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.2) !important;
            outline: 0 !important;
        }

        /* DataTables Pagination & Info */
        .dataTables_wrapper .dataTables_info {
            font-size: 0.85rem;
            color: #6b7280;
            padding-top: 0.75rem;
        }
        .dataTables_wrapper .dataTables_paginate {
            display: flex;
            justify-content: flex-end;
            padding-top: 0.5rem;
        }
        .dataTables_wrapper .pagination {
            margin-bottom: 0;
        }
    </style>
@endpush

@section('content')

    <!-- Flash Message / Alert -->
    @if(session('success'))
        <x-alert type="success" icon="fas fa-check-circle" dismissible>
            {{ session('success') }}
        </x-alert>
    @endif

    @if(isset($errors) && $errors->any())
        <x-alert type="danger" icon="fas fa-exclamation-circle" dismissible title="Terjadi Kesalahan Pengisian:">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <!-- Page Header -->
    <x-page-header
        title="Catatan Persalinan"
        subtitle="Rekam medik persalinan Kala I, II, III, dan ringkasan observasi Kala IV (Lembar Belakang Partograf)"
        icon="fas fa-clipboard-check"
    >
        <button type="button" class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahCatatan">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Catatan Persalinan
        </button>
    </x-page-header>

    <!-- Ringkasan Statistik -->
    <div class="row">
        <x-stat-card
            title="Total Catatan"
            :value="count($data_catatan)"
            icon="fas fa-clipboard-list"
            color="primary"
            subtitle="Lembar partograf tersimpan"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="Kala I Normal"
            :value="collect($data_catatan)->where('kala1_melewati_garis_waspada', 0)->count()"
            icon="fas fa-shield-alt"
            color="success"
            subtitle="Garis waspada aman"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="Non-Episiotomi"
            :value="collect($data_catatan)->where('kala2_episiotomi', 0)->count()"
            icon="fas fa-baby"
            color="info"
            subtitle="Tanpa sayatan perineum"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="MAK III Lengkap"
            :value="collect($data_catatan)->where('kala3_oksitosin_10u_2menit', 1)->where('kala3_penegangan_tali_pusat', 1)->where('kala3_masase_fundus', 1)->count()"
            icon="fas fa-check-double"
            color="warning"
            subtitle="Oksitosin, PTP, & Masase"
            col="col-xl-3 col-md-6 mb-4"
        />
    </div>

    <!-- Tabel Data Catatan Persalinan -->
    <x-card title="Daftar Catatan Persalinan" icon="fas fa-list-alt">
        <x-slot:actions>
            <x-badge color="primary" pill>
                Total: {{ count($data_catatan) }} Data
            </x-badge>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" id="dataTable" cellspacing="0">
                <thead>
                    <tr class="text-center text-uppercase">
                        <th style="width: 45px;">No</th>
                        <th style="min-width: 170px;">Pasien / No. Register</th>
                        <th style="min-width: 150px;">Fasilitas & Penolong</th>
                        <th style="width: 110px;">Kala I</th>
                        <th style="min-width: 160px;">Kala II</th>
                        <th style="min-width: 160px;">Kala III</th>
                        <th style="min-width: 140px;">Kala IV</th>
                        <th style="width: 110px;">Dicatat</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data_catatan as $data)
                        @php
                            $rawNama = $data->nama_pasien ?? ($data->ibu_pasien ?? 'Pasien #' . $data->persalinan_id);
                            $namaClean = preg_match('/^ny\.?\s*/i', $rawNama) ? $rawNama : 'Ny. ' . $rawNama;
                        @endphp
                        <tr>
                            {{-- 1. No --}}
                            <td class="text-center font-weight-bold text-gray-600 text-nowrap">
                                {{ $loop->iteration }}
                            </td>

                            {{-- 2. Pasien --}}
                            <td>
                                <div class="font-weight-bold text-primary mb-1">
                                    {{ $namaClean }}
                                </div>
                                <div class="small">
                                    <span class="badge badge-light border text-muted px-2 py-1 font-weight-normal">
                                        <i class="fas fa-id-card text-muted mr-1"></i>{{ $data->nomor_register ?? ($data->no_register ?? '-') }}
                                    </span>
                                    @if(!empty($data->usia_ibu))
                                        <span class="badge badge-light border text-muted px-2 py-1 font-weight-normal ml-1">
                                            {{ $data->usia_ibu }} th
                                        </span>
                                    @endif
                                </div>
                                @if(!empty($data->nama_suami))
                                    <div class="small text-muted mt-1">
                                        <i class="fas fa-user-friends text-gray-400 mr-1"></i>Suami: {{ $data->nama_suami }}
                                    </div>
                                @endif
                            </td>

                            {{-- 3. Fasilitas & Penolong --}}
                            <td>
                                <div class="font-weight-bold text-gray-800 mb-1">{{ $data->nama_fasilitas ?? '-' }}</div>
                                <div class="small text-muted">
                                    <i class="fas fa-user-nurse mr-1 text-primary"></i>{{ $data->nama_penolong ?? '-' }}
                                </div>
                            </td>

                            {{-- 4. Kala I --}}
                            <td class="text-center text-nowrap">
                                @if($data->kala1_melewati_garis_waspada)
                                    <span class="badge badge-danger px-2 py-1 shadow-sm" title="Melewati garis waspada partograf">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Lewat Waspada
                                    </span>
                                @else
                                    <span class="badge badge-success px-2 py-1 shadow-sm" title="Kemajuan persalinan normal">
                                        <i class="fas fa-check-circle mr-1"></i> Aman
                                    </span>
                                @endif
                            </td>

                            {{-- 5. Kala II --}}
                            <td>
                                <div class="mb-1">
                                    <span class="badge badge-light border text-dark font-weight-bold mr-1">
                                        <i class="fas fa-stopwatch text-primary mr-1"></i>{{ $data->kala2_lama_menit ? $data->kala2_lama_menit . ' mnt' : '-' }}
                                    </span>
                                    @if($data->kala2_episiotomi)
                                        <span class="badge badge-warning">Episiotomi</span>
                                    @else
                                        <span class="badge badge-light border text-muted">Non-Episiotomi</span>
                                    @endif
                                </div>
                                <div class="small text-muted text-capitalize">
                                    <i class="fas fa-user-friends text-gray-400 mr-1"></i>{{ str_replace('_', ' ', $data->kala2_pendamping ?? 'Tanpa pendamping') }}
                                </div>
                            </td>

                            {{-- 6. Kala III --}}
                            <td>
                                <div class="mb-1">
                                    <span class="badge badge-light border text-dark font-weight-bold mr-1">
                                        <i class="fas fa-stopwatch text-primary mr-1"></i>{{ $data->kala3_lama_menit ? $data->kala3_lama_menit . ' mnt' : '-' }}
                                    </span>
                                    <span class="badge badge-light border text-danger font-weight-bold">
                                        <i class="fas fa-tint mr-1"></i>{{ $data->kala3_jumlah_perdarahan_ml ? $data->kala3_jumlah_perdarahan_ml . ' ml' : '0 ml' }}
                                    </span>
                                </div>
                                <div class="small">
                                    @if($data->kala3_derajat_laserasi == 'tidak_ada')
                                        <span class="text-success small"><i class="fas fa-check mr-1"></i>Tanpa Laserasi</span>
                                    @else
                                        <span class="badge badge-info px-2 py-0 font-weight-normal">Laserasi Derajat {{ $data->kala3_derajat_laserasi }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- 7. Kala IV --}}
                            <td>
                                @if(!empty($data->kala4_masalah) && $data->kala4_masalah != 'Tidak ada masalah')
                                    <span class="badge badge-warning px-2 py-1 mb-1 d-inline-block text-truncate" style="max-width: 140px;" title="{{ $data->kala4_masalah }}">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $data->kala4_masalah }}
                                    </span>
                                @else
                                    <span class="badge badge-success px-2 py-1 mb-1">
                                        <i class="fas fa-check-circle mr-1"></i>Stabil
                                    </span>
                                @endif
                                <div class="small text-muted text-truncate" style="max-width: 150px;" title="{{ $data->kala4_hasil_evaluasi }}">
                                    {{ $data->kala4_hasil_evaluasi ?? '-' }}
                                </div>
                            </td>

                            {{-- 8. Waktu Dicatat --}}
                            <td class="text-center text-nowrap small text-gray-700">
                                <div class="font-weight-bold">{{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y') }}</div>
                                <div class="text-muted">{{ \Carbon\Carbon::parse($data->created_at)->format('H:i') }} WIB</div>
                            </td>

                            {{-- 9. Aksi --}}
                            <td class="text-center text-nowrap">
                                <div class="btn-group btn-group-sm shadow-sm" role="group">
                                    {{-- Tombol Detail ke Show Page --}}
                                    <a href="{{ route('catatan-persalinan.show', $data->id) }}" class="btn btn-info" title="Lihat Rincian Lengkap (Show)">
                                        <i class="fas fa-eye fa-sm mr-1"></i>Detail
                                    </a>

                                    {{-- Tombol Edit --}}
                                    <button type="button" class="btn btn-warning text-dark" data-toggle="modal"
                                        data-target="#modalEditCatatan{{ $data->id }}" title="Edit Data">
                                        <i class="fas fa-edit fa-sm">Edit</i>
                                    </button>

                                    {{-- Tombol Hapus --}}
                                    <button type="button" class="btn btn-danger"
                                        onclick="if(confirm('Apakah Anda yakin ingin menghapus catatan persalinan {{ $namaClean }}?')) document.getElementById('delete-form-{{ $data->id }}').submit();"
                                        title="Hapus Data">
                                        <i class="fas fa-trash fa-sm">Hapus</i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $data->id }}" action="{{ route('catatan-persalinan.destroy', $data->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fas fa-clipboard-list fa-3x mb-2 text-gray-300 d-block"></i>
                                Belum ada data catatan persalinan. Klik tombol <strong>Tambah Catatan Persalinan</strong> untuk mendokumentasikan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>


    {{-- ========================================================================= --}}
    {{-- MODAL TAMBAH DATA CATATAN PERSALINAN                                      --}}
    {{-- ========================================================================= --}}
    <div class="modal fade text-left" id="modalTambahCatatan" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered" role="document">
            <form action="{{ route('catatan-persalinan.store') }}" method="POST" class="modal-content shadow border-0">
                @csrf
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title font-weight-bold" id="modalTambahLabel">
                        <i class="fas fa-file-medical mr-2"></i> Tambah Catatan Persalinan (Lembar Belakang Partograf)
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    {{-- 1. PILIH PASIEN PERSALINAN --}}
                    <h6 class="font-weight-bold text-primary mb-3 section-header">
                        <i class="fas fa-user-injured mr-1"></i> 1. Identitas Pasien Persalinan
                    </h6>
                    <div class="row">
                        <div class="col-12 form-group">
                            <label class="font-weight-bold text-gray-700">Pilih Pasien Persalinan <span class="text-danger">*</span></label>
                            @if($data_persalinan->isEmpty())
                                <div class="alert alert-warning py-2 mb-0 small">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Semua pasien persalinan aktif sudah memiliki lembar catatan persalinan.
                                </div>
                            @else
                                <select name="persalinan_id" class="form-control" required>
                                    <option value="">-- Pilih Data Persalinan Pasien --</option>
                                    @foreach($data_persalinan as $dp)
                                        <option value="{{ $dp->id }}" {{ old('persalinan_id') == $dp->id ? 'selected' : '' }}>
                                            {{ $dp->no_register }} - {{ $dp->ibu_pasien }}
                                            (G{{ $dp->gravida }}P{{ $dp->para }}A{{ $dp->abortus }}, Masuk: {{ $dp->waktu_masuk ? \Carbon\Carbon::parse($dp->waktu_masuk)->format('d/m/Y H:i') : '-' }})
                                            [{{ ucfirst($dp->status_persalinan) }}]
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Menampilkan daftar persalinan yang belum memiliki catatan persalinan.</small>
                            @endif
                        </div>
                    </div>

                    {{-- 2. KALA I --}}
                    <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                        <i class="fas fa-clock mr-1"></i> 2. Catatan Kala I (Fase Persalinan)
                    </h6>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Melewati Garis Waspada? <span class="text-danger">*</span></label>
                            <select name="kala1_melewati_garis_waspada" class="form-control" required>
                                <option value="0" {{ old('kala1_melewati_garis_waspada', '0') == '0' ? 'selected' : '' }}>Tidak (Aman)</option>
                                <option value="1" {{ old('kala1_melewati_garis_waspada') == '1' ? 'selected' : '' }}>Ya (Melewati Garis Waspada)</option>
                            </select>
                        </div>
                        <div class="col-md-8 form-group">
                            <label class="font-weight-bold text-gray-700">Masalah Lain Kala I</label>
                            <input type="text" name="kala1_masalah_lain" class="form-control" placeholder="Contoh: Nyeri hebat, gelisah, dll" value="{{ old('kala1_masalah_lain') }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Penatalaksanaan Kala I</label>
                            <textarea name="kala1_penatalaksanaan" class="form-control" rows="2" placeholder="Tindakan yang dilakukan selama Kala I">{{ old('kala1_penatalaksanaan') }}</textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Hasil Evaluasi Kala I</label>
                            <textarea name="kala1_hasil_evaluasi" class="form-control" rows="2" placeholder="Hasil evaluasi kondisi ibu dan janin di Kala I">{{ old('kala1_hasil_evaluasi') }}</textarea>
                        </div>
                    </div>

                    {{-- 3. KALA II --}}
                    <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                        <i class="fas fa-baby mr-1"></i> 3. Catatan Kala II (Pengeluaran Janin)
                    </h6>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Lama Kala II (Menit)</label>
                            <input type="number" name="kala2_lama_menit" class="form-control" min="0" max="600" placeholder="Contoh: 30" value="{{ old('kala2_lama_menit') }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Episiotomi <span class="text-danger">*</span></label>
                            <select name="kala2_episiotomi" class="form-control" required>
                                <option value="0" {{ old('kala2_episiotomi', '0') == '0' ? 'selected' : '' }}>Tidak</option>
                                <option value="1" {{ old('kala2_episiotomi') == '1' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Indikasi Episiotomi (Jika Ya)</label>
                            <input type="text" name="kala2_indikasi_episiotomi" class="form-control" placeholder="Contoh: Perineum kaku, gawat janin" value="{{ old('kala2_indikasi_episiotomi') }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Pendamping Persalinan</label>
                            <select name="kala2_pendamping" class="form-control">
                                <option value="suami" {{ old('kala2_pendamping', 'suami') == 'suami' ? 'selected' : '' }}>Suami</option>
                                <option value="keluarga" {{ old('kala2_pendamping') == 'keluarga' ? 'selected' : '' }}>Keluarga</option>
                                <option value="teman" {{ old('kala2_pendamping') == 'teman' ? 'selected' : '' }}>Teman</option>
                                <option value="dukun" {{ old('kala2_pendamping') == 'dukun' ? 'selected' : '' }}>Dukun</option>
                                <option value="tidak_ada" {{ old('kala2_pendamping') == 'tidak_ada' ? 'selected' : '' }}>Tidak Ada</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Gawat Janin (Tindakan)</label>
                            <input type="text" name="kala2_gawat_janin" class="form-control" placeholder="Contoh: Miring kiri, O2, tidak ada" value="{{ old('kala2_gawat_janin', 'tidak_ada') }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Distosia Bahu (Tindakan)</label>
                            <input type="text" name="kala2_distosia_bahu" class="form-control" placeholder="Contoh: McRobert, tidak ada" value="{{ old('kala2_distosia_bahu', 'tidak_ada') }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Penatalaksanaan Kala II</label>
                            <textarea name="kala2_penatalaksanaan" class="form-control" rows="2" placeholder="Bimbingan meneran, posisi, dll">{{ old('kala2_penatalaksanaan') }}</textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Hasil Evaluasi Kala II</label>
                            <textarea name="kala2_hasil_evaluasi" class="form-control" rows="2" placeholder="Kondisi lahir bayi, jam lahir, tangisan">{{ old('kala2_hasil_evaluasi') }}</textarea>
                        </div>
                    </div>

                    {{-- 4. KALA III --}}
                    <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                        <i class="fas fa-heartbeat mr-1"></i> 4. Catatan Kala III (Pengeluaran Plasenta & MAK III)
                    </h6>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Lama Kala III (Menit)</label>
                            <input type="number" name="kala3_lama_menit" class="form-control" min="0" max="300" placeholder="Contoh: 10" value="{{ old('kala3_lama_menit') }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Jumlah Darah (ml)</label>
                            <input type="number" name="kala3_jumlah_perdarahan_ml" class="form-control" min="0" max="5000" placeholder="Contoh: 150" value="{{ old('kala3_jumlah_perdarahan_ml') }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Oksitosin 10U dlm 2 mnt? <span class="text-danger">*</span></label>
                            <select name="kala3_oksitosin_10u_2menit" class="form-control" required>
                                <option value="1" {{ old('kala3_oksitosin_10u_2menit', '1') == '1' ? 'selected' : '' }}>Ya</option>
                                <option value="0" {{ old('kala3_oksitosin_10u_2menit') == '0' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Alasan Tdk Oksitosin</label>
                            <input type="text" name="kala3_alasan_tidak_oksitosin" class="form-control" placeholder="Alasan bila tidak" value="{{ old('kala3_alasan_tidak_oksitosin') }}">
                        </div>

                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Oksitosin Ulang? <span class="text-danger">*</span></label>
                            <select name="kala3_oksitosin_ulang" class="form-control" required>
                                <option value="0" {{ old('kala3_oksitosin_ulang', '0') == '0' ? 'selected' : '' }}>Tidak</option>
                                <option value="1" {{ old('kala3_oksitosin_ulang') == '1' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Alasan Oksitosin Ulang</label>
                            <input type="text" name="kala3_alasan_oksitosin_ulang" class="form-control" placeholder="Alasan bila ya" value="{{ old('kala3_alasan_oksitosin_ulang') }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Penegangan Tali Pusat? <span class="text-danger">*</span></label>
                            <select name="kala3_penegangan_tali_pusat" class="form-control" required>
                                <option value="1" {{ old('kala3_penegangan_tali_pusat', '1') == '1' ? 'selected' : '' }}>Ya</option>
                                <option value="0" {{ old('kala3_penegangan_tali_pusat') == '0' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Alasan Tidak PTP</label>
                            <input type="text" name="kala3_alasan_tidak_ptp" class="form-control" placeholder="Alasan bila tidak" value="{{ old('kala3_alasan_tidak_ptp') }}">
                        </div>

                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Masase Fundus Uteri? <span class="text-danger">*</span></label>
                            <select name="kala3_masase_fundus" class="form-control" required>
                                <option value="1" {{ old('kala3_masase_fundus', '1') == '1' ? 'selected' : '' }}>Ya</option>
                                <option value="0" {{ old('kala3_masase_fundus') == '0' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Alasan Tidak Masase</label>
                            <input type="text" name="kala3_alasan_tidak_masase" class="form-control" placeholder="Alasan bila tidak" value="{{ old('kala3_alasan_tidak_masase') }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Derajat Laserasi <span class="text-danger">*</span></label>
                            <select name="kala3_derajat_laserasi" class="form-control" required>
                                <option value="tidak_ada" {{ old('kala3_derajat_laserasi', 'tidak_ada') == 'tidak_ada' ? 'selected' : '' }}>Tidak Ada</option>
                                <option value="1" {{ old('kala3_derajat_laserasi') == '1' ? 'selected' : '' }}>Derajat 1</option>
                                <option value="2" {{ old('kala3_derajat_laserasi') == '2' ? 'selected' : '' }}>Derajat 2</option>
                                <option value="3" {{ old('kala3_derajat_laserasi') == '3' ? 'selected' : '' }}>Derajat 3</option>
                                <option value="4" {{ old('kala3_derajat_laserasi') == '4' ? 'selected' : '' }}>Derajat 4</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Tindakan Laserasi</label>
                            <input type="text" name="kala3_tindakan_laserasi" class="form-control" placeholder="Penjahitan, rujuk, dll" value="{{ old('kala3_tindakan_laserasi') }}">
                        </div>

                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Atonia Uteri (Tindakan)</label>
                            <input type="text" name="kala3_atonia_uteri" class="form-control" placeholder="Kompresi bimanual, drip, tidak ada" value="{{ old('kala3_atonia_uteri', 'tidak_ada') }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Penatalaksanaan Kala III</label>
                            <textarea name="kala3_penatalaksanaan" class="form-control" rows="2" placeholder="MAK III, dll">{{ old('kala3_penatalaksanaan') }}</textarea>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Hasil Evaluasi Kala III</label>
                            <textarea name="kala3_hasil_evaluasi" class="form-control" rows="2" placeholder="Plasenta lahir lengkap, kontraksi keras">{{ old('kala3_hasil_evaluasi') }}</textarea>
                        </div>
                    </div>

                    {{-- 5. KALA IV --}}
                    <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                        <i class="fas fa-procedures mr-1"></i> 5. Catatan Ringkasan Kala IV (Pengawasan Post-Partum)
                    </h6>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Masalah Kala IV</label>
                            <textarea name="kala4_masalah" class="form-control" rows="2" placeholder="Masalah yang timbul selama 2 jam pasca salin">{{ old('kala4_masalah') }}</textarea>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Penatalaksanaan Kala IV</label>
                            <textarea name="kala4_penatalaksanaan" class="form-control" rows="2" placeholder="Pemantauan TTV, kontraksi, perdarahan berkala">{{ old('kala4_penatalaksanaan') }}</textarea>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Hasil Evaluasi Kala IV</label>
                            <textarea name="kala4_hasil_evaluasi" class="form-control" rows="2" placeholder="Kondisi umum stabil, kontraksi baik">{{ old('kala4_hasil_evaluasi') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Catatan Persalinan
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ========================================================================= --}}
    {{-- MODAL DETAIL & EDIT DATA CATATAN PERSALINAN                               --}}
    {{-- ========================================================================= --}}
    @foreach ($data_catatan as $data)
        @php
            $rawNama = $data->nama_pasien ?? ($data->ibu_pasien ?? 'Pasien #' . $data->persalinan_id);
            $namaClean = preg_match('/^ny\.?\s*/i', $rawNama) ? $rawNama : 'Ny. ' . $rawNama;
        @endphp

        {{-- 1. MODAL DETAIL --}}
        <div class="modal fade text-left" id="modalDetailCatatan{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel{{ $data->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered" role="document">
                <div class="modal-content shadow border-0">
                    <div class="modal-header bg-info text-white py-3">
                        <h5 class="modal-title font-weight-bold" id="modalDetailLabel{{ $data->id }}">
                            <i class="fas fa-file-medical-alt mr-2"></i> Detail Catatan Persalinan: {{ $namaClean }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-4">
                        {{-- Ringkasan Pasien & Faskes --}}
                        <div class="card mb-4 border-left-info shadow-sm">
                            <div class="card-body py-3">
                                <div class="row">
                                    <div class="col-md-3 mb-2 mb-md-0">
                                        <small class="text-muted text-uppercase font-weight-bold d-block">No. Register</small>
                                        <span class="h6 font-weight-bold text-gray-800">{{ $data->nomor_register ?? ($data->no_register ?? '-') }}</span>
                                    </div>
                                    <div class="col-md-3 mb-2 mb-md-0">
                                        <small class="text-muted text-uppercase font-weight-bold d-block">Nama Pasien / Suami</small>
                                        <span class="h6 font-weight-bold text-primary">{{ $namaClean }}</span>
                                        @if($data->nama_suami)
                                            <div class="small text-muted">Suami: {{ $data->nama_suami }}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-3 mb-2 mb-md-0">
                                        <small class="text-muted text-uppercase font-weight-bold d-block">Riwayat Obstetri</small>
                                        <span class="badge badge-info px-2 py-1 font-weight-bold">G{{ $data->gravida ?? 1 }} P{{ $data->para ?? 0 }} A{{ $data->abortus ?? 0 }}</span>
                                        <span class="small text-muted ml-1">{{ $data->usia_kehamilan_minggu ?? '-' }} minggu</span>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted text-uppercase font-weight-bold d-block">Fasilitas & Penolong</small>
                                        <span class="font-weight-bold text-gray-800 d-block">{{ $data->nama_fasilitas ?? '-' }}</span>
                                        <span class="small text-muted">{{ $data->nama_penolong ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Rincian Kala I --}}
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-light py-2">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-clock mr-1"></i> Kala I</h6>
                            </div>
                            <div class="card-body py-3">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <strong>Garis Waspada:</strong>
                                        @if($data->kala1_melewati_garis_waspada)
                                            <span class="badge badge-danger">Melewati Garis Waspada</span>
                                        @else
                                            <span class="badge badge-success">Tidak (Normal)</span>
                                        @endif
                                    </div>
                                    <div class="col-md-8 mb-2">
                                        <strong>Masalah Lain:</strong> {{ $data->kala1_masalah_lain ?? '-' }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Penatalaksanaan:</strong>
                                        <p class="text-muted mb-0">{{ $data->kala1_penatalaksanaan ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Hasil Evaluasi:</strong>
                                        <p class="text-muted mb-0">{{ $data->kala1_hasil_evaluasi ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Rincian Kala II --}}
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-light py-2">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-baby mr-1"></i> Kala II</h6>
                            </div>
                            <div class="card-body py-3">
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <strong>Lama Kala II:</strong> {{ $data->kala2_lama_menit ? $data->kala2_lama_menit . ' Menit' : '-' }}
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <strong>Episiotomi:</strong>
                                        <span class="badge {{ $data->kala2_episiotomi ? 'badge-warning' : 'badge-light border' }}">
                                            {{ $data->kala2_episiotomi ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <strong>Indikasi Episiotomi:</strong> {{ $data->kala2_indikasi_episiotomi ?? '-' }}
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <strong>Pendamping:</strong>
                                        <span class="text-capitalize">{{ str_replace('_', ' ', $data->kala2_pendamping ?? '-') }}</span>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <strong>Gawat Janin:</strong> {{ $data->kala2_gawat_janin ?? '-' }}
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <strong>Distosia Bahu:</strong> {{ $data->kala2_distosia_bahu ?? '-' }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Penatalaksanaan:</strong>
                                        <p class="text-muted mb-0">{{ $data->kala2_penatalaksanaan ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Hasil Evaluasi:</strong>
                                        <p class="text-muted mb-0">{{ $data->kala2_hasil_evaluasi ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Rincian Kala III --}}
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-light py-2">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-heartbeat mr-1"></i> Kala III</h6>
                            </div>
                            <div class="card-body py-3">
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <strong>Lama Kala III:</strong> {{ $data->kala3_lama_menit ? $data->kala3_lama_menit . ' Menit' : '-' }}
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <strong>Perdarahan:</strong> <span class="text-danger font-weight-bold">{{ $data->kala3_jumlah_perdarahan_ml ? $data->kala3_jumlah_perdarahan_ml . ' ml' : '-' }}</span>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <strong>Oksitosin 10U (2 mnt):</strong>
                                        <span class="badge {{ $data->kala3_oksitosin_10u_2menit ? 'badge-success' : 'badge-danger' }}">
                                            {{ $data->kala3_oksitosin_10u_2menit ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <strong>Oksitosin Ulang:</strong>
                                        <span class="badge {{ $data->kala3_oksitosin_ulang ? 'badge-warning' : 'badge-light border' }}">
                                            {{ $data->kala3_oksitosin_ulang ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </div>

                                    <div class="col-md-3 mb-2">
                                        <strong>Penegangan Tali Pusat:</strong>
                                        <span class="badge {{ $data->kala3_penegangan_tali_pusat ? 'badge-success' : 'badge-danger' }}">
                                            {{ $data->kala3_penegangan_tali_pusat ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <strong>Masase Fundus:</strong>
                                        <span class="badge {{ $data->kala3_masase_fundus ? 'badge-success' : 'badge-danger' }}">
                                            {{ $data->kala3_masase_fundus ? 'Ya' : 'Tidak' }}
                                        </span>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <strong>Laserasi:</strong>
                                        <span class="badge badge-info text-capitalize">{{ $data->kala3_derajat_laserasi == 'tidak_ada' ? 'Tidak Ada' : 'Derajat ' . $data->kala3_derajat_laserasi }}</span>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <strong>Tindakan Laserasi:</strong> {{ $data->kala3_tindakan_laserasi ?? '-' }}
                                    </div>

                                    <div class="col-md-3 mb-2">
                                        <strong>Alasan Tdk Oksitosin:</strong> {{ $data->kala3_alasan_tidak_oksitosin ?? '-' }}
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <strong>Alasan Oksitosin Ulang:</strong> {{ $data->kala3_alasan_oksitosin_ulang ?? '-' }}
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <strong>Alasan Tidak PTP:</strong> {{ $data->kala3_alasan_tidak_ptp ?? '-' }}
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <strong>Alasan Tidak Masase:</strong> {{ $data->kala3_alasan_tidak_masase ?? '-' }}
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <strong>Atonia Uteri:</strong> {{ $data->kala3_atonia_uteri ?? '-' }}
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <strong>Penatalaksanaan:</strong>
                                        <p class="text-muted mb-0">{{ $data->kala3_penatalaksanaan ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <strong>Hasil Evaluasi:</strong>
                                        <p class="text-muted mb-0">{{ $data->kala3_hasil_evaluasi ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Rincian Kala IV --}}
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-light py-2">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-procedures mr-1"></i> Kala IV (Ringkasan Pengawasan)</h6>
                            </div>
                            <div class="card-body py-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Masalah Kala IV:</strong>
                                        <p class="text-muted mb-0">{{ $data->kala4_masalah ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Penatalaksanaan:</strong>
                                        <p class="text-muted mb-0">{{ $data->kala4_penatalaksanaan ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Hasil Evaluasi:</strong>
                                        <p class="text-muted mb-0">{{ $data->kala4_hasil_evaluasi ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Metadata --}}
                        <div class="card mb-0 shadow-sm bg-light">
                            <div class="card-body py-2 small text-muted d-flex justify-content-between">
                                <div><i class="fas fa-calendar-plus mr-1"></i> Dibuat: {{ $data->created_at ? \Carbon\Carbon::parse($data->created_at)->format('d/m/Y H:i:s') : '-' }}</div>
                                <div><i class="fas fa-calendar-check mr-1"></i> Diperbarui: {{ $data->updated_at ? \Carbon\Carbon::parse($data->updated_at)->format('d/m/Y H:i:s') : '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light py-2 justify-content-between">
                        <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Tutup
                        </button>
                        <a href="{{ route('catatan-persalinan.show', $data->id) }}" class="btn btn-primary shadow-sm font-weight-bold">
                            <i class="fas fa-external-link-alt mr-1"></i> Buka Halaman Lengkap (Show)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. MODAL EDIT --}}
        <div class="modal fade text-left" id="modalEditCatatan{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel{{ $data->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered" role="document">
                <form action="{{ route('catatan-persalinan.update', $data->id) }}" method="POST" class="modal-content shadow border-0">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-warning text-dark py-3">
                        <h5 class="modal-title font-weight-bold" id="modalEditLabel{{ $data->id }}">
                            <i class="fas fa-edit mr-2"></i> Edit Catatan Persalinan: {{ $namaClean }}
                        </h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-4">
                        {{-- 1. IDENTITAS PASIEN (READONLY) --}}
                        <h6 class="font-weight-bold text-primary mb-3 section-header">
                            <i class="fas fa-user-injured mr-1"></i> 1. Identitas Pasien Persalinan
                        </h6>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Pasien Terdaftar</label>
                                <input type="text" class="form-control bg-light"
                                    value="{{ ($data->nomor_register ?? ($data->no_register ?? 'ID #' . $data->persalinan_id)) . ' - ' . $namaClean }}"
                                    readonly>
                                <input type="hidden" name="persalinan_id" value="{{ $data->persalinan_id }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Fasilitas / Penolong</label>
                                <input type="text" class="form-control bg-light"
                                    value="{{ ($data->nama_fasilitas ?? '-') . ' / ' . ($data->nama_penolong ?? '-') }}"
                                    readonly>
                            </div>
                        </div>

                        {{-- 2. KALA I --}}
                        <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                            <i class="fas fa-clock mr-1"></i> 2. Catatan Kala I (Fase Persalinan)
                        </h6>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Melewati Garis Waspada? <span class="text-danger">*</span></label>
                                <select name="kala1_melewati_garis_waspada" class="form-control" required>
                                    <option value="0" {{ old('kala1_melewati_garis_waspada', (string)$data->kala1_melewati_garis_waspada) === '0' ? 'selected' : '' }}>Tidak (Aman)</option>
                                    <option value="1" {{ old('kala1_melewati_garis_waspada', (string)$data->kala1_melewati_garis_waspada) === '1' ? 'selected' : '' }}>Ya (Melewati Garis Waspada)</option>
                                </select>
                            </div>
                            <div class="col-md-8 form-group">
                                <label class="font-weight-bold text-gray-700">Masalah Lain Kala I</label>
                                <input type="text" name="kala1_masalah_lain" class="form-control" value="{{ old('kala1_masalah_lain', $data->kala1_masalah_lain) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Penatalaksanaan Kala I</label>
                                <textarea name="kala1_penatalaksanaan" class="form-control" rows="2">{{ old('kala1_penatalaksanaan', $data->kala1_penatalaksanaan) }}</textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Hasil Evaluasi Kala I</label>
                                <textarea name="kala1_hasil_evaluasi" class="form-control" rows="2">{{ old('kala1_hasil_evaluasi', $data->kala1_hasil_evaluasi) }}</textarea>
                            </div>
                        </div>

                        {{-- 3. KALA II --}}
                        <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                            <i class="fas fa-baby mr-1"></i> 3. Catatan Kala II (Pengeluaran Janin)
                        </h6>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Lama Kala II (Menit)</label>
                                <input type="number" name="kala2_lama_menit" class="form-control" min="0" max="600" value="{{ old('kala2_lama_menit', $data->kala2_lama_menit) }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Episiotomi <span class="text-danger">*</span></label>
                                <select name="kala2_episiotomi" class="form-control" required>
                                    <option value="0" {{ old('kala2_episiotomi', (string)$data->kala2_episiotomi) === '0' ? 'selected' : '' }}>Tidak</option>
                                    <option value="1" {{ old('kala2_episiotomi', (string)$data->kala2_episiotomi) === '1' ? 'selected' : '' }}>Ya</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Indikasi Episiotomi (Jika Ya)</label>
                                <input type="text" name="kala2_indikasi_episiotomi" class="form-control" value="{{ old('kala2_indikasi_episiotomi', $data->kala2_indikasi_episiotomi) }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Pendamping Persalinan</label>
                                <select name="kala2_pendamping" class="form-control">
                                    <option value="suami" {{ old('kala2_pendamping', $data->kala2_pendamping) == 'suami' ? 'selected' : '' }}>Suami</option>
                                    <option value="keluarga" {{ old('kala2_pendamping', $data->kala2_pendamping) == 'keluarga' ? 'selected' : '' }}>Keluarga</option>
                                    <option value="teman" {{ old('kala2_pendamping', $data->kala2_pendamping) == 'teman' ? 'selected' : '' }}>Teman</option>
                                    <option value="dukun" {{ old('kala2_pendamping', $data->kala2_pendamping) == 'dukun' ? 'selected' : '' }}>Dukun</option>
                                    <option value="tidak_ada" {{ old('kala2_pendamping', $data->kala2_pendamping) == 'tidak_ada' ? 'selected' : '' }}>Tidak Ada</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Gawat Janin (Tindakan)</label>
                                <input type="text" name="kala2_gawat_janin" class="form-control" value="{{ old('kala2_gawat_janin', $data->kala2_gawat_janin) }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Distosia Bahu (Tindakan)</label>
                                <input type="text" name="kala2_distosia_bahu" class="form-control" value="{{ old('kala2_distosia_bahu', $data->kala2_distosia_bahu) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Penatalaksanaan Kala II</label>
                                <textarea name="kala2_penatalaksanaan" class="form-control" rows="2">{{ old('kala2_penatalaksanaan', $data->kala2_penatalaksanaan) }}</textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Hasil Evaluasi Kala II</label>
                                <textarea name="kala2_hasil_evaluasi" class="form-control" rows="2">{{ old('kala2_hasil_evaluasi', $data->kala2_hasil_evaluasi) }}</textarea>
                            </div>
                        </div>

                        {{-- 4. KALA III --}}
                        <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                            <i class="fas fa-heartbeat mr-1"></i> 4. Catatan Kala III (Pengeluaran Plasenta & MAK III)
                        </h6>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Lama Kala III (Menit)</label>
                                <input type="number" name="kala3_lama_menit" class="form-control" min="0" max="300" value="{{ old('kala3_lama_menit', $data->kala3_lama_menit) }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Jumlah Darah (ml)</label>
                                <input type="number" name="kala3_jumlah_perdarahan_ml" class="form-control" min="0" max="5000" value="{{ old('kala3_jumlah_perdarahan_ml', $data->kala3_jumlah_perdarahan_ml) }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Oksitosin 10U dlm 2 mnt? <span class="text-danger">*</span></label>
                                <select name="kala3_oksitosin_10u_2menit" class="form-control" required>
                                    <option value="1" {{ old('kala3_oksitosin_10u_2menit', (string)$data->kala3_oksitosin_10u_2menit) === '1' ? 'selected' : '' }}>Ya</option>
                                    <option value="0" {{ old('kala3_oksitosin_10u_2menit', (string)$data->kala3_oksitosin_10u_2menit) === '0' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Alasan Tdk Oksitosin</label>
                                <input type="text" name="kala3_alasan_tidak_oksitosin" class="form-control" value="{{ old('kala3_alasan_tidak_oksitosin', $data->kala3_alasan_tidak_oksitosin) }}">
                            </div>

                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Oksitosin Ulang? <span class="text-danger">*</span></label>
                                <select name="kala3_oksitosin_ulang" class="form-control" required>
                                    <option value="0" {{ old('kala3_oksitosin_ulang', (string)$data->kala3_oksitosin_ulang) === '0' ? 'selected' : '' }}>Tidak</option>
                                    <option value="1" {{ old('kala3_oksitosin_ulang', (string)$data->kala3_oksitosin_ulang) === '1' ? 'selected' : '' }}>Ya</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Alasan Oksitosin Ulang</label>
                                <input type="text" name="kala3_alasan_oksitosin_ulang" class="form-control" value="{{ old('kala3_alasan_oksitosin_ulang', $data->kala3_alasan_oksitosin_ulang) }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Penegangan Tali Pusat? <span class="text-danger">*</span></label>
                                <select name="kala3_penegangan_tali_pusat" class="form-control" required>
                                    <option value="1" {{ old('kala3_penegangan_tali_pusat', (string)$data->kala3_penegangan_tali_pusat) === '1' ? 'selected' : '' }}>Ya</option>
                                    <option value="0" {{ old('kala3_penegangan_tali_pusat', (string)$data->kala3_penegangan_tali_pusat) === '0' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Alasan Tidak PTP</label>
                                <input type="text" name="kala3_alasan_tidak_ptp" class="form-control" value="{{ old('kala3_alasan_tidak_ptp', $data->kala3_alasan_tidak_ptp) }}">
                            </div>

                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Masase Fundus Uteri? <span class="text-danger">*</span></label>
                                <select name="kala3_masase_fundus" class="form-control" required>
                                    <option value="1" {{ old('kala3_masase_fundus', (string)$data->kala3_masase_fundus) === '1' ? 'selected' : '' }}>Ya</option>
                                    <option value="0" {{ old('kala3_masase_fundus', (string)$data->kala3_masase_fundus) === '0' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Alasan Tidak Masase</label>
                                <input type="text" name="kala3_alasan_tidak_masase" class="form-control" value="{{ old('kala3_alasan_tidak_masase', $data->kala3_alasan_tidak_masase) }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Derajat Laserasi <span class="text-danger">*</span></label>
                                <select name="kala3_derajat_laserasi" class="form-control" required>
                                    <option value="tidak_ada" {{ old('kala3_derajat_laserasi', $data->kala3_derajat_laserasi) == 'tidak_ada' ? 'selected' : '' }}>Tidak Ada</option>
                                    <option value="1" {{ old('kala3_derajat_laserasi', $data->kala3_derajat_laserasi) == '1' ? 'selected' : '' }}>Derajat 1</option>
                                    <option value="2" {{ old('kala3_derajat_laserasi', $data->kala3_derajat_laserasi) == '2' ? 'selected' : '' }}>Derajat 2</option>
                                    <option value="3" {{ old('kala3_derajat_laserasi', $data->kala3_derajat_laserasi) == '3' ? 'selected' : '' }}>Derajat 3</option>
                                    <option value="4" {{ old('kala3_derajat_laserasi', $data->kala3_derajat_laserasi) == '4' ? 'selected' : '' }}>Derajat 4</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Tindakan Laserasi</label>
                                <input type="text" name="kala3_tindakan_laserasi" class="form-control" value="{{ old('kala3_tindakan_laserasi', $data->kala3_tindakan_laserasi) }}">
                            </div>

                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Atonia Uteri (Tindakan)</label>
                                <input type="text" name="kala3_atonia_uteri" class="form-control" value="{{ old('kala3_atonia_uteri', $data->kala3_atonia_uteri) }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Penatalaksanaan Kala III</label>
                                <textarea name="kala3_penatalaksanaan" class="form-control" rows="2">{{ old('kala3_penatalaksanaan', $data->kala3_penatalaksanaan) }}</textarea>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Hasil Evaluasi Kala III</label>
                                <textarea name="kala3_hasil_evaluasi" class="form-control" rows="2">{{ old('kala3_hasil_evaluasi', $data->kala3_hasil_evaluasi) }}</textarea>
                            </div>
                        </div>

                        {{-- 5. KALA IV --}}
                        <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                            <i class="fas fa-procedures mr-1"></i> 5. Catatan Ringkasan Kala IV (Pengawasan Post-Partum)
                        </h6>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Masalah Kala IV</label>
                                <textarea name="kala4_masalah" class="form-control" rows="2">{{ old('kala4_masalah', $data->kala4_masalah) }}</textarea>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Penatalaksanaan Kala IV</label>
                                <textarea name="kala4_penatalaksanaan" class="form-control" rows="2">{{ old('kala4_penatalaksanaan', $data->kala4_penatalaksanaan) }}</textarea>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Hasil Evaluasi Kala IV</label>
                                <textarea name="kala4_hasil_evaluasi" class="form-control" rows="2">{{ old('kala4_hasil_evaluasi', $data->kala4_hasil_evaluasi) }}</textarea>
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

    @endforeach

@endsection

@push('scripts')
    <!-- Page level DataTables plugins & custom script -->
    <script src="{{ asset('template/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                dom:
                    "<'row align-items-center mb-3'" +
                        "<'col-12 col-md-6 d-flex align-items-center mb-2 mb-md-0'l>" +
                        "<'col-12 col-md-6 d-flex justify-content-md-end justify-content-start'f>" +
                    ">" +
                    "<'row'" +
                        "<'col-12'" +
                            "tr" +
                        ">" +
                    ">" +
                    "<'row align-items-center mt-3'" +
                        "<'col-12 col-md-5 mb-2 mb-md-0'i>" +
                        "<'col-12 col-md-7 d-flex justify-content-md-end justify-content-start'p>" +
                    ">",
                language: {
                    search: "",
                    searchPlaceholder: "Cari catatan...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 - 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    paginate: {
                        first: '<i class="fas fa-angle-double-left"></i>',
                        last: '<i class="fas fa-angle-double-right"></i>',
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    },
                    emptyTable: "Belum ada data catatan persalinan",
                    zeroRecords: "Tidak ditemukan data yang sesuai"
                },
                columnDefs: [
                    { orderable: false, targets: [8] }
                ],
                pageLength: 10,
                order: [
                    [0, 'asc']
                ]
            });
        });
    </script>
@endpush
