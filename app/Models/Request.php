<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    //
    use HasFactory;
    public $timestamps = false;

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
