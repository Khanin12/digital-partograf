@extends('layouts.master')

@section('title', 'Pemantauan Kala IV')

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
        title="Pemantauan Kala IV"
        subtitle="Observasi kondisi ibu 2 jam pertama pasca persalinan (Tekanan Darah, Nadi, Suhu, TFU, Kontraksi, Kandung Kemih, & Perdarahan)"
        icon="fas fa-procedures"
    >
        <button type="button" class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahKala4">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Observasi
        </button>
    </x-page-header>

    @php
        $totalObs = count($data_pemantauan);
        $kontraksiBaik = collect($data_pemantauan)->filter(function($item) {
            return str_contains(strtolower($item->kontraksi_uterus), 'keras') || str_contains(strtolower($item->kontraksi_uterus), 'baik');
        })->count();
        $vitalStabil = collect($data_pemantauan)->filter(function($item) {
            return $item->tekanan_darah_sistolik < 140 && $item->tekanan_darah_diastolik < 90 && $item->nadi_per_menit <= 100;
        })->count();
        $totalPasien = collect($data_pemantauan)->pluck('persalinan_id')->unique()->count();
    @endphp

    <!-- Ringkasan Statistik -->
    <div class="row">
        <x-stat-card
            title="Total Observasi"
            :value="$totalObs"
            icon="fas fa-clipboard-check"
            color="primary"
            subtitle="Data interval pemantauan tersimpan"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="Kontraksi Uterus Baik"
            :value="$kontraksiBaik"
            icon="fas fa-shield-alt"
            color="success"
            subtitle="{{ $totalObs > 0 ? round(($kontraksiBaik / $totalObs) * 100) : 0 }}% uterus berkontraksi keras"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="Tanda Vital Stabil"
            :value="$vitalStabil"
            icon="fas fa-heartbeat"
            color="info"
            subtitle="{{ $totalObs > 0 ? round(($vitalStabil / $totalObs) * 100) : 0 }}% TD & nadi normal"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="Ibu Terpantau"
            :value="$totalPasien . ' Pasien'"
            icon="fas fa-female"
            color="warning"
            subtitle="Ibu dalam masa observasi Kala IV"
            col="col-xl-3 col-md-6 mb-4"
        />
    </div>

    <!-- Tabel Data Pemantauan Kala IV -->
    <x-card title="Daftar Lembar Observasi Kala IV" icon="fas fa-list-alt">
        <x-slot:actions>
            <x-badge color="primary" pill>
                Total: {{ $totalObs }} Observasi
            </x-badge>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" id="dataTable" cellspacing="0">
                <thead>
                    <tr class="text-center text-uppercase">
                        <th style="width: 40px;">No</th>
                        <th style="min-width: 160px;">Ibu Pasien / No. Reg</th>
                        <th style="min-width: 130px;">Periode Observasi</th>
                        <th style="min-width: 170px;">Tanda Vital</th>
                        <th style="min-width: 150px;">Evaluasi Uterus</th>
                        <th style="min-width: 140px;">Kemih & Darah</th>
                        <th style="width: 100px;">Dicatat</th>
                        <th style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data_pemantauan as $data)
                        @php
                            $rawNama = $data->nama_pasien ?? 'Pasien #' . $data->persalinan_id;
                            $namaClean = preg_match('/^ny\.?\s*/i', $rawNama) ? $rawNama : 'Ny. ' . $rawNama;
                            $waktuFormatted = substr($data->waktu_pemantauan, 0, 5);
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

                            {{-- 3. Periode Observasi --}}
                            <td>
                                <div class="mb-1">
                                    @if($data->jam_ke == 1)
                                        <span class="badge badge-primary px-2 py-1">Jam Ke-1 (15')</span>
                                    @else
                                        <span class="badge badge-info px-2 py-1">Jam Ke-2 (30')</span>
                                    @endif
                                    <span class="badge badge-light border ml-1 font-weight-bold">Ke-{{ $data->pemantauan_ke }}</span>
                                </div>
                                <div class="small text-muted">
                                    <i class="fas fa-clock text-gray-400 mr-1"></i>Waktu: <strong>{{ $waktuFormatted }} WIB</strong>
                                </div>
                            </td>

                            {{-- 4. Tanda Vital --}}
                            <td>
                                <div class="font-weight-bold text-gray-800 mb-1">
                                    <span class="text-primary"><i class="fas fa-tachometer-alt mr-1"></i>TD: {{ $data->tekanan_darah_sistolik }}/{{ $data->tekanan_darah_diastolik }}</span>
                                    <span class="small text-muted font-weight-normal">mmHg</span>
                                </div>
                                <div class="small text-muted">
                                    <span class="mr-2"><i class="fas fa-heart text-danger mr-1"></i>{{ $data->nadi_per_menit }} x/m</span>
                                    @if($data->suhu_tubuh !== null)
                                        <span><i class="fas fa-thermometer-half text-warning mr-1"></i>{{ $data->suhu_tubuh }} °C</span>
                                    @else
                                        <span class="text-gray-400"><i class="fas fa-thermometer-half mr-1"></i>-</span>
                                    @endif
                                </div>
                            </td>

                            {{-- 5. Evaluasi Uterus --}}
                            <td>
                                <div class="font-weight-bold text-gray-800 mb-1">
                                    <i class="fas fa-ruler mr-1 text-info"></i>{{ $data->tinggi_fundus_uteri }}
                                </div>
                                <div class="small">
                                    @if(str_contains(strtolower($data->kontraksi_uterus), 'keras') || str_contains(strtolower($data->kontraksi_uterus), 'baik'))
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>{{ $data->kontraksi_uterus }}</span>
                                    @else
                                        <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $data->kontraksi_uterus }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- 6. Kandung Kemih & Perdarahan --}}
                            <td>
                                <div class="mb-1 small">
                                    <span class="badge badge-light border text-dark font-weight-bold">
                                        <i class="fas fa-tint mr-1 text-primary"></i>Kemih: {{ $data->kandung_kemih }}
                                    </span>
                                </div>
                                <div class="small font-weight-bold {{ $data->perdarahan_ml > 200 ? 'text-danger' : 'text-gray-700' }}">
                                    <i class="fas fa-tint text-danger mr-1"></i>Perdarahan: {{ $data->perdarahan_ml }} ml
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
                                        data-target="#modalDetailKala4{{ $data->id }}" title="Lihat Rincian">
                                        <i class="fas fa-eye fa-sm"></i>
                                    </button>

                                    {{-- Modal Edit --}}
                                    <button type="button" class="btn btn-warning text-dark" data-toggle="modal"
                                        data-target="#modalEditKala4{{ $data->id }}" title="Edit Data">
                                        <i class="fas fa-edit fa-sm"></i>
                                    </button>

                                    {{-- Hapus --}}
                                    <button type="button" class="btn btn-danger" title="Hapus Observasi"
                                        onclick="if(confirm('Apakah Anda yakin ingin menghapus observasi Kala IV ini?')) { document.getElementById('form-delete-{{ $data->id }}').submit(); }">
                                        <i class="fas fa-trash fa-sm"></i>
                                    </button>
                                </div>

                                <form id="form-delete-{{ $data->id }}" action="{{ route('pemantauan-kala-4.destroy', $data->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-procedures fa-3x mb-2 text-gray-300 d-block"></i>
                                Belum ada data pemantauan Kala IV tercatat. Klik tombol <strong>Tambah Observasi</strong> untuk mencatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>


    {{-- ========================================================================= --}}
    {{-- MODAL TAMBAH DATA PEMANTAUAN KALA IV                                      --}}
    {{-- ========================================================================= --}}
    <div class="modal fade text-left" id="modalTambahKala4" tabindex="-1" role="dialog" aria-labelledby="modalTambahKala4Label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
            <form action="{{ route('pemantauan-kala-4.store') }}" method="POST" class="modal-content shadow border-0">
                @csrf
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title font-weight-bold" id="modalTambahKala4Label">
                        <i class="fas fa-procedures mr-2"></i> Tambah Observasi Pemantauan Kala IV
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    {{-- 1. IDENTITAS IBU & WAKTU --}}
                    <h6 class="font-weight-bold text-primary mb-3 section-header">
                        <i class="fas fa-user-injured mr-1"></i> 1. Pasien & Periode Waktu Observasi
                    </h6>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Pilih Pasien Persalinan <span class="text-danger">*</span></label>
                            @if($data_persalinan->isEmpty())
                                <div class="alert alert-warning py-2 mb-0 small">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Belum ada data persalinan aktif.
                                </div>
                            @else
                                <select name="persalinan_id" class="form-control" required>
                                    <option value="">-- Pilih Persalinan Ibu --</option>
                                    @foreach($data_persalinan as $dp)
                                        <option value="{{ $dp->id }}" {{ old('persalinan_id') == $dp->id ? 'selected' : '' }}>
                                            {{ $dp->no_register }} - {{ $dp->ibu_pasien }}
                                            (G{{ $dp->gravida }}P{{ $dp->para }}A{{ $dp->abortus }}, Masuk: {{ $dp->waktu_masuk ? \Carbon\Carbon::parse($dp->waktu_masuk)->format('d/m/Y H:i') : '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Jam Ke- <span class="text-danger">*</span></label>
                            <select name="jam_ke" class="form-control" required>
                                <option value="1" {{ old('jam_ke', '1') == '1' ? 'selected' : '' }}>Jam Ke-1 (Tiap 15 mnt)</option>
                                <option value="2" {{ old('jam_ke') == '2' ? 'selected' : '' }}>Jam Ke-2 (Tiap 30 mnt)</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Interval Ke- <span class="text-danger">*</span></label>
                            <select name="pemantauan_ke" class="form-control" required>
                                <option value="1" {{ old('pemantauan_ke', '1') == '1' ? 'selected' : '' }}>Ke-1</option>
                                <option value="2" {{ old('pemantauan_ke') == '2' ? 'selected' : '' }}>Ke-2</option>
                                <option value="3" {{ old('pemantauan_ke') == '3' ? 'selected' : '' }}>Ke-3</option>
                                <option value="4" {{ old('pemantauan_ke') == '4' ? 'selected' : '' }}>Ke-4</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Waktu Pemantauan (Jam:Menit) <span class="text-danger">*</span></label>
                            <input type="time" name="waktu_pemantauan" class="form-control" value="{{ old('waktu_pemantauan', date('H:i')) }}" required>
                        </div>
                    </div>

                    {{-- 2. TANDA VITAL --}}
                    <h6 class="font-weight-bold text-primary mt-3 mb-3 section-header">
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
                    <h6 class="font-weight-bold text-primary mt-3 mb-3 section-header">
                        <i class="fas fa-hand-holding-medical mr-1"></i> 3. Evaluasi Uterus, Kandung Kemih, & Perdarahan
                    </h6>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-gray-700">Tinggi Fundus Uteri (TFU) <span class="text-danger">*</span></label>
                            <input type="text" name="tinggi_fundus_uteri" class="form-control" list="listTfu" placeholder="Contoh: 2 jari di bawah pusat" value="{{ old('tinggi_fundus_uteri', '2 jari di bawah pusat') }}" required>
                            <datalist id="listTfu">
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
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary shadow-sm font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan Observasi
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ========================================================================= --}}
    {{-- MODAL DETAIL & MODAL EDIT PER ITEM                                        --}}
    {{-- ========================================================================= --}}
    @foreach ($data_pemantauan as $data)
        @php
            $rawNama = $data->nama_pasien ?? 'Pasien #' . $data->persalinan_id;
            $namaClean = preg_match('/^ny\.?\s*/i', $rawNama) ? $rawNama : 'Ny. ' . $rawNama;
            $waktuFormatted = substr($data->waktu_pemantauan, 0, 5);
        @endphp

        {{-- 1. MODAL DETAIL --}}
        <div class="modal fade text-left" id="modalDetailKala4{{ $data->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                <div class="modal-content shadow border-0">
                    <div class="modal-header bg-info text-white py-3">
                        <h5 class="modal-title font-weight-bold">
                            <i class="fas fa-procedures mr-2"></i> Rincian Observasi Kala IV #{{ $data->id }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-light border mb-3">
                            <div class="detail-label">Ibu Pasien</div>
                            <div class="font-weight-bold text-primary">{{ $namaClean }} ({{ $data->nomor_register ?? '-' }})</div>
                            <div class="small text-muted">Bidan / Penolong: {{ $data->nama_penolong ?? '-' }}</div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="detail-label">Periode Observasi</div>
                                <div class="detail-value font-weight-bold">
                                    Jam Ke-{{ $data->jam_ke }} (Ke-{{ $data->pemantauan_ke }})
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="detail-label">Waktu Observasi</div>
                                <div class="detail-value font-weight-bold text-primary">
                                    {{ $waktuFormatted }} WIB
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="detail-label">Tekanan Darah</div>
                                <div class="detail-value font-weight-bold">
                                    {{ $data->tekanan_darah_sistolik }}/{{ $data->tekanan_darah_diastolik }} mmHg
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="detail-label">Nadi</div>
                                <div class="detail-value font-weight-bold text-danger">
                                    {{ $data->nadi_per_menit }} x/menit
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="detail-label">Suhu Tubuh</div>
                                <div class="detail-value">
                                    {{ $data->suhu_tubuh !== null ? $data->suhu_tubuh . ' °C' : 'Tidak diukur' }}
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="detail-label">Tinggi Fundus Uteri (TFU)</div>
                                <div class="detail-value">
                                    {{ $data->tinggi_fundus_uteri }}
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="detail-label">Kontraksi Uterus</div>
                                <div class="detail-value">
                                    @if(str_contains(strtolower($data->kontraksi_uterus), 'keras') || str_contains(strtolower($data->kontraksi_uterus), 'baik'))
                                        <span class="badge badge-success px-2 py-1">{{ $data->kontraksi_uterus }}</span>
                                    @else
                                        <span class="badge badge-warning text-dark px-2 py-1">{{ $data->kontraksi_uterus }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="detail-label">Kandung Kemih</div>
                                <div class="detail-value">
                                    {{ $data->kandung_kemih }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="detail-label">Jumlah Perdarahan</div>
                                <div class="detail-value font-weight-bold {{ $data->perdarahan_ml > 200 ? 'text-danger' : 'text-success' }}">
                                    <i class="fas fa-tint mr-1 text-danger"></i>{{ $data->perdarahan_ml }} ml
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
        <div class="modal fade text-left" id="modalEditKala4{{ $data->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
                <form action="{{ route('pemantauan-kala-4.update', $data->id) }}" method="POST" class="modal-content shadow border-0">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-warning text-dark py-3">
                        <h5 class="modal-title font-weight-bold">
                            <i class="fas fa-edit mr-2"></i> Edit Observasi Kala IV #{{ $data->id }} ({{ $namaClean }})
                        </h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-4">
                        {{-- 1. IDENTITAS IBU & WAKTU --}}
                        <h6 class="font-weight-bold text-primary mb-3 section-header">
                            <i class="fas fa-user-injured mr-1"></i> 1. Pasien & Periode Waktu Observasi
                        </h6>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Pilih Pasien Persalinan <span class="text-danger">*</span></label>
                                <select name="persalinan_id" class="form-control" required>
                                    @foreach($data_persalinan as $dp)
                                        <option value="{{ $dp->id }}" {{ old('persalinan_id', $data->persalinan_id) == $dp->id ? 'selected' : '' }}>
                                            {{ $dp->no_register }} - {{ $dp->ibu_pasien }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Jam Ke- <span class="text-danger">*</span></label>
                                <select name="jam_ke" class="form-control" required>
                                    <option value="1" {{ old('jam_ke', $data->jam_ke) == '1' ? 'selected' : '' }}>Jam Ke-1 (15')</option>
                                    <option value="2" {{ old('jam_ke', $data->jam_ke) == '2' ? 'selected' : '' }}>Jam Ke-2 (30')</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Interval Ke- <span class="text-danger">*</span></label>
                                <select name="pemantauan_ke" class="form-control" required>
                                    <option value="1" {{ old('pemantauan_ke', $data->pemantauan_ke) == '1' ? 'selected' : '' }}>Ke-1</option>
                                    <option value="2" {{ old('pemantauan_ke', $data->pemantauan_ke) == '2' ? 'selected' : '' }}>Ke-2</option>
                                    <option value="3" {{ old('pemantauan_ke', $data->pemantauan_ke) == '3' ? 'selected' : '' }}>Ke-3</option>
                                    <option value="4" {{ old('pemantauan_ke', $data->pemantauan_ke) == '4' ? 'selected' : '' }}>Ke-4</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Waktu Pemantauan (Jam:Menit) <span class="text-danger">*</span></label>
                                <input type="time" name="waktu_pemantauan" class="form-control" value="{{ old('waktu_pemantauan', $waktuFormatted) }}" required>
                            </div>
                        </div>

                        {{-- 2. TANDA VITAL --}}
                        <h6 class="font-weight-bold text-primary mt-3 mb-3 section-header">
                            <i class="fas fa-heartbeat mr-1"></i> 2. Pemeriksaan Tanda Vital
                        </h6>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">TD Sistolik <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="tekanan_darah_sistolik" class="form-control" min="50" max="250" value="{{ old('tekanan_darah_sistolik', $data->tekanan_darah_sistolik) }}" required>
                                    <div class="input-group-append"><span class="input-group-text">mmHg</span></div>
                                </div>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">TD Diastolik <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="tekanan_darah_diastolik" class="form-control" min="30" max="150" value="{{ old('tekanan_darah_diastolik', $data->tekanan_darah_diastolik) }}" required>
                                    <div class="input-group-append"><span class="input-group-text">mmHg</span></div>
                                </div>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Nadi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="nadi_per_menit" class="form-control" min="40" max="180" value="{{ old('nadi_per_menit', $data->nadi_per_menit) }}" required>
                                    <div class="input-group-append"><span class="input-group-text">x/mnt</span></div>
                                </div>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Suhu Tubuh (°C)</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" name="suhu_tubuh" class="form-control" min="35" max="42" placeholder="Opsional" value="{{ old('suhu_tubuh', $data->suhu_tubuh) }}">
                                    <div class="input-group-append"><span class="input-group-text">°C</span></div>
                                </div>
                            </div>
                        </div>

                        {{-- 3. EVALUASI UTERUS & KEMIH & PERDARAHAN --}}
                        <h6 class="font-weight-bold text-primary mt-3 mb-3 section-header">
                            <i class="fas fa-hand-holding-medical mr-1"></i> 3. Evaluasi Uterus, Kandung Kemih, & Perdarahan
                        </h6>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Tinggi Fundus Uteri (TFU) <span class="text-danger">*</span></label>
                                <input type="text" name="tinggi_fundus_uteri" class="form-control" value="{{ old('tinggi_fundus_uteri', $data->tinggi_fundus_uteri) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Kontraksi Uterus <span class="text-danger">*</span></label>
                                <select name="kontraksi_uterus" class="form-control" required>
                                    <option value="Keras" {{ old('kontraksi_uterus', $data->kontraksi_uterus) == 'Keras' ? 'selected' : '' }}>Keras (Baik / Normal)</option>
                                    <option value="Lembek" {{ old('kontraksi_uterus', $data->kontraksi_uterus) == 'Lembek' ? 'selected' : '' }}>Lembek (Atonia / Waspada)</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Kandung Kemih <span class="text-danger">*</span></label>
                                <select name="kandung_kemih" class="form-control" required>
                                    <option value="Kosong" {{ old('kandung_kemih', $data->kandung_kemih) == 'Kosong' ? 'selected' : '' }}>Kosong</option>
                                    <option value="Penuh" {{ old('kandung_kemih', $data->kandung_kemih) == 'Penuh' ? 'selected' : '' }}>Penuh (Segera Kosongkan)</option>
                                    <option value="Kurang Penuh" {{ old('kandung_kemih', $data->kandung_kemih) == 'Kurang Penuh' ? 'selected' : '' }}>Kurang Penuh</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Perdarahan (ml)</label>
                                <div class="input-group">
                                    <input type="number" name="perdarahan_ml" class="form-control" min="0" max="3000" value="{{ old('perdarahan_ml', $data->perdarahan_ml) }}">
                                    <div class="input-group-append"><span class="input-group-text">ml</span></div>
                                </div>
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
                    searchPlaceholder: "Cari pemantauan...",
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
                    emptyTable: "Belum ada data pemantauan Kala IV",
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
