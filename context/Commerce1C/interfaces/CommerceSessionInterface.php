<?php

namespace context\Commerce1C\interfaces;

use repositories\Commerce1C\models\ImportSession;

interface CommerceSessionInterface
{
    /**
     * Создает новую сессию импорта
     */
    public function createSession(): ImportSession;
    
    /**
     * Получает сессию по ID
     */
    public function getSession(string $sessionId): ?ImportSession;
    
    /**
     * Сохраняет сессию
     */
    public function saveSession(ImportSession $session): void;
    
    /**
     * Удаляет сессию
     */
    public function deleteSession(string $sessionId): void;
    
    /**
     * Очищает истекшие сессии
     */
    public function cleanExpired(int $ttlMinutes = 60): void;
}
