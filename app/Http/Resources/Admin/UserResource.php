<?php

namespace App\Http\Resources\Admin;

use App\Http\Controllers\Constants;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\PaymentPermission as PaymentPermissionModel;
use App\Models\User as userModel;
use App\ServicesData\User\UserService as userService;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $userId = $this->id;
        $usersExecutor = [];
        $usersBeneficiary = [];

        if ($this->user_type === Constants::USER_TYPES['client']) // user type should be client
        {
            $usersExecutor = userService::getUsersExecutor($userId);
            $usersBeneficiary = userService::getUsersBeneficiary($userId);
        }


        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "phone" => $this->phone,
            "userType" => $this->user_type,
            "isBlocked" => $this->is_blocked,
            "allowLogin" => $this->allow_login,
            "wallet" => $this->wallet_user,
            "balance" => $this->user_type !== Constants::USER_TYPES['admin'] ? $this->balance : null,
            "merchant" => $this->merchants,
            "usersExecutor" => $usersExecutor,
            "usersBeneficiary" => $usersBeneficiary,
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
        ];

    }
}
