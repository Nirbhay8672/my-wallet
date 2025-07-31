<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseEntry extends Model
{
    protected $fillable = [
        'title',
        'account_id',
        'expense_type_id',
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

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
