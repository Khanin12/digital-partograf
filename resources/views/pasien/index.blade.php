@extends('layouts.master')
@section('title', 'Data Pasien')

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

    <!-- Page Heading -->
    <x-page-header
        title="Data Pasien"
        subtitle="Master data pasien terkini, identitas keluarga, dan rekam medis partograf."
        icon="fas fa-user-injured">
        <button type="button" class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahPasien">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Data Pasien
        </button>
    </x-page-header>

    <!-- Card Tabel Pasien -->
    <x-card title="Data Pasien Terdaftar" icon="fas fa-table">
        <x-slot:actions>
            <x-badge color="primary" pill>
                Total: {{ count($data_pasien) }} Pasien
            </x-badge>
        </x-slot:actions>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-light text-gray-700">
                    <tr>
                        <th class="text-center" style="width: 45px;">No</th>
                        <th class="text-center">No. Register</th>
                        <th>Nama Ibu</th>
                        <th>Nama Ayah</th>
                        <th class="text-center">Usia Ibu</th>
                        <th>Alamat</th>
                        <th class="text-center">No. Telepon</th>
                        <th class="text-center" style="width: 330px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data_pasien as $data)
                        <tr>
                            <td class="text-center align-middle font-weight-bold text-gray-600">
                                {{ $loop->iteration }}
                            </td>
                            <td class="text-center align-middle">
                                <x-badge color="info">
                                    {{ $data->nomer_registerer }}
                                </x-badge>
                            </td>
                            <td class="align-middle font-weight-bold text-gray-800">
                                {{ $data->nama_ibu }}
                            </td>
                            <td class="align-middle text-gray-700">
                                {{ $data->nama_ayah }}
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-light border">{{ $data->usia_ibu }} th</span>
                            </td>
                            <td class="align-middle small text-gray-600">
                                {{ $data->alamat ?? '-' }}
                            </td>
                            <td class="text-center align-middle small text-gray-600">
                                {{ $data->nomor_telepon ?? '-' }}
                            </td>
                            <td class="text-center align-middle text-nowrap">
                                <div class="d-inline-flex align-items-center justify-content-center">
                                    <!-- Tombol Dropdown Form & Status Partograf -->
                                    <div class="dropdown d-inline mr-1 text-left">
                                        <button class="btn btn-primary btn-sm dropdown-toggle shadow-sm font-weight-bold" type="button" id="dropdownRekamMedis{{ $data->id }}" data-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" title="Isi & Pantau Rekam Medis Partograf Pasien">
                                            <i class="fas fa-notes-medical mr-1"></i> Rekam Medis
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-rekam shadow border-0 p-2" aria-labelledby="dropdownRekamMedis{{ $data->id }}" style="min-width: 325px; border-radius: 8px;">
                                            <div class="px-2 py-1 mb-1 border-bottom d-flex justify-content-between align-items-center bg-light rounded">
                                                <span class="font-weight-bold text-primary small"><i class="fas fa-notes-medical mr-1"></i> Rekam Medis (1 s/d 6 Lengkap)</span>
                                                <span class="badge badge-primary">{{ $data->nomer_registerer }}</span>
                                            </div>
                                            
                                            <!-- 1. Admisi Persalinan -->
                                            <a class="dropdown-item rounded py-1 px-2 mb-1 d-flex align-items-center justify-content-between" href="{{ route('pasien.input_terpadu', ['pasien_id' => $data->id, 'tab' => 'persalinan']) }}">
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-2 text-center" style="width: 22px;">
                                                        <i class="{{ $data->persalinan_status['icon_modul'] }} text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold text-gray-800 small">1. Admisi Persalinan</div>
                                                        <div class="text-muted" style="font-size: 10px;">
                                                            @if($data->persalinan_status['time'])
                                                                <i class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($data->persalinan_status['time'])->format('d/m/y H:i') }}
                                                            @else
                                                                Belum ada admisi
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="badge badge-{{ $data->persalinan_status['badge'] }} px-2 py-1" style="font-size: 10px;">
                                                        <i class="{{ $data->persalinan_status['icon_status'] }} mr-1"></i>{{ $data->persalinan_status['label'] }}
                                                    </span>
                                                </div>
                                            </a>

                                            <!-- 2. Observasi Partograf -->
                                            <a class="dropdown-item rounded py-1 px-2 mb-1 d-flex align-items-center justify-content-between" href="{{ route('pasien.input_terpadu', ['pasien_id' => $data->id, 'tab' => 'observasi']) }}">
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-2 text-center" style="width: 22px;">
                                                        <i class="{{ $data->observasi_status['icon_modul'] }} text-info"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold text-gray-800 small">2. Observasi Partograf</div>
                                                        <div class="text-muted" style="font-size: 10px;">
                                                            @if($data->observasi_status['time'])
                                                                <i class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($data->observasi_status['time'])->format('d/m/y H:i') }}
                                                            @else
                                                                Belum ada observasi
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="badge badge-{{ $data->observasi_status['badge'] }} px-2 py-1" style="font-size: 10px;">
                                                        <i class="{{ $data->observasi_status['icon_status'] }} mr-1"></i>{{ $data->observasi_status['label'] }}
                                                    </span>
                                                </div>
                                            </a>

                                            <!-- 3. Catatan Persalinan -->
                                            <a class="dropdown-item rounded py-1 px-2 mb-1 d-flex align-items-center justify-content-between" href="{{ route('pasien.input_terpadu', ['pasien_id' => $data->id, 'tab' => 'catatan']) }}">
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-2 text-center" style="width: 22px;">
                                                        <i class="{{ $data->catatan_status['icon_modul'] }} text-success"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold text-gray-800 small">3. Catatan Persalinan</div>
                                                        <div class="text-muted" style="font-size: 10px;">
                                                            @if($data->catatan_status['time'])
                                                                <i class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($data->catatan_status['time'])->format('d/m/y H:i') }}
                                                            @else
                                                                Kala I s/d Kala IV
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="badge badge-{{ $data->catatan_status['badge'] }} px-2 py-1" style="font-size: 10px;">
                                                        <i class="{{ $data->catatan_status['icon_status'] }} mr-1"></i>{{ $data->catatan_status['label'] }}
                                                    </span>
                                                </div>
                                            </a>

                                            <!-- 4. Bayi Baru Lahir -->
                                            <a class="dropdown-item rounded py-1 px-2 mb-1 d-flex align-items-center justify-content-between" href="{{ route('pasien.input_terpadu', ['pasien_id' => $data->id, 'tab' => 'bayi']) }}">
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-2 text-center" style="width: 22px;">
                                                        <i class="{{ $data->bayi_status['icon_modul'] }} text-warning"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold text-gray-800 small">4. Bayi Baru Lahir</div>
                                                        <div class="text-muted" style="font-size: 10px;">
                                                            @if($data->bayi_status['time'])
                                                                <i class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($data->bayi_status['time'])->format('d/m/y H:i') }}
                                                            @else
                                                                Kelahiran & APGAR
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="badge badge-{{ $data->bayi_status['badge'] }} px-2 py-1" style="font-size: 10px;">
                                                        <i class="{{ $data->bayi_status['icon_status'] }} mr-1"></i>{{ $data->bayi_status['label'] }}
                                                    </span>
                                                </div>
                                            </a>

                                            <!-- 5. Pemantauan Kala IV -->
                                            <a class="dropdown-item rounded py-1 px-2 mb-1 d-flex align-items-center justify-content-between" href="{{ route('pasien.input_terpadu', ['pasien_id' => $data->id, 'tab' => 'kala4']) }}">
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-2 text-center" style="width: 22px;">
                                                        <i class="{{ $data->kala4_status['icon_modul'] }} text-danger"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold text-gray-800 small">5. Pemantauan Kala IV</div>
                                                        <div class="text-muted" style="font-size: 10px;">
                                                            @if($data->kala4_status['time'])
                                                                <i class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($data->kala4_status['time'])->format('d/m/y H:i') }}
                                                            @else
                                                                TTV & Perdarahan
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="badge badge-{{ $data->kala4_status['badge'] }} px-2 py-1" style="font-size: 10px;">
                                                        <i class="{{ $data->kala4_status['icon_status'] }} mr-1"></i>{{ $data->kala4_status['label'] }}
                                                    </span>
                                                </div>
                                            </a>

                                            <!-- 6. Edukasi & KIE -->
                                            <a class="dropdown-item rounded py-1 px-2 mb-1 d-flex align-items-center justify-content-between" href="{{ route('pasien.input_terpadu', ['pasien_id' => $data->id, 'tab' => 'edukasi']) }}">
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-2 text-center" style="width: 22px;">
                                                        <i class="{{ $data->edukasi_status['icon_modul'] }} text-secondary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold text-gray-800 small">6. Edukasi & KIE</div>
                                                        <div class="text-muted" style="font-size: 10px;">
                                                            @if($data->edukasi_status['time'])
                                                                <i class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($data->edukasi_status['time'])->format('d/m/y H:i') }}
                                                            @else
                                                                Konseling nakes
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="badge badge-{{ $data->edukasi_status['badge'] }} px-2 py-1" style="font-size: 10px;">
                                                        <i class="{{ $data->edukasi_status['icon_status'] }} mr-1"></i>{{ $data->edukasi_status['label'] }}
                                                    </span>
                                                </div>
                                            </a>

                                            <div class="dropdown-divider my-1"></div>

                                            <a class="dropdown-item text-center font-weight-bold text-primary py-2 rounded bg-light" href="{{ route('pasien.input_terpadu', ['pasien_id' => $data->id]) }}">
                                                <i class="fas fa-external-link-alt mr-1"></i> Buka Lembar Input Terpadu
                                            </a>
                                        </div>
                                    </div>

                                    <a href="{{ route('partograf.cetak_by_pasien', $data->id) }}"
                                       target="_blank"
                                       class="btn btn-info btn-sm shadow-sm mr-1"
                                       title="Cetak Lembar Resmi Partograf (PDF)">
                                        <i class="fas fa-print fa-sm mr-1"></i> Cetak
                                    </a>
                                    <button type="button"
                                            class="btn btn-warning btn-sm shadow-sm mr-1"
                                            data-toggle="modal"
                                            data-target="#modalEditPasien{{ $data->id }}"
                                            title="Edit Data Pasien">
                                        <i class="fas fa-edit fa-sm mr-1"></i> Edit
                                    </button>
                                    <form action="{{ route('pasien.destroy', $data->id) }}"
                                          method="POST"
                                          class="d-inline m-0 p-0"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pasien {{ $data->nama_ibu }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-danger btn-sm shadow-sm"
                                                title="Hapus Data">
                                            <i class="fas fa-trash fa-sm mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>

                                <!-- Modal Edit Data Pasien -->
                                <div class="modal fade" id="modalEditPasien{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel{{ $data->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg text-left" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('pasien.update', $data->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header bg-warning text-dark">
                                                    <h5 class="modal-title font-weight-bold" id="modalEditLabel{{ $data->id }}">
                                                        <i class="fas fa-edit mr-2"></i> Edit Data Pasien: {{ $data->nama_ibu }}
                                                    </h5>
                                                    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6 form-group">
                                                            <label class="font-weight-bold text-gray-700">Nomor Register <span class="text-danger">*</span></label>
                                                            <input type="text" name="nomer_registerer" class="form-control" value="{{ old('nomer_registerer', $data->nomer_registerer) }}" required>
                                                        </div>

                                                        <div class="col-md-6 form-group">
                                                            <label class="font-weight-bold text-gray-700">Usia Ibu (Tahun) <span class="text-danger">*</span></label>
                                                            <input type="number" name="usia_ibu" class="form-control" value="{{ old('usia_ibu', $data->usia_ibu) }}" min="10" max="100" required>
                                                        </div>

                                                        <div class="col-md-6 form-group">
                                                            <label class="font-weight-bold text-gray-700">Nama Lengkap Ibu <span class="text-danger">*</span></label>
                                                            <input type="text" name="nama_ibu" class="form-control" value="{{ old('nama_ibu', $data->nama_ibu) }}" required>
                                                        </div>

                                                        <div class="col-md-6 form-group">
                                                            <label class="font-weight-bold text-gray-700">Nama Lengkap Suami / Ayah <span class="text-danger">*</span></label>
                                                            <input type="text" name="nama_ayah" class="form-control" value="{{ old('nama_ayah', $data->nama_ayah) }}" required>
                                                        </div>

                                                        <div class="col-md-6 form-group">
                                                            <label class="font-weight-bold text-gray-700">Nomor Telepon / WhatsApp</label>
                                                            <input type="text" name="nomor_telepon" class="form-control" value="{{ old('nomor_telepon', $data->nomor_telepon) }}">
                                                        </div>

                                                        <div class="col-md-6 form-group">
                                                            <label class="font-weight-bold text-gray-700">Alamat Tempat Tinggal</label>
                                                            <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $data->alamat) }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning shadow-sm font-weight-bold">
                                                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-user-slash fa-3x mb-3 text-gray-300 d-block"></i>
                                <h6 class="font-weight-bold text-gray-600">Belum Ada Data Pasien</h6>
                                <p class="small text-muted mb-3">Silakan tambahkan data pasien baru untuk memulai pencatatan partograf.</p>
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalTambahPasien">
                                    <i class="fas fa-plus fa-sm mr-1"></i> Tambah Pasien Pertama
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- Modal Tambah Data Pasien -->
    <div class="modal fade" id="modalTambahPasien" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('pasien.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold" id="modalLabel">
                            <i class="fas fa-user-plus mr-2"></i> Tambah Data Pasien Baru
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700" for="nomer_registerer">Nomor Register <span class="text-danger">*</span></label>
                                <input type="text" name="nomer_registerer" id="nomer_registerer" class="form-control" placeholder="Contoh: REG-001" required value="{{ old('nomer_registerer') }}">
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700" for="usia_ibu">Usia Ibu (Tahun) <span class="text-danger">*</span></label>
                                <input type="number" name="usia_ibu" id="usia_ibu" class="form-control" placeholder="Contoh: 26" min="10" max="100" required value="{{ old('usia_ibu') }}">
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700" for="nama_ibu">Nama Lengkap Ibu <span class="text-danger">*</span></label>
                                <input type="text" name="nama_ibu" id="nama_ibu" class="form-control" placeholder="Nama ibu bersalin" required value="{{ old('nama_ibu') }}">
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700" for="nama_ayah">Nama Lengkap Suami / Ayah <span class="text-danger">*</span></label>
                                <input type="text" name="nama_ayah" id="nama_ayah" class="form-control" placeholder="Nama suami / ayah" required value="{{ old('nama_ayah') }}">
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700" for="nomor_telepon">Nomor Telepon / WhatsApp</label>
                                <input type="text" name="nomor_telepon" id="nomor_telepon" class="form-control" placeholder="Contoh: 081234567890" value="{{ old('nomor_telepon') }}">
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-gray-700" for="alamat">Alamat Tempat Tinggal</label>
                                <textarea name="alamat" id="alamat" class="form-control" rows="2" placeholder="Alamat lengkap domisili pasien">{{ old('alamat') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="fas fa-save mr-1"></i> Simpan Data Pasien
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
        /* Mencegah dropdown rekam medis terpotong oleh overflow table-responsive */
        .card,
        .card .card-body,
        .card .table-responsive,
        .dataTables_wrapper {
            overflow: visible !important;
        }
        .dropdown-menu-rekam {
            min-width: 325px;
            max-height: 480px;
            overflow-y: auto;
            z-index: 1060 !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.18) !important;
            border-radius: 10px;
        }
        .dropdown-menu-rekam::-webkit-scrollbar {
            width: 5px;
        }
        .dropdown-menu-rekam::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
        }
        .dropdown-menu-rekam .dropdown-item:hover {
            background-color: #f1f5f9;
        }
    </style>
@endpush

@push('scripts')
    <!-- Page level DataTables plugins & demo script -->
    <script src="{{ asset('template/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('template/js/demo/datatables-demo.js') }}"></script>
@endpush

