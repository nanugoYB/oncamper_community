<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gallery extends Model
{
    //
    protected $table = 'galleries';

    protected $fillable = [
        'region_id', 'name', 'description', 'manager_id', 
        'sub_manager_1', 'sub_manager_2', 'sub_manager_3', 
        'sub_manager_4', 'sub_manager_5'
    ];

    public $timestamps = true;

}
