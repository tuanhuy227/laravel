<?php

namespace App\Models;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['path'];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return config('app.url') . Storage::url($this->path);
    }
    

    public function imageable()
    {
        return $this->morphTo();
    }
}
