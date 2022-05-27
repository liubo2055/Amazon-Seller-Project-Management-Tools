@extends('layouts.master')
@php
  /**
   * @var \Illuminate\Support\ViewErrorBag $errors
   */
@endphp
@section('title',_ix('Reset password','Reset password'))

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
    <h3>{{ _ix('Welcome to X-project','Reset password') }}</h3>
    <form
      action="{{ route('resetPasswordPost') }}"
      class="m-t"
      method="POST"
    >
      {{ csrf_field() }}
      <input
        name="email"
        type="hidden"
        value="{{ $email?:old('email') }}"
      >
      <input
        name="token"
        type="hidden"
        value="{{ $token?:old('token') }}"
      >
      <div class="form-group required-field">
        <input
          autofocus
          class="form-control"
          name="password"
          placeholder="{{ _ix('New password','Reset password') }}"
          required
          type="password"
        >
        <p class="text-info">{{ _ix('The password must be between 6 and 16 characters long','Reset password') }}</p>
        @foreach($errors->get('password') as $error)
          <p class="text-danger">{{ $error }}</p>
        @endforeach
      </div>
      <div class="form-group required-field">
        <input
          class="form-control"
          name="password_confirmation"
          placeholder="{{ _ix('New password (confirmation)','Reset password') }}"
          required
          type="password"
        >
      </div>
      <button class="btn btn-primary block full-width m-b">{{ _ix('Reset password','Reset password') }}</button>
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
