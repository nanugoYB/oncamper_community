<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GalleryPost extends Model
{
    //
    protected $table = 'gallery_posts';

    protected $fillable = [
        'gallery_id', 'user_id', 'user_name', 'title', 
        'content', 'views'
    ];

    public $timestamps = true;

}
