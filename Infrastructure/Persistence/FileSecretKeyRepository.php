<?php

namespace Infrastructure\Persistence;

use Domain\Repository\SecretKeyRepositoryInterface;
use Random\RandomException;

class FileSecretKeyRepository implements SecretKeyRepositoryInterface
{
    private string $keyPath;

    public function __construct()
    {
        $relativePath = $_ENV['JWT_SECRET'] ?? './key.bin';
        $this->keyPath = base_path($relativePath);
    }

    /**
     * @throws RandomException
     */
    public function createSecretKey(): void
    {
        if (!file_exists($this->keyPath)) {
            $secretKeyBinary = random_bytes(32);
            file_put_contents($this->keyPath, $secretKeyBinary);
        }
    }

    public function getSecretKey(): false|string
    {
        if (!file_exists($this->keyPath)) {
            $this->createSecretKey();
        }
        return file_get_contents($this->keyPath);
    }
}
