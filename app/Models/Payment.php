<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function client() : BelongsTo
    {
        return $this->belongsTo(Client::class,'client_id', 'id');
    }

    public function website() : BelongsTo
    {
        return $this->belongsTo(Website::class,'website_id', 'id');
    }
}