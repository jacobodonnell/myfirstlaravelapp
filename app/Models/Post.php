<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

/**
 * @method static \App\Models\User create(array $attributes = [])
 */
class Post extends Model {
    use Searchable;

    protected $fillable = ['title', 'body', 'user_id'];

    public function toSearchableArray() {
        return [
            'title' => $this->title,
            'body' => $this->body
        ];
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
