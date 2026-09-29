@extends('layouts.master')

@section('title', 'Edukasi & Konseling KIE')

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
            font-weight: 700;
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

        /* Detail Modal Styling */
        .detail-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 0.8rem;
            text-transform: uppercase;
            margin-bottom: 0.2rem;
        }
        .detail-value {
            font-size: 0.9rem;
            color: #1f2937;
            background-color: #f9fafb;
            padding: 0.4rem 0.65rem;
            border-radius: 0.35rem;
            border: 1px solid #e5e7eb;
            min-height: 34px;
            display: flex;
            align-items: center;
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

    @if(session('error'))
        <x-alert type="danger" icon="fas fa-exclamation-circle" dismissible>
            {{ session('error') }}
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
        title="Edukasi & Konseling KIE"
        subtitle="Dokumentasi Komunikasi, Informasi, dan Edukasi (KIE) masa nifas (ASI Eksklusif, KB Pascasalin, Tali Pusat, Gizi, Imunisasi)"
        icon="fas fa-comments"
    >
        <button type="button" class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahEdukasi">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Edukasi KIE
        </button>
    </x-page-header>

    @php
        $totalEdukasi = count($data_edukasi);
        $totalAsi = collect($data_edukasi)->where('materi_edukasi', 'asi')->count();
        $totalKb = collect($data_edukasi)->where('materi_edukasi', 'kb')->count();
        $totalIbu = collect($data_edukasi)->pluck('persalinan_id')->unique()->count();

        // Helper label materi
        $materiMap = [
            'semua_nifas' => ['label' => 'Semua Asuhan Nifas', 'color' => 'primary', 'icon' => 'fa-female'],
            'breast_care' => ['label' => 'Perawatan Payudara', 'color' => 'success', 'icon' => 'fa-heart'],
            'asi' => ['label' => 'Manajemen ASI Eksklusif', 'color' => 'info', 'icon' => 'fa-baby'],
            'perawatan_tali_pusat' => ['label' => 'Perawatan Tali Pusat', 'color' => 'warning text-dark', 'icon' => 'fa-band-aid'],
            'kb' => ['label' => 'KB Pascasalin', 'color' => 'danger', 'icon' => 'fa-user-friends'],
            'gizi' => ['label' => 'Nutrisi & Gizi Ibu', 'color' => 'secondary', 'icon' => 'fa-utensils'],
            'imunisasi' => ['label' => 'Imunisasi Bayi', 'color' => 'dark', 'icon' => 'fa-syringe'],
        ];
    @endphp

    <!-- Ringkasan Statistik -->
    <div class="row">
        <x-stat-card
            title="Total Sesi Edukasi"
            :value="$totalEdukasi"
            icon="fas fa-bullhorn"
            color="primary"
            subtitle="Sesi KIE terdokumentasi"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="Edukasi ASI Eksklusif"
            :value="$totalAsi"
            icon="fas fa-baby"
            color="info"
            subtitle="{{ $totalEdukasi > 0 ? round(($totalAsi / $totalEdukasi) * 100) : 0 }}% materi laktasi/ASI"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="Konseling KB Pascasalin"
            :value="$totalKb"
            icon="fas fa-user-friends"
            color="danger"
            subtitle="{{ $totalEdukasi > 0 ? round(($totalKb / $totalEdukasi) * 100) : 0 }}% perencanaan kontrasepsi"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="Ibu Terkonseling"
            :value="$totalIbu . ' Pasien'"
            icon="fas fa-female"
            color="success"
            subtitle="Ibu menerima bimbingan KIE"
            col="col-xl-3 col-md-6 mb-4"
        />
    </div>

    <!-- Tabel Data Edukasi KIE -->
    <x-card title="Daftar Sesi Komunikasi, Informasi & Edukasi (KIE)" icon="fas fa-list-alt">
        <x-slot:actions>
            <x-badge color="primary" pill>
                Total: {{ $totalEdukasi }} Sesi
            </x-badge>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" id="dataTable" cellspacing="0">
                <thead>
                    <tr class="text-center text-uppercase">
                        <th style="width: 40px;">No</th>
                        <th style="min-width: 170px;">Ibu Pasien / No. Reg</th>
                        <th style="min-width: 160px;">Materi Edukasi</th>
                        <th style="width: 120px;">Tanggal KIE</th>
                        <th style="min-width: 140px;">Petugas Pelaksana</th>
                        <th style="min-width: 200px;">Keterangan / Catatan</th>
                        <th style="width: 100px;">Dicatat</th>
                        <th style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data_edukasi as $data)
                        @php
                            $rawNama = $data->nama_pasien ?? 'Pasien #' . $data->persalinan_id;
                            $namaClean = preg_match('/^ny\.?\s*/i', $rawNama) ? $rawNama : 'Ny. ' . $rawNama;
                            $materiInfo = $materiMap[$data->materi_edukasi] ?? ['label' => ucfirst(str_replace('_', ' ', $data->materi_edukasi)), 'color' => 'primary', 'icon' => 'fa-check'];
                        @endphp
                        <tr>
                            {{-- 1. No --}}
                            <td class="text-center font-weight-bold text-gray-600 text-nowrap">
                                {{ $loop->iteration }}
                            </td>

                            {{-- 2. Ibu Pasien / No. Register --}}
                            <td>
                                <div class="font-weight-bold text-primary mb-1">
                                    {{ $namaClean }}
                                </div>
                                <div class="small">
                                    <span class="badge badge-light border text-muted px-2 py-1 font-weight-normal">
                                        <i class="fas fa-id-card text-muted mr-1"></i>{{ $data->nomor_register ?? '-' }}
                                    </span>
                                    @if(!empty($data->usia_ibu))
                                        <span class="badge badge-light border text-muted px-2 py-1 font-weight-normal ml-1">
                                            {{ $data->usia_ibu }} th
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- 3. Materi Edukasi --}}
                            <td>
                                <span class="badge badge-{{ $materiInfo['color'] }} px-2 py-1 font-weight-bold">
                                    <i class="fas {{ $materiInfo['icon'] }} mr-1"></i>{{ $materiInfo['label'] }}
                                </span>
                            </td>

                            {{-- 4. Tanggal KIE --}}
                            <td class="text-center text-nowrap">
                                <div class="font-weight-bold text-gray-800">
                                    <i class="far fa-calendar-alt text-primary mr-1"></i>{{ \Carbon\Carbon::parse($data->tanggal_edukasi)->format('d/m/Y') }}
                                </div>
                            </td>

                            {{-- 5. Petugas Pelaksana --}}
                            <td>
                                <div class="font-weight-bold text-gray-800">
                                    <i class="fas fa-user-nurse text-success mr-1"></i>{{ $data->pelaksana }}
                                </div>
                            </td>

                            {{-- 6. Keterangan / Catatan --}}
                            <td>
                                <div class="small text-gray-700 text-truncate" style="max-width: 250px;" title="{{ $data->keterangan }}">
                                    {{ $data->keterangan ?: '-' }}
                                </div>
                            </td>

                            {{-- 7. Dicatat --}}
                            <td class="text-center text-nowrap small text-gray-700">
                                <div class="font-weight-bold">{{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y') }}</div>
                                <div class="text-muted">{{ \Carbon\Carbon::parse($data->created_at)->format('H:i') }}</div>
                            </td>

                            {{-- 8. Aksi --}}
                            <td class="text-center text-nowrap">
                                <div class="btn-group btn-group-sm shadow-sm" role="group">
                                    {{-- Modal Detail Cepat --}}
                                    <button type="button" class="btn btn-info" data-toggle="modal"
                                        data-target="#modalDetailEdukasi{{ $data->id }}" title="Lihat Rincian">
                                        <i class="fas fa-eye fa-sm"></i>
                                    </button>

                                    {{-- Modal Edit --}}
                                    <button type="button" class="btn btn-warning text-dark" data-toggle="modal"
                                        data-target="#modalEditEdukasi{{ $data->id }}" title="Edit Data">
                                        <i class="fas fa-edit fa-sm"></i>
                                    </button>

                                    {{-- Hapus --}}
                                    <button type="button" class="btn btn-danger" title="Hapus Edukasi"
                                        onclick="if(confirm('Apakah Anda yakin ingin menghapus data edukasi KIE ini?')) { document.getElementById('form-delete-{{ $data->id }}').submit(); }">
                                        <i class="fas fa-trash fa-sm"></i>
                                    </button>
                                </div>

                                <form id="form-delete-{{ $data->id }}" action="{{ route('edukasi-kie.destroy', $data->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-comments fa-3x mb-2 text-gray-300 d-block"></i>
                                Belum ada catatan edukasi KIE. Klik tombol <strong>Tambah Edukasi KIE</strong> untuk mendokumentasikan bimbingan nifas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>


    {{-- ========================================================================= --}}
    {{-- MODAL TAMBAH DATA EDUKASI KIE                                             --}}
    {{-- ========================================================================= --}}
    <div class="modal fade text-left" id="modalTambahEdukasi" tabindex="-1" role="dialog" aria-labelledby="modalTambahEdukasiLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
            <form action="{{ route('edukasi-kie.store') }}" method="POST" class="modal-content shadow border-0">
                @csrf
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title font-weight-bold" id="modalTambahEdukasiLabel">
                        <i class="fas fa-comments mr-2"></i> Tambah Dokumentasi Edukasi & Konseling KIE
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    {{-- 1. IDENTITAS IBU & WAKTU --}}
                    <h6 class="font-weight-bold text-primary mb-3 section-header">
                        <i class="fas fa-user-injured mr-1"></i> 1. Identitas Pasien & Waktu Edukasi
                    </h6>
                    <div class="row">
                        <div class="col-md-7 form-group">
                            <label class="font-weight-bold text-gray-700">Pilih Pasien Persalinan <span class="text-danger">*</span></label>
                            @if($data_persalinan->isEmpty())
                                <div class="alert alert-warning py-2 mb-0 small">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Belum ada data persalinan aktif.
                                </div>
                            @else
                                <select name="persalinan_id" class="form-control" required>
                                    <option value="">-- Pilih Persalinan Pasien --</option>
                                    @foreach($data_persalinan as $dp)
                                        <option value="{{ $dp->id }}" {{ old('persalinan_id') == $dp->id ? 'selected' : '' }}>
                                            {{ $dp->no_register }} - {{ $dp->ibu_pasien }}
                                            (Masuk: {{ $dp->waktu_masuk ? \Carbon\Carbon::parse($dp->waktu_masuk)->format('d/m/Y H:i') : '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-md-5 form-group">
                            <label class="font-weight-bold text-gray-700">Tanggal Pelaksanaan KIE <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_edukasi" class="form-control" value="{{ old('tanggal_edukasi', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    {{-- 2. MATERI & PELAKSANA --}}
                    <h6 class="font-weight-bold text-primary mt-3 mb-3 section-header">
                        <i class="fas fa-book-medical mr-1"></i> 2. Materi & Petugas Pelaksana
                    </h6>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Materi Edukasi Nifas <span class="text-danger">*</span></label>
                            <select name="materi_edukasi" class="form-control" required>
                                <option value="semua_nifas" {{ old('materi_edukasi') == 'semua_nifas' ? 'selected' : '' }}>Semua Asuhan Nifas</option>
                                <option value="breast_care" {{ old('materi_edukasi') == 'breast_care' ? 'selected' : '' }}>Perawatan Payudara (Breast Care)</option>
                                <option value="asi" {{ old('materi_edukasi', 'asi') == 'asi' ? 'selected' : '' }}>Manajemen ASI Eksklusif & Laktasi</option>
                                <option value="perawatan_tali_pusat" {{ old('materi_edukasi') == 'perawatan_tali_pusat' ? 'selected' : '' }}>Perawatan Tali Pusat Bayi</option>
                                <option value="kb" {{ old('materi_edukasi') == 'kb' ? 'selected' : '' }}>KB Pascasalin (Keluarga Berencana)</option>
                                <option value="gizi" {{ old('materi_edukasi') == 'gizi' ? 'selected' : '' }}>Nutrisi & Gizi Seimbang Ibu Nifas</option>
                                <option value="imunisasi" {{ old('materi_edukasi') == 'imunisasi' ? 'selected' : '' }}>Jadwal & Pentingnya Imunisasi Bayi</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Bidan / Petugas Pelaksana <span class="text-danger">*</span></label>
                            <input type="text" name="pelaksana" class="form-control" placeholder="Nama Bidan / Konselor KIE" value="{{ old('pelaksana', 'Bidan Jaga') }}" required>
                        </div>
                        <div class="col-12 form-group">
                            <label class="font-weight-bold text-gray-700">Catatan Konseling & Respon Pasien / Keluarga</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Pasien memahami teknik menyusui yang benar, perlekatan bayi sudah tepat, puting tidak lecet. Keluarga mendukung pemberian ASI eksklusif.">{{ old('keterangan') }}</textarea>
                            <small class="form-text text-muted">Tuliskan pemahaman pasien, respon keluarga, atau rencana tindak lanjut.</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary shadow-sm font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan Edukasi KIE
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ========================================================================= --}}
    {{-- MODAL DETAIL & MODAL EDIT PER ITEM                                        --}}
    {{-- ========================================================================= --}}
    @foreach ($data_edukasi as $data)
        @php
            $rawNama = $data->nama_pasien ?? 'Pasien #' . $data->persalinan_id;
            $namaClean = preg_match('/^ny\.?\s*/i', $rawNama) ? $rawNama : 'Ny. ' . $rawNama;
            $materiInfo = $materiMap[$data->materi_edukasi] ?? ['label' => ucfirst(str_replace('_', ' ', $data->materi_edukasi)), 'color' => 'primary', 'icon' => 'fa-check'];
        @endphp

        {{-- 1. MODAL DETAIL --}}
        <div class="modal fade text-left" id="modalDetailEdukasi{{ $data->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                <div class="modal-content shadow border-0">
                    <div class="modal-header bg-info text-white py-3">
                        <h5 class="modal-title font-weight-bold">
                            <i class="fas fa-comments mr-2"></i> Rincian Sesi Edukasi KIE #{{ $data->id }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-light border mb-3">
                            <div class="detail-label">Ibu Pasien</div>
                            <div class="font-weight-bold text-primary">{{ $namaClean }} ({{ $data->nomor_register ?? '-' }})</div>
                            <div class="small text-muted">Fasilitas: {{ $data->nama_fasilitas ?? '-' }}</div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="detail-label">Materi Edukasi</div>
                                <div class="detail-value">
                                    <span class="badge badge-{{ $materiInfo['color'] }} px-2 py-1 font-weight-bold">
                                        <i class="fas {{ $materiInfo['icon'] }} mr-1"></i>{{ $materiInfo['label'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="detail-label">Tanggal Pelaksanaan</div>
                                <div class="detail-value font-weight-bold text-primary">
                                    <i class="far fa-calendar-alt mr-1"></i>{{ \Carbon\Carbon::parse($data->tanggal_edukasi)->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="detail-label">Bidan / Pelaksana</div>
                                <div class="detail-value font-weight-bold">
                                    <i class="fas fa-user-nurse mr-1 text-success"></i>{{ $data->pelaksana }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="detail-label">Catatan Konseling / Respon Pasien</div>
                                <div class="detail-value" style="white-space: pre-line; align-items: flex-start; min-height: 70px;">
                                    {{ $data->keterangan ?: 'Tidak ada catatan khusus.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-sm btn-secondary shadow-sm" data-dismiss="modal">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. MODAL EDIT --}}
        <div class="modal fade text-left" id="modalEditEdukasi{{ $data->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
                <form action="{{ route('edukasi-kie.update', $data->id) }}" method="POST" class="modal-content shadow border-0">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-warning text-dark py-3">
                        <h5 class="modal-title font-weight-bold">
                            <i class="fas fa-edit mr-2"></i> Edit Edukasi KIE #{{ $data->id }} ({{ $namaClean }})
                        </h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-4">
                        <h6 class="font-weight-bold text-primary mb-3 section-header">
                            <i class="fas fa-user-injured mr-1"></i> 1. Identitas Pasien & Waktu Edukasi
                        </h6>
                        <div class="row">
                            <div class="col-md-7 form-group">
                                <label class="font-weight-bold text-gray-700">Pilih Pasien Persalinan <span class="text-danger">*</span></label>
                                <select name="persalinan_id" class="form-control" required>
                                    @foreach($data_persalinan as $dp)
                                        <option value="{{ $dp->id }}" {{ old('persalinan_id', $data->persalinan_id) == $dp->id ? 'selected' : '' }}>
                                            {{ $dp->no_register }} - {{ $dp->ibu_pasien }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5 form-group">
                                <label class="font-weight-bold text-gray-700">Tanggal Pelaksanaan KIE <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_edukasi" class="form-control" value="{{ old('tanggal_edukasi', $data->tanggal_edukasi) }}" required>
                            </div>
                        </div>

                        <h6 class="font-weight-bold text-primary mt-3 mb-3 section-header">
                            <i class="fas fa-book-medical mr-1"></i> 2. Materi & Petugas Pelaksana
                        </h6>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Materi Edukasi Nifas <span class="text-danger">*</span></label>
                                <select name="materi_edukasi" class="form-control" required>
                                    <option value="semua_nifas" {{ old('materi_edukasi', $data->materi_edukasi) == 'semua_nifas' ? 'selected' : '' }}>Semua Asuhan Nifas</option>
                                    <option value="breast_care" {{ old('materi_edukasi', $data->materi_edukasi) == 'breast_care' ? 'selected' : '' }}>Perawatan Payudara (Breast Care)</option>
                                    <option value="asi" {{ old('materi_edukasi', $data->materi_edukasi) == 'asi' ? 'selected' : '' }}>Manajemen ASI Eksklusif & Laktasi</option>
                                    <option value="perawatan_tali_pusat" {{ old('materi_edukasi', $data->materi_edukasi) == 'perawatan_tali_pusat' ? 'selected' : '' }}>Perawatan Tali Pusat Bayi</option>
                                    <option value="kb" {{ old('materi_edukasi', $data->materi_edukasi) == 'kb' ? 'selected' : '' }}>KB Pascasalin (Keluarga Berencana)</option>
                                    <option value="gizi" {{ old('materi_edukasi', $data->materi_edukasi) == 'gizi' ? 'selected' : '' }}>Nutrisi & Gizi Seimbang Ibu Nifas</option>
                                    <option value="imunisasi" {{ old('materi_edukasi', $data->materi_edukasi) == 'imunisasi' ? 'selected' : '' }}>Jadwal & Pentingnya Imunisasi Bayi</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Bidan / Petugas Pelaksana <span class="text-danger">*</span></label>
                                <input type="text" name="pelaksana" class="form-control" value="{{ old('pelaksana', $data->pelaksana) }}" required>
                            </div>
                            <div class="col-12 form-group">
                                <label class="font-weight-bold text-gray-700">Catatan Konseling & Respon Pasien / Keluarga</label>
                                <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $data->keterangan) }}</textarea>
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
                    searchPlaceholder: "Cari edukasi KIE...",
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
                    emptyTable: "Belum ada data edukasi KIE",
                    zeroRecords: "Tidak ditemukan data yang sesuai"
                },
                columnDefs: [
                    { orderable: false, targets: [7] }
                ],
                pageLength: 10,
                order: [
                    [0, 'asc']
                ]
            });
        });
    </script>
@endpush
