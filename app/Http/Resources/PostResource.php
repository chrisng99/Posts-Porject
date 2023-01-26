<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'post_text' => $this->post_text,
            'author' => $this->user->name,
            'category' => $this->category->name,
            'created_at' => $this->created_at,
        ];
    }
}
