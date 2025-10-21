<?php

namespace context\Commerce1C\services;

use context\Commerce1C\interfaces\CommerceSessionInterface;
use repositories\Commerce1C\models\ImportSession;
use context\AbstractService;

class CommerceSessionService extends AbstractService implements CommerceSessionInterface
{
    private const SESSION_PREFIX = 'commerce1c_session_';
    private const SESSION_CLEANUP_KEY = 'commerce1c_cleanup_time';
    
    public function __construct()
    {
        // Используем Yii Cache вместо Session
    }

    public function createSession(): ImportSession
    {
        $sessionId = $this->generateSessionId();
        $session = new ImportSession($sessionId, new \DateTime());
        
        return $session;
    }

    public function getSession(string $sessionId): ?ImportSession
    {
        $sessionKey = self::SESSION_PREFIX . $sessionId;
        $sessionData = \Yii::$app->cache->get($sessionKey);

        \Yii::error("Getting session: $sessionKey, found: " . ($sessionData ? 'yes' : 'no'), __METHOD__);

        if (!$sessionData) {
            return null;
        }

        $createdAt = new \DateTime($sessionData['created_at']);
        $session = new ImportSession($sessionId, $createdAt);

        // Восстанавливаем файлы сессии
        if (isset($sessionData['files'])) {
            foreach ($sessionData['files'] as $filename => $fileData) {
                // Если это массив с метаданными, извлекаем content
                if (is_array($fileData) && isset($fileData['content'])) {
                    $session->addFile($filename, $fileData['content']);
                } else {
                    // Если это просто строка
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
        
        // Сохраняем на 1 час (3600 секунд)
        \Yii::$app->cache->set($sessionKey, $data, 3600);
        
        \Yii::error("Saved session: $sessionKey", __METHOD__);
    }

    public function deleteSession(string $sessionId): void
    {
        $sessionKey = self::SESSION_PREFIX . $sessionId;
        \Yii::$app->cache->delete($sessionKey);
    }

    public function cleanExpired(int $ttlMinutes = 60): void
    {
        // Проверяем, не пора ли очистить сессии (раз в час)
        $lastCleanup = \Yii::$app->session->get(self::SESSION_CLEANUP_KEY, 0);
        $now = time();
        
        if ($now - $lastCleanup < 3600) { // 1 час
            return;
        }
        
        // Проходим по всем ключам сессии
        $session = \Yii::$app->session;
        $keysToRemove = [];
        
        foreach ($session as $key => $value) {
            if (strpos($key, self::SESSION_PREFIX) === 0) {
                $sessionId = substr($key, strlen(self::SESSION_PREFIX));
                $commerceSession = $this->getSession($sessionId);
                
                if ($commerceSession && $commerceSession->isExpired($ttlMinutes)) {
                    $keysToRemove[] = $key;
                }
            }
        }
        
        // Удаляем просроченные сессии
        foreach ($keysToRemove as $key) {
            $session->remove($key);
        }
        
        // Обновляем время последней очистки
        $session->set(self::SESSION_CLEANUP_KEY, $now);
    }

    private function generateSessionId(): string
    {
        return uniqid('commerce1c_', true);
    }
}
