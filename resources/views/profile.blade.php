@extends('layouts.app')
@section('title',_ix('Profile','Menu option'))

@section('content')
  <h2>{{ _ix('Profile','Profile') }}</h2>
  @component('components.sectionWrapper')
    @component('components.section')
      @slot('title')
        {{ _ix('Profile','Profile') }}
      @endslot
      <profile-section
        save-url="{{ $saveUrl }}"
        upload-url="{{ $uploadUrl }}"
        :locales="{!! jsObject($locales) !!}"
        :profile="{!! jsObject($profile) !!}"
        :show-storefronts="{{ $showStorefronts?'true':'false' }}"
        :timezones="{!! jsObject($timezones) !!}"
      ></profile-section>
    @endcomponent
  @endcomponent
@endsection

@push('scripts')
  @include('includes.vue',[
    'script'=>asset(mix('/js/profile.js'))
  ])
@endpush
