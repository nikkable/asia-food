<?php

namespace repositories\Commerce1C\models;

use context\Commerce1C\enums\CommerceTypeEnum;
use context\Commerce1C\enums\CommerceModeEnum;

class CommerceRequest
{
    public function __construct(
        private CommerceTypeEnum $type,
        private CommerceModeEnum $mode,
        private ?string $filename = null,
        private ?string $content = null,
        private array $additionalParams = []
    ) {}

    public function getType(): CommerceTypeEnum
    {
        return $this->type;
    }

    public function getMode(): CommerceModeEnum
    {
        return $this->mode;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getAdditionalParams(): array
    {
        return $this->additionalParams;
    }

    public function getParam(string $key): mixed
    {
        return $this->additionalParams[$key] ?? null;
    }

    public static function fromArray(array $data, ?string $content = null): self
    {
        $type = CommerceTypeEnum::from($data['type'] ?? 'catalog');
        $mode = CommerceModeEnum::from($data['mode'] ?? 'checkauth');
        
        return new self(
            type: $type,
            mode: $mode,
            filename: $data['filename'] ?? null,
            content: $content,
            additionalParams: array_diff_key($data, array_flip(['type', 'mode', 'filename']))
        );
    }
}
