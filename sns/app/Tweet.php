<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    protected $fillable = [   // <---　追加
        'user_id', 'tweet',
    ];
}