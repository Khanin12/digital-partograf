@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-download fa-sm text-white-50 mr-1"></i> Generate Report
    </a>
</div>

<!-- Content Row: 4 Stat Cards (SB Admin 2 Structure) -->
<div class="row">

    <!-- Earnings (Monthly) -> Ibu Bersalin (Aktif) -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Ibu Bersalin (Aktif)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">12 Pasien</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-female fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Annual) -> Persalinan Normal -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Persalinan Normal</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">48 Bayi</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-baby fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks -> Observasi Waspada (With Progress Bar) -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Observasi Waspada
                        </div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">50%</div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: 50%" aria-valuenow="50" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests -> Perlu Tindakan / Rujukan -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Perlu Tindakan / Rujukan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comments fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Content Row: Charts (Exact SB Admin 2 Heights & Structure) -->
<div class="row">

    <!-- Area Chart: Earnings Overview -> Tren Pemeriksaan Partograf -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Tren Pemeriksaan Partograf (Tahun {{ date('Y') }})</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Pilihan Filter:</div>
                        <a class="dropdown-item" href="#">Bulan Ini</a>
                        <a class="dropdown-item" href="#">Tahun Ini ({{ date('Y') }})</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Export Grafik</a>
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart: Revenue Sources -> Metode Persalinan -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Metode Persalinan</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLinkPie"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLinkPie">
                        <div class="dropdown-header">Opsi:</div>
                        <a class="dropdown-item" href="#">Detail Rasio</a>
                        <a class="dropdown-item" href="#">Download Grafik</a>
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="myPieChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> Spontan (68%)
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-info"></i> Induksi (22%)
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-warning"></i> SC (10%)
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Content Row: Projects & Illustrations Row (Exact SB Admin 2 Grid) -->
<div class="row">

    <!-- Content Column Left: Projects & Table -->
    <div class="col-lg-6 mb-4">

        <!-- Project Card Example: Kemajuan Fase Persalinan -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Status Pemantauan Partograf</h6>
            </div>
            <div class="card-body">
                <h4 class="small font-weight-bold">Fase Laten (Pembukaan 1-3 cm) <span class="float-right">20%</span></h4>
                <div class="progress mb-4">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 20%"
                        aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <h4 class="small font-weight-bold">Fase Aktif Normal (Pembukaan 4-10 cm) <span class="float-right">40%</span></h4>
                <div class="progress mb-4">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 40%"
                        aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <h4 class="small font-weight-bold">Mendekati Garis Waspada <span class="float-right">60%</span></h4>
                <div class="progress mb-4">
                    <div class="progress-bar" role="progressbar" style="width: 60%"
                        aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <h4 class="small font-weight-bold">Rujukan / Tindakan Cepat <span class="float-right">80%</span></h4>
                <div class="progress mb-4">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 80%"
                        aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <h4 class="small font-weight-bold">Persalinan Berhasil / Selesai <span class="float-right">Lengkap!</span></h4>
                <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%"
                        aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>

        <!-- Tabel Pasien Bersalin Aktif -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Pasien Bersalin Dalam Pemantauan</h6>
                <span class="badge badge-success px-2 py-1"><i class="fas fa-broadcast-tower mr-1"></i>Real-time</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="bg-light text-gray-700">
                            <tr>
                                <th>No. RM</th>
                                <th>Nama Pasien</th>
                                <th>Pembukaan</th>
                                <th>DJJ</th>
                                <th>Status Partograf</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-weight-bold">RM-081</td>
                                <td>Ny. Anisa Rahma <small class="text-muted d-block">G1P0A0 &bull; 26 th</small></td>
                                <td><span class="badge badge-primary px-2 py-1">6 cm</span></td>
                                <td><span class="text-success font-weight-bold">138 dpm</span></td>
                                <td><span class="badge badge-success px-2 py-1">Normal (Fase Aktif)</span></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">RM-084</td>
                                <td>Ny. Fitri Handayani <small class="text-muted d-block">G2P1A0 &bull; 31 th</small></td>
                                <td><span class="badge badge-info px-2 py-1">8 cm</span></td>
                                <td><span class="text-warning font-weight-bold">144 dpm</span></td>
                                <td><span class="badge badge-warning px-2 py-1 text-dark">Dekat Waspada</span></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">RM-089</td>
                                <td>Ny. Dewi Sartika <small class="text-muted d-block">G1P0A0 &bull; 29 th</small></td>
                                <td><span class="badge badge-danger px-2 py-1">4 cm</span></td>
                                <td><span class="text-danger font-weight-bold">162 dpm</span></td>
                                <td><span class="badge badge-danger px-2 py-1">Garis Bertindak</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-center py-2">
                <a href="#" class="small font-weight-bold text-primary">Lihat Seluruh Data Pasien &rarr;</a>
            </div>
        </div>

    </div>

    <!-- Content Column Right: Illustrations & WHO Guide -->
    <div class="col-lg-6 mb-4">

        <!-- Illustrations -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Panduan Klinis Partograf (WHO / Kemenkes)</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 25rem;"
                        src="{{ asset('template/img/undraw_posting_photo.svg') }}" alt="Medical Illustration">
                </div>
                <p class="text-gray-700">
                    Partograf adalah instrumen rekam medis standar WHO dan Kementerian Kesehatan RI untuk memantau kemajuan persalinan, mendeteksi penyulit sedini mungkin, dan mencegah keterlambatan penanganan rujukan obstetri.
                </p>
                <a target="_blank" rel="nofollow" href="#">Pelajari Standar Asuhan Persalinan Normal (APN) &rarr;</a>
            </div>
        </div>

        <!-- Approach / Critical Action Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tindakan Siaga & Evaluasi Garis</h6>
            </div>
            <div class="card-body text-gray-700">
                <p>
                    <strong class="text-warning"><i class="fas fa-exclamation-triangle mr-1"></i>Garis Waspada (Alert Line):</strong>
                    Mulai pembukaan 4 cm. Laju pembukaan serviks normal adalah minimal 1 cm/jam selama fase aktif. Bila grafik bergeser ke kanan garis waspada, waspadai adanya distosia atau kemajuan persalinan yang lambat.
                </p>
                <p class="mb-0">
                    <strong class="text-danger"><i class="fas fa-radiation mr-1"></i>Garis Bertindak (Action Line):</strong>
                    Terletak tepat 4 jam di sebelah kanan garis waspada. Bila kurva serviks menyentuh garis ini, keputusan tindakan definitif (augmentasi, rujukan ke RS PONEK, atau tindakan SC) harus segera dieksekusi.
                </p>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
    <!-- Page level plugins -->
    <script src="{{ asset('template/vendor/chart.js/Chart.min.js') }}"></script>

    <!-- Page level custom scripts -->
    <script src="{{ asset('template/js/demo/chart-area-demo.js') }}"></script>
    <script src="{{ asset('template/js/demo/chart-pie-demo.js') }}"></script>
@endpush
