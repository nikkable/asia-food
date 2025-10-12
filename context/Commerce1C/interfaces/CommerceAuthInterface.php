<?php

namespace context\Commerce1C\interfaces;

use context\Commerce1C\models\CommerceRequest;
use context\Commerce1C\models\CommerceResponse;

interface CommerceAuthInterface
{
    /**
     * Проверяет авторизацию и создает сессию
     */
    public function checkAuth(CommerceRequest $request): CommerceResponse;
    
    /**
     * Проверяет валидность сессии
     */
    public function validateSession(string $sessionId): bool;
    
    /**
     * Очищает истекшие сессии
     */
    public function cleanExpiredSessions(): void;
}
