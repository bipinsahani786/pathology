<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'user_id', 'balance'];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class)->latest();
    }

    /**
     * Credit amount to wallet and log transaction.
     */
    public function credit(float $amount, string $description, string $refType = null, int $refId = null): WalletTransaction
    {
        $this->increment('balance', $amount);

        return WalletTransaction::create([
            'wallet_id' => $this->id,
            'type' => 'credit',
            'amount' => $amount,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'description' => $description,
        ]);
    }

    /**
     * Debit amount from wallet and log transaction.
     */
    public function debit(float $amount, string $description, string $refType = null, int $refId = null): WalletTransaction
    {
        $this->decrement('balance', $amount);

        return WalletTransaction::create([
            'wallet_id' => $this->id,
            'type' => 'debit',
            'amount' => $amount,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'description' => $description,
        ]);
    }
}
