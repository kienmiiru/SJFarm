<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    //
    public function request()
    {
        return $this->hasMany(Request::class);
    }
}
