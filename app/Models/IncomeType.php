<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'active',
    ];

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }
}
