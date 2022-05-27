<?php

namespace Hui\Xproject\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller{
  public function index():View{
    return view('todos');
  }
}
