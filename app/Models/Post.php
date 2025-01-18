<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static \App\Models\User create(array $attributes = [])
 */
class Post extends Model {
    //

    protected $fillable = ['title', 'body', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
