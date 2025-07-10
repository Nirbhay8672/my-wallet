<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'initial_amount',
        'account_number',
        'bank_name',
        'balance',
        'type',
        'bank_id',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
} 