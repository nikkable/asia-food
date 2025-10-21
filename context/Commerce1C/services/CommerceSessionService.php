<?php

namespace context\Commerce1C\services;

use context\Commerce1C\interfaces\CommerceSessionInterface;
use DateTime;
use repositories\Commerce1C\models\ImportSession;
use context\AbstractService;

class CommerceSessionService extends AbstractService implements CommerceSessionInterface
{
    private const SESSION_PREFIX = 'commerce1c_session_';
    private const SESSION_CLEANUP_KEY = 'commerce1c_cleanup_time';
    
    public function __construct()
    {

    }

    public function createSession(): ImportSession
    {
        $sessionId = $this->generateSessionId();
        return new ImportSession($sessionId, new DateTime());
    }

    public function getSession(string $sessionId): ?ImportSession
    {
        $sessionKey = self::SESSION_PREFIX . $sessionId;
        $sessionData = \Yii::$app->cache->get($sessionKey);

        if (!$sessionData) {
            return null;
        }

        $createdAt = new DateTime($sessionData['created_at']);
        $session = new ImportSession($sessionId, $createdAt);

        if (isset($sessionData['files'])) {
            foreach ($sessionData['files'] as $filename => $fileData) {
                if (is_array($fileData) && isset($fileData['content'])) {
                    $session->addFile($filename, $fileData['content']);
                } else {
                    $session->addFile($filename, $fileData);
                }
            }
        }

        return $session;
    }

    public function saveSession(ImportSession $session): void
    {
        $sessionKey = self::SESSION_PREFIX . $session->getSessionId();
        
        $data = [
            'session_id' => $session->getSessionId(),
            'created_at' => $session->getCreatedAt()->format('Y-m-d H:i:s'),
            'files' => $session->getFiles()
        ];
        
        \Yii::$app->cache->set($sessionKey, $data, 3600);
    }

    public function deleteSession(string $sessionId): void
    {
        $sessionKey = self::SESSION_PREFIX . $sessionId;
        \Yii::$app->cache->delete($sessionKey);
    }

    private function generateSessionId(): string
    {
        return uniqid('commerce1c_', true);
    }
}
