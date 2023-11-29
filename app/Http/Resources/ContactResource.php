<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ContactResource extends JsonResource
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
            "id" => $this->id,
            "name" => $this->name,
            "phone" => $this->phone,
            "email" => $this->email,
            "address" => $this->address,
            "profile_photo" => $this->photo ? asset(Storage::url($this->photo)) : null,
            "owner" => $this->user->name,
            "created_at" => $this->created_at->format("j-M-Y"),
            "updated_at" => $this->updated_at->format("j-M-Y"),
        ];
    }

    // public static function collection($resource)
    // {
    //     return parent::collection($resource)->withPagination();
    // }
}
