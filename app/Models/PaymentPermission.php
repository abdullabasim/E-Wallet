<?php

namespace App\Models;

use App\Models\User as userModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PaymentPermission extends Model
{

    use    SoftDeletes;

    protected $table = 'payment_permissions';


    protected $fillable = [
        'user_id',
        'user_has_permission',

    ];

    public function allowedPayment()
    {
        return $this->hasMany('user_id', 'user_has_permission');
    }

    public function userExecutor()
    {
        return $this->belongsTo(userModel::class,'user_id','id' );
    }

    public function userBeneficiary()
    {
        return $this->belongsTo(userModel::class,'user_has_permission','id' );
    }



}
