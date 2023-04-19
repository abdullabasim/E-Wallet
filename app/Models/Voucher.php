<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ESolution\DBEncryption\Traits\EncryptedAttribute;
use App\Models\VoucherTransaction as voucherTransactionModel;
class Voucher extends Model
{
    use    SoftDeletes ,EncryptedAttribute;

    protected $table = 'vouchers';

    protected $casts = [
        'is_enabled' => 'bool',
        'is_used' => 'bool'
    ];

    protected $fillable = [
        'id',
        'pin',
        'amount',
        'starts_at',
        'expires_at',
        'is_enabled',
        'is_used',
        'uuid',
        'batch'

    ];

    protected $encryptable  = [
        'pin',

    ];



    public function transaction()
    {
        return $this->belongsTo(voucherTransactionModel::class, 'id','voucher_id');
    }


}
