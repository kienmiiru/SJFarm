<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    //
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'requested_stock_in_kg',
        'total_price',
        'requested_date',
        'status_changed_date',
        'status_changed_message',
        'fruit_id',
        'distributor_id',
        'status_id',
    ];

    public function fruit()
    {
        return $this->belongsTo(Fruit::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
