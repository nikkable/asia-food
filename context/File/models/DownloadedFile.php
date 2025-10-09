<?php

namespace context\File\models;

use context\File\interfaces\FileInterface;

class DownloadedFile implements FileInterface
{
    private string $name;
    private ?string $tempName;
    private ?string $type;
    private ?int $size;
    private int $error;

    public function __construct(string $name, ?string $tempName, ?string $type, ?int $size, int $error)
    {
        $this->name = $name;
        $this->tempName = $tempName;
        $this->type = $type;
        $this->size = $size;
        $this->error = $error;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTempName(): ?string
    {
        return $this->tempName;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function saveAs(string $file, bool $deleteTempFile = true): bool
    {
        if ($this->error !== UPLOAD_ERR_OK) {
            return false;
        }

        if ($deleteTempFile) {
            return rename($this->tempName, $file);
        } else {
            return copy($this->tempName, $file);
        }
    }
}
