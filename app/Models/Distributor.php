<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distributor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'email',
        'phone_number',
        'username',
        'password_hash',
        'created_at',
        'updated_at',
        'last_access',
        'deleted_at',
    ];

    public function request()
    {
        return $this->hasMany(Request::class);
    }
}
