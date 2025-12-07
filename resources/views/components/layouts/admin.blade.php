<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Dashboard Admin - KosanKu' }}</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('adminlte/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{asset('adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('adminlte/dist/css/adminlte.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{asset('adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{asset('adminlte/plugins/daterangepicker/daterangepicker.css')}}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    .brand-link {
      background: linear-gradient(135deg, #dc3545 0%, #bd2130 100%) !important;
      border-bottom: 1px solid #bd2130 !important;
    }
    .sidebar-dark-primary {
      background: linear-gradient(180deg, #343a40 0%, #1a1d20 100%) !important;
    }
    .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active {
      background: linear-gradient(135deg, #dc3545 0%, #bd2130 100%) !important;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
  </style>
  
  @livewireStyles
  @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ route('home') }}" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- User Info -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="far fa-user mr-1"></i>
          <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="{{ route('admin.profile') }}">
            <i class="fas fa-user-circle mr-2"></i> Profil Saya
          </a>
          <div class="dropdown-divider"></div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item">
              <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
          </form>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link text-center">
      <i class="fas fa-user-shield mr-2"></i>
      <span class="brand-text font-weight-light">KosanKu - Admin</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ \App\Helpers\AvatarHelper::getAvatar(auth()->user(), 50) }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="{{ route('admin.profile') }}" class="d-block">{{ auth()->user()->name }}</a>
          <small class="text-muted">Administrator</small>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
          <!-- Dashboard -->
          <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-header">MANAJEMEN DATA</li>

          <!-- Room Management -->
          <li class="nav-item">
            <a href="{{ route('admin.rooms.manage') }}" class="nav-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-door-open"></i>
              <p>Kelola Kamar</p>
            </a>
          </li>

          <!-- User Management -->
          <li class="nav-item">
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-users"></i>
              <p>Kelola Pengguna</p>
            </a>
          </li>

          <!-- Booking Management -->
          <li class="nav-item">
            <a href="{{ route('admin.bookings.index') }}" class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-calendar-check"></i>
              <p>Kelola Booking</p>
            </a>
          </li>

          <li class="nav-header">TRANSAKSI</li>

          <!-- Bill Management -->
          <li class="nav-item">
            <a href="{{ route('admin.bills.index') }}" class="nav-link {{ request()->routeIs('admin.bills.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-file-invoice-dollar"></i>
              <p>Kelola Tagihan</p>
            </a>
          </li>

          <!-- Payment Proof Verification -->
          <li class="nav-item">
            <a href="{{ route('admin.payment-proofs.index') }}" class="nav-link {{ request()->routeIs('admin.payment-proofs.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-check-circle"></i>
              <p>Verifikasi Pembayaran</p>
            </a>
          </li>

          <li class="nav-header">LAYANAN</li>

          <!-- Keluhan Management -->
          <li class="nav-item">
            <a href="{{ route('admin.complaints.index') }}" class="nav-link {{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-exclamation-circle"></i>
              <p>Kelola Keluhan</p>
            </a>
          </li>

          <!-- Announcements -->
          <li class="nav-item">
            <a href="{{ route('admin.announcements.index') }}" class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-bullhorn"></i>
              <p>Kelola Pengumuman</p>
            </a>
          </li>

          <li class="nav-header">LAPORAN</li>

          <!-- Export Data -->
          <li class="nav-item">
            <a href="{{ route('admin.export.index') }}" class="nav-link {{ request()->routeIs('admin.export.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-download"></i>
              <p>Export Data</p>
            </a>
          </li>

          <li class="nav-header">LAINNYA</li>

          <!-- Website -->
          <li class="nav-item">
            <a href="{{ route('home') }}" class="nav-link" target="_blank">
              <i class="nav-icon fas fa-globe"></i>
              <p>Halaman Website</p>
            </a>
          </li>

          <li class="nav-header">AKUN</li>

          <!-- Profile -->
          <li class="nav-item">
            <a href="{{ route('admin.profile') }}" class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-circle"></i>
              <p>Profil Saya</p>
            </a>
          </li>

          <!-- Logout -->
          <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="nav-link btn btn-link text-left w-100" style="color: #c2c7d0;">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                <p>Logout</p>
              </button>
            </form>
          </li>

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    @if(isset($header))
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">{{ $header }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
              @if(isset($breadcrumb))
                {!! $breadcrumb !!}
              @endif
            </ol>
          </div>
        </div>
      </div>
    </div>
    @endif
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        {{ $slot }}
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <!-- Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; {{ date('Y') }} <a href="{{ route('home') }}">KosanKu</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1.0.0
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{asset('adminlte/plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{asset('adminlte/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{asset('adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('adminlte/dist/js/adminlte.js')}}"></script>

@livewireScripts

<script>
  // Auto dismiss alerts
  setTimeout(function() {
    $('.alert').fadeOut('slow');
  }, 5000);
</script>

@stack('scripts')

</body>
</html>
