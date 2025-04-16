<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'account_id',
        'transaction_code_id',
        'status',
        'amount',
        'correlation_id',
    ];

    protected $casts = [
        'correlation_id' => 'string',
        'amount' => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function transactionCode(): BelongsTo
    {
        return $this->belongsTo(TransactionCode::class, 'transaction_code_id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', TransactionStatus::COMPLETED->value);
    }
}
