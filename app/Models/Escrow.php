<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Escrow extends Model
{
    public $table = 'escrow';

    use SoftDeletes;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'id',
        'escrow_id',
        'buyer',
        'seller',
        'amount',
        'id_rek_seller',
        'id_rek_buyer',
        'rek_seller',
        'rek_buyer',
        'conversaction_id',
        'buyer_approve',
        'seller_approve',
        'status',
        'bukti',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

}
