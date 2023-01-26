<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    public $message;

    public function __construct($resource, string $message = null)
    {
        parent::__construct($resource);

        $this->resource = $this->collectResource($resource);

        $this->message = $message;
    }

    public function toArray($request): array
    {
        return [
            'posts' => $this->collection,
        ];
    }

    public function with($request): array
    {
        return [
            'status' => 'Request was successful.',
            'message' => $this->message,
        ];
    }
}
