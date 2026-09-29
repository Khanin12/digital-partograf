<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $title ?? config('app.name', 'Partograf System'))</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('template/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <!-- Healthcare Green Theme -->
    <link href="{{ asset('template/css/custom-health.css') }}" rel="stylesheet">

    @stack('styles')
    {{ $styles ?? '' }}
</head>

<body id="page-top" class="{{ request()->has('embedded') ? 'embedded-view bg-white' : '' }}">

@if(request()->has('embedded'))
    <!-- Mode Embedded (Khusus iFrame / Modal Tanpa Sidebar & Topbar) -->
    <div class="embedded-content p-2 p-md-3">
        @yield('content')
        {{ $slot ?? '' }}
    </div>
@else
    <!-- Page Wrapper Standar -->
    <div id="wrapper">

        <!-- Sidebar Component -->
        @include('layouts.partials.sidebar')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Navbar / Topbar Component -->
                @include('layouts.partials.navbar')

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('content')
                    {{ $slot ?? '' }}
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer Component -->
            @include('layouts.partials.footer')

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Logout Modal & Scroll to Top -->
    @include('layouts.partials.logout-modal')
@endif

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('template/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('template/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('template/js/sb-admin-2.min.js') }}"></script>

    @stack('scripts')
    {{ $scripts ?? '' }}

    @if(request()->has('open_modal'))
    <script>
        $(document).ready(function() {
            var targetModalId = "{{ request('open_modal') }}";
            if (targetModalId && $('#' + targetModalId).length) {
                $('#' + targetModalId).modal('show');
            }
        });
    </script>
    @endif
</body>
</html>
