@extends('layouts.master')

@section('title', 'Halaman Contoh - Partograf System')

@section('content')
    <!-- Header Halaman -->
    <x-page-header title="Judul Halaman" subtitle="Deskripsi atau sub-judul halaman">
        <a href="#" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Data
        </a>
    </x-page-header>

    <!-- Baris Kartu Statistik (Opsional) -->
    <div class="row">
        <x-stat-card title="Total Pasien" value="128" icon="fas fa-user-injured" color="primary" />
        <x-stat-card title="Partograf Aktif" value="14" icon="fas fa-file-medical-alt" color="success" />
        <x-stat-card title="Selesai Observasi" value="85%" icon="fas fa-clipboard-check" color="info" :progress="85" />
        <x-stat-card title="Perlu Perhatian" value="3" icon="fas fa-exclamation-triangle" color="warning" />
    </div>

    <!-- Konten Utama -->
    <x-card title="Data & Informasi">
        <p class="mb-0">
            Ini adalah contoh halaman baru yang menggunakan <strong>Master Layout</strong> dan <strong>Blade Components</strong>.
            Cukup definisikan <code>@section('content')</code> untuk menambahkan konten baru tanpa perlu menulis ulang sidebar, navbar, atau footer.
        </p>
    </x-card>
@endsection
