<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    
    protected $fillable = ['title', 'short_description', 'long_description', 'author', 'thumbnail', 'media'];


    public function categories()
    {
        return $this->belongsToMany(Category::class, 'post_relation_categories');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_relation_tags');
    }
    
}
