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

    public function addUploadedFile(string $filename, string $contentOrPath): void
    {
        // Проверяем, это путь к файлу или содержимое
        if (file_exists($contentOrPath)) {
            // Это путь к файлу
            $this->uploadedFiles[$filename] = [
                'file_path' => $contentOrPath,
                'uploaded_at' => new \DateTime(),
                'size' => filesize($contentOrPath)
            ];
        } else {
            // Это содержимое (старая логика)
            $this->uploadedFiles[$filename] = [
                'content' => $contentOrPath,
                'uploaded_at' => new \DateTime(),
                'size' => strlen($contentOrPath)
            ];
        }
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
        if (!isset($this->uploadedFiles[$filename])) {
            return null;
        }
        
        $fileData = $this->uploadedFiles[$filename];
        
        // Если есть путь к файлу, возвращаем его
        if (isset($fileData['file_path'])) {
            return $fileData['file_path'];
        }
        
        // Иначе возвращаем содержимое (старая логика)
        return $fileData['content'] ?? null;
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
    
    /**
     * Псевдоним для getUploadedFiles() для совместимости с CommerceSessionService
     */
    public function getFiles(): array
    {
        return $this->uploadedFiles;
    }
    
    /**
     * Псевдоним для addUploadedFile() для совместимости с CommerceSessionService
     */
    public function addFile(string $filename, string $content): void
    {
        $this->addUploadedFile($filename, $content);
    }
    
    /**
     * Получает информацию о файле
     */
    public function getFile(string $filename): ?array
    {
        if (!isset($this->uploadedFiles[$filename])) {
            return null;
        }
        
        $fileData = $this->uploadedFiles[$filename];
        
        // Формируем путь к файлу
        if (isset($fileData['file_path'])) {
            return [
                'path' => $fileData['file_path'],
                'size' => $fileData['size'] ?? 0,
                'uploaded_at' => $fileData['uploaded_at'] ?? null
            ];
        }
        
        // Если содержимое хранится в памяти (старая логика)
        if (isset($fileData['content'])) {
            return [
                'content' => $fileData['content'],
                'size' => $fileData['size'] ?? 0,
                'uploaded_at' => $fileData['uploaded_at'] ?? null
            ];
        }
        
        return null;
    }
}
