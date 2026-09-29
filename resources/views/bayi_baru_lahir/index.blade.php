@extends('layouts.master')

@section('title', 'Data Bayi Baru Lahir')

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
        title="Data Bayi Baru Lahir"
        subtitle="Dokumentasi antropometri, evaluasi skor APGAR, inisiasi menyusu dini (IMD), dan asuhan pasca lahir"
        icon="fas fa-baby"
    >
        <button type="button" class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahBayi">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Data Bayi
        </button>
    </x-page-header>

    @php
        $totalBayi = count($data_bayi_baru_lahir);
        $bayiNormal = collect($data_bayi_baru_lahir)->where('kondisi_bayi', 'normal')->count();
        $bayiImd = collect($data_bayi_baru_lahir)->where('pemberian_asi_kurang_1jam', 1)->count();
        $totalL = collect($data_bayi_baru_lahir)->where('jenis_kelamin', 'L')->count();
        $totalP = collect($data_bayi_baru_lahir)->where('jenis_kelamin', 'P')->count();
    @endphp

    <!-- Ringkasan Statistik -->
    <div class="row">
        <x-stat-card
            title="Total Bayi Lahir"
            :value="$totalBayi"
            icon="fas fa-baby-carriage"
            color="primary"
            subtitle="Bayi tercatat di sistem"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="Bayi Bugar (Normal)"
            :value="$bayiNormal"
            icon="fas fa-heartbeat"
            color="success"
            subtitle="{{ $totalBayi > 0 ? round(($bayiNormal / $totalBayi) * 100) : 0 }}% kondisi normal"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="IMD < 1 Jam"
            :value="$bayiImd"
            icon="fas fa-hand-holding-heart"
            color="info"
            subtitle="{{ $totalBayi > 0 ? round(($bayiImd / $totalBayi) * 100) : 0 }}% berhasil IMD"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="Rasio Kelamin"
            :value="$totalL . ' L / ' . $totalP . ' P'"
            icon="fas fa-venus-mars"
            color="warning"
            subtitle="{{ $totalL }} Laki-laki | {{ $totalP }} Perempuan"
            col="col-xl-3 col-md-6 mb-4"
        />
    </div>

    <!-- Tabel Data Bayi Baru Lahir -->
    <x-card title="Daftar Rekam Bayi Baru Lahir" icon="fas fa-clipboard-list">
        <x-slot:actions>
            <x-badge color="primary" pill>
                Total: {{ $totalBayi }} Bayi
            </x-badge>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" id="dataTable" cellspacing="0">
                <thead>
                    <tr class="text-center text-uppercase">
                        <th style="width: 40px;">No</th>
                        <th style="min-width: 170px;">Ibu Pasien / No. Reg</th>
                        <th style="min-width: 120px;">Kelahiran & Kelamin</th>
                        <th style="min-width: 130px;">Antropometri (BB/PB)</th>
                        <th style="min-width: 130px;">Skor APGAR</th>
                        <th style="min-width: 140px;">Kondisi & IMD</th>
                        <th style="width: 110px;">Dicatat</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data_bayi_baru_lahir as $data)
                        @php
                            $rawNama = $data->nama_pasien ?? ($data->nama_ibu ?? 'Pasien #' . $data->persalinan_id);
                            $namaClean = preg_match('/^ny\.?\s*/i', $rawNama) ? $rawNama : 'Ny. ' . $rawNama;

                            // Label Urutan Lahir
                            $urutanLabel = $data->urutan_kelahiran == 1 ? 'Bayi Tunggal / Ke-1' : 'Kembar Ke-' . $data->urutan_kelahiran;
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
                                @if(!empty($data->nama_suami))
                                    <div class="small text-muted mt-1">
                                        <i class="fas fa-user-friends text-gray-400 mr-1"></i>Suami: {{ $data->nama_suami }}
                                    </div>
                                @endif
                            </td>

                            {{-- 3. Kelahiran & Kelamin --}}
                            <td>
                                <div class="mb-1">
                                    @if($data->jenis_kelamin == 'L')
                                        <span class="badge badge-primary px-2 py-1">
                                            <i class="fas fa-mars mr-1"></i>Laki-laki
                                        </span>
                                    @else
                                        <span class="badge badge-danger px-2 py-1" style="background-color: #ec4899; border-color: #ec4899;">
                                            <i class="fas fa-venus mr-1"></i>Perempuan
                                        </span>
                                    @endif
                                </div>
                                <div class="small text-muted">
                                    <i class="fas fa-sort-numeric-up mr-1 text-gray-400"></i>{{ $urutanLabel }}
                                </div>
                            </td>

                            {{-- 4. Antropometri --}}
                            <td>
                                <div class="font-weight-bold text-gray-800 mb-1">
                                    <i class="fas fa-weight text-primary mr-1"></i>{{ number_format($data->berat_badan_gram, 0, ',', '.') }} gram
                                </div>
                                <div class="small text-muted">
                                    <i class="fas fa-ruler-vertical text-info mr-1"></i>Panjang: {{ $data->panjang_badan_cm }} cm
                                </div>
                            </td>

                            {{-- 5. Skor APGAR --}}
                            <td>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge badge-light border mr-1 font-weight-bold" title="Menit ke-1">
                                        1': <strong class="{{ ($data->apgar_menit_1 >= 7) ? 'text-success' : 'text-danger' }}">{{ $data->apgar_menit_1 ?? '-' }}</strong>
                                    </span>
                                    <span class="badge badge-light border mr-1 font-weight-bold" title="Menit ke-5">
                                        5': <strong class="{{ ($data->apgar_menit_5 >= 7) ? 'text-success' : 'text-danger' }}">{{ $data->apgar_menit_5 ?? '-' }}</strong>
                                    </span>
                                    <span class="badge badge-light border font-weight-bold" title="Menit ke-10">
                                        10': <strong class="{{ ($data->apgar_menit_10 >= 7) ? 'text-success' : 'text-danger' }}">{{ $data->apgar_menit_10 ?? '-' }}</strong>
                                    </span>
                                </div>
                                <div class="small">
                                    @php
                                        $avgApgar = $data->apgar_menit_5 ?? $data->apgar_menit_1;
                                    @endphp
                                    @if($avgApgar !== null)
                                        @if($avgApgar >= 7)
                                            <span class="text-success font-weight-bold"><i class="fas fa-smile mr-1"></i>Bugar</span>
                                        @elseif($avgApgar >= 4)
                                            <span class="text-warning font-weight-bold"><i class="fas fa-meh mr-1"></i>Asfiksia Ringan-Sedang</span>
                                        @else
                                            <span class="text-danger font-weight-bold"><i class="fas fa-frown mr-1"></i>Asfiksia Berat</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </td>

                            {{-- 6. Kondisi & IMD --}}
                            <td>
                                <div class="mb-1">
                                    @if($data->kondisi_bayi == 'normal')
                                        <span class="badge badge-success px-2 py-1">
                                            <i class="fas fa-check-circle mr-1"></i>Normal
                                        </span>
                                    @else
                                        <span class="badge badge-danger px-2 py-1" title="Asfiksia / Pucat / Biru / Lemas">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Asfiksia
                                        </span>
                                    @endif
                                </div>
                                <div class="small">
                                    @if($data->pemberian_asi_kurang_1jam)
                                        <span class="text-success"><i class="fas fa-check mr-1"></i>IMD &lt; 1 Jam</span>
                                    @else
                                        <span class="text-danger" title="{{ $data->alasan_tidak_asi ?? 'Tidak IMD' }}">
                                            <i class="fas fa-times-circle mr-1"></i>Tidak IMD
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- 7. Dicatat --}}
                            <td class="text-center text-nowrap small text-gray-700">
                                <div class="font-weight-bold">{{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y') }}</div>
                                <div class="text-muted">{{ \Carbon\Carbon::parse($data->created_at)->format('H:i') }} WIB</div>
                            </td>

                            {{-- 8. Aksi --}}
                            <td class="text-center text-nowrap">
                                <div class="btn-group btn-group-sm shadow-sm" role="group">
                                    {{-- Modal Detail Cepat --}}
                                    <button type="button" class="btn btn-info" data-toggle="modal"
                                        data-target="#modalDetailBayi{{ $data->id }}" title="Lihat Rincian Cepat">
                                        <i class="fas fa-eye fa-sm"></i>
                                    </button>

                                    {{-- Ke Halaman Show Lengkap --}}
                                    <a href="{{ route('bayi-baru-lahir.show', $data->id) }}" class="btn btn-outline-info" title="Halaman Lembar Medis Lengkap">
                                        <i class="fas fa-file-medical-alt fa-sm"></i>
                                    </a>

                                    {{-- Modal Edit --}}
                                    <button type="button" class="btn btn-warning text-dark" data-toggle="modal"
                                        data-target="#modalEditBayi{{ $data->id }}" title="Edit Data Bayi">
                                        <i class="fas fa-edit fa-sm"></i>
                                    </button>

                                    {{-- Hapus --}}
                                    <button type="button" class="btn btn-danger" title="Hapus Data Bayi"
                                        onclick="if(confirm('Apakah Anda yakin ingin menghapus data rekam medis Bayi ini?')) { document.getElementById('form-delete-{{ $data->id }}').submit(); }">
                                        <i class="fas fa-trash fa-sm"></i>
                                    </button>
                                </div>

                                <form id="form-delete-{{ $data->id }}" action="{{ route('bayi-baru-lahir.destroy', $data->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-baby fa-3x mb-2 text-gray-300 d-block"></i>
                                Belum ada data bayi baru lahir tercatat. Klik tombol <strong>Tambah Data Bayi</strong> untuk mendokumentasikan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>


    {{-- ========================================================================= --}}
    {{-- MODAL TAMBAH DATA BAYI BARU LAHIR                                        --}}
    {{-- ========================================================================= --}}
    <div class="modal fade text-left" id="modalTambahBayi" tabindex="-1" role="dialog" aria-labelledby="modalTambahBayiLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered" role="document">
            <form action="{{ route('bayi-baru-lahir.store') }}" method="POST" class="modal-content shadow border-0">
                @csrf
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title font-weight-bold" id="modalTambahBayiLabel">
                        <i class="fas fa-baby mr-2"></i> Tambah Data Bayi Baru Lahir
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    {{-- 1. IDENTITAS IBU & KELAHIRAN --}}
                    <h6 class="font-weight-bold text-primary mb-3 section-header">
                        <i class="fas fa-user-injured mr-1"></i> 1. Identitas Ibu & Urutan Kelahiran
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
                                            (G{{ $dp->gravida }}P{{ $dp->para }}A{{ $dp->abortus }}, Masuk: {{ $dp->waktu_masuk ? \Carbon\Carbon::parse($dp->waktu_masuk)->format('d/m/Y H:i') : '-' }})
                                            [{{ ucfirst($dp->status_persalinan) }}]
                                        </option>
                                    @endforeach
                                </select>
                            @endif
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
                    <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
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
                    <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                        <i class="fas fa-heartbeat mr-1"></i> 3. Inisiasi Menyusu Dini (IMD) & Kondisi Bayi
                    </h6>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Pemberian ASI &lt; 1 Jam (IMD) <span class="text-danger">*</span></label>
                            <select name="pemberian_asi_kurang_1jam" class="form-control" id="selectImdTambah" required>
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
                    <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                        <i class="fas fa-first-aid mr-1"></i> 4. Tindakan Resusitasi
                    </h6>
                    <div class="row">
                        <div class="col-12 form-group">
                            <label class="font-weight-bold text-gray-700 mb-2">Langkah / Tindakan Resusitasi yang Dilakukan:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="custom-control custom-checkbox mr-3 mb-2">
                                    <input type="checkbox" name="tindakan_resusitasi[]" value="mengeringkan" class="custom-control-input" id="res_kering" checked>
                                    <label class="custom-control-label" for="res_kering">Mengeringkan</label>
                                </div>
                                <div class="custom-control custom-checkbox mr-3 mb-2">
                                    <input type="checkbox" name="tindakan_resusitasi[]" value="menghangatkan" class="custom-control-input" id="res_hangat" checked>
                                    <label class="custom-control-label" for="res_hangat">Menghangatkan</label>
                                </div>
                                <div class="custom-control custom-checkbox mr-3 mb-2">
                                    <input type="checkbox" name="tindakan_resusitasi[]" value="rangsang_aktif" class="custom-control-input" id="res_rangsang" checked>
                                    <label class="custom-control-label" for="res_rangsang">Rangsang Aktif / Taktil</label>
                                </div>
                                <div class="custom-control custom-checkbox mr-3 mb-2">
                                    <input type="checkbox" name="tindakan_resusitasi[]" value="bebaskan_jalan_napas" class="custom-control-input" id="res_napas">
                                    <label class="custom-control-label" for="res_napas">Bebaskan Jalan Napas (Isap Lendir)</label>
                                </div>
                                <div class="custom-control custom-checkbox mr-3 mb-2">
                                    <input type="checkbox" name="tindakan_resusitasi[]" value="ventilasi_tekanan_positif" class="custom-control-input" id="res_vtp">
                                    <label class="custom-control-label" for="res_vtp">Ventilasi Tekanan Positif (VTP)</label>
                                </div>
                                <div class="custom-control custom-checkbox mr-3 mb-2">
                                    <input type="checkbox" name="tindakan_resusitasi[]" value="kompresi_dada" class="custom-control-input" id="res_kompresi">
                                    <label class="custom-control-label" for="res_kompresi">Kompresi Dada</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 form-group">
                            <label class="font-weight-bold text-gray-700">Tindakan Resusitasi Lainnya (Jika ada):</label>
                            <input type="text" name="tindakan_resusitasi_lainnya" class="form-control" placeholder="Contoh: Pemberian Oksigen 2 L/m, Sungkup O2, dll" value="{{ old('tindakan_resusitasi_lainnya') }}">
                        </div>
                    </div>

                    {{-- 5. CATATAN MEDIS & ASUHAN --}}
                    <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
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
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary shadow-sm font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan Data Bayi
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ========================================================================= --}}
    {{-- MODAL DETAIL & MODAL EDIT PER ITEM                                        --}}
    {{-- ========================================================================= --}}
    @foreach ($data_bayi_baru_lahir as $data)
        @php
            $rawNama = $data->nama_pasien ?? ($data->nama_ibu ?? 'Pasien #' . $data->persalinan_id);
            $namaClean = preg_match('/^ny\.?\s*/i', $rawNama) ? $rawNama : 'Ny. ' . $rawNama;
            $resArray = array_map('trim', explode(',', $data->tindakan_resusitasi ?? ''));
        @endphp

        {{-- 1. MODAL DETAIL --}}
        <div class="modal fade text-left" id="modalDetailBayi{{ $data->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
                <div class="modal-content shadow border-0">
                    <div class="modal-header bg-info text-white py-3">
                        <h5 class="modal-title font-weight-bold">
                            <i class="fas fa-baby mr-2"></i> Rincian Medis Bayi Baru Lahir #{{ $data->id }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        {{-- IDENTITAS IBU --}}
                        <div class="alert alert-light border mb-3">
                            <div class="row">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <div class="detail-label">Ibu Kandung</div>
                                    <div class="font-weight-bold text-primary">{{ $namaClean }} ({{ $data->nomor_register ?? '-' }})</div>
                                    <div class="small text-muted">Usia: {{ $data->usia_ibu ? $data->usia_ibu . ' tahun' : '-' }} | Suami: {{ $data->nama_suami ?? '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-label">Persalinan Terkait</div>
                                    <div class="text-dark font-weight-bold">G{{ $data->gravida ?? '-' }}P{{ $data->para ?? '-' }}A{{ $data->abortus ?? '-' }}</div>
                                    <div class="small text-muted">Fasilitas: {{ $data->nama_fasilitas ?? '-' }} | Bidan/Penolong: {{ $data->nama_penolong ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- ANTROPOMETRI & KELAHIRAN --}}
                        <h6 class="font-weight-bold text-primary mb-3 section-header">
                            <i class="fas fa-info-circle mr-1"></i> Data Kelahiran & Antropometri
                        </h6>
                        <div class="row mb-3">
                            <div class="col-md-3 mb-2">
                                <div class="detail-label">Urutan Kelahiran</div>
                                <div class="detail-value font-weight-bold">{{ $data->urutan_kelahiran == 1 ? 'Bayi Ke-1 (Tunggal)' : 'Kembar Ke-' . $data->urutan_kelahiran }}</div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="detail-label">Jenis Kelamin</div>
                                <div class="detail-value">
                                    @if($data->jenis_kelamin == 'L')
                                        <span class="badge badge-primary px-2 py-1"><i class="fas fa-mars mr-1"></i>Laki-laki</span>
                                    @else
                                        <span class="badge badge-danger px-2 py-1" style="background-color: #ec4899;"><i class="fas fa-venus mr-1"></i>Perempuan</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="detail-label">Berat Badan</div>
                                <div class="detail-value font-weight-bold text-primary">{{ number_format($data->berat_badan_gram, 0, ',', '.') }} gram</div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="detail-label">Panjang Badan</div>
                                <div class="detail-value font-weight-bold text-info">{{ $data->panjang_badan_cm }} cm</div>
                            </div>
                        </div>

                        {{-- NILAI APGAR & IMD --}}
                        <h6 class="font-weight-bold text-primary mb-3 section-header">
                            <i class="fas fa-heartbeat mr-1"></i> Nilai APGAR & Asuhan Awal
                        </h6>
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <div class="detail-label">Skor APGAR (1', 5', 10')</div>
                                <div class="detail-value">
                                    <span class="badge badge-light border mr-1 font-weight-bold">1': {{ $data->apgar_menit_1 ?? '-' }}</span>
                                    <span class="badge badge-light border mr-1 font-weight-bold">5': {{ $data->apgar_menit_5 ?? '-' }}</span>
                                    <span class="badge badge-light border font-weight-bold">10': {{ $data->apgar_menit_10 ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="detail-label">Kondisi Bayi</div>
                                <div class="detail-value">
                                    @if($data->kondisi_bayi == 'normal')
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>Normal / Bugar</span>
                                    @else
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-exclamation-triangle mr-1"></i>Asfiksia Pucat/Biru/Lemas</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="detail-label">IMD (&lt; 1 Jam)</div>
                                <div class="detail-value">
                                    @if($data->pemberian_asi_kurang_1jam)
                                        <span class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i>Ya, Berhasil</span>
                                    @else
                                        <span class="text-danger font-weight-bold"><i class="fas fa-times-circle mr-1"></i>Tidak: {{ $data->alasan_tidak_asi ?? '-' }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="detail-label">Tindakan Resusitasi</div>
                                <div class="detail-value">
                                    {{ $data->tindakan_resusitasi ? str_replace('_', ' ', $data->tindakan_resusitasi) : 'Tidak dilakukan resusitasi khusus (asuhan rutin normal)' }}
                                </div>
                            </div>
                        </div>

                        {{-- PENATALAKSANAAN & EVALUASI --}}
                        <h6 class="font-weight-bold text-primary mb-3 section-header">
                            <i class="fas fa-clipboard-check mr-1"></i> Penatalaksanaan & Evaluasi Medis
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="detail-label">Cacat Bawaan</div>
                                <div class="detail-value">{{ $data->cacat_bawaan ?: 'Tidak ada cacat bawaan' }}</div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="detail-label">Masalah Lain</div>
                                <div class="detail-value">{{ $data->masalah_lain ?: 'Tidak ada masalah' }}</div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="detail-label">Penatalaksanaan Segera</div>
                                <div class="detail-value" style="white-space: pre-line; align-items: flex-start; min-height: 50px;">
                                    {{ $data->penatalaksanaan ?: '-' }}
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="detail-label">Hasil Evaluasi</div>
                                <div class="detail-value" style="white-space: pre-line; align-items: flex-start; min-height: 50px;">
                                    {{ $data->hasil_evaluasi ?: '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2 justify-content-between">
                        <a href="{{ route('bayi-baru-lahir.show', $data->id) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-external-link-alt mr-1"></i> Buka Lembar Lengkap (Show)
                        </a>
                        <button type="button" class="btn btn-sm btn-secondary shadow-sm" data-dismiss="modal">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. MODAL EDIT --}}
        <div class="modal fade text-left" id="modalEditBayi{{ $data->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered" role="document">
                <form action="{{ route('bayi-baru-lahir.update', $data->id) }}" method="POST" class="modal-content shadow border-0">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-warning text-dark py-3">
                        <h5 class="modal-title font-weight-bold">
                            <i class="fas fa-edit mr-2"></i> Edit Data Bayi Baru Lahir #{{ $data->id }} ({{ $namaClean }})
                        </h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-4">
                        {{-- 1. IDENTITAS IBU & KELAHIRAN --}}
                        <h6 class="font-weight-bold text-primary mb-3 section-header">
                            <i class="fas fa-user-injured mr-1"></i> 1. Identitas Ibu & Urutan Kelahiran
                        </h6>
                        <div class="row">
                            <div class="col-md-7 form-group">
                                <label class="font-weight-bold text-gray-700">Pilih Pasien Persalinan <span class="text-danger">*</span></label>
                                <select name="persalinan_id" class="form-control" required>
                                    @foreach($data_persalinan as $dp)
                                        <option value="{{ $dp->id }}" {{ (old('persalinan_id', $data->persalinan_id) == $dp->id) ? 'selected' : '' }}>
                                            {{ $dp->no_register }} - {{ $dp->ibu_pasien }}
                                            (G{{ $dp->gravida }}P{{ $dp->para }}A{{ $dp->abortus }}, Masuk: {{ $dp->waktu_masuk ? \Carbon\Carbon::parse($dp->waktu_masuk)->format('d/m/Y H:i') : '-' }})
                                            [{{ ucfirst($dp->status_persalinan) }}]
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Urutan Kelahiran <span class="text-danger">*</span></label>
                                <select name="urutan_kelahiran" class="form-control" required>
                                    <option value="1" {{ old('urutan_kelahiran', $data->urutan_kelahiran) == '1' ? 'selected' : '' }}>Bayi Tunggal / Ke-1</option>
                                    <option value="2" {{ old('urutan_kelahiran', $data->urutan_kelahiran) == '2' ? 'selected' : '' }}>Bayi Ke-2 (Gemelli)</option>
                                    <option value="3" {{ old('urutan_kelahiran', $data->urutan_kelahiran) == '3' ? 'selected' : '' }}>Bayi Ke-3 (Triplet)</option>
                                    <option value="4" {{ old('urutan_kelahiran', $data->urutan_kelahiran) == '4' ? 'selected' : '' }}>Bayi Ke-4</option>
                                </select>
                            </div>
                            <div class="col-md-2 form-group">
                                <label class="font-weight-bold text-gray-700">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="L" {{ old('jenis_kelamin', $data->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $data->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>

                        {{-- 2. ANTROPOMETRI & SKOR APGAR --}}
                        <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                            <i class="fas fa-weight mr-1"></i> 2. Antropometri & Nilai APGAR
                        </h6>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Berat Badan (gram) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="berat_badan_gram" class="form-control" min="300" max="9000" value="{{ old('berat_badan_gram', $data->berat_badan_gram) }}" required>
                                    <div class="input-group-append"><span class="input-group-text">gram</span></div>
                                </div>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Panjang Badan (cm) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="panjang_badan_cm" class="form-control" min="20" max="75" value="{{ old('panjang_badan_cm', $data->panjang_badan_cm) }}" required>
                                    <div class="input-group-append"><span class="input-group-text">cm</span></div>
                                </div>
                            </div>
                            <div class="col-md-2 form-group">
                                <label class="font-weight-bold text-gray-700">APGAR 1' (Menit 1)</label>
                                <input type="number" name="apgar_menit_1" class="form-control" min="0" max="10" value="{{ old('apgar_menit_1', $data->apgar_menit_1) }}">
                            </div>
                            <div class="col-md-2 form-group">
                                <label class="font-weight-bold text-gray-700">APGAR 5' (Menit 5)</label>
                                <input type="number" name="apgar_menit_5" class="form-control" min="0" max="10" value="{{ old('apgar_menit_5', $data->apgar_menit_5) }}">
                            </div>
                            <div class="col-md-2 form-group">
                                <label class="font-weight-bold text-gray-700">APGAR 10' (Menit 10)</label>
                                <input type="number" name="apgar_menit_10" class="form-control" min="0" max="10" value="{{ old('apgar_menit_10', $data->apgar_menit_10) }}">
                            </div>
                        </div>

                        {{-- 3. INISIASI MENYUSU DINI & KONDISI BAYI --}}
                        <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                            <i class="fas fa-heartbeat mr-1"></i> 3. Inisiasi Menyusu Dini (IMD) & Kondisi Bayi
                        </h6>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Pemberian ASI &lt; 1 Jam (IMD) <span class="text-danger">*</span></label>
                                <select name="pemberian_asi_kurang_1jam" class="form-control" required>
                                    <option value="1" {{ old('pemberian_asi_kurang_1jam', $data->pemberian_asi_kurang_1jam ? '1' : '0') == '1' ? 'selected' : '' }}>Ya (Berhasil IMD &lt; 1 Jam)</option>
                                    <option value="0" {{ old('pemberian_asi_kurang_1jam', $data->pemberian_asi_kurang_1jam ? '1' : '0') == '0' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Alasan Tidak ASI / IMD</label>
                                <input type="text" name="alasan_tidak_asi" class="form-control" placeholder="Alasan jika tidak IMD" value="{{ old('alasan_tidak_asi', $data->alasan_tidak_asi) }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Kondisi Bayi Saat Lahir <span class="text-danger">*</span></label>
                                <select name="kondisi_bayi" class="form-control" required>
                                    <option value="normal" {{ old('kondisi_bayi', $data->kondisi_bayi) == 'normal' ? 'selected' : '' }}>Normal / Bugar</option>
                                    <option value="asfiksia_pucat_biru_lemas" {{ old('kondisi_bayi', $data->kondisi_bayi) == 'asfiksia_pucat_biru_lemas' ? 'selected' : '' }}>Asfiksia (Pucat / Biru / Lemas)</option>
                                </select>
                            </div>
                        </div>

                        {{-- 4. TINDAKAN RESUSITASI --}}
                        <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                            <i class="fas fa-first-aid mr-1"></i> 4. Tindakan Resusitasi
                        </h6>
                        <div class="row">
                            <div class="col-12 form-group">
                                <label class="font-weight-bold text-gray-700 mb-2">Langkah / Tindakan Resusitasi yang Dilakukan:</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="custom-control custom-checkbox mr-3 mb-2">
                                        <input type="checkbox" name="tindakan_resusitasi[]" value="mengeringkan" class="custom-control-input" id="edit_res_kering_{{ $data->id }}" {{ in_array('mengeringkan', $resArray) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="edit_res_kering_{{ $data->id }}">Mengeringkan</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mr-3 mb-2">
                                        <input type="checkbox" name="tindakan_resusitasi[]" value="menghangatkan" class="custom-control-input" id="edit_res_hangat_{{ $data->id }}" {{ in_array('menghangatkan', $resArray) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="edit_res_hangat_{{ $data->id }}">Menghangatkan</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mr-3 mb-2">
                                        <input type="checkbox" name="tindakan_resusitasi[]" value="rangsang_aktif" class="custom-control-input" id="edit_res_rangsang_{{ $data->id }}" {{ in_array('rangsang_aktif', $resArray) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="edit_res_rangsang_{{ $data->id }}">Rangsang Aktif / Taktil</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mr-3 mb-2">
                                        <input type="checkbox" name="tindakan_resusitasi[]" value="bebaskan_jalan_napas" class="custom-control-input" id="edit_res_napas_{{ $data->id }}" {{ in_array('bebaskan_jalan_napas', $resArray) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="edit_res_napas_{{ $data->id }}">Bebaskan Jalan Napas</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mr-3 mb-2">
                                        <input type="checkbox" name="tindakan_resusitasi[]" value="ventilasi_tekanan_positif" class="custom-control-input" id="edit_res_vtp_{{ $data->id }}" {{ in_array('ventilasi_tekanan_positif', $resArray) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="edit_res_vtp_{{ $data->id }}">Ventilasi Tekanan Positif (VTP)</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mr-3 mb-2">
                                        <input type="checkbox" name="tindakan_resusitasi[]" value="kompresi_dada" class="custom-control-input" id="edit_res_kompresi_{{ $data->id }}" {{ in_array('kompresi_dada', $resArray) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="edit_res_kompresi_{{ $data->id }}">Kompresi Dada</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 form-group">
                                <label class="font-weight-bold text-gray-700">Tindakan Resusitasi Lainnya (Jika ada):</label>
                                @php
                                    $known = ['mengeringkan', 'menghangatkan', 'rangsang_aktif', 'bebaskan_jalan_napas', 'ventilasi_tekanan_positif', 'kompresi_dada'];
                                    $otherArr = array_diff($resArray, $known);
                                    $otherStr = implode(', ', $otherArr);
                                @endphp
                                <input type="text" name="tindakan_resusitasi_lainnya" class="form-control" placeholder="Tindakan tambahan jika ada" value="{{ old('tindakan_resusitasi_lainnya', $otherStr) }}">
                            </div>
                        </div>

                        {{-- 5. CATATAN MEDIS & ASUHAN --}}
                        <h6 class="font-weight-bold text-primary mt-4 mb-3 section-header">
                            <i class="fas fa-notes-medical mr-1"></i> 5. Catatan Medis, Penatalaksanaan, & Evaluasi
                        </h6>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Cacat Bawaan (Jika Ada)</label>
                                <input type="text" name="cacat_bawaan" class="form-control" value="{{ old('cacat_bawaan', $data->cacat_bawaan) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Masalah Lain</label>
                                <input type="text" name="masalah_lain" class="form-control" value="{{ old('masalah_lain', $data->masalah_lain) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Penatalaksanaan Segera</label>
                                <textarea name="penatalaksanaan" class="form-control" rows="3">{{ old('penatalaksanaan', $data->penatalaksanaan) }}</textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700">Hasil Evaluasi</label>
                                <textarea name="hasil_evaluasi" class="form-control" rows="3">{{ old('hasil_evaluasi', $data->hasil_evaluasi) }}</textarea>
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
                    searchPlaceholder: "Cari data bayi...",
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
                    emptyTable: "Belum ada data bayi baru lahir",
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
