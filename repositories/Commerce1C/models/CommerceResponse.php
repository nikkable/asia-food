<?php

namespace repositories\Commerce1C\models;

class CommerceResponse
{
    public function __construct(
        private string $status = 'success',
        private ?string $message = null,
        private array $data = [],
        private int $httpCode = 200
    ) {}

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data
        ];
    }

    public function toString(): string
    {
        if ($this->status === 'success') {
            return $this->message ? "success\n{$this->message}" : 'success';
        }
        
        return $this->message ? "failure\n{$this->message}" : 'failure';
    }

    public static function success(string $message = null, array $data = []): self
    {
        return new self('success', $message, $data);
    }

    public static function failure(string $message = null, int $httpCode = 400): self
    {
        return new self('failure', $message, [], $httpCode);
    }

    public static function progressSuccess(string $sessionId): self
    {
        return new self('success', "progress\nsession_id={$sessionId}");
    }

    public static function authSuccess(string $sessionId, string $version = '2.05'): self
    {
        return new self('success', "success\nsession_id={$sessionId}\nversion={$version}");
    }
}
