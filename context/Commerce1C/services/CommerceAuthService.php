<?php

namespace context\Commerce1C\services;

use context\Commerce1C\interfaces\CommerceAuthInterface;
use context\Commerce1C\interfaces\CommerceSessionInterface;
use repositories\Commerce1C\models\CommerceRequest;
use repositories\Commerce1C\models\CommerceResponse;
use context\AbstractService;

class CommerceAuthService extends AbstractService implements CommerceAuthInterface
{
    public function __construct(
        private CommerceSessionInterface $sessionService
    ) {}

    public function checkAuth(CommerceRequest $request): CommerceResponse
    {
        $username = $_SERVER['PHP_AUTH_USER'] ?? null;
        $password = $_SERVER['PHP_AUTH_PW'] ?? null;


        if (!$username && !$password) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

            if ($authHeader && preg_match('/Basic\s+(.*)$/i', $authHeader, $matches)) {
                $credentials = base64_decode($matches[1]);
                if (strpos($credentials, ':') !== false) {
                    list($username, $password) = explode(':', $credentials, 2);
                }
            }
        }

        if (!$this->validateCredentials($username, $password)) {
            return CommerceResponse::failure('Invalid credentials', 401);
        }

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
        
        if ($session->isExpired()) {
            $this->sessionService->deleteSession($sessionId);
            return false;
        }
        
        return true;
    }

    public function getSessionIdFromRequest(): ?string
    {
        $cookieName = 'COMMERCE1C_SESSION';
        
        $cookies = $_COOKIE ?? [];
        if (isset($cookies[$cookieName])) {
            return $cookies[$cookieName];
        }

        return null;
    }

    private function validateCredentials(?string $username, ?string $password): bool
    {
        return $username === '12257247' && $password === 'dbf61458dc34319eda48ac71f0e12e63';
    }
}
