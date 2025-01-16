<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller {

    public function showCorrectHomepage() {
        /** @var StatefulGuard $auth */
        $auth = auth();

        if ($auth->check()) {
            return view('homepage-feed');
        } else {
            return view('homepage');
        }
    }

    public function login(Request $request) {
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required',
        ]);

        /** @var StatefulGuard $auth */
        $auth =  auth();

        if ($auth->attempt([
            'username' => $incomingFields['loginusername'],
            'password' => $incomingFields['loginpassword'],
        ])) {
            $request->session()->regenerate();
            return 'Congrats!!!';
        } else {
            return 'Sorry...';
        }
    }

    public function register(Request $request) {
        $incomingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:30', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);
        User::create($incomingFields);
        return $incomingFields;
    }
}
