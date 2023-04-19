<?php

namespace App\Models;

use App\Models\Voucher as voucherModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User as userModel;

class VoucherTransaction extends Model
{
    use    SoftDeletes;

    protected $table = 'voucher_transactions';


    protected $fillable = [
        'user_id',
        'voucher_id',

    ];

    public function user()
    {
        return $this->belongsTo(userModel::class);
    }

    public function voucher()
    {
        return $this->hasOne(voucherModel::class, 'id', 'voucher_id');
    }

}
