<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fruit extends Model
{
    //
    public $timestamps = false;
    protected $fillable = ['name', 'stock_in_kg', 'price_per_kg'];

    public function request()
    {
        return $this->hasMany(Request::class);
    }

    public function harvest()
    {
        return $this->hasMany(Harvest::class);
    }
}
