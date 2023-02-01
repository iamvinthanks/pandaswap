<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRekening extends Model
{
    use SoftDeletes;

    protected $table = 'user_rekening';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'bank_code',
        'bank_number',
        'bank_name',
    ];
}
