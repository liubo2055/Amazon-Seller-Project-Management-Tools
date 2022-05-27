@extends('layouts.app')
@section('title',_ix('Users','Menu option'))

@section('content')
  <h2>{{ _ix('Users','Users') }}</h2>
  @component('components.sectionWrapper')
    @component('components.section')
      @slot('title')
        {{ _ix('Users','Users') }}
      @endslot
      <users-section
        freelancer-role="{{ $freelancerRole }}"
        template-sheet-url="{{ $templateSheetUrl }}"
        :columns="{!! jsObject($columns,false) !!}"
        :creator-users="{!! jsObject($creatorUsers,false) !!}"
        :filter-fields="{!! jsObject($filters,false) !!}"
        :initial-size="50"
        :locales="{!! jsObject($locales) !!}"
        :only-private-freelancers="{{ $onlyPrivateFreelancers?'true':'false' }}"
        :roles="{!! jsObject($roles) !!}"
        :show-import-button="{{ $showImportButton?'true':'false' }}"
        :timezones="{!! jsObject($timezones) !!}"
        :urls="{!! jsObject($urls) !!}"
      ></users-section>
    @endcomponent
  @endcomponent
@endsection

@push('scripts')
  @include('includes.vue',[
    'script'=>asset(mix('/js/users.js'))
  ])
@endpush
