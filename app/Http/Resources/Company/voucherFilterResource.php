<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Resources\Json\JsonResource;

class voucherFilterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return
            [  "id" => $this->id,
            "serialNumber" => $this->uuid,
            "amount" => $this->amount,
            "isEnabled" => $this->is_enabled,
            "isUsed" =>  $this->is_used  ,
            "startDate" => $this->starts_at,
            "expiredDate" => $this->expires_at,
            "usedBy"=>isset($this->transaction->user) ? $this->transaction->user : [],
            "createdAt" => $this->created_at ,
            "updatedAt" => $this->updated_at,
        ];
    }
}
