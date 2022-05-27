<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta
    content="IE=edge"
    http-equiv="X-UA-Compatible"
  >
  <title>@yield('title'){{ _ix(' - X-project','Master') }}</title>
  <script src="{{ asset(sprintf('/js/i18n/%s.js',userLocale())) }}"></script>
  @stack('styles')
</head>
@yield('body')
</html>
