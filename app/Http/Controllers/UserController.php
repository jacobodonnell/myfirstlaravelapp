<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

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
      'postCount' => $user->posts()->count(),
      'avatar' => $user->avatar
    ]);
  }

  public function showAvatarForm() {
    return view('avatar-form');
  }

  public function storeAvatar(Request $request) {
    $request->validate([
      'avatar' => 'required|image|max:5000'
    ]);

    $user = auth()->user();

    $filename = $user->id . "-" . uniqid() . ".jpg";

    $manager = new ImageManager(new Driver());
    $image = $manager->read($request->file('avatar'));
    $imgData = $image->cover(120, 120)->toJpeg();
    Storage::disk('public')->put('avatars/' . $filename, $imgData);

    $oldAvatar = $user->avatar;

    $user->avatar = $filename;
    $user->save();

    if ($oldAvatar != "/fallback-avatar.jpg") {
      Storage::disk('public')->delete(str_replace("/storage/", "", $oldAvatar));
    }

    return back()->with('success', 'Congrats on the new avatar!');
  }
}
