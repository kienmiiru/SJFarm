<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Harvest extends Model
{
    //
    public $timestamps = false;
    protected $fillable = ['fruit_id', 'amount_in_kg', 'harvest_date'];

    public function fruit()
    {
        return $this->belongsTo(Fruit::class);
    }
}
