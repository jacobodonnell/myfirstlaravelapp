<x-profile :sharedDate="$sharedData" doctitle="{{$sharedData['username']}}'s Followers'">
  <div class="list-group">
    @foreach ($followers as $follow)
    <a href="/profile/{{$follow->userDoingTheFollowing->username}}" wire:navigate
      class="list-group-item list-group-item-action">
      <img class="avatar-tiny" src="{{$follow->userDoingTheFollowing->avatar}}" />
      <strong>{{$follow->userDoingTheFollowing->username}}</strong>
    </a>
    @endforeach
  </div>
</x-profile>