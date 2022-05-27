@extends('layouts.master')

@push('styles')
  <link
    href="{!! asset(mix('/css/vendor.css')) !!}"
    rel="stylesheet"
  >
  <link
    href="{!! asset(mix('/css/app.css')) !!}"
    rel="stylesheet"
  >
@endpush

@section('body')
  @php
    if(!empty($miniNavbar))
    $class=' class="mini-navbar"';
    else
    $class='';
  @endphp
  <body{!! $class !!}>
  <script>
    window.Xproject={!! json_encode([
      'csrfToken'=>csrf_token()
    ]) !!}
  </script>
  <div id="wrapper">
    @include('layouts.navigation')
    <div
      class="gray-bg"
      id="page-wrapper"
    >
      @include('layouts.topnavbar')
      @includeIf('appsIo::warning')
      @yield('content')
      @include('layouts.footer')
    </div>
  </div>
  <script src="{!! asset(mix('/js/app.js')) !!}"></script>
  @stack('scripts')
  </body>
@stop
