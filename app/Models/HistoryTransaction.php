<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoryTransaction extends Model
{
    use SoftDeletes;

    protected $table = 'history_transaction';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'unique_code',
        'type',
        'amount',
        'recipient_id',
        'crypto_payment_id',
        'flat_payment_id',
        'binance_code_payment_id',
        'status'
    ];
    public function crypto_payment()
    {
        return $this->hasOne('App\Models\CryptoPayment', 'id', 'crypto_payment_id')->select('id', 'user_id', 'payment_wallet', 'amount', 'payout_tx', 'coin', 'expired_at','status');
    }
}
