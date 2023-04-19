<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
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
            "id" => $this->id,
            "name" => $this->name,
            "phone" => $this->phone,
            "email" => $this->email,
            "merchantKey" =>  $this->merchant_key  ,
            "whiteList" => $this->white_list ,
            "isActive" =>  $this->is_active  ,
            "WhiteListActive" =>  $this->white_list_active  ,
            "created_at" => $this->created_at ,
            "updated_at" => $this->updated_at,
        ];
    }
}
