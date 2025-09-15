<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'stock'];

    public function images() {
        return $this->morphMany(Image::class, 'imageable');
    }
    
    public function categories() {
        return $this->belongToMany(Category::class);
    }
}
