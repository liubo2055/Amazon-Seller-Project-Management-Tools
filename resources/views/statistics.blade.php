@extends('layouts.app')
@section('title',_ix('Statistics','Menu option'))

@section('content')
  <h2>{{ _ix('Statistics','Statistics') }}</h2>
  @component('components.sectionWrapper')
    @component('components.section')
      @slot('title')
        {{ _ix('Statistics','Statistics') }}
      @endslot
      <statistics-section
        csrf="{{ csrf_token() }}"
        :columns="{!! jsObject($columns,false) !!}"
        :filter-fields="{!! jsObject($filters,false) !!}"
        :initial-size="50"
        :urls="{!! jsObject($urls) !!}"
      ></statistics-section>
    @endcomponent
  @endcomponent
@endsection

@push('scripts')
  @include('includes.vue',[
    'script'=>asset(mix('/js/statistics.js'))
  ])
@endpush
