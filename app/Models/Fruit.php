<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fruit extends Model
{
    //
    public $timestamps = false;

    public function request()
    {
        return $this->hasMany(Request::class);
    }
}
