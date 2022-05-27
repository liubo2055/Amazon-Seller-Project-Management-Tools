<?php

namespace Hui\Xproject\Http\Controllers\Auth;

use Hui\AppsIoLaravel\Services\AppsIoHelper;
use Hui\Xproject\Entities\User;
use Hui\Xproject\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller{
  use AuthenticatesUsers {
    login as doLogin;
  }

  public function showLoginForm():View{
    $blocked=session('blocked',false);

    /**
     * @var AppsIoHelper $appsIoHelper
     */
    $appsIoHelper=app(AppsIoHelper::class);
    $appsIoLoginUrl=$appsIoHelper->loginUrl();

    return view('auth.login')->with(compact(
      'blocked',
      'appsIoLoginUrl'
    ));
  }

  public function login(Request $request):Response{
    changeLocale($request->input('locale',LOCALE_CN));

    return $this->doLogin($request);
  }

  protected function authenticated(/** @noinspection PhpUnusedParameterInspection */
    Request $request,User $user):RedirectResponse{
    if(!$user->isBlocked())
      return redirect()->intended($this->redirectPath());
    else{
      auth()->logout();

      return redirect()
        ->route('login')
        ->with([
          'blocked'=>true
        ]);
    }
  }

  protected $redirectTo='/';
}
