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
             'expense_date' => $this->expense_date,
             'created_at' => $this->created_at,
             'updated_at' => $this->updated_at,
             'user_id' => $this->user_id,
             'group_id' => $this->when($this->group_id, $this->group_id),
             'tags' => TagResource::collection($this->whenLoaded('tags')),
             'group' => new GroupResource($this->whenLoaded('group')),
             'payeur' => $this->whenLoaded('participations', function () {
                return $this->participations
                    ->where('type', 'payeur')
                    ->map(function ($participation) {
                        return [
                            'user_id' => $participation->user_id,
                            'name' => $participation->user->name ?? 'Utilisateur inconnu',
                            'amount' => $participation->amount,
                            'percentage' => $participation->percentage,
                        ];
                    });
            }),

            'benificier' => $this->whenLoaded('participations', function () {
                return $this->participations
                    ->where('type', 'benificier')
                    ->map(function ($participation) {
                        return [
                            'user_id' => $participation->user_id,
                            'name' => $participation->user->name ?? 'Utilisateur inconnu',
                            'amount' => $participation->amount,
                            'percentage' => $participation->percentage,
                        ];
                    });
            }),


        ];

    }
}
