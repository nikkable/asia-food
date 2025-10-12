<?php

namespace context\Commerce1C\services;

use context\Commerce1C\interfaces\CommerceAuthInterface;
use context\Commerce1C\interfaces\CommerceSessionInterface;
use context\Commerce1C\models\CommerceRequest;
use context\Commerce1C\models\CommerceResponse;
use context\AbstractService;

class CommerceAuthService extends AbstractService implements CommerceAuthInterface
{
    public function __construct(
        private CommerceSessionInterface $sessionService
    ) {}

    public function checkAuth(CommerceRequest $request): CommerceResponse
    {
        // Получаем данные авторизации из запроса
        $username = $_SERVER['PHP_AUTH_USER'] ?? null;
        $password = $_SERVER['PHP_AUTH_PW'] ?? null;

        // Проверяем авторизацию
        if (!$this->validateCredentials($username, $password)) {
            return CommerceResponse::failure('Invalid credentials', 401);
        }

        // Создаем новую сессию
        $session = $this->sessionService->createSession();
        $this->sessionService->saveSession($session);

        return CommerceResponse::authSuccess($session->getSessionId());
    }

    public function validateSession(string $sessionId): bool
    {
        $session = $this->sessionService->getSession($sessionId);
        
        if (!$session) {
            return false;
        }

        // Проверяем не истекла ли сессия
        if ($session->isExpired()) {
            $this->sessionService->deleteSession($sessionId);
            return false;
        }

        return true;
    }

    public function cleanExpiredSessions(): void
    {
        $this->sessionService->cleanExpired();
    }

    private function validateCredentials(?string $username, ?string $password): bool
    {
        // TODO: Реализовать проверку через конфигурацию или базу данных
        // Пока используем статичные данные
        return $username === 'admin' && $password === 'password';
    }
}
