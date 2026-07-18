<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuccessResource extends JsonResource
{
    protected string $message;

    public function __construct($resource, string $message = 'Success')
    {
        parent::__construct($resource);
        $this->message = $message;
    }

    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'message' => $this->message,
            'data' => parent::toArray($request),
        ];
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode(200);
    }
}
