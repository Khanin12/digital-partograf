<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/test') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-heartbeat"></i>
        </div>
        <div class="sidebar-brand-text mx-2 font-weight-bold">PARTOGRAF</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->is('test') || request()->is('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/test') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Pelayanan Partograf
    </div>

    <!-- Nav Item - Partograf Collapse Menu -->
    <li class="nav-item {{ request()->is('partograf*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePartograf"
            aria-expanded="true" aria-controls="collapsePartograf">
            <i class="fas fa-fw fa-file-medical-alt"></i>
            <span>Partograf</span>
        </a>
        <div id="collapsePartograf" class="collapse" aria-labelledby="headingPartograf" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Menu Rekam Medis:</h6>
                <a class="collapse-item" href="#">Data Ibu Bersalin</a>
                <a class="collapse-item" href="#">Observasi Persalinan</a>
                <a class="collapse-item" href="#">Grafik Pembukaan</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Pasien -->
    <li class="nav-item {{ request()->is('pasien*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('pasien.index') }}">
            <i class="fas fa-fw fa-user-injured"></i>
            <span>master pasien</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('persalinan*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('persalinan.index') }}">
            <i class="fas fa-fw fa-heartbeat"></i>
            <span>master persalinan</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('observasi-partograf*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('observasi-partograf.index') }}">
            <i class="fas fa-fw fa-baby"></i>
            <span>Observasi Partograf</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('catatan-persalinan*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('catatan-persalinan.index') }}">
            <i class="fas fa-fw fa-clipboard-list"></i>
            <span>catatan persalinan</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('bayi-baru-lahir*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('bayi-baru-lahir.index') }}">
            <i class="fas fa-fw fa-baby"></i>
            <span>Bayi Baru Lahir</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('pemantauan-kala-4*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('pemantauan-kala-4.index') }}">
            <i class="fas fa-fw fa-procedures"></i>
            <span>Pemantauan Kala IV</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('edukasi-kie*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('edukasi-kie.index') }}">
            <i class="fas fa-fw fa-comments"></i>
            <span>Edukasi & KIE</span>
        </a>
    </li>


    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Manajemen
    </div>

    <!-- Nav Item - Bidan / Nakes -->
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-user-md"></i>
            <span>Petugas Medis</span>
        </a>
    </li>

    <!-- Nav Item - Laporan -->
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-chart-line"></i>
            <span>Laporan & Statistik</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
