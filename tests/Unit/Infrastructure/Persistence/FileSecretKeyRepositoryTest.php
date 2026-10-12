<?php

namespace Tests\Unit\Infrastructure\Persistence;

use Infrastructure\Persistence\FileSecretKeyRepository;
use Tests\TestCase;

class FileSecretKeyRepositoryTest extends TestCase
{
    /** @test */
    public function keyFileExistAfterCreatingKey(): void
    {
        $repository = new FileSecretKeyRepository();

        $repository->createSecretKey();
        $result = file_exists($_ENV['JWT_SECRET']);

        $this->assertTrue($result);
    }

    /** @test */
    public function keyFileContainsKeyAfterCreatingKey(): void
    {
        $repository = new FileSecretKeyRepository();

        $repository->createSecretKey();
        $result = $repository->getSecretKey();

        $this->assertNotEquals('', $result);
    }
}
