<?php

namespace App\Http\Controllers;

use App\Events\OurExampleEvent;
use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class UserController extends Controller {

  public function showCorrectHomepage() {
    /** @var StatefulGuard $auth */
    $auth = auth();

    if ($auth->check()) {
      return view('homepage-feed', [
        'posts' => $auth->user()->feedPosts()->latest()->paginate(4)
      ]);
    } else {
      $postCount = Cache::remember('postCount', 120, function () {
        return Post::count();
      });
      return view('homepage', ['postCount' => $postCount]);
    }
  }

  public function logout() {
    /** @var StatefulGuard $auth */
    $auth = auth();
    event(new OurExampleEvent(['username' => auth()->user()->username, 'action' => 'logout']));
    $auth->logout();
    return redirect('/')->with('success', 'You are now logged out.');
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
      event(new OurExampleEvent(['username' => auth()->user()->username, 'action' => 'login']));
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

  private function getSharedData(User $user) {
    $currentlyFollowing = 0;
    /** @var StatefulGuard $auth */
    $auth = auth();
    if ($auth->check()) {
      $currentlyFollowing = Follow::where([['user_id', '=', $auth->user()->id], ['followeduser', '=', $user->id]])->count();
    }

    View::share('sharedData', [
      'username' => $user->username,
      'postCount' => $user->posts()->count(),
      'avatar' => $user->avatar,
      'currentlyFollowing' => $currentlyFollowing,
      'followerCount' => $user->followers()->count(),
      'followingCount' => $user->followingTheseUsers()->count()
    ]);
  }

  public function profile(User $user) {
    $this->getSharedData($user);
    return view('profile-posts', [
      'posts' => $user->posts()->latest()->get(),
    ]);
  }

  public function profileFollowers(User $user) {
    $this->getSharedData($user);
    return view('profile-followers', [
      'followers' => $user->followers()->latest()->get(),
    ]);
  }

  public function profileFollowing(User $user) {
    $this->getSharedData($user);
    return view('profile-following', [
      'followingUsers' => $user->followingTheseUsers()->latest()->get(),
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
