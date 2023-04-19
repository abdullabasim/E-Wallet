<?php

namespace App\Models;


use App\Models\PaymentPermission as paymentPermissionModel;
use App\Models\Merchant as merchantModel;
use App\Models\VoucherTransaction as voucherTransactionModel;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Interfaces\Wallet;
use Laravel\Passport\HasApiTokens;

use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Model implements AuthenticatableContract, AuthorizableContract, Wallet
{
    use  HasApiTokens ,Authenticatable, Authorizable, HasFactory, HasWallet ,SoftDeletes;


    protected $table = 'users';

    protected $casts = [
        'is_blocked' => 'bool',
        'allow_login' => 'bool',
        'is_password_changed' => 'bool'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $fillable = [
        'id',
        'name',
        'wallet_user',
        'email',
        'password',
        'user_type',
        'is_blocked',
        'allow_login',
        'is_password_changed',
        'remember_token',
        'phone',

    ];

    public function transaction()
    {
        return $this->hasMany(voucherTransactionModel::class, 'user_id', 'id');
    }

    public function merchants()
    {
        return $this->belongsToMany(merchantModel::class);
    }

    public function usersExecutor()
    {
        return $this->hasMany(paymentPermissionModel::class,'user_id', 'id');
    }

    public function usersBeneficiary()
    {
        return $this->hasMany(paymentPermissionModel::class,'user_has_permission', 'id');
    }



}
