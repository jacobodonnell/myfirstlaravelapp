<form wire:submit="save" action="/post/{{$post->id}}" method="POST">
    <p><small><strong>
                <a wire:navigate href="/post/{{$post->id}}">&laquo; Back to post permalink</a>
            </strong></small></p>
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="post-title" class="text-muted mb-1"><small>Title</small></label>
        <input wire:model="title" name="title" id="post-title" class="form-control form-control-lg form-control-title"
            type="text" placeholder="" autocomplete="off" value="{{old('title', $post->title)}}" />
        @error('title')
        <p class="m-0 small alert alert-danger shadow-sm">{{$message}}</p>
        @enderror
    </div>

    <div class="form-group">
        <label for="post-body" class="text-muted mb-1"><small>Body Content</small></label>
        <textarea wire:model="body" name="body" id="post-body" class="body-content tall-textarea form-control"
            type="text">{{old('body', $post->body)}}</textarea>
        @error('body')
        <p class="m-0 small alert alert-danger shadow-sm">{{$message}}</p>
        @enderror
    </div>

    <button class="btn btn-primary">Save Changes</button>
</form>