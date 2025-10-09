<?php

namespace context\File\interfaces;

interface FileInterface
{
    public function getName(): string;
    public function getTempName(): ?string;
    public function getType(): ?string;
    public function getSize(): ?int;
    public function getError(): int;
    public function saveAs(string $file, bool $deleteTempFile = true): bool;
}
