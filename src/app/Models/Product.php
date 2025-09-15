<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'price', 'stock'];

    public function images() {
        return $this->morphMany(Image::class, 'imageable');
    }
    
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }
}
