<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeEntry extends Model
{
    protected $fillable = [
        'title',
        'account_id',
        'income_type_id',
        'amount',
        'proof',
        'description',
        'user_id',
        'date',
        'time',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i:s',
        'amount' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function incomeType()
    {
        return $this->belongsTo(IncomeType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
