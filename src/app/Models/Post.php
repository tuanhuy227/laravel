<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'content', 'author', 'published_at'];

    public function images() {
        return $this->morphMany(Image::class, 'imageable');
    }
}
