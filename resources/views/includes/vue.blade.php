@php
  global $manifestAndVendorIncluded;
@endphp
@if(empty($manifestAndVendorIncluded))
  <script src="{!! asset(mix('/js/manifest.js')) !!}"></script>
  <script src="{!! asset(mix('/js/vendor.js')) !!}"></script>
  @php
    $manifestAndVendorIncluded=true;
  @endphp
@endif
<script src="{!! $script !!}"></script>
