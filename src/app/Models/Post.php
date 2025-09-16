<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'author', 'published_at'];

    public function images() {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function types() {
        return $this->belongsToMany(Type::class, 'post_type');
    }
}
