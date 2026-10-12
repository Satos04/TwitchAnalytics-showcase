<?php

namespace Tests\Doubles;

use Domain\Entity\TokenJWT;
use Domain\Repository\SecretKeyRepositoryInterface;
use Application\UseCase\TokenUseCase;
use Exception;

class TokenUseCaseStub extends TokenUseCase
{
    /**
     * @throws Exception
     */
    public function generateTokenJwt(
        string $clientIp,
        mixed $requestContent,
        SecretKeyRepositoryInterface $secretKeyRepository,
        ?TokenJWT $tokenGenerator = null
    ): string {
        return "12345";
    }
}
