<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{env('APP_NAME')}}</title>
  @if(file_exists(asset('favicon.ico')))
  <link rel="icon" href="{{ asset('favicon.ico') }}">
  @endif
  <script>var SITE_PATH = "{{  request()->root() }}";</script>
  <meta name="_token" content="{{ csrf_token() }}"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @include('partials.assets.lte.styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    @if(file_exists(asset('img/logo.png')))
    <img src="{{asset('img/logo.png')}}" alt="AdminLTELogo" class="animation__shake" height="60" width="60">
    @else
        <h3>{{env('APP_NAME')}}</h3>
    @endif
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>

    </ul>

    @include('partials.lte.nav_top_right')
  </nav>

    @include('partials.lte.nav_bar_left')

  <div class="content-wrapper">
    @include('flash::message')
    @yield('content_lte')
  </div>
  <footer class="main-footer">
        <strong>Copyright &copy; 2014-{{date('Y')}} <a href="https://www.linkedin.com/in/tfariasg3/" target="_blank">Tiago F S</a>.</strong>
        <a href="https://adminlte.io" target="_blank">Layout AdminLTE.io</a>
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 3.1.0
        </div>
    </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <div id="form-delete"></div>
</div>
  @include('partials.assets.lte.scripts')
  @yield('page-script')
</body>
</html>
