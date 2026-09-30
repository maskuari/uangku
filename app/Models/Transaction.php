<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = ['type', 'title', 'category', 'amount', 'occurred_on', 'notes'];
    protected $casts = ['occurred_on' => 'date', 'amount' => 'integer'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
