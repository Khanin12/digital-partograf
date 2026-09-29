@extends('layouts.master')

@section('title', 'Rekam Medis Terpadu - ' . $pasien->nama_ibu . ' (' . ($pasien->nomor_register ?? 'REG-' . $pasien->id) . ')')

@push('styles')
<style>
    .nav-tabs-clinical {
        border-bottom: 2px solid #e2e8f0;
        gap: 6px;
    }
    .nav-tabs-clinical .nav-item {
        margin-bottom: -2px;
    }
    .nav-tabs-clinical .nav-link {
        border: 2px solid transparent;
        border-radius: 8px 8px 0 0;
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        padding: 10px 16px;
        transition: all 0.2s ease-in-out;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
        min-width: 170px;
    }
    .nav-tabs-clinical .nav-link:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .nav-tabs-clinical .nav-link.active {
        background: #ffffff;
        color: #059669 !important;
        border-color: #059669 #059669 #ffffff !important;
        border-top-width: 3px;
        box-shadow: 0 -2px 6px rgba(0,0,0,0.03);
    }
    .clinical-card {
        border-radius: 0 0 10px 10px;
        border-top: none;
    }
    .section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        border-left: 4px solid #059669;
        padding-left: 8px;
        margin-bottom: 1rem;
    }
    .mode-switch-pill {
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
    }
</style>
@endpush

