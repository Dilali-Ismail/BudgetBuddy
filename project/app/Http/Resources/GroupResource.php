<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'devise'=> $this->devise,
            'admin_id' => $this->admin_id,
            'admin' => $this->whenLoaded('admin',function(){
                return [
                    'id' => $this->admin->id ,
                    'name' => $this->admin->name,
                    'email' => $this->admin->email
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'members' => $this->whenLoaded('users',function(){

                return $this->users->map(function($user){
                       return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email

                       ];
                });

            })
        ];
    }
}
