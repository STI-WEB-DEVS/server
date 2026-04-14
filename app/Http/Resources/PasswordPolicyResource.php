<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PasswordPolicyResource extends JsonResource
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
      
            'min_length' => $this->min_length,
      
            'requires_2fa'=> $this->requires_2fa,
      
            'updated_at' => $this->updated_at,
      
            // 'password' is NOT exposed — intentionally
      
          ];
    }
}
