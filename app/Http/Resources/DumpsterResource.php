<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\DumpsterSizeResource;
use App\Http\Resources\WarehouseResource;

class DumpsterResource extends JsonResource
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
            'serial_number' => $this->serial_number,
            'status' => $this->status,
            'last_service_date' => $this->last_service_date,
            'next_service_due' => $this->next_service_due,
            'notes' => $this->notes,
            'is_available' => $this->is_available,
            'is_active' => $this->is_active,
            'company' => new CompanyResource($this->company),
            'dumpster_size' => new DumpsterSizeResource($this->size),
            'warehouse' => new WarehouseResource($this->warehouse),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
