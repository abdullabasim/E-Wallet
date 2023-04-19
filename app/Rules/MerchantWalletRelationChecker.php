<?php

namespace App\Rules;

use App\Http\Controllers\Constants;
use Illuminate\Contracts\Validation\Rule;

use App\Models\User as userModel;
class MerchantWalletRelationChecker implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($merchantKey)
    {

        $this->merchantKey = (string)$merchantKey;


    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {


         $merchantKeys = $this->merchantKey;

           //Just wallet for client is allowed
        return UserModel::where('wallet_user',$value)->where('is_blocked',false)->where('user_type',Constants::USER_TYPES['client'])->whereHas('merchants', function ( $query) use($merchantKeys) {
                $query->where('merchant_key', $merchantKeys);
            })->exists();


    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The wallet not related to merchant key';
    }
}
