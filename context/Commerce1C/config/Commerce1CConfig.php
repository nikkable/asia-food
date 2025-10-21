<?php

namespace context\Commerce1C\config;

use context\AbstractConfig;

class Commerce1CConfig extends AbstractConfig
{
    public function __construct(
        private string $username = '12257247',
        private string $password = 'dbf61458dc34319eda48ac71f0e12e63',
        private int    $sessionTtlMinutes = 60,
        private int    $maxFileSize = 1048576,
        private string $version = '2.05',
        private bool   $allowZip = false,
        private array  $allowedFileTypes = ['import0_1.xml', 'offers0_1.xml']
    ) {}

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSessionTtlMinutes(): int
    {
        return $this->sessionTtlMinutes;
    }

    public function getMaxFileSize(): int
    {
        return $this->maxFileSize;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function isZipAllowed(): bool
    {
        return $this->allowZip;
    }

    public function getAllowedFileTypes(): array
    {
        return $this->allowedFileTypes;
    }

    public function isFileTypeAllowed(string $filename): bool
    {
        return in_array($filename, $this->allowedFileTypes, true);
    }
}
