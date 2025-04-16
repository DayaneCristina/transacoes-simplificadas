<?php

namespace App\Models;

use Database\Factories\TransactionCodeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionCode extends Model
{
    /** @use HasFactory<TransactionCodeFactory> */
    use HasFactory;

    protected $table = 'transaction_codes';

    protected $fillable = [
        'title',
        'description',
        'type'
    ];

    protected $casts = [
        'type' => 'string',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'transaction_code_id');
    }
}
