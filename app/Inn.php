<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inn extends Model
{

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'last_check' => 'datetime',
    ];
}
