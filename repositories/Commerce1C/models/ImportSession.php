<?php

namespace repositories\Commerce1C\models;

class ImportSession
{
    public function __construct(
        private string $sessionId,
        private \DateTime $createdAt,
        private array $uploadedFiles = [],
        private array $importedFiles = [],
        private array $metadata = []
    ) {}

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    public function getImportedFiles(): array
    {
        return $this->importedFiles;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function addUploadedFile(string $filename, string $content): void
    {
        $this->uploadedFiles[$filename] = [
            'content' => $content,
            'uploaded_at' => new \DateTime(),
            'size' => strlen($content)
        ];
    }

    public function markFileAsImported(string $filename): void
    {
        if (isset($this->uploadedFiles[$filename])) {
            $this->importedFiles[$filename] = new \DateTime();
        }
    }

    public function isFileUploaded(string $filename): bool
    {
        return isset($this->uploadedFiles[$filename]);
    }

    public function isFileImported(string $filename): bool
    {
        return isset($this->importedFiles[$filename]);
    }

    public function getFileContent(string $filename): ?string
    {
        return $this->uploadedFiles[$filename]['content'] ?? null;
    }

    public function setMetadata(string $key, mixed $value): void
    {
        $this->metadata[$key] = $value;
    }

    public function getMetadataValue(string $key): mixed
    {
        return $this->metadata[$key] ?? null;
    }

    public function isExpired(int $ttlMinutes = 60): bool
    {
        $expiresAt = (clone $this->createdAt)->modify("+{$ttlMinutes} minutes");
        return new \DateTime() > $expiresAt;
    }
}
