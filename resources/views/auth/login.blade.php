@extends('layouts.master')
@php
  /**
   * @var \Illuminate\Support\ViewErrorBag $errors
   */
@endphp
@section('title',_ix('Login','Login'))

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
  <body class="gray-bg">
  <div class="middle-box text-center loginscreen animated fadeInDown">
    <div>
      <h1>
        <img
          src="{{ asset('img/logo.png') }}"
        >
      </h1>
    </div>
    <h3>{{ _ix('Welcome to X-project','Login') }}</h3>
    <div class="row login-forms">
      <div class="col-sm-6 m-t">
        <div class="above-button">
          <a href="http://kuaitui911.com/wp-content/uploads/L22-爱推网-荟员登录.mp4"><img class="col-sm-12 m-t" src="storage/user-1/loginvideo.png"/></a>
        </div>
        <a
          class="btn btn-primary block full-width m-b"
          href="{{ $appsIoLoginUrl }}"
        >
          {{ _ix('Member Login','Login') }}
        </a>
      </div>
      <div class="col-sm-6 m-t">
        <form
          action="{{ route('loginPost') }}"
          method="POST"
        >
          {{ csrf_field() }}
          <div class="above-button">
            <input
              name="locale"
              type="hidden"
              value="{{ app()->getLocale() }}"
            >
            <div class="form-group">
              <input
                autofocus
                class="form-control"
                name="email"
                placeholder="{{ _ix('Email address','Login') }}"
                required
                type="email"
                value="{{ old('email') }}"
              >
              @if($blocked)
                <p class="text-danger">
                  {{ _ix('Please contact','Login') }}
                  <a href="https://huistore.kf5.com">{{ _ix('Huistore support team','Login') }}</a>
                  .
                </p>
              @else
                @foreach($errors->get('email') as $error)
                  <p class="text-danger">{{ $error }}</p>
                @endforeach
              @endif
            </div>
            <div class="form-group">
              <input
                class="form-control"
                name="password"
                placeholder="{{ _ix('Password','Login') }}"
                required
                type="password"
              >
            </div>
            <p class="text-muted text-center">
              <a href="{{ route('forgotPassword') }}">{{ _ix('Forgot password?','Login') }}</a>
            </p>
          </div>
          <button class="btn btn-primary block full-width m-b">{{ _ix('Admin Login','Login') }}</button>
        </form>
      </div>
    </div>
    <div class="text-left m-t" style="margin: 0 20% 10% 20%;">
      <p class="m-t">
        <strong>荟员登录</strong>: 请点击『荟员登录』按钮，在荟官网huistore.com授权登录itui.market
      </p>
      <p class="m-t">
        <strong>管理员登录</strong>: 如果您是荟网旗下爱推网认证管理员，请输入我们为您分配的管理员用户名和密码登录
      </p>
      <p class="m-t">
        <strong>非荟员登录</strong>: 荟网系统仅针对荟网荟员提供服务，如果您需要了解荟网相关系统功能和服务，请拨打荟网全国统一服务热线：4000-321-484
      </p>
    </div>
    <p class="m-t">
      <small>
        <strong>&copy; Copyright</strong>
        Huistore Company 2011-2018
      </small>
    </p>
  </div>
  </body>
@stop
