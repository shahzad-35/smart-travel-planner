<?php

namespace App\DTOs;

class ResponseDTO
{
    public bool $success;
    public string $message;
    public mixed $data;

    public function __construct(bool $success, string $message, mixed $data = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }
}
