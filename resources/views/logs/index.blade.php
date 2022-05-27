@extends('layouts.app')
@section('title',_ix('Log Viewer','Menu option'))

@section('content')
  <h2>{{ _ix('Log Viewer','Log Viewer') }}</h2>
  @component('components.sectionWrapper')
    @component('components.section')
      @slot('title')
        {{ _ix('Log Viewer','Log Viewer') }}
      @endslot
      <logs-section
        csrf="{{ csrf_token() }}"
        download-url="{{ $downloadUrl }}"
        load-url="{{ $loadUrl }}"
        :filter-fields="{!! jsObject($filters,false) !!}"
        :filter-fields-without-search="{!! jsObject($filtersWithoutSearch,false) !!}"
      ></logs-section>
    @endcomponent
  @endcomponent
@endsection

@push('scripts')
  @include('includes.vue',[
    'script'=>asset(mix('/js/logs.js'))
  ])
@endpush
