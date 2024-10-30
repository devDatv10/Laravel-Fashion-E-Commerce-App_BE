<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'review_id' => $this->review_id,
            'customer_id' => $this->customer_id,
            'product_id' => $this->product_id,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_type' => $this->customer_type,
            'stars_review' => $this->stars_review,
            'review_title' => $this->review_title,
            'review_product' => $this->review_product,
            'media' => $this->media,
            'status' => $this->status,
            'review_date' => $this->review_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}