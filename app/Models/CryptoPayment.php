<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CryptoPayment extends Model
{
    use SoftDeletes;

    protected $table = 'CryptoPayment';
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = 'created_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'user_id', 
        'payment_wallet', 
        'private_key', 
        'amount', 
        'paid_amount', 
        'payout_tx',
        'coin', 
        'completed_at', 
        'expired_at'
    ];
}
