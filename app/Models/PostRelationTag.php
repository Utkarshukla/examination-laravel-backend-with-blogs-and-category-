<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostRelationTag extends Model
{
    use HasFactory;
    protected $table = 'post_relation_tags';
    protected $fillable = ['post_id', 'tag_id'];
}
