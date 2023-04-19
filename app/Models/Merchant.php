<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User as userModel;
/**
 * Class Merchant
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property string $merchant_key
 * @property longText $white_list
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Merchant extends Model
{
    use SoftDeletes;
    protected $table = 'merchants';

    protected $casts = [
        'id' => 'int',
        'white_list' => 'json',
        'white_list_active'=>'bool',
        'is_active' =>'bool',

    ];

    protected $fillable = [
        'id',
        'name',
        'phone',
        'email',
        'merchant_key',
        'white_list',
        'white_list_active',
        'is_active'
    ];

    public function users()
    {
        return $this->belongsToMany(userModel::class)->withTimestamps();
    }


}
