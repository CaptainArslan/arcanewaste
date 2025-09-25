<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\TaxResource;
use App\Http\Resources\PromotionResource;
use App\Http\Resources\CompanyResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DumpsterSizeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'min_rental_days' => $this->min_rental_days,
            'max_rental_days' => $this->max_rental_days,
            'base_rent' => $this->base_rent,
            'extra_day_rent' => $this->extra_day_rent,
            'overdue_rent' => $this->overdue_rent,
            'volume_cubic_yards' => $this->volume_cubic_yards,
            'weight_limit_lbs' => $this->weight_limit_lbs,
            'is_active' => $this->is_active,
            'taxes' => TaxResource::collection($this->taxes),
            'company' => new CompanyResource($this->company),
        ];
    }
}
