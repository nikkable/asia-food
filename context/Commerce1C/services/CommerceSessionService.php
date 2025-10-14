<?php

namespace context\Commerce1C\services;

use context\Commerce1C\interfaces\CommerceSessionInterface;
use repositories\Commerce1C\models\ImportSession;
use context\AbstractService;

class CommerceSessionService extends AbstractService implements CommerceSessionInterface
{
    private array $sessions = [];

    public function createSession(): ImportSession
    {
        $sessionId = $this->generateSessionId();
        $session = new ImportSession($sessionId, new \DateTime());
        
        return $session;
    }

    public function getSession(string $sessionId): ?ImportSession
    {
        return $this->sessions[$sessionId] ?? null;
    }

    public function saveSession(ImportSession $session): void
    {
        $this->sessions[$session->getSessionId()] = $session;
    }

    public function deleteSession(string $sessionId): void
    {
        unset($this->sessions[$sessionId]);
    }

    public function cleanExpired(int $ttlMinutes = 60): void
    {
        foreach ($this->sessions as $sessionId => $session) {
            if ($session->isExpired($ttlMinutes)) {
                $this->deleteSession($sessionId);
            }
        }
    }

    private function generateSessionId(): string
    {
        return uniqid('commerce1c_', true);
    }
}
