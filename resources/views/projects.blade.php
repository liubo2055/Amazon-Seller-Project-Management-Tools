@extends('layouts.app')
@section('title',_ix('Projects','Menu option'))

@section('content')
  <h2>{{ _ix('Projects','Projects') }}</h2>
  @component('components.sectionWrapper')
    @component('components.section')
      @slot('title')
        {{ _ix('Projects','Projects') }}
      @endslot
      <projects-section
        csrf="{{ csrf_token() }}"
        :columns="{!! jsObject($columns,false) !!}"
        :filter-fields="{!! jsObject($filters,false) !!}"
        :initial-size="50"
        :similar-projects-columns="{!! jsObject($similarProjectsColumns,false) !!}"
        :urls="{!! jsObject($urls) !!}"
      ></projects-section>
    @endcomponent
  @endcomponent
@endsection

@push('scripts')
  @include('includes.vue',[
    'script'=>asset(mix('/js/projects.js'))
  ])
@endpush