@section('content')

    <!-- Flash Messages / Alerts -->
    @if(session('success'))
        <x-alert type="success" icon="fas fa-check-circle" dismissible>
            {{ session('success') }}
        </x-alert>
    @endif

    @if(session('error'))
        <x-alert type="danger" icon="fas fa-exclamation-triangle" dismissible>
            {{ session('error') }}
        </x-alert>
    @endif

    @if(isset($errors) && $errors->any())
        <x-alert type="danger" icon="fas fa-exclamation-circle" dismissible title="Terdapat Kesalahan Pengisian Formulir:">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <!-- Header Ringkasan Pasien & Tombol Navigasi -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); border-left: 5px solid #059669 !important;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center mb-1">
                        <span class="badge badge-success px-2 py-1 mr-2 font-weight-bold" style="font-size: 11px;">
                            <i class="fas fa-id-card mr-1"></i> {{ $pasien->nomor_register ?? 'REG-' . $pasien->id }}
                        </span>
                        <h4 class="font-weight-bold text-gray-900 mb-0">
                            {{ $pasien->nama_ibu }}
                        </h4>
                        <span class="badge badge-light border text-muted ml-2">
                            {{ $pasien->usia_ibu }} Tahun
                        </span>
                    </div>
                    <div class="small text-gray-600 d-flex flex-wrap align-items-center mt-2" style="gap: 12px;">
                        <span><i class="fas fa-user mr-1 text-muted"></i> <strong>Suami:</strong> {{ $pasien->nama_suami ?? '-' }}</span>
                        <span><i class="fas fa-phone mr-1 text-muted"></i> <strong>Telepon:</strong> {{ $pasien->nomor_telepon ?? '-' }}</span>
                        <span><i class="fas fa-map-marker-alt mr-1 text-muted"></i> <strong>Alamat:</strong> {{ $pasien->alamat ?? '-' }}</span>
                        @if($persalinan)
                            <span class="badge badge-info px-2 py-1 font-weight-bold">
                                Obstetri: G{{ $persalinan->gravida }} P{{ $persalinan->para }} A{{ $persalinan->abortus }} ({{ $persalinan->usia_kehamilan_minggu }} mgg)
                            </span>
                            <span class="badge badge-{{ $persalinan->status_persalinan === 'proses' ? 'warning' : 'success' }} px-2 py-1">
                                Status: {{ ucfirst($persalinan->status_persalinan) }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <a href="{{ route('pasien.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Data Pasien
                    </a>
                    @if($persalinan)
                        <a href="{{ route('partograf.cetak', $persalinan->id) }}" target="_blank" class="btn btn-success btn-sm shadow-sm font-weight-bold">
                            <i class="fas fa-print mr-1"></i> Cetak Partograf (PDF)
                        </a>
                    @else
                        <a href="{{ route('partograf.cetak_by_pasien', $pasien->id) }}" target="_blank" class="btn btn-success btn-sm shadow-sm font-weight-bold">
                            <i class="fas fa-print mr-1"></i> Cetak Partograf
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Nav-Tabs Opsi A: Horizontal Stage Tabs -->
    <ul class="nav nav-tabs nav-tabs-clinical" id="clinicalFormTabs" role="tablist">
        <!-- Tab 1: Admisi Persalinan -->
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $active_tab === 'persalinan' ? 'active' : '' }}" id="tab-persalinan-btn" data-toggle="tab" href="#tab-persalinan" role="tab" aria-controls="tab-persalinan" aria-selected="{{ $active_tab === 'persalinan' ? 'true' : 'false' }}">
                <div class="d-flex align-items-center w-100 justify-content-between">
                    <span><i class="{{ $statuses['persalinan']['icon_modul'] }} mr-1"></i> 1. Admisi</span>
                    <span class="badge badge-{{ $statuses['persalinan']['badge'] }} px-1 py-0 ml-2" style="font-size: 9px;">
                        {{ $statuses['persalinan']['state'] === 'selesai' ? 'Selesai' : ($statuses['persalinan']['state'] === 'proses' ? 'Proses' : 'Belum') }}
                    </span>
                </div>
                <small class="text-muted" style="font-size: 10px;">
                    @if($statuses['persalinan']['time'])
                        {{ \Carbon\Carbon::parse($statuses['persalinan']['time'])->format('d/m H:i') }}
                    @else
                        Masuk & Riwayat
                    @endif
                </small>
            </a>
        </li>

        <!-- Tab 2: Observasi Partograf -->
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $active_tab === 'observasi' ? 'active' : '' }}" id="tab-observasi-btn" data-toggle="tab" href="#tab-observasi" role="tab" aria-controls="tab-observasi" aria-selected="{{ $active_tab === 'observasi' ? 'true' : 'false' }}">
                <div class="d-flex align-items-center w-100 justify-content-between">
                    <span><i class="{{ $statuses['observasi']['icon_modul'] }} mr-1"></i> 2. Observasi</span>
                    <span class="badge badge-{{ $statuses['observasi']['badge'] }} px-1 py-0 ml-2" style="font-size: 9px;">
                        {{ $statuses['observasi']['state'] === 'selesai' ? 'Selesai' : ($statuses['observasi']['state'] === 'proses' ? $data_observasi->count().' Titik' : 'Belum') }}
                    </span>
                </div>
                <small class="text-muted" style="font-size: 10px;">
                    @if($statuses['observasi']['time'])
                        {{ \Carbon\Carbon::parse($statuses['observasi']['time'])->format('d/m H:i') }}
                    @else
                        Matriks Partograf
                    @endif
                </small>
            </a>
        </li>

        <!-- Tab 3: Catatan Persalinan -->
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $active_tab === 'catatan' ? 'active' : '' }}" id="tab-catatan-btn" data-toggle="tab" href="#tab-catatan" role="tab" aria-controls="tab-catatan" aria-selected="{{ $active_tab === 'catatan' ? 'true' : 'false' }}">
                <div class="d-flex align-items-center w-100 justify-content-between">
                    <span><i class="{{ $statuses['catatan']['icon_modul'] }} mr-1"></i> 3. Catatan</span>
                    <span class="badge badge-{{ $statuses['catatan']['badge'] }} px-1 py-0 ml-2" style="font-size: 9px;">
                        {{ $statuses['catatan']['state'] === 'selesai' ? 'Selesai' : 'Belum' }}
                    </span>
                </div>
                <small class="text-muted" style="font-size: 10px;">
                    @if($statuses['catatan']['time'])
                        {{ \Carbon\Carbon::parse($statuses['catatan']['time'])->format('d/m H:i') }}
                    @else
                        Kala I s/d IV
                    @endif
                </small>
            </a>
        </li>

        <!-- Tab 4: Bayi Baru Lahir -->
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $active_tab === 'bayi' ? 'active' : '' }}" id="tab-bayi-btn" data-toggle="tab" href="#tab-bayi" role="tab" aria-controls="tab-bayi" aria-selected="{{ $active_tab === 'bayi' ? 'true' : 'false' }}">
                <div class="d-flex align-items-center w-100 justify-content-between">
                    <span><i class="{{ $statuses['bayi']['icon_modul'] }} mr-1"></i> 4. Bayi Baru Lahir</span>
                    <span class="badge badge-{{ $statuses['bayi']['badge'] }} px-1 py-0 ml-2" style="font-size: 9px;">
                        {{ $statuses['bayi']['state'] === 'selesai' ? $data_bayi->count().' Bayi' : 'Belum' }}
                    </span>
                </div>
                <small class="text-muted" style="font-size: 10px;">
                    @if($statuses['bayi']['time'])
                        {{ \Carbon\Carbon::parse($statuses['bayi']['time'])->format('d/m H:i') }}
                    @else
                        APGAR & Resusitasi
                    @endif
                </small>
            </a>
        </li>

        <!-- Tab 5: Pemantauan Kala IV -->
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $active_tab === 'kala4' ? 'active' : '' }}" id="tab-kala4-btn" data-toggle="tab" href="#tab-kala4" role="tab" aria-controls="tab-kala4" aria-selected="{{ $active_tab === 'kala4' ? 'true' : 'false' }}">
                <div class="d-flex align-items-center w-100 justify-content-between">
                    <span><i class="{{ $statuses['kala4']['icon_modul'] }} mr-1"></i> 5. Kala IV</span>
                    <span class="badge badge-{{ $statuses['kala4']['badge'] }} px-1 py-0 ml-2" style="font-size: 9px;">
                        {{ $statuses['kala4']['state'] === 'selesai' ? 'Lengkap' : ($statuses['kala4']['state'] === 'proses' ? $data_kala4->count().'x Cek' : 'Belum') }}
                    </span>
                </div>
                <small class="text-muted" style="font-size: 10px;">
                    @if($statuses['kala4']['time'])
                        {{ \Carbon\Carbon::parse($statuses['kala4']['time'])->format('d/m H:i') }}
                    @else
                        TTV & Uterus 2 Jam
                    @endif
                </small>
            </a>
        </li>

        <!-- Tab 6: Edukasi KIE -->
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $active_tab === 'edukasi' ? 'active' : '' }}" id="tab-edukasi-btn" data-toggle="tab" href="#tab-edukasi" role="tab" aria-controls="tab-edukasi" aria-selected="{{ $active_tab === 'edukasi' ? 'true' : 'false' }}">
                <div class="d-flex align-items-center w-100 justify-content-between">
                    <span><i class="{{ $statuses['edukasi']['icon_modul'] }} mr-1"></i> 6. Edukasi KIE</span>
                    <span class="badge badge-{{ $statuses['edukasi']['badge'] }} px-1 py-0 ml-2" style="font-size: 9px;">
                        {{ $statuses['edukasi']['state'] === 'selesai' ? $data_edukasi->count().' Sesi' : 'Belum' }}
                    </span>
                </div>
                <small class="text-muted" style="font-size: 10px;">
                    @if($statuses['edukasi']['time'])
                        {{ \Carbon\Carbon::parse($statuses['edukasi']['time'])->format('d/m H:i') }}
                    @else
                        Bimbingan Nifas
                    @endif
                </small>
            </a>
        </li>
    </ul>

    <!-- Tab Contents Box -->
    <div class="tab-content bg-white shadow-sm p-4 border border-top-0 clinical-card mb-4" id="clinicalFormTabsContent">

        {{-- ========================================================================= --}}
        {{-- TAB 1: ADMISI PERSALINAN                                                  --}}
        {{-- ========================================================================= --}}
        <div class="tab-pane fade {{ $active_tab === 'persalinan' ? 'show active' : '' }}" id="tab-persalinan" role="tabpanel" aria-labelledby="tab-persalinan-btn">
            
            <!-- Switch Mode: Form vs iFrame Master -->
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div class="section-title m-0">
                    Formulir 1: Data Admisi Persalinan & Riwayat Obstetri Masuk
                </div>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-primary mode-switch-pill" onclick="$('#view-form-persalinan').show(); $('#view-iframe-persalinan').hide(); $(this).addClass('btn-primary').removeClass('btn-outline-primary'); $(this).siblings().removeClass('btn-primary').addClass('btn-outline-primary');">
                        <i class="fas fa-edit mr-1"></i> Formulir Identik Master
                    </button>
                    <button type="button" class="btn btn-outline-primary mode-switch-pill" onclick="$('#view-form-persalinan').hide(); $('#view-iframe-persalinan').show(); $(this).addClass('btn-primary').removeClass('btn-outline-primary'); $(this).siblings().removeClass('btn-primary').addClass('btn-outline-primary');">
                        <i class="fas fa-window-maximize mr-1"></i> Tampilan Master (iFrame)
                    </button>
                </div>
            </div>

            <!-- View iFrame Persalinan -->
            <div id="view-iframe-persalinan" style="display: none;" class="mb-4">
                <div class="alert alert-light border small py-2 mb-2 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-info-circle text-info mr-1"></i> Menyematkan Master Data Persalinan langsung via iFrame:</span>
                    <a href="{{ route('persalinan.index') }}" target="_blank" class="btn btn-sm btn-outline-info py-0">Buka Tab Penuh</a>
                </div>
                <iframe src="{{ route('persalinan.index') }}?embedded=1" style="width: 100%; height: 750px; border: 1px solid #cbd5e1; border-radius: 8px;"></iframe>
            </div>

            <!-- View Form Persalinan (Identik 100% dengan modal master) -->
            <div id="view-form-persalinan">
                <form action="{{ $persalinan ? route('persalinan.update', $persalinan->id) : route('persalinan.store') }}" method="POST">
                    @csrf
                    @if($persalinan)
                        @method('PUT')
                    @endif
                    <input type="hidden" name="pasien_id" value="{{ $pasien->id }}">
                    <input type="hidden" name="redirect_to" value="input_terpadu">

                    <!-- Seksi 1: Data Pasien & GPA -->
                    <h6 class="font-weight-bold text-primary mb-3">
                        <i class="fas fa-user-injured mr-1"></i> 1. Data Pasien & Riwayat Obstetri
                    </h6>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Pasien Terdaftar</label>
                            <input type="text" class="form-control bg-light"
                                value="{{ ($pasien->nomor_register ?? 'REG-' . $pasien->id) . ' - ' . $pasien->nama_ibu . ($pasien->nama_suami ? ' (Suami: ' . $pasien->nama_suami . ')' : '') }}"
                                readonly>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Usia Kehamilan (Minggu) <span class="text-danger">*</span></label>
                            <input type="number" name="usia_kehamilan_minggu" class="form-control"
                                placeholder="Contoh: 38" min="10" max="50"
                                value="{{ old('usia_kehamilan_minggu', $persalinan->usia_kehamilan_minggu ?? '') }}" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Gravida (G) <span class="text-danger">*</span></label>
                            <input type="number" name="gravida" class="form-control" min="1" max="30"
                                value="{{ old('gravida', $persalinan->gravida ?? 1) }}" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Para (P) <span class="text-danger">*</span></label>
                            <input type="number" name="para" class="form-control" min="0" max="30"
                                value="{{ old('para', $persalinan->para ?? 0) }}" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Abortus (A) <span class="text-danger">*</span></label>
                            <input type="number" name="abortus" class="form-control" min="0" max="30"
                                value="{{ old('abortus', $persalinan->abortus ?? 0) }}" required>
                        </div>
                    </div>

                    <!-- Seksi 2: Fasilitas & Penolong -->
                    <h6 class="font-weight-bold text-primary mt-3 mb-3">
                        <i class="fas fa-hospital mr-1"></i> 2. Tempat & Penolong Medis
                    </h6>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Nama Fasilitas Kesehatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_fasilitas" class="form-control"
                                placeholder="Contoh: Puskesmas Sehat Sejahtera"
                                value="{{ old('nama_fasilitas', $persalinan->nama_fasilitas ?? 'Puskesmas Sejahtera') }}" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Tempat Persalinan <span class="text-danger">*</span></label>
                            @php
                                $selectedTempat = old('tempat_persalinan', $persalinan->tempat_persalinan ?? 'puskesmas');
                            @endphp
                            <select name="tempat_persalinan" class="form-control" required>
                                <option value="puskesmas" {{ $selectedTempat == 'puskesmas' ? 'selected' : '' }}>Puskesmas</option>
                                <option value="rumah_ibu" {{ $selectedTempat == 'rumah_ibu' ? 'selected' : '' }}>Rumah Ibu</option>
                                <option value="klinik_swasta" {{ $selectedTempat == 'klinik_swasta' ? 'selected' : '' }}>Klinik Swasta</option>
                                <option value="rumah_sakit" {{ $selectedTempat == 'rumah_sakit' ? 'selected' : '' }}>Rumah Sakit</option>
                                <option value="lainnya" {{ $selectedTempat == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Nama Penolong Persalinan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_penolong" class="form-control"
                                placeholder="Contoh: Bidan Siti, S.Tr.Keb"
                                value="{{ old('nama_penolong', $persalinan->nama_penolong ?? 'Bidan Jaga') }}" required>
                        </div>
                    </div>

                    <!-- Seksi 3: Waktu Observasi & Status -->
                    <h6 class="font-weight-bold text-primary mt-3 mb-3">
                        <i class="fas fa-clock mr-1"></i> 3. Waktu Observasi Awal & Status
                    </h6>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Waktu Masuk <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="waktu_masuk" class="form-control"
                                value="{{ old('waktu_masuk', $persalinan && $persalinan->waktu_masuk ? \Carbon\Carbon::parse($persalinan->waktu_masuk)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Waktu Mules Mulai</label>
                            <input type="datetime-local" name="waktu_mules_mulai" class="form-control"
                                value="{{ old('waktu_mules_mulai', $persalinan && $persalinan->waktu_mules_mulai ? \Carbon\Carbon::parse($persalinan->waktu_mules_mulai)->format('Y-m-d\TH:i') : '') }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Waktu Ketuban Pecah</label>
                            <input type="datetime-local" name="waktu_ketuban_pecah" class="form-control"
                                value="{{ old('waktu_ketuban_pecah', $persalinan && $persalinan->waktu_ketuban_pecah ? \Carbon\Carbon::parse($persalinan->waktu_ketuban_pecah)->format('Y-m-d\TH:i') : '') }}">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Status Persalinan <span class="text-danger">*</span></label>
                            @php
                                $selectedStatus = old('status_persalinan', $persalinan->status_persalinan ?? 'proses');
                            @endphp
                            <select name="status_persalinan" class="form-control" required>
                                <option value="proses" {{ $selectedStatus == 'proses' ? 'selected' : '' }}>Dalam Proses</option>
                                <option value="selesai" {{ $selectedStatus == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="rujuk" {{ $selectedStatus == 'rujuk' ? 'selected' : '' }}>Rujuk</option>
                                <option value="batal" {{ $selectedStatus == 'batal' ? 'selected' : '' }}>Batal</option>
                            </select>
                        </div>
                    </div>

                    <!-- Seksi 4: Asupan Nutrisi Terakhir -->
                    <h6 class="font-weight-bold text-primary mt-3 mb-3">
                        <i class="fas fa-utensils mr-1"></i> 4. Asupan Terakhir Ibu (Footer Partograf)
                    </h6>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Waktu Makan Terakhir</label>
                            <input type="datetime-local" name="waktu_makan_terakhir" class="form-control"
                                value="{{ old('waktu_makan_terakhir', $persalinan && $persalinan->waktu_makan_terakhir ? \Carbon\Carbon::parse($persalinan->waktu_makan_terakhir)->format('Y-m-d\TH:i') : '') }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Jenis Makanan</label>
                            <input type="text" name="jenis_makan" class="form-control" placeholder="Contoh: Nasi, Sayur"
                                value="{{ old('jenis_makan', $persalinan->jenis_makan ?? '') }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Porsi Makan</label>
                            <input type="text" name="porsi_makan" class="form-control"
                                placeholder="Contoh: 1 porsi habis, 1/2 porsi"
                                value="{{ old('porsi_makan', $persalinan->porsi_makan ?? '') }}">
                        </div>

                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Waktu Minum Terakhir</label>
                            <input type="datetime-local" name="waktu_minum_terakhir" class="form-control"
                                value="{{ old('waktu_minum_terakhir', $persalinan && $persalinan->waktu_minum_terakhir ? \Carbon\Carbon::parse($persalinan->waktu_minum_terakhir)->format('Y-m-d\TH:i') : '') }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Jenis Minuman</label>
                            <input type="text" name="jenis_minum" class="form-control" placeholder="Contoh: Air putih, Teh manis"
                                value="{{ old('jenis_minum', $persalinan->jenis_minum ?? '') }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Porsi Minum</label>
                            <input type="text" name="porsi_minum" class="form-control"
                                placeholder="Contoh: 1 gelas air putih"
                                value="{{ old('porsi_minum', $persalinan->porsi_minum ?? '') }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold">
                            <i class="fas fa-save mr-1"></i> {{ $persalinan ? 'Perbarui Data Persalinan' : 'Simpan Data Persalinan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>


        {{-- ========================================================================= --}}
        {{-- TAB 2: OBSERVASI PARTOGRAF                                                --}}
        {{-- ========================================================================= --}}
        <div class="tab-pane fade {{ $active_tab === 'observasi' ? 'show active' : '' }}" id="tab-observasi" role="tabpanel" aria-labelledby="tab-observasi-btn">
            
            <!-- Switch Mode: Form vs iFrame Master -->
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div class="section-title m-0">
                    Formulir 2: Catatan Observasi Fase Aktif Partograf
                </div>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-primary mode-switch-pill" onclick="$('#view-form-observasi').show(); $('#view-iframe-observasi').hide(); $(this).addClass('btn-primary').removeClass('btn-outline-primary'); $(this).siblings().removeClass('btn-primary').addClass('btn-outline-primary');">
                        <i class="fas fa-edit mr-1"></i> Formulir Identik Master
                    </button>
                    <button type="button" class="btn btn-outline-primary mode-switch-pill" onclick="$('#view-form-observasi').hide(); $('#view-iframe-observasi').show(); $(this).addClass('btn-primary').removeClass('btn-outline-primary'); $(this).siblings().removeClass('btn-primary').addClass('btn-outline-primary');">
                        <i class="fas fa-window-maximize mr-1"></i> Tampilan Master (iFrame)
                    </button>
                </div>
            </div>

            <!-- View iFrame Observasi -->
            <div id="view-iframe-observasi" style="display: none;" class="mb-4">
                <div class="alert alert-light border small py-2 mb-2 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-info-circle text-info mr-1"></i> Menyematkan Master Observasi Partograf langsung via iFrame:</span>
                    <a href="{{ route('observasi-partograf.index') }}" target="_blank" class="btn btn-sm btn-outline-info py-0">Buka Tab Penuh</a>
                </div>
                <iframe src="{{ route('observasi-partograf.index') }}?embedded=1" style="width: 100%; height: 750px; border: 1px solid #cbd5e1; border-radius: 8px;"></iframe>
            </div>

            <!-- View Form Observasi -->
            <div id="view-form-observasi">
                @if(! $persalinan)
                    <div class="alert alert-warning border-left-warning shadow-sm">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> Silakan lengkapi <strong>Tab 1. Admisi Persalinan</strong> terlebih dahulu sebelum mencatat observasi partograf.
                    </div>
                @else
                    <!-- Card Form Tambah Observasi Baru -->
                    <div class="card shadow-sm border-0 mb-4 bg-light">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-file-medical mr-1"></i> Tambah Titik Observasi Partograf
                            </h6>
                            <span class="badge badge-info font-weight-normal px-2 py-1">Titik Ke-{{ $data_observasi->count() + 1 }}</span>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('observasi-partograf.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="persalinan_id" value="{{ $persalinan->id }}">
                                <input type="hidden" name="pasien_id" value="{{ $pasien->id }}">
                                <input type="hidden" name="redirect_to" value="input_terpadu">

                                {{-- Seksi 1: Identitas & Waktu --}}
                                <h6 class="font-weight-bold text-primary mb-3">
                                    <i class="fas fa-clock mr-1"></i> 1. Waktu Pemeriksaan & Kolom Matriks
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-gray-700">Waktu Pemeriksaan <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="waktu_pemeriksaan" class="form-control"
                                            value="{{ old('waktu_pemeriksaan', now()->format('Y-m-d\TH:i')) }}" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-gray-700">Kolom Partograf Ke- (1 - 16)</label>
                                        <input type="number" name="kolom_partograf_ke" class="form-control"
                                            placeholder="Contoh: 1, 2, 3..." min="1" max="16"
                                            value="{{ old('kolom_partograf_ke', min($data_observasi->count() + 1, 16)) }}">
                                        <small class="text-muted">Kolom matriks partograf (interval 30 menit)</small>
                                    </div>
                                </div>

                                {{-- Seksi 2: Denyut Jantung Janin & Cairan Ketuban --}}
                                <h6 class="font-weight-bold text-primary mt-3 mb-3">
                                    <i class="fas fa-heartbeat mr-1"></i> 2. Kondisi Janin
                                </h6>
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label class="font-weight-bold text-gray-700">DJJ (Denyut Jantung Janin / menit)</label>
                                        <input type="number" name="djj_denyut_per_menit" class="form-control"
                                            placeholder="Contoh: 140 (Normal 120-160)" min="0" max="300"
                                            value="{{ old('djj_denyut_per_menit') }}">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="font-weight-bold text-gray-700">Air Ketuban</label>
                                        <select name="air_ketuban" class="form-control">
                                            <option value="">-- Pilih Status Ketuban --</option>
                                            <option value="U" {{ old('air_ketuban') == 'U' ? 'selected' : '' }}>U - Utuh (belum pecah)</option>
                                            <option value="J" {{ old('air_ketuban') == 'J' ? 'selected' : '' }}>J - Jernih (air jernih)</option>
                                            <option value="M" {{ old('air_ketuban') == 'M' ? 'selected' : '' }}>M - Mekonium (bercampur mekonium)</option>
                                            <option value="D" {{ old('air_ketuban') == 'D' ? 'selected' : '' }}>D - Darah (bercampur darah)</option>
                                            <option value="K" {{ old('air_ketuban') == 'K' ? 'selected' : '' }}>K - Kering (cairan kering)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="font-weight-bold text-gray-700">Penyusupan Tulang Kepala (Molase)</label>
                                        <select name="penyusupan_kepala" class="form-control">
                                            <option value="">-- Pilih Derajat Molase --</option>
                                            <option value="0" {{ old('penyusupan_kepala') === '0' ? 'selected' : '' }}>0 - Sutura terpisah</option>
                                            <option value="1" {{ old('penyusupan_kepala') === '1' ? 'selected' : '' }}>1 - Tulang bersentuhan</option>
                                            <option value="2" {{ old('penyusupan_kepala') === '2' ? 'selected' : '' }}>2 - Tumpang tindih (bisa dipisahkan)</option>
                                            <option value="3" {{ old('penyusupan_kepala') === '3' ? 'selected' : '' }}>3 - Tumpang tindih berat (sulit dipisah)</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Seksi 3: Kemajuan Persalinan & Kontraksi --}}
                                <h6 class="font-weight-bold text-primary mt-3 mb-3">
                                    <i class="fas fa-chart-line mr-1"></i> 3. Kemajuan Persalinan & Kontraksi Uterus
                                </h6>
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">Pembukaan Serviks (cm)</label>
                                        <input type="number" step="0.1" name="pembukaan_serviks" class="form-control"
                                            placeholder="0.0 - 10.0 (simbol X)" min="0" max="10"
                                            value="{{ old('pembukaan_serviks') }}">
                                        <small class="text-muted">Garis Waspada mulai 4 cm</small>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">Turunnya Kepala (Perlimaan)</label>
                                        <input type="number" name="turunnya_kepala" class="form-control"
                                            placeholder="0 - 5 (simbol O)" min="0" max="5"
                                            value="{{ old('turunnya_kepala') }}">
                                        <small class="text-muted">5/5 (tinggi) s/d 0/5 (dasar panggul)</small>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">Frekuensi Kontraksi (/10 mnt)</label>
                                        <input type="number" name="frekuensi_kontraksi" class="form-control"
                                            placeholder="Contoh: 3 kali" min="0" max="10"
                                            value="{{ old('frekuensi_kontraksi') }}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">Durasi Kontraksi</label>
                                        <select name="durasi_kontraksi" class="form-control">
                                            <option value="">-- Pilih Durasi --</option>
                                            <option value="kurang_20" {{ old('durasi_kontraksi') == 'kurang_20' ? 'selected' : '' }}>&lt; 20 detik (Titik-titik)</option>
                                            <option value="antara_20_40" {{ old('durasi_kontraksi') == 'antara_20_40' ? 'selected' : '' }}>20 - 40 detik (Garis arsir)</option>
                                            <option value="lebih_40" {{ old('durasi_kontraksi') == 'lebih_40' ? 'selected' : '' }}>&gt; 40 detik (Blok hitam)</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Seksi 4: Oksitosin & Cairan IV --}}
                                <h6 class="font-weight-bold text-primary mt-3 mb-3">
                                    <i class="fas fa-syringe mr-1"></i> 4. Oksitosin & Terapi Cairan IV
                                </h6>
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">Oksitosin (Unit)</label>
                                        <input type="number" step="0.01" name="oksitosin_unit" class="form-control"
                                            placeholder="Contoh: 0.5" min="0" value="{{ old('oksitosin_unit') }}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">Tetes Oksitosin (/menit)</label>
                                        <input type="number" name="oksitosin_tetes_per_menit" class="form-control"
                                            placeholder="Contoh: 20" min="0" max="300"
                                            value="{{ old('oksitosin_tetes_per_menit') }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-gray-700">Obat & Cairan IV Lainnya</label>
                                        <input type="text" name="obat_dan_cairan_iv" class="form-control"
                                            placeholder="Contoh: Infus RL 500 ml" value="{{ old('obat_dan_cairan_iv') }}">
                                    </div>
                                </div>

                                {{-- Seksi 5: Tanda-Tanda Vital Ibu --}}
                                <h6 class="font-weight-bold text-primary mt-3 mb-3">
                                    <i class="fas fa-heart mr-1"></i> 5. Tanda-Tanda Vital Ibu
                                </h6>
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">TD Sistolik (mmHg)</label>
                                        <input type="number" name="tekanan_darah_sistolik" class="form-control"
                                            placeholder="Contoh: 120" min="0" max="300"
                                            value="{{ old('tekanan_darah_sistolik') }}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">TD Diastolik (mmHg)</label>
                                        <input type="number" name="tekanan_darah_diastolik" class="form-control"
                                            placeholder="Contoh: 80" min="0" max="200"
                                            value="{{ old('tekanan_darah_diastolik') }}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">Nadi (/menit)</label>
                                        <input type="number" name="nadi_per_menit" class="form-control"
                                            placeholder="Contoh: 82" min="0" max="300"
                                            value="{{ old('nadi_per_menit') }}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">Suhu Tubuh (°C)</label>
                                        <input type="number" step="0.1" name="suhu_tubuh" class="form-control"
                                            placeholder="Contoh: 36.7" min="30" max="45"
                                            value="{{ old('suhu_tubuh') }}">
                                    </div>
                                </div>

                                {{-- Seksi 6: Urin --}}
                                <h6 class="font-weight-bold text-primary mt-3 mb-3">
                                    <i class="fas fa-flask mr-1"></i> 6. Pemeriksaan Urin
                                </h6>
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label class="font-weight-bold text-gray-700">Protein Urin</label>
                                        <select name="urin_protein" class="form-control">
                                            <option value="">-- Pilih Hasil Protein --</option>
                                            <option value="-" {{ old('urin_protein') == '-' ? 'selected' : '' }}>- (Negatif)</option>
                                            <option value="+" {{ old('urin_protein') == '+' ? 'selected' : '' }}>+ (Positif 1)</option>
                                            <option value="++" {{ old('urin_protein') == '++' ? 'selected' : '' }}>++ (Positif 2)</option>
                                            <option value="+++" {{ old('urin_protein') == '+++' ? 'selected' : '' }}>+++ (Positif 3)</option>
                                            <option value="++++" {{ old('urin_protein') == '++++' ? 'selected' : '' }}>++++ (Positif 4)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="font-weight-bold text-gray-700">Aseton Urin</label>
                                        <select name="urin_aseton" class="form-control">
                                            <option value="">-- Pilih Hasil Aseton --</option>
                                            <option value="-" {{ old('urin_aseton') == '-' ? 'selected' : '' }}>- (Negatif)</option>
                                            <option value="+" {{ old('urin_aseton') == '+' ? 'selected' : '' }}>+ (Positif 1)</option>
                                            <option value="++" {{ old('urin_aseton') == '++' ? 'selected' : '' }}>++ (Positif 2)</option>
                                            <option value="+++" {{ old('urin_aseton') == '+++' ? 'selected' : '' }}>+++ (Positif 3)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="font-weight-bold text-gray-700">Volume Urin (ml)</label>
                                        <input type="number" name="urin_volume_ml" class="form-control"
                                            placeholder="Contoh: 150" min="0" max="3000"
                                            value="{{ old('urin_volume_ml') }}">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold">
                                        <i class="fas fa-plus mr-1"></i> Simpan Catatan Observasi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Riwayat Observasi Partograf Pasien Ini -->
                    <h6 class="font-weight-bold text-gray-800 mb-2">
                        <i class="fas fa-list-alt mr-1"></i> Riwayat Observasi Partograf Pasien Ini (Total: {{ $data_observasi->count() }} Data)
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover text-center align-middle" style="font-size: 11px;">
                            <thead class="bg-light text-gray-700">
                                <tr>
                                    <th>Kolom</th>
                                    <th>Waktu</th>
                                    <th>DJJ</th>
                                    <th>Ketuban</th>
                                    <th>Molase</th>
                                    <th>Pembukaan</th>
                                    <th>Turun Kepala</th>
                                    <th>Kontraksi</th>
                                    <th>TTV (TD / Nadi / Suhu)</th>
                                    <th>Urin</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data_observasi as $obs)
                                    <tr>
                                        <td><span class="badge badge-info font-weight-bold">Kolom {{ $obs->kolom_partograf_ke }}</span></td>
                                        <td>{{ \Carbon\Carbon::parse($obs->waktu_pemeriksaan)->format('d/m H:i') }}</td>
                                        <td class="font-weight-bold {{ ($obs->djj_denyut_per_menit < 120 || $obs->djj_denyut_per_menit > 160) ? 'text-danger' : 'text-success' }}">
                                            {{ $obs->djj_denyut_per_menit ? $obs->djj_denyut_per_menit . ' x/m' : '-' }}
                                        </td>
                                        <td><span class="badge badge-light border">{{ $obs->air_ketuban ?? '-' }}</span></td>
                                        <td>{{ $obs->penyusupan_kepala ?? '-' }}</td>
                                        <td class="font-weight-bold text-primary">{{ $obs->pembukaan_serviks !== null ? $obs->pembukaan_serviks . ' cm' : '-' }}</td>
                                        <td>{{ $obs->turunnya_kepala !== null ? $obs->turunnya_kepala . '/5' : '-' }}</td>
                                        <td>{{ $obs->frekuensi_kontraksi ? $obs->frekuensi_kontraksi . 'x/10m' : '-' }}</td>
                                        <td>
                                            {{ $obs->tekanan_darah_sistolik ? $obs->tekanan_darah_sistolik . '/' . $obs->tekanan_darah_diastolik : '-' }}
                                            | {{ $obs->nadi_per_menit ? $obs->nadi_per_menit . 'm' : '-' }}
                                            | {{ $obs->suhu_tubuh ? $obs->suhu_tubuh . '°C' : '-' }}
                                        </td>
                                        <td>{{ $obs->urin_volume_ml ? $obs->urin_volume_ml . 'ml' : '-' }}</td>
                                        <td>
                                            <form action="{{ route('observasi-partograf.destroy', $obs->id) }}" method="POST" onsubmit="return confirm('Hapus titik observasi ini?')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="redirect_to" value="input_terpadu">
                                                <input type="hidden" name="pasien_id" value="{{ $pasien->id }}">
                                                <button type="submit" class="btn btn-outline-danger btn-sm p-0 px-1" title="Hapus">
                                                    <i class="fas fa-trash fa-xs"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-3">Belum ada titik observasi partograf tercatat untuk pasien ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>


        {{-- ========================================================================= --}}
        {{-- TAB 3: CATATAN PERSALINAN (LEMBAR BELAKANG)                               --}}
        {{-- ========================================================================= --}}
        <div class="tab-pane fade {{ $active_tab === 'catatan' ? 'show active' : '' }}" id="tab-catatan" role="tabpanel" aria-labelledby="tab-catatan-btn">
            
            <!-- Switch Mode: Form vs iFrame Master -->
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div class="section-title m-0">
                    Formulir 3: Catatan Persalinan Kala I - IV (Lembar Belakang Partograf)
                </div>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-primary mode-switch-pill" onclick="$('#view-form-catatan').show(); $('#view-iframe-catatan').hide(); $(this).addClass('btn-primary').removeClass('btn-outline-primary'); $(this).siblings().removeClass('btn-primary').addClass('btn-outline-primary');">
                        <i class="fas fa-edit mr-1"></i> Formulir Identik Master
                    </button>
                    <button type="button" class="btn btn-outline-primary mode-switch-pill" onclick="$('#view-form-catatan').hide(); $('#view-iframe-catatan').show(); $(this).addClass('btn-primary').removeClass('btn-outline-primary'); $(this).siblings().removeClass('btn-primary').addClass('btn-outline-primary');">
                        <i class="fas fa-window-maximize mr-1"></i> Tampilan Master (iFrame)
                    </button>
                </div>
            </div>

            <!-- View iFrame Catatan -->
            <div id="view-iframe-catatan" style="display: none;" class="mb-4">
                <div class="alert alert-light border small py-2 mb-2 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-info-circle text-info mr-1"></i> Menyematkan Master Catatan Persalinan langsung via iFrame:</span>
                    <a href="{{ route('catatan-persalinan.index') }}" target="_blank" class="btn btn-sm btn-outline-info py-0">Buka Tab Penuh</a>
                </div>
                <iframe src="{{ route('catatan-persalinan.index') }}?embedded=1" style="width: 100%; height: 750px; border: 1px solid #cbd5e1; border-radius: 8px;"></iframe>
            </div>

            <!-- View Form Catatan Persalinan (Identik 100% modal master) -->
            <div id="view-form-catatan">
                @if(! $persalinan)
                    <div class="alert alert-warning border-left-warning shadow-sm">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> Silakan lengkapi <strong>Tab 1. Admisi Persalinan</strong> terlebih dahulu sebelum mengisi catatan persalinan.
                    </div>
                @else
                    <form action="{{ $catatan_persalinan ? route('catatan-persalinan.update', $catatan_persalinan->id) : route('catatan-persalinan.store') }}" method="POST">
                        @csrf
                        @if($catatan_persalinan)
                            @method('PUT')
                        @endif
                        <input type="hidden" name="persalinan_id" value="{{ $persalinan->id }}">
                        <input type="hidden" name="pasien_id" value="{{ $pasien->id }}">
                        <input type="hidden" name="redirect_to" value="input_terpadu">

                        {{-- 1. IDENTITAS PERSALINAN --}}
                        <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="font-weight-bold text-primary">{{ $pasien->nomor_register ?? 'REG-' . $pasien->id }} - {{ $pasien->nama_ibu }}</span>
                                <span class="text-muted ml-2">(G{{ $persalinan->gravida }}P{{ $persalinan->para }}A{{ $persalinan->abortus }}, Masuk: {{ $persalinan->waktu_masuk ? \Carbon\Carbon::parse($persalinan->waktu_masuk)->format('d/m/Y H:i') : '-' }})</span>
                            </div>
                            <span class="badge badge-{{ $catatan_persalinan ? 'success' : 'secondary' }}">
                                {{ $catatan_persalinan ? 'Data Tersimpan' : 'Baru' }}
                            </span>
                        </div>

                        {{-- 2. KALA I --}}
                        <h6 class="font-weight-bold text-primary mb-3">
                            <i class="fas fa-clock mr-1"></i> 2. Catatan Kala I (Fase Persalinan)
                        </h6>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Melewati Garis Waspada? <span class="text-danger">*</span></label>
                                @php
                                    $kala1Garis = old('kala1_melewati_garis_waspada', $catatan_persalinan->kala1_melewati_garis_waspada ?? '0');
                                @endphp
                                <select name="kala1_melewati_garis_waspada" class="form-control" required>
                                    <option value="0" {{ $kala1Garis == '0' ? 'selected' : '' }}>Tidak (Aman)</option>
                                    <option value="1" {{ $kala1Garis == '1' ? 'selected' : '' }}>Ya (Melewati Garis Waspada)</option>
                                </select>
                            </div>
                            <div class="col-md-8 form-group">
                                <label class="font-weight-bold text-gray-700">Masalah Lain Kala I</label>
                                <input type="text" name="kala1_masalah_lain" class="form-control" placeholder="Contoh: Nyeri hebat, gelisah, dll"
                                    value="{{ old('kala1_masalah_lain', $catatan_persalinan->kala1_masalah_lain ?? '') }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Penatalaksanaan Kala I</label>
                                <textarea name="kala1_penatalaksanaan" class="form-control" rows="2" placeholder="Tindakan yang dilakukan selama Kala I">{{ old('kala1_penatalaksanaan', $catatan_persalinan->kala1_penatalaksanaan ?? '') }}</textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Hasil Evaluasi Kala I</label>
                                <textarea name="kala1_hasil_evaluasi" class="form-control" rows="2" placeholder="Hasil evaluasi kondisi ibu dan janin di Kala I">{{ old('kala1_hasil_evaluasi', $catatan_persalinan->kala1_hasil_evaluasi ?? '') }}</textarea>
                            </div>
                        </div>

                        {{-- 3. KALA II --}}
                        <h6 class="font-weight-bold text-primary mt-4 mb-3">
                            <i class="fas fa-baby mr-1"></i> 3. Catatan Kala II (Pengeluaran Janin)
                        </h6>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Lama Kala II (Menit)</label>
                                <input type="number" name="kala2_lama_menit" class="form-control" min="0" max="600" placeholder="Contoh: 30"
                                    value="{{ old('kala2_lama_menit', $catatan_persalinan->kala2_lama_menit ?? '') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Episiotomi <span class="text-danger">*</span></label>
                                @php
                                    $kala2Epi = old('kala2_episiotomi', $catatan_persalinan->kala2_episiotomi ?? '0');
                                @endphp
                                <select name="kala2_episiotomi" class="form-control" required>
                                    <option value="0" {{ $kala2Epi == '0' ? 'selected' : '' }}>Tidak</option>
                                    <option value="1" {{ $kala2Epi == '1' ? 'selected' : '' }}>Ya</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Indikasi Episiotomi (Jika Ya)</label>
                                <input type="text" name="kala2_indikasi_episiotomi" class="form-control" placeholder="Contoh: Perineum kaku, gawat janin"
                                    value="{{ old('kala2_indikasi_episiotomi', $catatan_persalinan->kala2_indikasi_episiotomi ?? '') }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Pendamping Persalinan</label>
                                @php
                                    $kala2Pend = old('kala2_pendamping', $catatan_persalinan->kala2_pendamping ?? 'suami');
                                @endphp
                                <select name="kala2_pendamping" class="form-control">
                                    <option value="suami" {{ $kala2Pend == 'suami' ? 'selected' : '' }}>Suami</option>
                                    <option value="keluarga" {{ $kala2Pend == 'keluarga' ? 'selected' : '' }}>Keluarga</option>
                                    <option value="teman" {{ $kala2Pend == 'teman' ? 'selected' : '' }}>Teman</option>
                                    <option value="dukun" {{ $kala2Pend == 'dukun' ? 'selected' : '' }}>Dukun</option>
                                    <option value="tidak_ada" {{ $kala2Pend == 'tidak_ada' ? 'selected' : '' }}>Tidak Ada</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Gawat Janin (Tindakan)</label>
                                <input type="text" name="kala2_gawat_janin" class="form-control" placeholder="Contoh: Miring kiri, O2, tidak ada"
                                    value="{{ old('kala2_gawat_janin', $catatan_persalinan->kala2_gawat_janin ?? 'tidak_ada') }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Distosia Bahu (Tindakan)</label>
                                <input type="text" name="kala2_distosia_bahu" class="form-control" placeholder="Contoh: McRobert, tidak ada"
                                    value="{{ old('kala2_distosia_bahu', $catatan_persalinan->kala2_distosia_bahu ?? 'tidak_ada') }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Penatalaksanaan Kala II</label>
                                <textarea name="kala2_penatalaksanaan" class="form-control" rows="2" placeholder="Bimbingan meneran, posisi, dll">{{ old('kala2_penatalaksanaan', $catatan_persalinan->kala2_penatalaksanaan ?? '') }}</textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Hasil Evaluasi Kala II</label>
                                <textarea name="kala2_hasil_evaluasi" class="form-control" rows="2" placeholder="Kondisi lahir bayi, jam lahir, tangisan">{{ old('kala2_hasil_evaluasi', $catatan_persalinan->kala2_hasil_evaluasi ?? '') }}</textarea>
                            </div>
                        </div>

                        {{-- 4. KALA III --}}
                        <h6 class="font-weight-bold text-primary mt-4 mb-3">
                            <i class="fas fa-heartbeat mr-1"></i> 4. Catatan Kala III (Pengeluaran Plasenta & MAK III)
                        </h6>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Lama Kala III (Menit)</label>
                                <input type="number" name="kala3_lama_menit" class="form-control" min="0" max="300" placeholder="Contoh: 10"
                                    value="{{ old('kala3_lama_menit', $catatan_persalinan->kala3_lama_menit ?? '') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Jumlah Darah (ml)</label>
                                <input type="number" name="kala3_jumlah_perdarahan_ml" class="form-control" min="0" max="5000" placeholder="Contoh: 150"
                                    value="{{ old('kala3_jumlah_perdarahan_ml', $catatan_persalinan->kala3_jumlah_perdarahan_ml ?? '') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Oksitosin 10U dlm 2 mnt? <span class="text-danger">*</span></label>
                                @php
                                    $k3Oksi = old('kala3_oksitosin_10u_2menit', $catatan_persalinan->kala3_oksitosin_10u_2menit ?? '1');
                                @endphp
                                <select name="kala3_oksitosin_10u_2menit" class="form-control" required>
                                    <option value="1" {{ $k3Oksi == '1' ? 'selected' : '' }}>Ya</option>
                                    <option value="0" {{ $k3Oksi == '0' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Alasan Tdk Oksitosin</label>
                                <input type="text" name="kala3_alasan_tidak_oksitosin" class="form-control" placeholder="Alasan bila tidak"
                                    value="{{ old('kala3_alasan_tidak_oksitosin', $catatan_persalinan->kala3_alasan_tidak_oksitosin ?? '') }}">
                            </div>

                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Oksitosin Ulang? <span class="text-danger">*</span></label>
                                @php
                                    $k3OksiUlang = old('kala3_oksitosin_ulang', $catatan_persalinan->kala3_oksitosin_ulang ?? '0');
                                @endphp
                                <select name="kala3_oksitosin_ulang" class="form-control" required>
                                    <option value="0" {{ $k3OksiUlang == '0' ? 'selected' : '' }}>Tidak</option>
                                    <option value="1" {{ $k3OksiUlang == '1' ? 'selected' : '' }}>Ya</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Alasan Oksitosin Ulang</label>
                                <input type="text" name="kala3_alasan_oksitosin_ulang" class="form-control" placeholder="Alasan bila ya"
                                    value="{{ old('kala3_alasan_oksitosin_ulang', $catatan_persalinan->kala3_alasan_oksitosin_ulang ?? '') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Penegangan Tali Pusat? <span class="text-danger">*</span></label>
                                @php
                                    $k3Ptp = old('kala3_penegangan_tali_pusat', $catatan_persalinan->kala3_penegangan_tali_pusat ?? '1');
                                @endphp
                                <select name="kala3_penegangan_tali_pusat" class="form-control" required>
                                    <option value="1" {{ $k3Ptp == '1' ? 'selected' : '' }}>Ya</option>
                                    <option value="0" {{ $k3Ptp == '0' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Alasan Tidak PTP</label>
                                <input type="text" name="kala3_alasan_tidak_ptp" class="form-control" placeholder="Alasan bila tidak"
                                    value="{{ old('kala3_alasan_tidak_ptp', $catatan_persalinan->kala3_alasan_tidak_ptp ?? '') }}">
                            </div>

                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Masase Fundus Uteri? <span class="text-danger">*</span></label>
                                @php
                                    $k3Masase = old('kala3_masase_fundus', $catatan_persalinan->kala3_masase_fundus ?? '1');
                                @endphp
                                <select name="kala3_masase_fundus" class="form-control" required>
                                    <option value="1" {{ $k3Masase == '1' ? 'selected' : '' }}>Ya</option>
                                    <option value="0" {{ $k3Masase == '0' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Alasan Tidak Masase</label>
                                <input type="text" name="kala3_alasan_tidak_masase" class="form-control" placeholder="Alasan bila tidak"
                                    value="{{ old('kala3_alasan_tidak_masase', $catatan_persalinan->kala3_alasan_tidak_masase ?? '') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Derajat Laserasi <span class="text-danger">*</span></label>
                                @php
                                    $k3Las = old('kala3_derajat_laserasi', $catatan_persalinan->kala3_derajat_laserasi ?? 'tidak_ada');
                                @endphp
                                <select name="kala3_derajat_laserasi" class="form-control" required>
                                    <option value="tidak_ada" {{ $k3Las == 'tidak_ada' ? 'selected' : '' }}>Tidak Ada</option>
                                    <option value="1" {{ $k3Las == '1' ? 'selected' : '' }}>Derajat 1</option>
                                    <option value="2" {{ $k3Las == '2' ? 'selected' : '' }}>Derajat 2</option>
                                    <option value="3" {{ $k3Las == '3' ? 'selected' : '' }}>Derajat 3</option>
                                    <option value="4" {{ $k3Las == '4' ? 'selected' : '' }}>Derajat 4</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Tindakan Laserasi</label>
                                <input type="text" name="kala3_tindakan_laserasi" class="form-control" placeholder="Penjahitan, rujuk, dll"
                                    value="{{ old('kala3_tindakan_laserasi', $catatan_persalinan->kala3_tindakan_laserasi ?? '') }}">
                            </div>

                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Atonia Uteri (Tindakan)</label>
                                <input type="text" name="kala3_atonia_uteri" class="form-control" placeholder="Kompresi bimanual, drip, tidak ada"
                                    value="{{ old('kala3_atonia_uteri', $catatan_persalinan->kala3_atonia_uteri ?? 'tidak_ada') }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Penatalaksanaan Kala III</label>
                                <textarea name="kala3_penatalaksanaan" class="form-control" rows="2" placeholder="MAK III, dll">{{ old('kala3_penatalaksanaan', $catatan_persalinan->kala3_penatalaksanaan ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Hasil Evaluasi Kala III</label>
                                <textarea name="kala3_hasil_evaluasi" class="form-control" rows="2" placeholder="Plasenta lahir lengkap, kontraksi keras">{{ old('kala3_hasil_evaluasi', $catatan_persalinan->kala3_hasil_evaluasi ?? '') }}</textarea>
                            </div>
                        </div>

                        {{-- 5. KALA IV --}}
                        <h6 class="font-weight-bold text-primary mt-4 mb-3">
                            <i class="fas fa-procedures mr-1"></i> 5. Catatan Ringkasan Kala IV (Pengawasan Post-Partum)
                        </h6>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Masalah Kala IV</label>
                                <textarea name="kala4_masalah" class="form-control" rows="2" placeholder="Masalah yang timbul selama 2 jam pasca salin">{{ old('kala4_masalah', $catatan_persalinan->kala4_masalah ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Penatalaksanaan Kala IV</label>
                                <textarea name="kala4_penatalaksanaan" class="form-control" rows="2" placeholder="Pemantauan TTV, kontraksi, perdarahan berkala">{{ old('kala4_penatalaksanaan', $catatan_persalinan->kala4_penatalaksanaan ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Hasil Evaluasi Kala IV</label>
                                <textarea name="kala4_hasil_evaluasi" class="form-control" rows="2" placeholder="Kondisi umum stabil, kontraksi baik">{{ old('kala4_hasil_evaluasi', $catatan_persalinan->kala4_hasil_evaluasi ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold">
                                <i class="fas fa-save mr-1"></i> {{ $catatan_persalinan ? 'Perbarui Catatan Persalinan' : 'Simpan Catatan Persalinan' }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>


        {{-- ========================================================================= --}}
        {{-- TAB 4: BAYI BARU LAHIR                                                    --}}
        {{-- ========================================================================= --}}
        <div class="tab-pane fade {{ $active_tab === 'bayi' ? 'show active' : '' }}" id="tab-bayi" role="tabpanel" aria-labelledby="tab-bayi-btn">
            
            <!-- Switch Mode: Form vs iFrame Master -->
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div class="section-title m-0">
                    Formulir 4: Rekam Medis Bayi Baru Lahir, APGAR & Resusitasi
                </div>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-primary mode-switch-pill" onclick="$('#view-form-bayi').show(); $('#view-iframe-bayi').hide(); $(this).addClass('btn-primary').removeClass('btn-outline-primary'); $(this).siblings().removeClass('btn-primary').addClass('btn-outline-primary');">
                        <i class="fas fa-edit mr-1"></i> Formulir Identik Master
                    </button>
                    <button type="button" class="btn btn-outline-primary mode-switch-pill" onclick="$('#view-form-bayi').hide(); $('#view-iframe-bayi').show(); $(this).addClass('btn-primary').removeClass('btn-outline-primary'); $(this).siblings().removeClass('btn-primary').addClass('btn-outline-primary');">
                        <i class="fas fa-window-maximize mr-1"></i> Tampilan Master (iFrame)
                    </button>
                </div>
            </div>

            <!-- View iFrame Bayi -->
            <div id="view-iframe-bayi" style="display: none;" class="mb-4">
                <div class="alert alert-light border small py-2 mb-2 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-info-circle text-info mr-1"></i> Menyematkan Master Bayi Baru Lahir langsung via iFrame:</span>
                    <a href="{{ route('bayi-baru-lahir.index') }}" target="_blank" class="btn btn-sm btn-outline-info py-0">Buka Tab Penuh</a>
                </div>
                <iframe src="{{ route('bayi-baru-lahir.index') }}?embedded=1" style="width: 100%; height: 750px; border: 1px solid #cbd5e1; border-radius: 8px;"></iframe>
            </div>

            <!-- View Form Bayi (Identik 100% modal master) -->
            <div id="view-form-bayi">
                @if(! $persalinan)
                    <div class="alert alert-warning border-left-warning shadow-sm">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> Silakan lengkapi <strong>Tab 1. Admisi Persalinan</strong> terlebih dahulu sebelum mencatat kelahiran bayi.
                    </div>
                @else
                    <!-- Form Input Bayi Baru Lahir Baru -->
                    <div class="card shadow-sm border-0 mb-4 bg-light">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-baby mr-1"></i> Tambah Data Bayi Baru Lahir
                            </h6>
                            <span class="badge badge-success font-weight-normal px-2 py-1">Kelahiran Bayi</span>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('bayi-baru-lahir.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="persalinan_id" value="{{ $persalinan->id }}">
                                <input type="hidden" name="pasien_id" value="{{ $pasien->id }}">
                                <input type="hidden" name="redirect_to" value="input_terpadu">

                                {{-- 1. IDENTITAS IBU & KELAHIRAN --}}
                                <h6 class="font-weight-bold text-primary mb-3">
                                    <i class="fas fa-user-injured mr-1"></i> 1. Identitas Ibu & Urutan Kelahiran
                                </h6>
                                <div class="row">
                                    <div class="col-md-7 form-group">
                                        <label class="font-weight-bold text-gray-700">Pasien Terkait</label>
                                        <input type="text" class="form-control bg-white" readonly
                                            value="{{ ($pasien->nomor_register ?? 'REG-' . $pasien->id) . ' - ' . $pasien->nama_ibu . ' (G' . $persalinan->gravida . 'P' . $persalinan->para . 'A' . $persalinan->abortus . ')' }}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">Urutan Kelahiran <span class="text-danger">*</span></label>
                                        <select name="urutan_kelahiran" class="form-control" required>
                                            <option value="1" {{ old('urutan_kelahiran', '1') == '1' ? 'selected' : '' }}>Bayi Tunggal / Ke-1</option>
                                            <option value="2" {{ old('urutan_kelahiran') == '2' ? 'selected' : '' }}>Bayi Ke-2 (Gemelli)</option>
                                            <option value="3" {{ old('urutan_kelahiran') == '3' ? 'selected' : '' }}>Bayi Ke-3 (Triplet)</option>
                                            <option value="4" {{ old('urutan_kelahiran') == '4' ? 'selected' : '' }}>Bayi Ke-4</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label class="font-weight-bold text-gray-700">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select name="jenis_kelamin" class="form-control" required>
                                            <option value="L" {{ old('jenis_kelamin', 'L') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- 2. ANTROPOMETRI & SKOR APGAR --}}
                                <h6 class="font-weight-bold text-primary mt-4 mb-3">
                                    <i class="fas fa-weight mr-1"></i> 2. Antropometri & Nilai APGAR
                                </h6>
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">Berat Badan (gram) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="berat_badan_gram" class="form-control" min="300" max="9000" placeholder="3100" value="{{ old('berat_badan_gram', 3100) }}" required>
                                            <div class="input-group-append"><span class="input-group-text">gram</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">Panjang Badan (cm) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="panjang_badan_cm" class="form-control" min="20" max="75" placeholder="49" value="{{ old('panjang_badan_cm', 49) }}" required>
                                            <div class="input-group-append"><span class="input-group-text">cm</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label class="font-weight-bold text-gray-700">APGAR 1' (Menit 1)</label>
                                        <input type="number" name="apgar_menit_1" class="form-control" min="0" max="10" placeholder="0-10" value="{{ old('apgar_menit_1', 8) }}">
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label class="font-weight-bold text-gray-700">APGAR 5' (Menit 5)</label>
                                        <input type="number" name="apgar_menit_5" class="form-control" min="0" max="10" placeholder="0-10" value="{{ old('apgar_menit_5', 9) }}">
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label class="font-weight-bold text-gray-700">APGAR 10' (Menit 10)</label>
                                        <input type="number" name="apgar_menit_10" class="form-control" min="0" max="10" placeholder="0-10" value="{{ old('apgar_menit_10', 10) }}">
                                    </div>
                                </div>

                                {{-- 3. INISIASI MENYUSU DINI & KONDISI BAYI --}}
                                <h6 class="font-weight-bold text-primary mt-4 mb-3">
                                    <i class="fas fa-heartbeat mr-1"></i> 3. Inisiasi Menyusu Dini (IMD) & Kondisi Bayi
                                </h6>
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label class="font-weight-bold text-gray-700">Pemberian ASI &lt; 1 Jam (IMD) <span class="text-danger">*</span></label>
                                        <select name="pemberian_asi_kurang_1jam" class="form-control" required>
                                            <option value="1" {{ old('pemberian_asi_kurang_1jam', '1') == '1' ? 'selected' : '' }}>Ya (Berhasil IMD &lt; 1 Jam)</option>
                                            <option value="0" {{ old('pemberian_asi_kurang_1jam') === '0' ? 'selected' : '' }}>Tidak</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="font-weight-bold text-gray-700">Alasan Tidak ASI / IMD</label>
                                        <input type="text" name="alasan_tidak_asi" class="form-control" placeholder="Contoh: Kondisi ibu lemah, asfiksia, dll" value="{{ old('alasan_tidak_asi') }}">
                                        <small class="form-text text-muted">Hanya diisi jika tidak berhasil IMD &lt; 1 jam.</small>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="font-weight-bold text-gray-700">Kondisi Bayi Saat Lahir <span class="text-danger">*</span></label>
                                        <select name="kondisi_bayi" class="form-control" required>
                                            <option value="normal" {{ old('kondisi_bayi', 'normal') == 'normal' ? 'selected' : '' }}>Normal / Bugar</option>
                                            <option value="asfiksia_pucat_biru_lemas" {{ old('kondisi_bayi') == 'asfiksia_pucat_biru_lemas' ? 'selected' : '' }}>Asfiksia (Pucat / Biru / Lemas)</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- 4. TINDAKAN RESUSITASI --}}
                                <h6 class="font-weight-bold text-primary mt-4 mb-3">
                                    <i class="fas fa-first-aid mr-1"></i> 4. Tindakan Resusitasi
                                </h6>
                                <div class="row">
                                    <div class="col-12 form-group">
                                        <label class="font-weight-bold text-gray-700 mb-2">Langkah / Tindakan Resusitasi yang Dilakukan:</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            <div class="custom-control custom-checkbox mr-3 mb-2">
                                                <input type="checkbox" name="tindakan_resusitasi[]" value="mengeringkan" class="custom-control-input" id="res_kering_tp" checked>
                                                <label class="custom-control-label" for="res_kering_tp">Mengeringkan</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-3 mb-2">
                                                <input type="checkbox" name="tindakan_resusitasi[]" value="menghangatkan" class="custom-control-input" id="res_hangat_tp" checked>
                                                <label class="custom-control-label" for="res_hangat_tp">Menghangatkan</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-3 mb-2">
                                                <input type="checkbox" name="tindakan_resusitasi[]" value="rangsang_aktif" class="custom-control-input" id="res_rangsang_tp" checked>
                                                <label class="custom-control-label" for="res_rangsang_tp">Rangsang Aktif / Taktil</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-3 mb-2">
                                                <input type="checkbox" name="tindakan_resusitasi[]" value="bebaskan_jalan_napas" class="custom-control-input" id="res_napas_tp">
                                                <label class="custom-control-label" for="res_napas_tp">Bebaskan Jalan Napas (Isap Lendir)</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-3 mb-2">
                                                <input type="checkbox" name="tindakan_resusitasi[]" value="ventilasi_tekanan_positif" class="custom-control-input" id="res_vtp_tp">
                                                <label class="custom-control-label" for="res_vtp_tp">Ventilasi Tekanan Positif (VTP)</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-3 mb-2">
                                                <input type="checkbox" name="tindakan_resusitasi[]" value="kompresi_dada" class="custom-control-input" id="res_kompresi_tp">
                                                <label class="custom-control-label" for="res_kompresi_tp">Kompresi Dada</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 form-group">
                                        <label class="font-weight-bold text-gray-700">Tindakan Resusitasi Lainnya (Jika ada):</label>
                                        <input type="text" name="tindakan_resusitasi_lainnya" class="form-control" placeholder="Contoh: Pemberian Oksigen 2 L/m, Sungkup O2, dll" value="{{ old('tindakan_resusitasi_lainnya') }}">
                                    </div>
                                </div>

                                {{-- 5. CATATAN MEDIS & ASUHAN --}}
                                <h6 class="font-weight-bold text-primary mt-4 mb-3">
                                    <i class="fas fa-notes-medical mr-1"></i> 5. Catatan Medis, Penatalaksanaan, & Evaluasi
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-gray-700">Cacat Bawaan (Jika Ada)</label>
                                        <input type="text" name="cacat_bawaan" class="form-control" placeholder="Kosongkan jika tidak ada cacat bawaan" value="{{ old('cacat_bawaan') }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-gray-700">Masalah Lain</label>
                                        <input type="text" name="masalah_lain" class="form-control" placeholder="Masalah lain pada bayi jika ada" value="{{ old('masalah_lain') }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-gray-700">Penatalaksanaan Segera</label>
                                        <textarea name="penatalaksanaan" class="form-control" rows="3" placeholder="Contoh: IMD < 1 jam, profilaksis salep mata, injeksi Vit K1 1 mg IM paha kiri anterolateral, imunisasi Hepatitis B0 paha kanan">{{ old('penatalaksanaan', 'Inisiasi Menyusu Dini (IMD) < 1 jam, profilaksis salep mata antibiotik, injeksi Vitamin K1 1 mg IM pada paha kiri anterolateral, dan imunisasi Hepatitis B (HB0) pada paha kanan setelah 1-2 jam pasca lahir.') }}</textarea>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-gray-700">Hasil Evaluasi</label>
                                        <textarea name="hasil_evaluasi" class="form-control" rows="3" placeholder="Contoh: Bayi bugar, menangis kuat, gerakan aktif, warna kemerahan, tanda vital stabil">{{ old('hasil_evaluasi', 'Bayi bugar, menangis kuat, gerakan aktif, warna kulit kemerahan, refleks hisap dan menelan baik, tanda-tanda vital stabil.') }}</textarea>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold">
                                        <i class="fas fa-save mr-1"></i> Simpan Data Bayi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Riwayat Bayi Lahir Pasien Ini -->
                    <h6 class="font-weight-bold text-gray-800 mb-2">
                        <i class="fas fa-list-alt mr-1"></i> Riwayat Bayi Baru Lahir Terdaftar (Total: {{ $data_bayi->count() }})
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover text-center align-middle" style="font-size: 11px;">
                            <thead class="bg-light text-gray-700">
                                <tr>
                                    <th>Urutan</th>
                                    <th>Kelamin</th>
                                    <th>BB / PB</th>
                                    <th>APGAR (1'/5'/10')</th>
                                    <th>IMD &lt; 1 Jam</th>
                                    <th>Kondisi Lahir</th>
                                    <th>Resusitasi</th>
                                    <th>Waktu Dicatat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data_bayi as $bayi)
                                    <tr>
                                        <td>Bayi Ke-{{ $bayi->urutan_kelahiran }}</td>
                                        <td>
                                            @if($bayi->jenis_kelamin == 'L')
                                                <span class="badge badge-primary">Laki-laki</span>
                                            @else
                                                <span class="badge badge-danger" style="background:#ec4899;">Perempuan</span>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold">{{ number_format($bayi->berat_badan_gram, 0, ',', '.') }} gr / {{ $bayi->panjang_badan_cm }} cm</td>
                                        <td>
                                            <span class="badge badge-light border">{{ $bayi->apgar_menit_1 ?? '-' }}</span> /
                                            <span class="badge badge-light border">{{ $bayi->apgar_menit_5 ?? '-' }}</span> /
                                            <span class="badge badge-light border">{{ $bayi->apgar_menit_10 ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if($bayi->pemberian_asi_kurang_1jam)
                                                <span class="badge badge-success">Ya (IMD)</span>
                                            @else
                                                <span class="badge badge-danger">Tidak</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $bayi->kondisi_bayi == 'normal' ? 'success' : 'danger' }}">
                                                {{ ucfirst(str_replace('_', ' ', $bayi->kondisi_bayi)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $bayi->tindakan_resusitasi ? Str::limit($bayi->tindakan_resusitasi, 25) : '-' }}</small>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($bayi->created_at)->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <form action="{{ route('bayi-baru-lahir.destroy', $bayi->id) }}" method="POST" onsubmit="return confirm('Hapus data rekam medis bayi ini?')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="redirect_to" value="input_terpadu">
                                                <input type="hidden" name="pasien_id" value="{{ $pasien->id }}">
                                                <button type="submit" class="btn btn-outline-danger btn-sm p-0 px-1" title="Hapus">
                                                    <i class="fas fa-trash fa-xs"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-3">Belum ada data bayi baru lahir tercatat untuk pasien ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>


        {{-- ========================================================================= --}}
        {{-- TAB 5: PEMANTAUAN KALA IV                                                 --}}
        {{-- ========================================================================= --}}
        <div class="tab-pane fade {{ $active_tab === 'kala4' ? 'show active' : '' }}" id="tab-kala4" role="tabpanel" aria-labelledby="tab-kala4-btn">
            
            <!-- Switch Mode: Form vs iFrame Master -->
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div class="section-title m-0">
                    Formulir 5: Lembar Pemantauan Kala IV (2 Jam Pasca Persalinan)
                </div>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-primary mode-switch-pill" onclick="$('#view-form-kala4').show(); $('#view-iframe-kala4').hide(); $(this).addClass('btn-primary').removeClass('btn-outline-primary'); $(this).siblings().removeClass('btn-primary').addClass('btn-outline-primary');">
                        <i class="fas fa-edit mr-1"></i> Formulir Identik Master
                    </button>
                    <button type="button" class="btn btn-outline-primary mode-switch-pill" onclick="$('#view-form-kala4').hide(); $('#view-iframe-kala4').show(); $(this).addClass('btn-primary').removeClass('btn-outline-primary'); $(this).siblings().removeClass('btn-primary').addClass('btn-outline-primary');">
                        <i class="fas fa-window-maximize mr-1"></i> Tampilan Master (iFrame)
                    </button>
                </div>
            </div>

            <!-- View iFrame Kala IV -->
            <div id="view-iframe-kala4" style="display: none;" class="mb-4">
                <div class="alert alert-light border small py-2 mb-2 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-info-circle text-info mr-1"></i> Menyematkan Master Pemantauan Kala IV langsung via iFrame:</span>
                    <a href="{{ route('pemantauan-kala-4.index') }}" target="_blank" class="btn btn-sm btn-outline-info py-0">Buka Tab Penuh</a>
                </div>
                <iframe src="{{ route('pemantauan-kala-4.index') }}?embedded=1" style="width: 100%; height: 750px; border: 1px solid #cbd5e1; border-radius: 8px;"></iframe>
            </div>

            <!-- View Form Kala IV (Identik 100% modal master) -->
            <div id="view-form-kala4">
                @if(! $persalinan)
                    <div class="alert alert-warning border-left-warning shadow-sm">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> Silakan lengkapi <strong>Tab 1. Admisi Persalinan</strong> terlebih dahulu sebelum mencatat observasi Kala IV.
                    </div>
                @else
                    <!-- Form Input Titik Pemantauan Baru -->
                    <div class="card shadow-sm border-0 mb-4 bg-light">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-procedures mr-2"></i> Tambah Observasi Pemantauan Kala IV
                            </h6>
                            <span class="badge badge-primary font-weight-normal px-2 py-1">Observasi Ke-{{ $data_kala4->count() + 1 }}</span>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('pemantauan-kala-4.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="persalinan_id" value="{{ $persalinan->id }}">
                                <input type="hidden" name="pasien_id" value="{{ $pasien->id }}">
                                <input type="hidden" name="redirect_to" value="input_terpadu">

                                {{-- 1. IDENTITAS IBU & WAKTU --}}
                                <h6 class="font-weight-bold text-primary mb-3">
                                    <i class="fas fa-user-injured mr-1"></i> 1. Pasien & Periode Waktu Observasi
                                </h6>
                                <div class="row">
                                    <div class="col-md-5 form-group">
                                        <label class="font-weight-bold text-gray-700">Pasien Terkait</label>
                                        <input type="text" class="form-control bg-white" readonly
                                            value="{{ ($pasien->nomor_register ?? 'REG-' . $pasien->id) . ' - ' . $pasien->nama_ibu }}">
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label class="font-weight-bold text-gray-700">Jam Ke- <span class="text-danger">*</span></label>
                                        <select name="jam_ke" class="form-control" required>
                                            <option value="1" {{ old('jam_ke', $data_kala4->count() >= 4 ? '2' : '1') == '1' ? 'selected' : '' }}>Jam Ke-1 (15 mnt)</option>
                                            <option value="2" {{ old('jam_ke', $data_kala4->count() >= 4 ? '2' : '1') == '2' ? 'selected' : '' }}>Jam Ke-2 (30 mnt)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label class="font-weight-bold text-gray-700">Interval Ke- <span class="text-danger">*</span></label>
                                        @php
                                            $defaultPKe = ($data_kala4->count() % 4) + 1;
                                        @endphp
                                        <select name="pemantauan_ke" class="form-control" required>
                                            <option value="1" {{ old('pemantauan_ke', $defaultPKe) == 1 ? 'selected' : '' }}>Ke-1</option>
                                            <option value="2" {{ old('pemantauan_ke', $defaultPKe) == 2 ? 'selected' : '' }}>Ke-2</option>
                                            <option value="3" {{ old('pemantauan_ke', $defaultPKe) == 3 ? 'selected' : '' }}>Ke-3</option>
                                            <option value="4" {{ old('pemantauan_ke', $defaultPKe) == 4 ? 'selected' : '' }}>Ke-4</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">Waktu Pemantauan (Jam:Menit) <span class="text-danger">*</span></label>
                                        <input type="time" name="waktu_pemantauan" class="form-control" value="{{ old('waktu_pemantauan', date('H:i')) }}" required>
                                    </div>
                                </div>

                                {{-- 2. TANDA VITAL --}}
                                <h6 class="font-weight-bold text-primary mt-3 mb-3">
                                    <i class="fas fa-heartbeat mr-1"></i> 2. Pemeriksaan Tanda Vital
                                </h6>
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">TD Sistolik <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="tekanan_darah_sistolik" class="form-control" min="50" max="250" placeholder="120" value="{{ old('tekanan_darah_sistolik', 120) }}" required>
                                            <div class="input-group-append"><span class="input-group-text">mmHg</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">TD Diastolik <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="tekanan_darah_diastolik" class="form-control" min="30" max="150" placeholder="80" value="{{ old('tekanan_darah_diastolik', 80) }}" required>
                                            <div class="input-group-append"><span class="input-group-text">mmHg</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">Nadi <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="nadi_per_menit" class="form-control" min="40" max="180" placeholder="80" value="{{ old('nadi_per_menit', 80) }}" required>
                                            <div class="input-group-append"><span class="input-group-text">x/mnt</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold text-gray-700">Suhu Tubuh <small class="text-muted">(1x tiap jam)</small></label>
                                        <div class="input-group">
                                            <input type="number" step="0.1" name="suhu_tubuh" class="form-control" min="35" max="42" placeholder="36.8" value="{{ old('suhu_tubuh', 36.8) }}">
                                            <div class="input-group-append"><span class="input-group-text">°C</span></div>
                                        </div>
                                        <small class="text-muted">Boleh dikosongkan jika tidak diukur.</small>
                                    </div>
                                </div>

                                {{-- 3. EVALUASI UTERUS & KEMIH & PERDARAHAN --}}
                                <h6 class="font-weight-bold text-primary mt-3 mb-3">
                                    <i class="fas fa-hand-holding-medical mr-1"></i> 3. Evaluasi Uterus, Kandung Kemih, & Perdarahan
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-gray-700">Tinggi Fundus Uteri (TFU) <span class="text-danger">*</span></label>
                                        <input type="text" name="tinggi_fundus_uteri" class="form-control" list="listTfuInputTerpadu" placeholder="Contoh: 2 jari di bawah pusat" value="{{ old('tinggi_fundus_uteri', '2 jari di bawah pusat') }}" required>
                                        <datalist id="listTfuInputTerpadu">
                                            <option value="2 jari di bawah pusat">
                                            <option value="Sepusat">
                                            <option value="1 jari di bawah pusat">
                                            <option value="3 jari di bawah pusat">
                                            <option value="Pertengahan pusat-simfisis">
                                        </datalist>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-gray-700">Kontraksi Uterus <span class="text-danger">*</span></label>
                                        <select name="kontraksi_uterus" class="form-control" required>
                                            <option value="Keras" {{ old('kontraksi_uterus', 'Keras') == 'Keras' ? 'selected' : '' }}>Keras (Baik / Normal)</option>
                                            <option value="Lembek" {{ old('kontraksi_uterus') == 'Lembek' ? 'selected' : '' }}>Lembek (Atonia / Waspada)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-gray-700">Kandung Kemih <span class="text-danger">*</span></label>
                                        <select name="kandung_kemih" class="form-control" required>
                                            <option value="Kosong" {{ old('kandung_kemih', 'Kosong') == 'Kosong' ? 'selected' : '' }}>Kosong</option>
                                            <option value="Penuh" {{ old('kandung_kemih') == 'Penuh' ? 'selected' : '' }}>Penuh (Segera Kosongkan)</option>
                                            <option value="Kurang Penuh" {{ old('kandung_kemih') == 'Kurang Penuh' ? 'selected' : '' }}>Kurang Penuh</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-gray-700">Perdarahan (ml)</label>
                                        <div class="input-group">
                                            <input type="number" name="perdarahan_ml" class="form-control" min="0" max="3000" placeholder="0" value="{{ old('perdarahan_ml', 0) }}">
                                            <div class="input-group-append"><span class="input-group-text">ml</span></div>
                                        </div>
                                        <small class="text-muted">Isi 0 jika hanya bekuan darah tipis/normal.</small>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold">
                                        <i class="fas fa-save mr-1"></i> Simpan Observasi Kala IV
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Riwayat Kala 4 Pasien Ini -->
                    <h6 class="font-weight-bold text-gray-800 mb-2">
                        <i class="fas fa-list-ol mr-1"></i> Riwayat Observasi Kala IV Pasien Ini (Total: {{ $data_kala4->count() }} Kali)
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover text-center align-middle" style="font-size: 11px;">
                            <thead class="bg-light text-gray-700">
                                <tr>
                                    <th>Jam Ke</th>
                                    <th>Ke-</th>
                                    <th>Waktu</th>
                                    <th>Tekanan Darah</th>
                                    <th>Nadi</th>
                                    <th>Suhu</th>
                                    <th>TFU</th>
                                    <th>Kontraksi</th>
                                    <th>Kandung Kemih</th>
                                    <th>Darah (ml)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data_kala4 as $k4)
                                    @php
                                        $waktuFormatted = substr($k4->waktu_pemantauan, 0, 5);
                                    @endphp
                                    <tr>
                                        <td>
                                            @if($k4->jam_ke == 1)
                                                <span class="badge badge-primary px-2 py-1">Jam Ke-1 (15')</span>
                                            @else
                                                <span class="badge badge-info px-2 py-1">Jam Ke-2 (30')</span>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold">{{ $k4->pemantauan_ke }}</td>
                                        <td><strong>{{ $waktuFormatted }} WIB</strong></td>
                                        <td class="font-weight-bold text-primary">{{ $k4->tekanan_darah_sistolik }}/{{ $k4->tekanan_darah_diastolik }} mmHg</td>
                                        <td>{{ $k4->nadi_per_menit }} x/m</td>
                                        <td>{{ $k4->suhu_tubuh ? $k4->suhu_tubuh . '°C' : '-' }}</td>
                                        <td>{{ $k4->tinggi_fundus_uteri }}</td>
                                        <td>
                                            @if(str_contains(strtolower($k4->kontraksi_uterus), 'keras') || str_contains(strtolower($k4->kontraksi_uterus), 'baik'))
                                                <span class="badge badge-success">{{ $k4->kontraksi_uterus }}</span>
                                            @else
                                                <span class="badge badge-warning text-dark">{{ $k4->kontraksi_uterus }}</span>
                                            @endif
                                        </td>
                                        <td><span class="badge badge-light border">{{ $k4->kandung_kemih }}</span></td>
                                        <td class="{{ $k4->perdarahan_ml > 200 ? 'text-danger font-weight-bold' : '' }}">{{ $k4->perdarahan_ml }} ml</td>
                                        <td>
                                            <form action="{{ route('pemantauan-kala-4.destroy', $k4->id) }}" method="POST" onsubmit="return confirm('Hapus titik pemantauan Kala IV ini?')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="redirect_to" value="input_terpadu">
                                                <input type="hidden" name="pasien_id" value="{{ $pasien->id }}">
                                                <button type="submit" class="btn btn-outline-danger btn-sm p-0 px-1" title="Hapus">
                                                    <i class="fas fa-trash fa-xs"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-3">Belum ada rekaman pemantauan Kala IV untuk pasien ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>


        {{-- ========================================================================= --}}
        {{-- TAB 6: EDUKASI & KIE                                                      --}}
        {{-- ========================================================================= --}}
        <div class="tab-pane fade {{ $active_tab === 'edukasi' ? 'show active' : '' }}" id="tab-edukasi" role="tabpanel" aria-labelledby="tab-edukasi-btn">
            
            <!-- Switch Mode: Form vs iFrame Master -->
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div class="section-title m-0">
                    Formulir 6: Komunikasi, Informasi, & Edukasi (KIE) Masa Nifas
                </div>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-primary mode-switch-pill" onclick="$('#view-form-edukasi').show(); $('#view-iframe-edukasi').hide(); $(this).addClass('btn-primary').removeClass('btn-outline-primary'); $(this).siblings().removeClass('btn-primary').addClass('btn-outline-primary');">
                        <i class="fas fa-edit mr-1"></i> Formulir Identik Master
                    </button>
                    <button type="button" class="btn btn-outline-primary mode-switch-pill" onclick="$('#view-form-edukasi').hide(); $('#view-iframe-edukasi').show(); $(this).addClass('btn-primary').removeClass('btn-outline-primary'); $(this).siblings().removeClass('btn-primary').addClass('btn-outline-primary');">
                        <i class="fas fa-window-maximize mr-1"></i> Tampilan Master (iFrame)
                    </button>
                </div>
            </div>

            <!-- View iFrame Edukasi -->
            <div id="view-iframe-edukasi" style="display: none;" class="mb-4">
                <div class="alert alert-light border small py-2 mb-2 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-info-circle text-info mr-1"></i> Menyematkan Master Edukasi KIE langsung via iFrame:</span>
                    <a href="{{ route('edukasi-kie.index') }}" target="_blank" class="btn btn-sm btn-outline-info py-0">Buka Tab Penuh</a>
                </div>
                <iframe src="{{ route('edukasi-kie.index') }}?embedded=1" style="width: 100%; height: 750px; border: 1px solid #cbd5e1; border-radius: 8px;"></iframe>
            </div>

            <!-- View Form Edukasi (Identik 100% modal master) -->
            <div id="view-form-edukasi">
                @if(! $persalinan)
                    <div class="alert alert-warning border-left-warning shadow-sm">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> Silakan lengkapi <strong>Tab 1. Admisi Persalinan</strong> terlebih dahulu sebelum mencatat konseling KIE.
                    </div>
                @else
                    <!-- Form Input Sesi Edukasi Baru -->
                    <div class="card shadow-sm border-0 mb-4 bg-light">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-comments mr-2"></i> Tambah Dokumentasi Edukasi & Konseling KIE
                            </h6>
                            <span class="badge badge-success font-weight-normal px-2 py-1">Sesi Konseling</span>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('edukasi-kie.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="persalinan_id" value="{{ $persalinan->id }}">
                                <input type="hidden" name="pasien_id" value="{{ $pasien->id }}">
                                <input type="hidden" name="redirect_to" value="input_terpadu">

                                {{-- 1. IDENTITAS IBU & WAKTU --}}
                                <h6 class="font-weight-bold text-primary mb-3">
                                    <i class="fas fa-user-injured mr-1"></i> 1. Identitas Pasien & Waktu Edukasi
                                </h6>
                                <div class="row">
                                    <div class="col-md-7 form-group">
                                        <label class="font-weight-bold text-gray-700">Pasien Terkait</label>
                                        <input type="text" class="form-control bg-white" readonly
                                            value="{{ ($pasien->nomor_register ?? 'REG-' . $pasien->id) . ' - ' . $pasien->nama_ibu }}">
                                    </div>
                                    <div class="col-md-5 form-group">
                                        <label class="font-weight-bold text-gray-700">Tanggal Pelaksanaan KIE <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_edukasi" class="form-control" value="{{ old('tanggal_edukasi', date('Y-m-d')) }}" required>
                                    </div>
                                </div>

                                {{-- 2. MATERI & PELAKSANA --}}
                                <h6 class="font-weight-bold text-primary mt-3 mb-3">
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
                                        <input type="text" name="pelaksana" class="form-control" placeholder="Nama Bidan / Konselor KIE" value="{{ old('pelaksana', $persalinan->nama_penolong ?? 'Bidan Jaga') }}" required>
                                    </div>
                                    <div class="col-12 form-group">
                                        <label class="font-weight-bold text-gray-700">Catatan Konseling & Respon Pasien / Keluarga</label>
                                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Pasien memahami teknik menyusui yang benar, perlekatan bayi sudah tepat, puting tidak lecet. Keluarga mendukung pemberian ASI eksklusif.">{{ old('keterangan') }}</textarea>
                                        <small class="form-text text-muted">Tuliskan pemahaman pasien, respon keluarga, atau rencana tindak lanjut.</small>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold">
                                        <i class="fas fa-save mr-1"></i> Simpan Catatan Edukasi KIE
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Riwayat Edukasi Pasien Ini -->
                    @php
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
                    <h6 class="font-weight-bold text-gray-800 mb-2">
                        <i class="fas fa-history mr-1"></i> Catatan Sesi KIE yang Diberikan (Total: {{ $data_edukasi->count() }})
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center align-middle" style="font-size: 12px;">
                            <thead class="bg-light text-gray-700">
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th>Tanggal Pelaksanaan</th>
                                    <th>Materi Edukasi</th>
                                    <th>Petugas Pelaksana</th>
                                    <th>Catatan Konseling / Respon</th>
                                    <th style="width: 70px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data_edukasi as $edu)
                                    @php
                                        $mInfo = $materiMap[$edu->materi_edukasi] ?? ['label' => ucfirst(str_replace('_', ' ', $edu->materi_edukasi)), 'color' => 'primary', 'icon' => 'fa-check'];
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="font-weight-bold">
                                            <i class="far fa-calendar-alt text-primary mr-1"></i>
                                            {{ \Carbon\Carbon::parse($edu->tanggal_edukasi)->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $mInfo['color'] }} px-2 py-1 font-weight-bold">
                                                <i class="fas {{ $mInfo['icon'] }} mr-1"></i>{{ $mInfo['label'] }}
                                            </span>
                                        </td>
                                        <td><i class="fas fa-user-nurse text-success mr-1"></i>{{ $edu->pelaksana }}</td>
                                        <td class="text-left">{{ $edu->keterangan ?: '-' }}</td>
                                        <td>
                                            <form action="{{ route('edukasi-kie.destroy', $edu->id) }}" method="POST" onsubmit="return confirm('Hapus catatan sesi edukasi ini?')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="redirect_to" value="input_terpadu">
                                                <input type="hidden" name="pasien_id" value="{{ $pasien->id }}">
                                                <button type="submit" class="btn btn-outline-danger btn-sm p-1" title="Hapus Data">
                                                    <i class="fas fa-trash fa-sm"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">Belum ada catatan sesi edukasi KIE untuk pasien ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Otomatis aktifkan tab berdasarkan URL query param ?tab=xxx jika ada
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        if (tabParam) {
            const targetTabBtn = $('#tab-' + tabParam + '-btn');
            if (targetTabBtn.length) {
                targetTabBtn.tab('show');
            }
        }

        // Simpan hash/tab saat berpindah tab ke URL tanpa reload
        $('#clinicalFormTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            const targetId = $(e.target).attr('href').replace('#tab-', '');
            const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + targetId;
            window.history.replaceState({path: newUrl}, '', newUrl);
        });
    });
</script>
@endpush
