@extends('layouts.master')
@php
  /**
   * @var \Illuminate\Support\ViewErrorBag $errors
   */
@endphp
@section('title',_ix('Forgot password','Forgot password'))

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
    <h3>{{ _ix('Welcome to X-project','Forgot password') }}</h3>
    <form
      action="{{ route('forgotPasswordPost') }}"
      class="m-t"
      method="POST"
    >
      {{ csrf_field() }}
      <div class="form-group required-field">
        <input
          autofocus
          class="form-control"
          name="email"
          placeholder="{{ _ix('Email address','Forgot password') }}"
          required
          type="email"
          value="{{ old('email') }}"
        >
        @foreach($errors->get('email') as $error)
          <p class="text-danger">{{ $error }}</p>
        @endforeach
      </div>
      <button class="btn btn-primary block full-width m-b">{{ _ix('Send reset email','Forgot password') }}</button>
      @if(!empty(session('status')))
        <div class="alert alert-info">
          <p>{{ _ix('An email has been sent to you.','Forgot password') }}</p>
        </div>
      @endif
      <a
        class="btn btn-sm btn-white btn-block"
        href="{{ route('login') }}"
      >
        {{ _ix('Login','Forgot password') }}
      </a>
    </form>
    <p class="m-t">
      <small>
        <strong>&copy; Copyright</strong>
        荟网 2017-2018
      </small>
    </p>
  </div>
  </body>
@stop
