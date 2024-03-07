<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostRelationCategory extends Model
{
    use HasFactory;
    protected $table = 'post_relation_categories';
    protected $fillable = ['post_id', 'category_id'];
}
