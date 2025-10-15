<?php

namespace context\File\models;

use context\File\interfaces\FileInterface;
use yii\web\UploadedFile;

class UploadedFileWrapper implements FileInterface
{
    private UploadedFile $uploadedFile;

    public function __construct(UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;
    }

    public function getName(): string
    {
        return $this->uploadedFile->name;
    }

    public function getTempName(): ?string
    {
        return $this->uploadedFile->tempName;
    }

    public function getType(): ?string
    {
        return $this->uploadedFile->type;
    }

    public function getSize(): ?int
    {
        return $this->uploadedFile->size;
    }

    public function getError(): int
    {
        return $this->uploadedFile->error;
    }

    public function saveAs(string $file, bool $deleteTempFile = true): bool
    {
        return $this->uploadedFile->saveAs($file, $deleteTempFile);
    }
    
    public function getOriginalFile(): UploadedFile
    {
        return $this->uploadedFile;
    }
}
