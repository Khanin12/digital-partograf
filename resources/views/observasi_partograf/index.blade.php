@extends('layouts.master')

@section('title', 'Data Observasi Partograf')

@section('content')

    {{-- Alert Success --}}
    @if(session('success'))
        <x-alert type="success" icon="fas fa-check-circle" dismissible>
            {{ session('success') }}
        </x-alert>
    @endif

    {{-- Alert Error --}}
    @if(isset($errors) && $errors->any())
        <x-alert type="danger" icon="fas fa-exclamation-circle" dismissible title="Terjadi Kesalahan Pengisian:">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    {{-- Page Header --}}
    <x-page-header
        title="Data Observasi Partograf"
        subtitle="Master data observasi berkala kemajuan persalinan, denyut jantung janin, kontraksi, dan tanda vital (Lembar Depan Partograf)"
        icon="fas fa-baby"
    >
        <button
            type="button"
            class="btn btn-sm btn-primary shadow-sm"
            data-toggle="modal"
            data-target="#modaltambahdataobservasipersalinan"
        >
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i>
            Tambah Data Observasi
        </button>
    </x-page-header>

    {{-- Ringkasan Statistik Observasi --}}
    <div class="row">
        <x-stat-card
            title="Total Observasi"
            :value="count($data_observasi)"
            icon="fas fa-clipboard-check"
            color="primary"
            subtitle="Seluruh titik rekam pemantauan"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="DJJ Normal (120-160)"
            :value="collect($data_observasi)->whereBetween('djj_denyut_per_menit', [120, 160])->count()"
            icon="fas fa-heartbeat"
            color="success"
            subtitle="Kondisi janin dalam batas aman"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="Fase Aktif (≥ 4 cm)"
            :value="collect($data_observasi)->where('pembukaan_serviks', '>=', 4)->count()"
            icon="fas fa-chart-line"
            color="info"
            subtitle="Pembukaan serviks aktif"
            col="col-xl-3 col-md-6 mb-4"
        />

        <x-stat-card
            title="Pasien Terpantau"
            :value="collect($data_observasi)->pluck('persalinan_id')->unique()->count()"
            icon="fas fa-procedures"
            color="warning"
            subtitle="Persalinan dalam observasi"
            col="col-xl-3 col-md-6 mb-4"
        />
    </div>

    {{-- Filter Pasien Persalinan --}}
    <div class="card shadow mb-4 border-left-primary">
        <div class="card-body py-3">
            <form action="{{ route('observasi-partograf.index') }}" method="GET" class="row align-items-center">
                <div class="col-12 col-md-6 col-lg-5 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-gray-700 mb-1 d-block">
                        <i class="fas fa-filter text-primary mr-1"></i> Filter Pasien Persalinan:
                    </label>
                    <select name="persalinan_id" class="form-control form-control-sm select-persalinan" onchange="this.form.submit()">
                        <option value="">-- Tampilkan Semua Pasien Persalinan --</option>
                        @foreach($data_persalinan as $dp)
                            <option value="{{ $dp->id }}" {{ (string)($selected_persalinan_id ?? '') === (string)$dp->id ? 'selected' : '' }}>
                                {{ $dp->nomer_registerer ?? 'REG-' . $dp->id }} - {{ $dp->nama_ibu ?? $dp->nama_pasien }} (G{{ $dp->gravida }}P{{ $dp->para }}A{{ $dp->abortus }}, {{ $dp->usia_kehamilan_minggu }} mg) [{{ ucfirst($dp->status_persalinan) }}]
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-7 text-md-right mt-2 mt-md-0">
                    @if(!empty($selected_persalinan))
                        <button
                            type="button"
                            class="btn btn-sm btn-primary shadow-sm mr-2"
                            data-toggle="modal"
                            data-target="#modaltambahdataobservasipersalinan"
                        >
                            <i class="fas fa-plus-circle mr-1"></i> Input Kolom Ke-{{ $next_kolom }}
                        </button>
                        <a href="{{ route('partograf.cetak', $selected_persalinan_id) }}" target="_blank" class="btn btn-sm btn-success shadow-sm mr-2" title="Cetak Dokumen Resmi Partograf (PDF 2 Halaman)">
                            <i class="fas fa-print mr-1"></i> Cetak Partograf (PDF)
                        </a>
                        <a href="{{ route('observasi-partograf.index') }}" class="btn btn-sm btn-outline-secondary shadow-sm">
                            <i class="fas fa-sync-alt mr-1"></i> Reset Filter
                        </a>
                    @else
                        <small class="text-muted font-italic">
                            <i class="fas fa-info-circle mr-1"></i> Pilih pasien di atas untuk melihat lembar partograf perorangan.
                        </small>
                    @endif
                </div>
            </form>

            @if(!empty($selected_persalinan))
                <hr class="my-3">
                <div class="row text-gray-800 small align-items-center">
                    <div class="col-12 col-md-3">
                        <span class="text-muted">Nama Ibu / Usia:</span><br>
                        <strong class="text-primary">{{ $selected_persalinan->nama_ibu ?? $selected_persalinan->nama_pasien }}</strong> ({{ $selected_persalinan->usia_ibu ?? '-' }} th)
                    </div>
                    <div class="col-12 col-md-2">
                        <span class="text-muted">No. Register:</span><br>
                        <strong>{{ $selected_persalinan->nomer_registerer ?? 'REG-' . $selected_persalinan->id }}</strong>
                    </div>
                    <div class="col-12 col-md-2">
                        <span class="text-muted">Obstetri (GPA):</span><br>
                        <span class="badge badge-primary px-2 py-1 font-weight-bold">
                            G{{ $selected_persalinan->gravida }}P{{ $selected_persalinan->para }}A{{ $selected_persalinan->abortus }}
                        </span> ({{ $selected_persalinan->usia_kehamilan_minggu }} mg)
                    </div>
                    <div class="col-12 col-md-3">
                        <span class="text-muted">Waktu Masuk Faskes:</span><br>
                        <strong>{{ $selected_persalinan->waktu_masuk ? \Carbon\Carbon::parse($selected_persalinan->waktu_masuk)->format('d/m/Y H:i') : '-' }}</strong>
                    </div>
                    <div class="col-12 col-md-2 text-md-right">
                        <span class="text-muted">Status:</span><br>
                        @if($selected_persalinan->status_persalinan === 'proses')
                            <span class="badge badge-warning px-2 py-1">Dalam Proses</span>
                        @elseif($selected_persalinan->status_persalinan === 'selesai')
                            <span class="badge badge-success px-2 py-1">Selesai</span>
                        @elseif($selected_persalinan->status_persalinan === 'rujuk')
                            <span class="badge badge-danger px-2 py-1">Rujuk</span>
                        @else
                            <span class="badge badge-secondary px-2 py-1">{{ ucfirst($selected_persalinan->status_persalinan) }}</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Card Tabel Utama --}}
    <x-card>
        <x-slot:actions>
            <x-badge color="primary" pill>
                Total: {{ count($data_observasi) }} Titik Observasi
            </x-badge>
        </x-slot:actions>

        <div class="table-responsive">
            <table
                class="table table-bordered table-hover text-nowrap"
                id="dataTable"
                width="100%"
                cellspacing="0"
            >
                <thead class="bg-light text-gray-700">
                    <tr>
                        <th class="text-center" style="width: 40px;">No</th>
                        <th class="text-center">Pasien / Register</th>
                        <th class="text-center">Waktu Pemeriksaan</th>
                        <th class="text-center">Kolom Ke-</th>
                        <th class="text-center">DJJ (x/mnt)</th>
                        <th class="text-center">Air Ketuban</th>
                        <th class="text-center">Penyusupan</th>
                        <th class="text-center">Pembukaan</th>
                        <th class="text-center">Turun Kepala</th>
                        <th class="text-center">Kontraksi (Frekuensi)</th>
                        <th class="text-center">Kontraksi (Durasi)</th>
                        <th class="text-center">Oksitosin (Unit)</th>
                        <th class="text-center">Oksitosin (Tetes)</th>
                        <th class="text-center">Obat & Cairan IV</th>
                        <th class="text-center">TD Sistolik</th>
                        <th class="text-center">TD Diastolik</th>
                        <th class="text-center">Nadi (x/mnt)</th>
                        <th class="text-center">Suhu (°C)</th>
                        <th class="text-center">Urin Protein</th>
                        <th class="text-center">Urin Aseton</th>
                        <th class="text-center">Urin Volume (ml)</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($data_observasi as $data)
                        <tr>
                            <td class="text-center align-middle font-weight-bold text-gray-600">
                                {{ $loop->iteration }}
                            </td>

                            <td class="align-middle font-weight-bold text-gray-800">
                                {{ $data->nama_pasien ?? 'Persalinan #' . $data->persalinan_id }}
                                @if(!empty($data->nomer_registerer))
                                    <br><small class="text-muted">({{ $data->nomer_registerer }})</small>
                                @endif
                            </td>

                            <td class="text-center align-middle small text-gray-700 font-weight-bold">
                                {{ $data->waktu_pemeriksaan ? \Carbon\Carbon::parse($data->waktu_pemeriksaan)->format('d/m/Y H:i') : '-' }}
                            </td>

                            <td class="text-center align-middle">
                                @if($data->kolom_partograf_ke)
                                    <span class="badge badge-primary px-2 py-1 font-weight-bold">
                                        Kolom {{ $data->kolom_partograf_ke }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- DJJ --}}
                            <td class="align-middle text-center font-weight-bold">
                                @if($data->djj_denyut_per_menit !== null)
                                    @if($data->djj_denyut_per_menit < 120)
                                        <span class="badge badge-danger px-2 py-1" title="Bradikardia Janin (< 120 dpm)">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ $data->djj_denyut_per_menit }} x/m
                                        </span>
                                    @elseif($data->djj_denyut_per_menit > 160)
                                        <span class="badge badge-warning text-dark px-2 py-1" title="Takikardia Janin (> 160 dpm)">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ $data->djj_denyut_per_menit }} x/m
                                        </span>
                                    @else
                                        <span class="badge badge-success px-2 py-1 font-weight-bold">
                                            {{ $data->djj_denyut_per_menit }} x/m
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Air Ketuban --}}
                            <td class="text-center align-middle">
                                @if($data->air_ketuban === 'U')
                                    <span class="badge badge-info px-2 py-1" title="Utuh">U (Utuh)</span>
                                @elseif($data->air_ketuban === 'J')
                                    <span class="badge badge-primary px-2 py-1" title="Jernih">J (Jernih)</span>
                                @elseif($data->air_ketuban === 'M')
                                    <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold" title="Mekonium">
                                        <i class="fas fa-exclamation-circle mr-1"></i>M (Mekonium)
                                    </span>
                                @elseif($data->air_ketuban === 'D')
                                    <span class="badge badge-danger px-2 py-1 font-weight-bold" title="Darah">
                                        <i class="fas fa-tint mr-1"></i>D (Darah)
                                    </span>
                                @elseif($data->air_ketuban === 'K')
                                    <span class="badge badge-secondary px-2 py-1" title="Kering">K (Kering)</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Penyusupan Kepala --}}
                            <td class="text-center align-middle">
                                @if($data->penyusupan_kepala === '0')
                                    <span class="badge badge-light border" title="0: Sutura terpisah">0 (Terpisah)</span>
                                @elseif($data->penyusupan_kepala === '1')
                                    <span class="badge badge-light border text-primary" title="1: Tulang bersentuhan">1 (Bersentuhan)</span>
                                @elseif($data->penyusupan_kepala === '2')
                                    <span class="badge badge-warning text-dark font-weight-bold" title="2: Tumpang tindih dapat dipisah">2 (Tumpang Tindih)</span>
                                @elseif($data->penyusupan_kepala === '3')
                                    <span class="badge badge-danger font-weight-bold" title="3: Tumpang tindih sulit dipisah">3 (Tumpang Tindih Berat)</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Pembukaan Serviks --}}
                            <td class="text-center align-middle font-weight-bold">
                                @if($data->pembukaan_serviks !== null)
                                    @if($data->pembukaan_serviks >= 4)
                                        <span class="badge badge-primary px-2 py-1 font-weight-bold" title="Fase Aktif Persalinan">
                                            {{ (float)$data->pembukaan_serviks }} cm
                                        </span>
                                    @else
                                        <span class="badge badge-light border px-2 py-1">
                                            {{ (float)$data->pembukaan_serviks }} cm
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Turunnya Kepala --}}
                            <td class="text-center align-middle font-weight-bold">
                                {{ $data->turunnya_kepala !== null ? $data->turunnya_kepala . '/5' : '-' }}
                            </td>

                            {{-- Kontraksi (Frekuensi) --}}
                            <td class="text-center align-middle">
                                {{ $data->frekuensi_kontraksi !== null ? $data->frekuensi_kontraksi . 'x / 10 mnt' : '-' }}
                            </td>

                            {{-- Kontraksi (Durasi) --}}
                            <td class="text-center align-middle">
                                @if($data->durasi_kontraksi === 'kurang_20')
                                    <span class="badge badge-secondary px-2 py-1" title="< 20 detik (simbol titik-titik)">
                                        • &lt; 20 dtk
                                    </span>
                                @elseif($data->durasi_kontraksi === 'antara_20_40')
                                    <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold" title="20 - 40 detik (simbol garis arsir)">
                                        /// 20 - 40 dtk
                                    </span>
                                @elseif($data->durasi_kontraksi === 'lebih_40')
                                    <span class="badge badge-dark px-2 py-1 font-weight-bold" title="> 40 detik (simbol blok hitam)">
                                        ■ &gt; 40 dtk
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Oksitosin Unit --}}
                            <td class="text-center align-middle">
                                {{ $data->oksitosin_unit !== null ? (float)$data->oksitosin_unit . ' U' : '-' }}
                            </td>

                            {{-- Oksitosin Tetes --}}
                            <td class="text-center align-middle">
                                {{ $data->oksitosin_tetes_per_menit !== null ? $data->oksitosin_tetes_per_menit . ' tpm' : '-' }}
                            </td>

                            {{-- Obat & Cairan IV --}}
                            <td class="text-center align-middle small" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis;">
                                {{ $data->obat_dan_cairan_iv ?? '-' }}
                            </td>

                            {{-- Tekanan Darah Sistolik --}}
                            <td class="text-center align-middle">
                                {{ $data->tekanan_darah_sistolik !== null ? $data->tekanan_darah_sistolik . ' mmHg' : '-' }}
                            </td>

                            {{-- Tekanan Darah Diastolik --}}
                            <td class="text-center align-middle">
                                {{ $data->tekanan_darah_diastolik !== null ? $data->tekanan_darah_diastolik . ' mmHg' : '-' }}
                            </td>

                            {{-- Nadi --}}
                            <td class="text-center align-middle">
                                {{ $data->nadi_per_menit !== null ? $data->nadi_per_menit . ' x/m' : '-' }}
                            </td>

                            {{-- Suhu --}}
                            <td class="text-center align-middle">
                                {{ $data->suhu_tubuh !== null ? (float)$data->suhu_tubuh . ' °C' : '-' }}
                            </td>

                            {{-- Urin Protein --}}
                            <td class="text-center align-middle">
                                {{ $data->urin_protein ?? '-' }}
                            </td>

                            {{-- Urin Aseton --}}
                            <td class="text-center align-middle">
                                {{ $data->urin_aseton ?? '-' }}
                            </td>

                            {{-- Urin Volume --}}
                            <td class="text-center align-middle">
                                {{ $data->urin_volume_ml !== null ? $data->urin_volume_ml . ' ml' : '-' }}
                            </td>

                            {{-- Kolom Aksi --}}
                            <td class="text-center align-middle text-nowrap">
                                <div class="d-inline-flex align-items-center justify-content-center">
                                    <button
                                        type="button"
                                        class="btn btn-info btn-sm shadow-sm mr-1"
                                        data-toggle="modal"
                                        data-target="#modalDetailObservasi{{ $data->id }}"
                                        title="Detail Pemeriksaan"
                                    >
                                        <i class="fas fa-eye fa-sm mr-1"></i> Detail
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-warning btn-sm shadow-sm mr-1"
                                        data-toggle="modal"
                                        data-target="#modalEditObservasi{{ $data->id }}"
                                        title="Edit Data"
                                    >
                                        <i class="fas fa-edit fa-sm mr-1"></i> Edit
                                    </button>

                                    <form
                                        action="{{ route('observasi-partograf.destroy', $data->id) }}"
                                        method="POST"
                                        class="d-inline m-0 p-0"
                                        onsubmit="return confirm('Apakah anda yakin ingin menghapus catatan observasi ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="btn btn-danger btn-sm shadow-sm"
                                            title="Hapus Data"
                                        >
                                            <i class="fas fa-trash fa-sm mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>


    {{-- Modal Tambah Data Observasi --}}
    <div
        class="modal fade text-left"
        id="modaltambahdataobservasipersalinan"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modalTambahLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered" role="document">
            <form action="{{ route('observasi-partograf.store') }}" method="POST" class="modal-content shadow border-0">
                @csrf

                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title font-weight-bold d-flex align-items-center" id="modalTambahLabel">
                        <i class="fas fa-file-medical mr-2"></i> Tambah Catatan Observasi Partograf
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-3 p-md-4">
                    {{-- Seksi 1: Data Pasien & Waktu --}}
                    <h6 class="font-weight-bold text-primary mb-3 section-header">
                        <i class="fas fa-user-injured mr-1"></i> 1. Identitas Persalinan & Waktu Pemeriksaan
                    </h6>
                    <div class="row">
                        <div class="col-12 col-md-5 form-group">
                            <label class="font-weight-bold text-gray-700">Pilih Pasien Persalinan <span class="text-danger">*</span></label>
                            <select name="persalinan_id" class="form-control" required>
                                <option value="">-- Pilih Data Persalinan Pasien --</option>
                                @foreach($data_persalinan as $dp)
                                    <option value="{{ $dp->id }}" {{ (old('persalinan_id', $selected_persalinan_id ?? '') == $dp->id) ? 'selected' : '' }}>
                                        {{ $dp->nomer_registerer ?? 'REG-' . $dp->id }} - {{ $dp->nama_ibu ?? $dp->nama_pasien }} (G{{ $dp->gravida }}P{{ $dp->para }}A{{ $dp->abortus }}, Masuk: {{ $dp->waktu_masuk ? \Carbon\Carbon::parse($dp->waktu_masuk)->format('d/m/Y H:i') : '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Waktu Pemeriksaan <span class="text-danger">*</span></label>
                            <input
                                type="datetime-local"
                                name="waktu_pemeriksaan"
                                class="form-control"
                                value="{{ old('waktu_pemeriksaan', now()->format('Y-m-d\TH:i')) }}"
                                required
                            >
                        </div>

                        <div class="col-12 col-sm-6 col-md-3 form-group">
                            <label class="font-weight-bold text-gray-700">Kolom Partograf Ke- (1 - 16)</label>
                            <input
                                type="number"
                                name="kolom_partograf_ke"
                                class="form-control"
                                placeholder="Contoh: 1, 2, 3..."
                                min="1"
                                max="16"
                                value="{{ old('kolom_partograf_ke', $next_kolom ?? 1) }}"
                            >
                            <small class="text-muted">Kolom matriks partograf (interval 30 menit)</small>
                        </div>
                    </div>

                    {{-- Seksi 2: Denyut Jantung Janin & Cairan Ketuban --}}
                    <h6 class="font-weight-bold text-primary mt-3 mb-3 section-header">
                        <i class="fas fa-heartbeat mr-1"></i> 2. Kondisi Janin
                    </h6>
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">DJJ (Denyut Jantung Janin / menit)</label>
                            <input
                                type="number"
                                name="djj_denyut_per_menit"
                                class="form-control"
                                placeholder="Contoh: 140 (Normal 120-160)"
                                min="0"
                                max="300"
                                value="{{ old('djj_denyut_per_menit') }}"
                            >
                        </div>

                        <div class="col-12 col-sm-6 col-md-4 form-group">
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

                        <div class="col-12 col-md-4 form-group">
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
                    <h6 class="font-weight-bold text-primary mt-3 mb-3 section-header">
                        <i class="fas fa-chart-line mr-1"></i> 3. Kemajuan Persalinan & Kontraksi Uterus
                    </h6>
                    <div class="row">
                        <div class="col-12 col-sm-6 col-lg-3 form-group">
                            <label class="font-weight-bold text-gray-700">Pembukaan Serviks (cm)</label>
                            <input
                                type="number"
                                step="0.1"
                                name="pembukaan_serviks"
                                class="form-control"
                                placeholder="0.0 - 10.0 (simbol X)"
                                min="0"
                                max="10"
                                value="{{ old('pembukaan_serviks') }}"
                            >
                            <small class="text-muted">Garis Waspada mulai 4 cm</small>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 form-group">
                            <label class="font-weight-bold text-gray-700">Turunnya Kepala (Perlimaan)</label>
                            <input
                                type="number"
                                name="turunnya_kepala"
                                class="form-control"
                                placeholder="0 - 5 (simbol O)"
                                min="0"
                                max="5"
                                value="{{ old('turunnya_kepala') }}"
                            >
                            <small class="text-muted">5/5 (tinggi) s/d 0/5 (dasar panggul)</small>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 form-group">
                            <label class="font-weight-bold text-gray-700">Frekuensi Kontraksi (/10 mnt)</label>
                            <input
                                type="number"
                                name="frekuensi_kontraksi"
                                class="form-control"
                                placeholder="Contoh: 3 kali"
                                min="0"
                                max="10"
                                value="{{ old('frekuensi_kontraksi') }}"
                            >
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 form-group">
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
                    <h6 class="font-weight-bold text-primary mt-3 mb-3 section-header">
                        <i class="fas fa-syringe mr-1"></i> 4. Oksitosin & Terapi Cairan IV
                    </h6>
                    <div class="row">
                        <div class="col-12 col-sm-6 col-lg-3 form-group">
                            <label class="font-weight-bold text-gray-700">Oksitosin (Unit)</label>
                            <input
                                type="number"
                                step="0.01"
                                name="oksitosin_unit"
                                class="form-control"
                                placeholder="Contoh: 0.5"
                                min="0"
                                value="{{ old('oksitosin_unit') }}"
                            >
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 form-group">
                            <label class="font-weight-bold text-gray-700">Tetes Oksitosin (/menit)</label>
                            <input
                                type="number"
                                name="oksitosin_tetes_per_menit"
                                class="form-control"
                                placeholder="Contoh: 20"
                                min="0"
                                max="300"
                                value="{{ old('oksitosin_tetes_per_menit') }}"
                            >
                        </div>

                        <div class="col-12 col-lg-6 form-group">
                            <label class="font-weight-bold text-gray-700">Obat & Cairan IV Lainnya</label>
                            <input
                                type="text"
                                name="obat_dan_cairan_iv"
                                class="form-control"
                                placeholder="Contoh: Infus RL 500 ml"
                                value="{{ old('obat_dan_cairan_iv') }}"
                            >
                        </div>
                    </div>

                    {{-- Seksi 5: Tanda-Tanda Vital Ibu --}}
                    <h6 class="font-weight-bold text-primary mt-3 mb-3 section-header">
                        <i class="fas fa-heart mr-1"></i> 5. Tanda-Tanda Vital Ibu
                    </h6>
                    <div class="row">
                        <div class="col-12 col-sm-6 col-lg-3 form-group">
                            <label class="font-weight-bold text-gray-700">TD Sistolik (mmHg)</label>
                            <input
                                type="number"
                                name="tekanan_darah_sistolik"
                                class="form-control"
                                placeholder="Contoh: 120"
                                min="0"
                                max="300"
                                value="{{ old('tekanan_darah_sistolik') }}"
                            >
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 form-group">
                            <label class="font-weight-bold text-gray-700">TD Diastolik (mmHg)</label>
                            <input
                                type="number"
                                name="tekanan_darah_diastolik"
                                class="form-control"
                                placeholder="Contoh: 80"
                                min="0"
                                max="200"
                                value="{{ old('tekanan_darah_diastolik') }}"
                            >
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 form-group">
                            <label class="font-weight-bold text-gray-700">Nadi (/menit)</label>
                            <input
                                type="number"
                                name="nadi_per_menit"
                                class="form-control"
                                placeholder="Contoh: 82"
                                min="0"
                                max="300"
                                value="{{ old('nadi_per_menit') }}"
                            >
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 form-group">
                            <label class="font-weight-bold text-gray-700">Suhu Tubuh (°C)</label>
                            <input
                                type="number"
                                step="0.1"
                                name="suhu_tubuh"
                                class="form-control"
                                placeholder="Contoh: 36.7"
                                min="30"
                                max="45"
                                value="{{ old('suhu_tubuh') }}"
                            >
                        </div>
                    </div>

                    {{-- Seksi 6: Urin --}}
                    <h6 class="font-weight-bold text-primary mt-3 mb-3 section-header">
                        <i class="fas fa-flask mr-1"></i> 6. Pemeriksaan Urin
                    </h6>
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-4 form-group">
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

                        <div class="col-12 col-sm-6 col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Aseton Urin</label>
                            <select name="urin_aseton" class="form-control">
                                <option value="">-- Pilih Hasil Aseton --</option>
                                <option value="-" {{ old('urin_aseton') == '-' ? 'selected' : '' }}>- (Negatif)</option>
                                <option value="+" {{ old('urin_aseton') == '+' ? 'selected' : '' }}>+ (Positif 1)</option>
                                <option value="++" {{ old('urin_aseton') == '++' ? 'selected' : '' }}>++ (Positif 2)</option>
                                <option value="+++" {{ old('urin_aseton') == '+++' ? 'selected' : '' }}>+++ (Positif 3)</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4 form-group">
                            <label class="font-weight-bold text-gray-700">Volume Urin (ml)</label>
                            <input
                                type="number"
                                name="urin_volume_ml"
                                class="form-control"
                                placeholder="Contoh: 150"
                                min="0"
                                max="5000"
                                value="{{ old('urin_volume_ml') }}"
                            >
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2 px-3 border-top">
                    <button type="button" class="btn btn-secondary px-3 shadow-sm mr-2" data-dismiss="modal">
                        <i class="fas fa-times fa-sm mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold">
                        <i class="fas fa-save fa-sm mr-1"></i> Simpan Data Observasi
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- Modal Detail Pemeriksaan Observasi --}}
    @foreach ($data_observasi as $data)
        <div
            class="modal fade text-left"
            id="modalDetailObservasi{{ $data->id }}"
            tabindex="-1"
            role="dialog"
            aria-labelledby="modalDetailLabel{{ $data->id }}"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
                <div class="modal-content shadow border-0">
                    <div class="modal-header bg-info text-white py-3">
                        <h5 class="modal-title font-weight-bold d-flex align-items-center" id="modalDetailLabel{{ $data->id }}">
                            <i class="fas fa-info-circle mr-2"></i> Rincian Observasi Partograf
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-3 p-md-4">
                        {{-- Header Singkat Pasien --}}
                        <div class="alert alert-light border shadow-sm mb-4">
                            <div class="row align-items-center">
                                <div class="col-sm-6">
                                    <span class="text-muted small">Nama Pasien:</span><br>
                                    <h5 class="font-weight-bold text-gray-800 mb-0">
                                        {{ $data->nama_pasien ?? 'Persalinan #' . $data->persalinan_id }}
                                    </h5>
                                    <small class="text-muted">No. Register: <strong>{{ $data->nomer_registerer ?? '-' }}</strong></small>
                                </div>
                                <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
                                    <span class="text-muted small">Waktu Pemeriksaan:</span><br>
                                    <strong class="text-primary font-weight-bold">
                                        {{ $data->waktu_pemeriksaan ? \Carbon\Carbon::parse($data->waktu_pemeriksaan)->format('d F Y, H:i') : '-' }} WIB
                                    </strong><br>
                                    <span class="badge badge-primary px-2 py-1 mt-1">Kolom Partograf Ke-{{ $data->kolom_partograf_ke ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Panel Parameter Medis --}}
                        <div class="row">
                            {{-- Janin --}}
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-left-success shadow-sm">
                                    <div class="card-header bg-light py-2 font-weight-bold text-success small">
                                        <i class="fas fa-heartbeat mr-1"></i> Kondisi Janin
                                    </div>
                                    <div class="card-body p-3 small">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td class="text-muted font-weight-bold" style="width: 45%;">Denyut Jantung Janin</td>
                                                <td>:
                                                    @if($data->djj_denyut_per_menit !== null)
                                                        @if($data->djj_denyut_per_menit < 120 || $data->djj_denyut_per_menit > 160)
                                                            <strong class="text-danger">{{ $data->djj_denyut_per_menit }} x/mnt (Abnormal)</strong>
                                                        @else
                                                            <strong class="text-success">{{ $data->djj_denyut_per_menit }} x/mnt (Normal)</strong>
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted font-weight-bold">Cairan Ketuban</td>
                                                <td>:
                                                    @if($data->air_ketuban === 'U') Utuh (belum pecah)
                                                    @elseif($data->air_ketuban === 'J') Jernih
                                                    @elseif($data->air_ketuban === 'M') <span class="text-warning font-weight-bold">Bercampur Mekonium</span>
                                                    @elseif($data->air_ketuban === 'D') <span class="text-danger font-weight-bold">Bercampur Darah</span>
                                                    @elseif($data->air_ketuban === 'K') Kering
                                                    @else - @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted font-weight-bold">Penyusupan (Molase)</td>
                                                <td>:
                                                    @if($data->penyusupan_kepala === '0') 0 (Tulang sutura terpisah)
                                                    @elseif($data->penyusupan_kepala === '1') 1 (Tulang bersentuhan)
                                                    @elseif($data->penyusupan_kepala === '2') 2 (Tumpang tindih, bisa pisah)
                                                    @elseif($data->penyusupan_kepala === '3') <span class="text-danger font-weight-bold">3 (Tumpang tindih berat)</span>
                                                    @else - @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- Kemajuan Persalinan & Kontraksi --}}
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-left-primary shadow-sm">
                                    <div class="card-header bg-light py-2 font-weight-bold text-primary small">
                                        <i class="fas fa-chart-line mr-1"></i> Kemajuan Persalinan & Kontraksi
                                    </div>
                                    <div class="card-body p-3 small">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td class="text-muted font-weight-bold" style="width: 45%;">Pembukaan Serviks</td>
                                                <td>:
                                                    @if($data->pembukaan_serviks !== null)
                                                        <strong>{{ (float)$data->pembukaan_serviks }} cm</strong>
                                                        @if($data->pembukaan_serviks >= 4)
                                                            <span class="badge badge-primary ml-1">Fase Aktif</span>
                                                        @else
                                                            <span class="badge badge-secondary ml-1">Fase Laten</span>
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted font-weight-bold">Turunnya Kepala</td>
                                                <td>: {{ $data->turunnya_kepala !== null ? $data->turunnya_kepala . ' / 5 (Perlimaan)' : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted font-weight-bold">Frekuensi Kontraksi</td>
                                                <td>: {{ $data->frekuensi_kontraksi !== null ? $data->frekuensi_kontraksi . ' kali / 10 menit' : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted font-weight-bold">Durasi Kontraksi</td>
                                                <td>:
                                                    @if($data->durasi_kontraksi === 'kurang_20') &lt; 20 detik (Titik-titik)
                                                    @elseif($data->durasi_kontraksi === 'antara_20_40') 20 - 40 detik (Garis arsir)
                                                    @elseif($data->durasi_kontraksi === 'lebih_40') &gt; 40 detik (Blok hitam)
                                                    @else - @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- Terapi Oksitosin & Obat --}}
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-left-warning shadow-sm">
                                    <div class="card-header bg-light py-2 font-weight-bold text-warning small">
                                        <i class="fas fa-syringe mr-1"></i> Oksitosin & Cairan IV
                                    </div>
                                    <div class="card-body p-3 small">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td class="text-muted font-weight-bold" style="width: 45%;">Dosis Oksitosin</td>
                                                <td>: {{ $data->oksitosin_unit !== null ? (float)$data->oksitosin_unit . ' Unit' : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted font-weight-bold">Tetes Oksitosin</td>
                                                <td>: {{ $data->oksitosin_tetes_per_menit !== null ? $data->oksitosin_tetes_per_menit . ' tetes / mnt' : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted font-weight-bold">Obat & Cairan IV</td>
                                                <td>: {{ $data->obat_dan_cairan_iv ?? '-' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- Tanda Vital & Urin --}}
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-left-danger shadow-sm">
                                    <div class="card-header bg-light py-2 font-weight-bold text-danger small">
                                        <i class="fas fa-user-check mr-1"></i> Tanda Vital Ibu & Urin
                                    </div>
                                    <div class="card-body p-3 small">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td class="text-muted font-weight-bold" style="width: 45%;">Tekanan Darah</td>
                                                <td>: <strong>{{ $data->tekanan_darah_sistolik ?? '-' }} / {{ $data->tekanan_darah_diastolik ?? '-' }} mmHg</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted font-weight-bold">Nadi & Suhu</td>
                                                <td>: {{ $data->nadi_per_menit !== null ? $data->nadi_per_menit . ' x/mnt' : '-' }} | {{ $data->suhu_tubuh !== null ? (float)$data->suhu_tubuh . ' °C' : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted font-weight-bold">Urin (Protein / Aseton)</td>
                                                <td>: {{ $data->urin_protein ?? '-' }} / {{ $data->urin_aseton ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted font-weight-bold">Volume Urin</td>
                                                <td>: {{ $data->urin_volume_ml !== null ? $data->urin_volume_ml . ' ml' : '-' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light py-2 px-3 border-top">
                        <button type="button" class="btn btn-secondary px-3 shadow-sm" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach


    {{-- Modal Edit Data Observasi --}}
    @foreach ($data_observasi as $data)
        <div
            class="modal fade text-left"
            id="modalEditObservasi{{ $data->id }}"
            tabindex="-1"
            role="dialog"
            aria-labelledby="modalEditLabel{{ $data->id }}"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered" role="document">
                <form action="{{ route('observasi-partograf.update', $data->id) }}" method="POST" class="modal-content shadow border-0">
                    @csrf
                    @method('PUT')

                    <div class="modal-header bg-warning text-dark py-3">
                        <h5 class="modal-title font-weight-bold d-flex align-items-center" id="modalEditLabel{{ $data->id }}">
                            <i class="fas fa-edit mr-2"></i> Edit Observasi Partograf: {{ $data->nama_pasien ?? ('ID Persalinan: ' . $data->persalinan_id) }}
                        </h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-3 p-md-4">
                        {{-- Seksi 1: Data Pasien & Waktu --}}
                        <h6 class="font-weight-bold text-primary mb-3 section-header">
                            <i class="fas fa-user-injured mr-1"></i> 1. Identitas Persalinan & Waktu Pemeriksaan
                        </h6>
                        <div class="row">
                            <div class="col-12 col-md-5 form-group">
                                <label class="font-weight-bold text-gray-700">Pasien Persalinan</label>
                                <input
                                    type="text"
                                    class="form-control bg-light"
                                    value="{{ ($data->nomer_registerer ?? 'REG-' . $data->persalinan_id) . ' - ' . ($data->nama_pasien ?? 'Pasien Persalinan #' . $data->persalinan_id) }}"
                                    readonly
                                >
                                <input type="hidden" name="persalinan_id" value="{{ $data->persalinan_id }}">
                            </div>

                            <div class="col-12 col-sm-6 col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Waktu Pemeriksaan <span class="text-danger">*</span></label>
                                <input
                                    type="datetime-local"
                                    name="waktu_pemeriksaan"
                                    class="form-control"
                                    value="{{ old('waktu_pemeriksaan', $data->waktu_pemeriksaan ? \Carbon\Carbon::parse($data->waktu_pemeriksaan)->format('Y-m-d\TH:i') : '') }}"
                                    required
                                >
                            </div>

                            <div class="col-12 col-sm-6 col-md-3 form-group">
                                <label class="font-weight-bold text-gray-700">Kolom Partograf Ke- (1 - 16)</label>
                                <input
                                    type="number"
                                    name="kolom_partograf_ke"
                                    class="form-control"
                                    min="1"
                                    max="16"
                                    value="{{ old('kolom_partograf_ke', $data->kolom_partograf_ke) }}"
                                >
                                <small class="text-muted">Kolom matriks partograf</small>
                            </div>
                        </div>

                        {{-- Seksi 2: Denyut Jantung Janin & Cairan Ketuban --}}
                        <h6 class="font-weight-bold text-primary mt-3 mb-3 section-header">
                            <i class="fas fa-heartbeat mr-1"></i> 2. Kondisi Janin
                        </h6>
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">DJJ (Denyut Jantung Janin / menit)</label>
                                <input
                                    type="number"
                                    name="djj_denyut_per_menit"
                                    class="form-control"
                                    min="0"
                                    max="300"
                                    value="{{ old('djj_denyut_per_menit', $data->djj_denyut_per_menit) }}"
                                >
                            </div>

                            <div class="col-12 col-sm-6 col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Air Ketuban</label>
                                <select name="air_ketuban" class="form-control">
                                    <option value="">-- Pilih Status Ketuban --</option>
                                    @foreach(['U' => 'U - Utuh (belum pecah)', 'J' => 'J - Jernih (air jernih)', 'M' => 'M - Mekonium (bercampur mekonium)', 'D' => 'D - Darah (bercampur darah)', 'K' => 'K - Kering (cairan kering)'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('air_ketuban', $data->air_ketuban) == $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Penyusupan Tulang Kepala (Molase)</label>
                                <select name="penyusupan_kepala" class="form-control">
                                    <option value="">-- Pilih Derajat Molase --</option>
                                    @foreach(['0' => '0 - Sutura terpisah', '1' => '1 - Tulang bersentuhan', '2' => '2 - Tumpang tindih (bisa dipisah)', '3' => '3 - Tumpang tindih (sulit dipisah)'] as $val => $label)
                                        <option value="{{ $val }}" {{ (string) old('penyusupan_kepala', $data->penyusupan_kepala) === (string) $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Seksi 3: Kemajuan Persalinan & Kontraksi --}}
                        <h6 class="font-weight-bold text-primary mt-3 mb-3 section-header">
                            <i class="fas fa-chart-line mr-1"></i> 3. Kemajuan Persalinan & Kontraksi Uterus
                        </h6>
                        <div class="row">
                            <div class="col-12 col-sm-6 col-lg-3 form-group">
                                <label class="font-weight-bold text-gray-700">Pembukaan Serviks (cm)</label>
                                <input
                                    type="number"
                                    step="0.1"
                                    name="pembukaan_serviks"
                                    class="form-control"
                                    min="0"
                                    max="10"
                                    value="{{ old('pembukaan_serviks', $data->pembukaan_serviks) }}"
                                >
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3 form-group">
                                <label class="font-weight-bold text-gray-700">Turunnya Kepala (Perlimaan)</label>
                                <input
                                    type="number"
                                    name="turunnya_kepala"
                                    class="form-control"
                                    min="0"
                                    max="5"
                                    value="{{ old('turunnya_kepala', $data->turunnya_kepala) }}"
                                >
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3 form-group">
                                <label class="font-weight-bold text-gray-700">Frekuensi Kontraksi (/10 mnt)</label>
                                <input
                                    type="number"
                                    name="frekuensi_kontraksi"
                                    class="form-control"
                                    min="0"
                                    max="10"
                                    value="{{ old('frekuensi_kontraksi', $data->frekuensi_kontraksi) }}"
                                >
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3 form-group">
                                <label class="font-weight-bold text-gray-700">Durasi Kontraksi</label>
                                <select name="durasi_kontraksi" class="form-control">
                                    <option value="">-- Pilih Durasi --</option>
                                    @foreach(['kurang_20' => '< 20 detik', 'antara_20_40' => '20 - 40 detik', 'lebih_40' => '> 40 detik'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('durasi_kontraksi', $data->durasi_kontraksi) == $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Seksi 4: Oksitosin & Cairan IV --}}
                        <h6 class="font-weight-bold text-primary mt-3 mb-3 section-header">
                            <i class="fas fa-syringe mr-1"></i> 4. Oksitosin & Terapi Cairan IV
                        </h6>
                        <div class="row">
                            <div class="col-12 col-sm-6 col-lg-3 form-group">
                                <label class="font-weight-bold text-gray-700">Oksitosin (Unit)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="oksitosin_unit"
                                    class="form-control"
                                    min="0"
                                    value="{{ old('oksitosin_unit', $data->oksitosin_unit) }}"
                                >
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3 form-group">
                                <label class="font-weight-bold text-gray-700">Tetes Oksitosin (/menit)</label>
                                <input
                                    type="number"
                                    name="oksitosin_tetes_per_menit"
                                    class="form-control"
                                    min="0"
                                    max="300"
                                    value="{{ old('oksitosin_tetes_per_menit', $data->oksitosin_tetes_per_menit) }}"
                                >
                            </div>

                            <div class="col-12 col-lg-6 form-group">
                                <label class="font-weight-bold text-gray-700">Obat & Cairan IV Lainnya</label>
                                <input
                                    type="text"
                                    name="obat_dan_cairan_iv"
                                    class="form-control"
                                    value="{{ old('obat_dan_cairan_iv', $data->obat_dan_cairan_iv) }}"
                                >
                            </div>
                        </div>

                        {{-- Seksi 5: Tanda-Tanda Vital Ibu --}}
                        <h6 class="font-weight-bold text-primary mt-3 mb-3 section-header">
                            <i class="fas fa-heart mr-1"></i> 5. Tanda-Tanda Vital Ibu
                        </h6>
                        <div class="row">
                            <div class="col-12 col-sm-6 col-lg-3 form-group">
                                <label class="font-weight-bold text-gray-700">TD Sistolik (mmHg)</label>
                                <input
                                    type="number"
                                    name="tekanan_darah_sistolik"
                                    class="form-control"
                                    min="0"
                                    max="300"
                                    value="{{ old('tekanan_darah_sistolik', $data->tekanan_darah_sistolik) }}"
                                >
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3 form-group">
                                <label class="font-weight-bold text-gray-700">TD Diastolik (mmHg)</label>
                                <input
                                    type="number"
                                    name="tekanan_darah_diastolik"
                                    class="form-control"
                                    min="0"
                                    max="200"
                                    value="{{ old('tekanan_darah_diastolik', $data->tekanan_darah_diastolik) }}"
                                >
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3 form-group">
                                <label class="font-weight-bold text-gray-700">Nadi (/menit)</label>
                                <input
                                    type="number"
                                    name="nadi_per_menit"
                                    class="form-control"
                                    min="0"
                                    max="300"
                                    value="{{ old('nadi_per_menit', $data->nadi_per_menit) }}"
                                >
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3 form-group">
                                <label class="font-weight-bold text-gray-700">Suhu Tubuh (°C)</label>
                                <input
                                    type="number"
                                    step="0.1"
                                    name="suhu_tubuh"
                                    class="form-control"
                                    min="30"
                                    max="45"
                                    value="{{ old('suhu_tubuh', $data->suhu_tubuh) }}"
                                >
                            </div>
                        </div>

                        {{-- Seksi 6: Urin --}}
                        <h6 class="font-weight-bold text-primary mt-3 mb-3 section-header">
                            <i class="fas fa-flask mr-1"></i> 6. Pemeriksaan Urin
                        </h6>
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Protein Urin</label>
                                <select name="urin_protein" class="form-control">
                                    <option value="">-- Pilih Hasil Protein --</option>
                                    @foreach(['-' => '- (Negatif)', '+' => '+ (Positif 1)', '++' => '++ (Positif 2)', '+++' => '+++ (Positif 3)', '++++' => '++++ (Positif 4)'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('urin_protein', $data->urin_protein) == $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Aseton Urin</label>
                                <select name="urin_aseton" class="form-control">
                                    <option value="">-- Pilih Hasil Aseton --</option>
                                    @foreach(['-' => '- (Negatif)', '+' => '+ (Positif 1)', '++' => '++ (Positif 2)', '+++' => '+++ (Positif 3)'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('urin_aseton', $data->urin_aseton) == $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-4 form-group">
                                <label class="font-weight-bold text-gray-700">Volume Urin (ml)</label>
                                <input
                                    type="number"
                                    name="urin_volume_ml"
                                    class="form-control"
                                    min="0"
                                    max="5000"
                                    value="{{ old('urin_volume_ml', $data->urin_volume_ml) }}"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light py-2 px-3 border-top">
                        <button type="button" class="btn btn-secondary px-3 shadow-sm mr-2" data-dismiss="modal">
                            <i class="fas fa-times fa-sm mr-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-warning shadow-sm font-weight-bold">
                            <i class="fas fa-save fa-sm mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

@endsection


@push('styles')
    <!-- Custom styles for DataTables -->
    <link href="{{ asset('template/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <style>
        /*
        |--------------------------------------------------------------------------
        | Perbaikan Modal Scrollable & Tombol Footer
        |--------------------------------------------------------------------------
        */
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

        .modal-dialog-scrollable .modal-header {
            flex-shrink: 0 !important;
            z-index: 1050;
        }

        .modal-dialog-scrollable .modal-body {
            flex: 1 1 auto !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
            padding: 1.25rem 1.5rem !important;
        }

        .modal-dialog-scrollable .modal-footer {
            flex-shrink: 0 !important;
            background-color: #f8f9fc !important;
            border-top: 1px solid #e3e6f0 !important;
            z-index: 1050;
            padding: 0.85rem 1.5rem !important;
            box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.04);
        }

        /* Scrollbar Halus untuk Modal Body */
        .modal-dialog-scrollable .modal-body::-webkit-scrollbar {
            width: 8px;
        }
        .modal-dialog-scrollable .modal-body::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Form Styling & Header Seksi */
        .modal-body .form-group {
            margin-bottom: 0.85rem;
        }
        .modal-body .form-group label {
            font-size: 0.825rem;
            font-weight: 600;
            margin-bottom: 0.35rem;
            color: #374151;
        }
        .modal-body h6.section-header {
            background-color: #f0fdf4;
            color: #15803d !important;
            border-left: 4px solid #16a34a;
            padding: 0.45rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0 0.35rem 0.35rem 0;
        }

        /* Select Persalinan Styling */
        .select-persalinan {
            border: 1.5px solid #4e73df;
            border-radius: 0.35rem;
            font-weight: 600;
            color: #2e59d9;
        }

        /*
        |--------------------------------------------------------------------------
        | DataTables & Responsive Table Styling
        |--------------------------------------------------------------------------
        */
        .table-responsive {
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
            padding-bottom: 6px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
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
            white-space: nowrap;
            vertical-align: middle;
            font-size: 0.85rem;
        }
    </style>
@endpush

@push('scripts')
    <!-- Custom scripts for DataTables -->
    <script src="{{ asset('template/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable({
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari data observasi...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    emptyTable: "Belum ada data observasi partograf",
                    zeroRecords: "Tidak ditemukan data observasi yang sesuai",
                    paginate: {
                        first: '<i class="fas fa-angle-double-left"></i>',
                        last: '<i class="fas fa-angle-double-right"></i>',
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    }
                },
                columnDefs: [
                    {
                        orderable: false,
                        targets: [21]
                    }
                ],
                pageLength: 10,
                order: [
                    [3, 'asc'], // Urutkan berdasarkan kolom partograf ke-
                    [2, 'asc']
                ]
            });
        });
    </script>
@endpush
