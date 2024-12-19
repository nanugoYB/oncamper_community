<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    protected $table = 'comments';

    protected $fillable = [
        'post_id', 'user_id', 'username', 'password', 
        'content', 'parent_comment_id'
    ];

    public $timestamps = true;
}
