<?php

namespace App\Http\Controllers;

use App\Models\Admin\GroupCompany;
use App\Models\Config\SystemHierarchy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthenticationController extends Controller
{
    public function login_page()
    {
        return view('login');
    }

    public function login_authenticate(Request $request)
    {
        $dadosLogin = $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        if (Auth::attempt($dadosLogin)) {

            if (Auth::user()->lock_system === 'Sim') {
                Auth::logout();
                return back()->withErrors([
                    'lock' => 'Usuário está bloqueado no sistema.'
                ])->withInput();
            }

            $request->session()->regenerate();

            Session::put('activeGroupCompany', GroupCompany::findOrFail(1)->getAttributes());
            $hierarchy = SystemHierarchy::findOrFail(auth()->user()->hierarchy_id);
            if ($hierarchy !== null) {
                Session::put('permissions', unserialize($hierarchy->permissions));
            } else {
                Session::put('permissions', array());
            }
            return redirect()->intended('workflow/schedule/shared');
        }

        return back()->withErrors([
            'password' => 'O e-mail e/ou senha não são válidos'
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
