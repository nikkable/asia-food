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
                if (is_array($fileData)) {
                    // Если это массив с метаданными, используем addUploadedFile напрямую
                    if (isset($fileData['file_path'])) {
                        $session->addUploadedFile($filename, $fileData['file_path']);
                    } elseif (isset($fileData['content'])) {
                        $session->addUploadedFile($filename, $fileData['content']);
                    }
                } else {
                    $session->addUploadedFile($filename, $fileData);
                }
            }
        }

        if (isset($sessionData['metadata']) && is_array($sessionData['metadata'])) {
            foreach ($sessionData['metadata'] as $key => $value) {
                $session->setMetadata($key, $value);
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
            'files' => $session->getFiles(),
            'metadata' => $session->getMetadata(),
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
    
    public function saveFile(ImportSession $session, string $filename, string $content): ?string
    {
        $filesDirectory = dirname(\Yii::getAlias('@app')) . '/context/Commerce1C/files';
        if (!is_dir($filesDirectory)) {
            mkdir($filesDirectory, 0755, true);
        }
        
        $sessionDir = $filesDirectory . '/' . $session->getSessionId();
        if (!is_dir($sessionDir)) {
            mkdir($sessionDir, 0755, true);
        }
        
        $safeFilename = basename($filename);
        $filePath = $sessionDir . '/' . $safeFilename;
        
        if (file_put_contents($filePath, $content) === false) {
            \Yii::error("Failed to save file: {$filePath}", __METHOD__);
            return null;
        }
        
        $session->addUploadedFile($safeFilename, $filePath);
        $this->saveSession($session);
        
        \Yii::info("File saved: {$filePath} (size: " . strlen($content) . " bytes)", __METHOD__);
        
        return $filePath;
    }
}
