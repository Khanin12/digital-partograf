@extends('layouts.app')

@section('title', 'Selamat Datang - Partograf System')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5 text-center">
                    <div class="mb-4">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fs-6">
                            <i class="bi bi-bootstrap-fill me-1"></i> HTML Bootstrap Template Ready
                        </span>
                    </div>
                    <h1 class="display-6 fw-bold text-dark mb-3">Selamat Datang di Partograf System</h1>
                    <p class="lead text-secondary mb-4">
                        Aplikasi telah dikonfigurasi untuk menggunakan template Bootstrap HTML statis tanpa dependensi atau bundler Vite.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#panduan" class="btn btn-primary px-4 py-2">
                            <i class="bi bi-info-circle me-1"></i> Panduan Memasang Template
                        </a>
                    </div>
                </div>
            </div>

            <!-- Petunjuk Penempatan File Template -->
            <div class="card shadow-sm border-0 mt-4" id="panduan">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="bi bi-folder2-open me-2 text-primary"></i>Panduan Memasang Template Bootstrap Anda
                    </h5>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <strong>1. Simpan File Aset Statis (CSS, JS, Gambar, Vendor):</strong>
                            <p class="mb-1 text-muted">Salin folder aset template Anda (misal: <code>css</code>, <code>js</code>, <code>vendor</code>, <code>img</code>) ke dalam folder <code>public/</code>.</p>
                            <span class="badge bg-secondary">Contoh lokasi: <code>public/assets/</code> atau <code>public/css/</code></span>
                        </li>
                        <li class="list-group-item px-0">
                            <strong>2. Cara Panggil Aset di Blade:</strong>
                            <p class="mb-1 text-muted">Gunakan fungsi <code>asset('path/ke/file')</code>, tanpa memerlukan Vite:</p>
                            <pre class="bg-light p-2 rounded"><code>&lt;link rel="stylesheet" href="@{{ asset('css/bootstrap.min.css') }}"&gt;
&lt;script src="@{{ asset('js/bootstrap.bundle.min.js') }}"&gt;&lt;/script&gt;</code></pre>
                        </li>
                        <li class="list-group-item px-0">
                            <strong>3. Mengatur Layout & View:</strong>
                            <p class="mb-0 text-muted">Layout utama dapat disesuaikan di <code>resources/views/layouts/app.blade.php</code> dan halaman lainnya cukup menggunakan <code>@{{-- @extends('layouts.app') --}}</code>.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
