<?php

namespace App\Http\Requests\User;

use Anik\Form\FormRequest;
use App\Http\Controllers\Constants;
use App\Models\User as UserModel;
use Illuminate\Http\Request;
use App\ServicesData\User\UserService as userService;
class CompanyInfo extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize(): bool
    {
        $checkWalletMerchantAuthorization =  userService::checkWalletMerchantAuthorization(Request::header('key') ,auth()->user());

        if(
            !$checkWalletMerchantAuthorization ||
            auth()->user()->user_type !== Constants::USER_TYPES['company']  //not Company
            )
            return false;
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            //
        ];
    }
}
