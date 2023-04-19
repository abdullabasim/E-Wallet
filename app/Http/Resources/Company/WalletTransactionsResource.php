<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "uuid" => $this->uuid,
            "walletOwner" => $this->payable->name,
            "walletId" => $this->payable->wallet_user,
            "operationType" => $this->type,
            "amount" => $this->amount,
            "walletOwnerUserType" => $this->payable->user_type,
            "createdAt" => $this->created_at ,
            "updatedAt" => $this->updated_at,
        ];
    }
}
