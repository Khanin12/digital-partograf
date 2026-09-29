@extends('layouts.master')
@section('title', 'Data Persalinan')

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
        title="Data Persalinan"
        subtitle="Master data admisi persalinan, riwayat obstetri, dan catatan masuk"
        icon="fas fa-heartbeat"
    >
        <button type="button" class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modaltambahdatapersalinan">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Data Persalinan
        </button>
    </x-page-header>

    <!-- Ringkasan Statistik -->
    <div class="row">
        <x-stat-card
            title="Total Persalinan"
            :value="count($data_persalinan)"
            icon="fas fa-clipboard-list"
            color="primary"
            subtitle="Seluruh data admisi"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="Dalam Proses"
            :value="collect($data_persalinan)->where('status_persalinan', 'proses')->count()"
            icon="fas fa-hourglass-half"
            color="warning"
            subtitle="Sedang dalam pemantauan"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="Persalinan Selesai"
            :value="collect($data_persalinan)->where('status_persalinan', 'selesai')->count()"
            icon="fas fa-check-circle"
            color="success"
            subtitle="Ibu & bayi telah lahir"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="Kasus Rujukan"
            :value="collect($data_persalinan)->where('status_persalinan', 'rujuk')->count()"
            icon="fas fa-ambulance"
            color="danger"
            subtitle="Dirujuk ke faskes lanjutan"
            col="col-xl-3 col-md-6 mb-4"
        />
    </div>

    <!-- Card Tabel Data Persalinan -->
    <x-card>
        <x-slot:actions>
            <x-badge color="primary" pill>
                Total: {{ count($data_persalinan) }} Data
            </x-badge>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table table-bordered table-hover text-nowrap align-middle" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-light text-gray-700">
                    <tr class="text-center small font-weight-bold text-uppercase">
                        <th style="width: 45px;">No</th>
                        <th>Pasien / No. Register</th>
                        <th>Obstetri (GPA)</th>
                        <th>Usia Hamil</th>
                        <th>Fasilitas & Tempat</th>
                        <th>Waktu Masuk</th>
                        <th>Ketuban Pecah</th>
                        <th>Mules Mulai</th>
                        <th>Penolong</th>
                        <th>Asupan Terakhir</th>
                        <th>Status</th>
                        <th style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data_persalinan as $data)
                        <tr>
                            <td class="text-center align-middle font-weight-bold text-gray-600">
                                {{ $loop->iteration }}
                            </td>
                            <td class="align-middle">
                                <div class="font-weight-bold text-primary">
                                    {{ $data->nama_pasien ?? ($data->nama_ibu ?? 'Pasien #' . $data->pasien_id) }}
                                </div>
                                <div class="small text-muted">
                                    <span class="badge badge-light border">{{ $data->nomor_register ?? ($data->nomer_registerer ?? 'ID #' . $data->pasien_id) }}</span>
                                    @if(!empty($data->nama_suami))
                                        <span class="ml-1">Suami: {{ $data->nama_suami }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="align-middle text-center">
                                <span class="badge badge-info px-2 py-1 font-weight-bold" title="Gravida {{ $data->gravida }}, Para {{ $data->para }}, Abortus {{ $data->abortus }}">
                                    G{{ $data->gravida }} P{{ $data->para }} A{{ $data->abortus }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-light border">{{ $data->usia_kehamilan_minggu }} mgg</span>
                            </td>
                            <td class="align-middle small">
                                <div class="font-weight-bold text-gray-800">{{ $data->nama_fasilitas }}</div>
                                @php
                                    $tempatLabels = [
                                        'rumah_ibu' => 'Rumah Ibu',
                                        'puskesmas' => 'Puskesmas',
                                        'klinik_swasta' => 'Klinik Swasta',
                                        'rumah_sakit' => 'Rumah Sakit',
                                        'lainnya' => 'Lainnya'
                                    ];
                                @endphp
                                <span class="text-muted">{{ $tempatLabels[$data->tempat_persalinan] ?? ucfirst($data->tempat_persalinan) }}</span>
                            </td>
                            <td class="text-center align-middle small text-gray-700">
                                {{ $data->waktu_masuk ? \Carbon\Carbon::parse($data->waktu_masuk)->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="text-center align-middle small text-gray-700">
                                {{ $data->waktu_ketuban_pecah ? \Carbon\Carbon::parse($data->waktu_ketuban_pecah)->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="text-center align-middle small text-gray-700">
                                {{ $data->waktu_mules_mulai ? \Carbon\Carbon::parse($data->waktu_mules_mulai)->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="align-middle small text-gray-700">
                                <i class="fas fa-user-nurse mr-1 text-primary"></i> {{ $data->nama_penolong }}
                            </td>
                            <td class="align-middle small">
                                <div>
                                    <i class="fas fa-utensils text-muted mr-1"></i>
                                    <strong>Mkn:</strong> {{ $data->waktu_makan_terakhir ? \Carbon\Carbon::parse($data->waktu_makan_terakhir)->format('H:i') : '-' }}
                                    @if($data->porsi_makan) ({{ $data->porsi_makan }}) @endif
                                </div>
                                <div>
                                    <i class="fas fa-glass-whiskey text-muted mr-1"></i>
                                    <strong>Mnm:</strong> {{ $data->waktu_minum_terakhir ? \Carbon\Carbon::parse($data->waktu_minum_terakhir)->format('H:i') : '-' }}
                                    @if($data->porsi_minum) ({{ $data->porsi_minum }}) @endif
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                @php
                                    $statusBadge = match($data->status_persalinan) {
                                        'proses' => ['color' => 'warning', 'label' => 'Dalam Proses', 'icon' => 'fa-hourglass-half'],
                                        'selesai' => ['color' => 'success', 'label' => 'Selesai', 'icon' => 'fa-check-circle'],
                                        'rujuk' => ['color' => 'danger', 'label' => 'Rujuk', 'icon' => 'fa-ambulance'],
                                        'batal' => ['color' => 'secondary', 'label' => 'Batal', 'icon' => 'fa-times-circle'],
                                        default => ['color' => 'secondary', 'label' => ucfirst($data->status_persalinan), 'icon' => 'fa-info-circle']
                                    };
                                @endphp
                                <span class="badge badge-{{ $statusBadge['color'] }} px-2 py-1 shadow-sm">
                                    <i class="fas {{ $statusBadge['icon'] }} mr-1"></i> {{ $statusBadge['label'] }}
                                </span>
                            </td>
                            <td class="text-center align-middle text-nowrap">
                                <div class="d-inline-flex align-items-center justify-content-center">
                                    <a href="{{ route('partograf.cetak', $data->id) }}" target="_blank" class="btn btn-success btn-sm shadow-sm mr-1" title="Cetak Lembar Partograf Resmi (PDF 2 Halaman)">
                                        <i class="fas fa-print fa-sm mr-1"></i> Cetak
                                    </a>
                                    <button type="button" class="btn btn-warning btn-sm shadow-sm mr-1" data-toggle="modal"
                                        data-target="#modalEditPersalinan{{ $data->id }}" title="Edit Data">
                                        <i class="fas fa-edit fa-sm mr-1"></i> Edit
                                    </button>
                                    <form action="{{ route('persalinan.destroy', $data->id) }}" method="POST"
                                        class="d-inline m-0 p-0"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data persalinan pasien {{ $data->nama_pasien ?? $data->nama_ibu }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm shadow-sm" title="Hapus Data">
                                            <i class="fas fa-trash fa-sm mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-3x mb-2 text-gray-300 d-block"></i>
                                Belum ada data admisi persalinan. Klik tombol <strong>Tambah Data Persalinan</strong> untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>


    {{-- ========================================================================= --}}
    {{-- MODAL EDIT DATA PERSALINAN (PER DATA)                                     --}}
    {{-- ========================================================================= --}}
    @foreach ($data_persalinan as $data)
        <div class="modal fade text-left" id="modalEditPersalinan{{ $data->id }}" tabindex="-1"
            role="dialog" aria-labelledby="modalEditLabel{{ $data->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered" role="document">
                <div class="modal-content shadow border-0">
                    <form action="{{ route('persalinan.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header bg-warning text-dark py-3">
                            <h5 class="modal-title font-weight-bold" id="modalEditLabel{{ $data->id }}">
                                <i class="fas fa-edit mr-2"></i> Edit Data Persalinan: {{ $data->nama_pasien ?? ($data->nama_ibu ?? 'ID: ' . $data->pasien_id) }}
                            </h5>
                            <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body p-4">
                            <!-- Seksi 1: Data Pasien & GPA -->
                            <h6 class="section-header font-weight-bold text-primary mb-3">
                                <i class="fas fa-user-injured mr-1"></i> 1. Data Pasien & Riwayat Obstetri
                            </h6>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold text-gray-700">Pasien Terdaftar</label>
                                    <input type="text" class="form-control bg-light"
                                        value="{{ ($data->nomor_register ?? ($data->nomer_registerer ?? 'REG-' . $data->pasien_id)) . ' - ' . ($data->nama_pasien ?? ($data->nama_ibu ?? 'ID: ' . $data->pasien_id)) }}"
                                        readonly>
                                    <input type="hidden" name="pasien_id" value="{{ $data->pasien_id }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold text-gray-700">Usia Kehamilan (Minggu) <span class="text-danger">*</span></label>
                                    <input type="number" name="usia_kehamilan_minggu" class="form-control" min="10" max="50"
                                        value="{{ old('usia_kehamilan_minggu', $data->usia_kehamilan_minggu) }}" required>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold text-gray-700">Gravida (G) <span class="text-danger">*</span></label>
                                    <input type="number" name="gravida" class="form-control" min="1" max="30"
                                        value="{{ old('gravida', $data->gravida) }}" required>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold text-gray-700">Para (P) <span class="text-danger">*</span></label>
                                    <input type="number" name="para" class="form-control" min="0" max="30"
                                        value="{{ old('para', $data->para) }}" required>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold text-gray-700">Abortus (A) <span class="text-danger">*</span></label>
                                    <input type="number" name="abortus" class="form-control" min="0" max="30"
                                        value="{{ old('abortus', $data->abortus) }}" required>
                                </div>
                            </div>

                            <!-- Seksi 2: Fasilitas & Penolong -->
                            <h6 class="section-header font-weight-bold text-primary mt-3 mb-3">
                                <i class="fas fa-hospital mr-1"></i> 2. Tempat & Penolong Medis
                            </h6>
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold text-gray-700">Nama Fasilitas Kesehatan <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_fasilitas" class="form-control"
                                        value="{{ old('nama_fasilitas', $data->nama_fasilitas) }}" required>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold text-gray-700">Tempat Persalinan <span class="text-danger">*</span></label>
                                    <select name="tempat_persalinan" class="form-control" required>
                                        @foreach(['puskesmas' => 'Puskesmas', 'rumah_ibu' => 'Rumah Ibu', 'klinik_swasta' => 'Klinik Swasta', 'rumah_sakit' => 'Rumah Sakit', 'lainnya' => 'Lainnya'] as $val => $label)
                                            <option value="{{ $val }}" {{ old('tempat_persalinan', $data->tempat_persalinan) == $val ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold text-gray-700">Nama Penolong Persalinan <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_penolong" class="form-control"
                                        value="{{ old('nama_penolong', $data->nama_penolong) }}" required>
                                </div>
                            </div>

                            <!-- Seksi 3: Waktu Observasi & Status -->
                            <h6 class="section-header font-weight-bold text-primary mt-3 mb-3">
                                <i class="fas fa-clock mr-1"></i> 3. Waktu Observasi Awal & Status
                            </h6>
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label class="font-weight-bold text-gray-700">Waktu Masuk <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="waktu_masuk" class="form-control"
                                        value="{{ old('waktu_masuk', $data->waktu_masuk ? \Carbon\Carbon::parse($data->waktu_masuk)->format('Y-m-d\TH:i') : '') }}"
                                        required>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="font-weight-bold text-gray-700">Waktu Mules Mulai</label>
                                    <input type="datetime-local" name="waktu_mules_mulai" class="form-control"
                                        value="{{ old('waktu_mules_mulai', $data->waktu_mules_mulai ? \Carbon\Carbon::parse($data->waktu_mules_mulai)->format('Y-m-d\TH:i') : '') }}">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="font-weight-bold text-gray-700">Waktu Ketuban Pecah</label>
                                    <input type="datetime-local" name="waktu_ketuban_pecah" class="form-control"
                                        value="{{ old('waktu_ketuban_pecah', $data->waktu_ketuban_pecah ? \Carbon\Carbon::parse($data->waktu_ketuban_pecah)->format('Y-m-d\TH:i') : '') }}">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="font-weight-bold text-gray-700">Status Persalinan <span class="text-danger">*</span></label>
                                    <select name="status_persalinan" class="form-control" required>
                                        @foreach(['proses' => 'Dalam Proses', 'selesai' => 'Selesai', 'rujuk' => 'Rujuk', 'batal' => 'Batal'] as $val => $label)
                                            <option value="{{ $val }}" {{ old('status_persalinan', $data->status_persalinan) == $val ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Seksi 4: Asupan Nutrisi Terakhir (Footer Partograf) -->
                            <h6 class="section-header font-weight-bold text-primary mt-3 mb-3">
                                <i class="fas fa-utensils mr-1"></i> 4. Asupan Terakhir Ibu (Footer Partograf)
                            </h6>
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold text-gray-700">Waktu Makan Terakhir</label>
                                    <input type="datetime-local" name="waktu_makan_terakhir" class="form-control"
                                        value="{{ old('waktu_makan_terakhir', $data->waktu_makan_terakhir ? \Carbon\Carbon::parse($data->waktu_makan_terakhir)->format('Y-m-d\TH:i') : '') }}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold text-gray-700">Jenis Makanan</label>
                                    <input type="text" name="jenis_makan" class="form-control" placeholder="Contoh: Nasi, Sayur"
                                        value="{{ old('jenis_makan', $data->jenis_makan ?? '') }}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold text-gray-700">Porsi Makan</label>
                                    <input type="text" name="porsi_makan" class="form-control" placeholder="Contoh: 1 porsi habis"
                                        value="{{ old('porsi_makan', $data->porsi_makan) }}">
                                </div>

                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold text-gray-700">Waktu Minum Terakhir</label>
                                    <input type="datetime-local" name="waktu_minum_terakhir" class="form-control"
                                        value="{{ old('waktu_minum_terakhir', $data->waktu_minum_terakhir ? \Carbon\Carbon::parse($data->waktu_minum_terakhir)->format('Y-m-d\TH:i') : '') }}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold text-gray-700">Jenis Minuman</label>
                                    <input type="text" name="jenis_minum" class="form-control" placeholder="Contoh: Air putih, Teh manis"
                                        value="{{ old('jenis_minum', $data->jenis_minum ?? '') }}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold text-gray-700">Porsi Minum</label>
                                    <input type="text" name="porsi_minum" class="form-control" placeholder="Contoh: 1 gelas"
                                        value="{{ old('porsi_minum', $data->porsi_minum) }}">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                                <i class="fas fa-times mr-1"></i> Batal
                            </button>
                            <button type="submit" class="btn btn-warning btn-sm font-weight-bold shadow-sm">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach


    {{-- ========================================================================= --}}
    {{-- MODAL TAMBAH DATA PERSALINAN (DI LUAR LOOP)                               --}}
    {{-- ========================================================================= --}}
    <div class="modal fade" id="modaltambahdatapersalinan" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered" role="document">
            <div class="modal-content shadow border-0">
                <form action="{{ route('persalinan.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title font-weight-bold d-flex align-items-center" id="modalTambahLabel">
                            <i class="fas fa-plus-circle mr-2"></i> Tambah Data Persalinan Baru
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-4">
                        <!-- Seksi 1: Data Pasien & Riwayat Obstetri (GPA) -->
                        <h6 class="section-header font-weight-bold text-primary mb-3">
                            <i class="fas fa-user-injured mr-1"></i> 1. Data Pasien & Riwayat Obstetri
                        </h6>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Pilih Pasien Terdaftar <span class="text-danger">*</span></label>
                                <select name="pasien_id" class="form-control" required>
                                    <option value="">-- Pilih Pasien --</option>
                                    @foreach($data_pasien as $pasien)
                                        <option value="{{ $pasien->id }}" {{ old('pasien_id') == $pasien->id ? 'selected' : '' }}>
                                            {{ $pasien->nomor_register ?? ($pasien->nomer_registerer ?? 'ID: ' . $pasien->id) }} - {{ $pasien->nama_ibu }}
                                            @if(!empty($pasien->nama_suami)) (Suami: {{ $pasien->nama_suami }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Usia Kehamilan (Minggu) <span class="text-danger">*</span></label>
                                <input type="number" name="usia_kehamilan_minggu" class="form-control"
                                    placeholder="Contoh: 38" min="10" max="50" value="{{ old('usia_kehamilan_minggu') }}" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Gravida (G) <span class="text-danger">*</span></label>
                                <input type="number" name="gravida" class="form-control" min="1" max="30"
                                    value="{{ old('gravida', 1) }}" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Para (P) <span class="text-danger">*</span></label>
                                <input type="number" name="para" class="form-control" min="0" max="30"
                                    value="{{ old('para', 0) }}" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Abortus (A) <span class="text-danger">*</span></label>
                                <input type="number" name="abortus" class="form-control" min="0" max="30"
                                    value="{{ old('abortus', 0) }}" required>
                            </div>
                        </div>

                        <!-- Seksi 2: Fasilitas & Penolong -->
                        <h6 class="section-header font-weight-bold text-primary mt-3 mb-3">
                            <i class="fas fa-hospital mr-1"></i> 2. Tempat & Penolong Medis
                        </h6>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Nama Fasilitas Kesehatan <span class="text-danger">*</span></label>
                                <input type="text" name="nama_fasilitas" class="form-control"
                                    placeholder="Contoh: Puskesmas Sehat Sejahtera" value="{{ old('nama_fasilitas') }}" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Tempat Persalinan <span class="text-danger">*</span></label>
                                <select name="tempat_persalinan" class="form-control" required>
                                    <option value="puskesmas" {{ old('tempat_persalinan') == 'puskesmas' ? 'selected' : '' }}>Puskesmas</option>
                                    <option value="rumah_ibu" {{ old('tempat_persalinan') == 'rumah_ibu' ? 'selected' : '' }}>Rumah Ibu</option>
                                    <option value="klinik_swasta" {{ old('tempat_persalinan') == 'klinik_swasta' ? 'selected' : '' }}>Klinik Swasta</option>
                                    <option value="rumah_sakit" {{ old('tempat_persalinan') == 'rumah_sakit' ? 'selected' : '' }}>Rumah Sakit</option>
                                    <option value="lainnya" {{ old('tempat_persalinan') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Nama Penolong Persalinan <span class="text-danger">*</span></label>
                                <input type="text" name="nama_penolong" class="form-control"
                                    placeholder="Contoh: Bidan Siti, S.Tr.Keb" value="{{ old('nama_penolong') }}" required>
                            </div>
                        </div>

                        <!-- Seksi 3: Waktu Observasi & Status -->
                        <h6 class="section-header font-weight-bold text-primary mt-3 mb-3">
                            <i class="fas fa-clock mr-1"></i> 3. Waktu Observasi Awal & Status
                        </h6>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Waktu Masuk <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="waktu_masuk" class="form-control"
                                    value="{{ old('waktu_masuk', now()->format('Y-m-d\TH:i')) }}" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Waktu Mules Mulai</label>
                                <input type="datetime-local" name="waktu_mules_mulai" class="form-control"
                                    value="{{ old('waktu_mules_mulai') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Waktu Ketuban Pecah</label>
                                <input type="datetime-local" name="waktu_ketuban_pecah" class="form-control"
                                    value="{{ old('waktu_ketuban_pecah') }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Status Persalinan <span class="text-danger">*</span></label>
                                <select name="status_persalinan" class="form-control" required>
                                    <option value="proses" {{ old('status_persalinan', 'proses') == 'proses' ? 'selected' : '' }}>Dalam Proses</option>
                                    <option value="selesai" {{ old('status_persalinan') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="rujuk" {{ old('status_persalinan') == 'rujuk' ? 'selected' : '' }}>Rujuk</option>
                                    <option value="batal" {{ old('status_persalinan') == 'batal' ? 'selected' : '' }}>Batal</option>
                                </select>
                            </div>
                        </div>

                        <!-- Seksi 4: Asupan Nutrisi Terakhir -->
                        <h6 class="section-header font-weight-bold text-primary mt-3 mb-3">
                            <i class="fas fa-utensils mr-1"></i> 4. Asupan Terakhir Ibu (Footer Partograf)
                        </h6>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Waktu Makan Terakhir</label>
                                <input type="datetime-local" name="waktu_makan_terakhir" class="form-control"
                                    value="{{ old('waktu_makan_terakhir') }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Jenis Makanan</label>
                                <input type="text" name="jenis_makan" class="form-control" placeholder="Contoh: Nasi, Sayur"
                                    value="{{ old('jenis_makan') }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Porsi Makan</label>
                                <input type="text" name="porsi_makan" class="form-control"
                                    placeholder="Contoh: 1 porsi habis, 1/2 porsi" value="{{ old('porsi_makan') }}">
                            </div>

                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Waktu Minum Terakhir</label>
                                <input type="datetime-local" name="waktu_minum_terakhir" class="form-control"
                                    value="{{ old('waktu_minum_terakhir') }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Jenis Minuman</label>
                                <input type="text" name="jenis_minum" class="form-control" placeholder="Contoh: Air putih, Teh manis"
                                    value="{{ old('jenis_minum') }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Porsi Minum</label>
                                <input type="text" name="porsi_minum" class="form-control"
                                    placeholder="Contoh: 1 gelas air putih" value="{{ old('porsi_minum') }}">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm shadow-sm font-weight-bold">
                            <i class="fas fa-save mr-1"></i> Simpan Data Persalinan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <!-- Custom styles for DataTables -->
    <link href="{{ asset('template/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <style>
        .section-header {
            background-color: #f8fafc;
            color: #334155 !important;
            border-left: 4px solid #4e73df;
            padding: 0.45rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0 0.35rem 0.35rem 0;
        }

        .modal-dialog-scrollable {
            max-height: calc(100vh - 2rem) !important;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }
        .modal-dialog-scrollable form.modal-content,
        .modal-dialog-scrollable .modal-content {
            max-height: calc(100vh - 2rem) !important;
            height: calc(100vh - 2rem) !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden !important;
            border-radius: 0.5rem !important;
        }
        .modal-dialog-scrollable .modal-body {
            flex: 1 1 auto !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
            padding: 1.25rem 1.5rem !important;
        }

        /* Table Responsive Horizontal Scrollbar */
        .table-responsive {
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            margin-top: 0.5rem;
            margin-bottom: 1.25rem;
            padding-bottom: 8px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table-responsive::-webkit-scrollbar {
            height: 9px;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 10px;
        }
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        #dataTable th,
        #dataTable td {
            vertical-align: middle;
        }

        /* DataTables Search & Filter */
        .dataTables_wrapper .dataTables_filter {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin: 0;
        }
        .dataTables_wrapper .dataTables_filter label {
            display: inline-flex;
            align-items: center;
            position: relative;
            margin-bottom: 0;
        }
        .dataTables_wrapper .dataTables_filter label::before {
            font-family: "Font Awesome 5 Free";
            content: "\f002";
            font-weight: 900;
            position: absolute;
            left: 12px;
            color: #9ca3af;
            font-size: 0.85rem;
            pointer-events: none;
            z-index: 2;
        }
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0 !important;
            padding: 0.45rem 1rem 0.45rem 2.25rem !important;
            border-radius: 20px !important;
            border: 1px solid #d1d5db !important;
            font-size: 0.875rem !important;
            height: auto !important;
            width: 240px;
            background-color: #fff !important;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            width: 300px;
            border-color: #4e73df !important;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.2) !important;
            outline: 0 !important;
        }
    </style>
@endpush

@push('scripts')
    <!-- Page level DataTables plugins & custom script -->
    <script src="{{ asset('template/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                dom:
                    "<'row align-items-center mb-3'" +
                        "<'col-12 col-md-6 mb-2 mb-md-0'l>" +
                        "<'col-12 col-md-6'f>" +
                    ">" +
                    "<'row'" +
                        "<'col-12'" +
                            "tr" +
                        ">" +
                    ">" +
                    "<'row align-items-center mt-3'" +
                        "<'col-12 col-md-5 mb-2 mb-md-0'i>" +
                        "<'col-12 col-md-7'p>" +
                    ">",
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari data persalinan...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    paginate: {
                        first: '<i class="fas fa-angle-double-left"></i>',
                        last: '<i class="fas fa-angle-double-right"></i>',
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    },
                    emptyTable: "Belum ada data persalinan",
                    zeroRecords: "Tidak ditemukan data yang sesuai"
                },
                columnDefs: [
                    { orderable: false, targets: [11] }
                ],
                pageLength: 10,
                order: [
                    [0, 'asc']
                ]
            });
        });
    </script>
@endpush
