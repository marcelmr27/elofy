<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        if (LoginController::is_email_valid($request->email)) {
            if ((int) strlen(trim($request->password)) >= 6) {
                $dadosLogin = $request->validate([
                    'email' => ['required'],
                    'password' => ['required']
                ]);

                if (Auth::attempt($dadosLogin)) {
                    return json_encode(array(
                        'status' => 'SUCCESS',
                        'message' => 'Usuário encontrado',
                        'user' => array(
                            'id' => auth()->user()->id,
                            'name' => auth()->user()->name,
                            'email' => auth()->user()->email,
                            'token' => Hash::make(Str::random())
                        )
                    ));
                } else {
                    return json_encode(array(
                        'status' => 'ERROR',
                        'message' => 'Usuário não encontrado'
                    ));
                }
            } else {
                return json_encode(array(
                    'status' => 'ERROR',
                    'message' => 'Login inválido'
                ));
            }
        } else {
            return json_encode(array(
                'status' => 'ERROR',
                'message' => 'E-mail inválido'
            ));
        }
    }

    public static function is_email_valid($email)
    {
        $email = mb_strtolower(trim($email), 'UTF-8');
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }
}
