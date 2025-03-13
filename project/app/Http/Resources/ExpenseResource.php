<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
             'id' => $this->id ,
             'title' => $this->title ,
             'amount' => $this->amount ,
             'expence_date' => $this->expence_date,
             'created_at' => $this->created_at,
             'updated_at' => $this->updated_at,
             'tags' => TagResource::collection($this->whenLoaded('tags')),

        ];

    }
}
