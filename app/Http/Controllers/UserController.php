<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\VarDumper\VarDumper;

class UserController extends Controller {

  public function logout() {
    /** @var StatefulGuard $auth */
    $auth = auth();
    $auth->logout();

    return redirect('/')->with('success', 'You are now logged out.');
  }

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
      return redirect('/')->with('success', 'You have successfully logged in.');
    } else {
      return redirect('/')->with('failure', 'Sorry, your username and/or password were incorrect.');
    }
  }

  public function register(Request $request) {
    $incomingFields = $request->validate([
      'username' => ['required', 'min:3', 'max:30', Rule::unique('users', 'username')],
      'email' => ['required', 'email', Rule::unique('users', 'email')],
      'password' => ['required', 'min:8', 'confirmed'],
    ]);
    $user = User::create($incomingFields);
    /** @var StatefulGuard $auth */
    $auth = auth();
    $auth->login($user);
    return redirect('/')->with('success', 'Thank you for creating an account');
  }

  public function profile(User $user) {
    return view('profile-posts', [
      'username' => $user->username,
      'posts' => $user->posts()->latest()->get(),
      'postCount' => $user->posts()->count()
    ]);
  }
}
