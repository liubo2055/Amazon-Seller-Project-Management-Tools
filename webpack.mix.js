const mix=require('laravel-mix')
require('laravel-mix-auto-extract')

mix
  .sass('resources/assets/scss/app.scss','public/css')
  .copy('resources/assets/vendor/bootstrap/fonts','public/fonts')
  .copy('resources/assets/vendor/font-awesome/fonts','public/fonts')
  .copy('resources/assets/vendor/flag-icon-css/flags/4x3','public/css/flags/4x3')
  .copy('resources/assets/scss/patterns','public/css/patterns')
  .copy('resources/assets/img','public/img')
  .combine(
    [
      'resources/assets/vendor/bootstrap/css/bootstrap.css',
      'resources/assets/vendor/animate/animate.css',
      'resources/assets/vendor/font-awesome/css/font-awesome.css',
      'resources/assets/vendor/flag-icon-css/flag-icon.min.css',
      'resources/assets/vendor/jasny/jasny-bootstrap.min.css'
    ],
    'public/css/vendor.css')
  .combine(
    [
      'resources/assets/vendor/jquery/jquery-3.2.1.min.js',
      'resources/assets/vendor/bootstrap/js/bootstrap.min.js',
      'resources/assets/vendor/metisMenu/jquery.metisMenu.js',
      'resources/assets/vendor/slimscroll/jquery.slimscroll.min.js',
      'resources/assets/vendor/pace/pace.min.js',
      'resources/assets/vendor/jasny/jasny-bootstrap.min.js',
      'resources/assets/js/navigation.js',
      'resources/assets/js/app.js'
    ],
    'public/js/app.js')
  .autoExtract()
  .webpackConfig({
    resolve:{
      modules:[
        'node_modules',
        'resources/assets/vue'
      ]
    }
  })

const path=require('path')

// https://github.com/JeffreyWay/laravel-mix/issues/1118
// Until this bug gets fixed, all the output must be in the same path
;[
  'todos.js',
  'projects.js',
  'statistics.js',
  'users.js',
  'logs.js',
  'profile.js'
].forEach(file=>{
  let output
  if(Array.isArray(file))
    [
      file,
      output
    ]=file
  else
    output=path.basename(file)

  mix.js(`resources/assets/js/${file}`,`public/js/${output}`)
})

if(Config.production)
  mix.version()
