<?php

namespace App\Rules;

use App\Http\Controllers\Constants;
use Illuminate\Contracts\Validation\Rule;

use App\ServicesData\Merchant\MerchantService as merchantService;
class MerchantExistAndActive implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($userType)
    {
        $this->userType = $userType;
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


        if(in_array($this->userType,[Constants::USER_TYPES['company'],Constants::USER_TYPES['client']])) //company ,client
            return  merchantService::checkExistAndActive($value) ;
        else
            return true;


    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Merchant Key is not correct or not active';
    }
}
