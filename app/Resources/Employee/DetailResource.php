<?php

namespace App\Resources\Employee;

use Illuminate\Http\Resources\Json\JsonResource;

class DetailResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "client_code" => (string)$this->client_code,
            "contract_length" => (int)$this->contract_length,
            "created_at" => (string)date('Y-m-d H:i:s', strtotime($this->created_at)),
            "avolution_commission" => (float)$this->avolution_commission,
            "monthly_fee" => (float)$this->monthly_fee,
            "cross_sales" => (float)$this->cross_sales,
            "ops_commission" => (float)$this->ops_commission
        ];
    }
}
