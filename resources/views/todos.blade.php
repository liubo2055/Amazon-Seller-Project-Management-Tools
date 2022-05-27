@extends('layouts.app')
@section('title',_ix('To-Dos','Menu option'))

@section('content')
  <h2>{{ _ix('To-Dos','To-Dos') }}</h2>
  @component('components.sectionWrapper')
    @component('components.section')
      @slot('title')
        {{ _ix('To-Dos','To-Dos') }}
      @endslot
      <todos-section
        :columns="{!! jsObject($columns,false) !!}"
        :filter-fields="{!! jsObject($filters,false) !!}"
        :initial-size="50"
        :marketplaces="{!! jsObject($marketplaces) !!}"
        :urls="{!! jsObject($urls) !!}"
      ></todos-section>
    @endcomponent
  @endcomponent
@endsection

@push('scripts')
  @include('includes.vue',[
    'script'=>asset(mix('/js/todos.js'))
  ])
@endpush
