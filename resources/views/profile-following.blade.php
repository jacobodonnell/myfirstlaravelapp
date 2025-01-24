<x-profile :sharedDate="$sharedData" doctitle="Who {{$sharedData['username']}} Follows">
  <div class="list-group">
    @foreach ($followingUsers as $followingUser)
    <a href="/profile/{{$followingUser->userBeingFollowed->username}}" wire:navigate
      class="list-group-item list-group-item-action">
      <img class="avatar-tiny" src="{{$followingUser->userBeingFollowed->avatar}}" />
      <strong>{{$followingUser->userBeingFollowed->username}}</strong>
    </a>
    @endforeach
  </div>
</x-profile>