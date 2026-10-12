<?php

namespace Application\UseCase;

use Domain\Authentication\ApiKeyGeneratorInterface;
use Domain\Repository\UsersRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Infrastructure\Http\Validators\RegisterRequestValidator;
use Infrastructure\Http\Validators\ValidatorInterface;

class RegisterUseCase
{
    private UsersRepositoryInterface $repository;
    private ApiKeyGeneratorInterface $apiKeyRepository;

    public function __construct(
        UsersRepositoryInterface $repository,
        ApiKeyGeneratorInterface $apiKeyRepository
    ) {
        $this->repository = $repository;
        $this->apiKeyRepository = $apiKeyRepository;
    }

    /**
     * @throws Exception
     */
    public function registerNewUser(Request $request): array
    {
        $requestData = $request->all();

        $email = trim($requestData['email']);

        if ($this->emailExists($email)) {
            throw new Exception('Email already registered.', 409);
        }

        try {
            $result = $this->register($email);
            return [
                'email'   => $result['email'],
                'api_key' => $result['apiKey'],
            ];
        } catch (Exception) {
            throw new Exception('Internal server error.', 500);
        }
    }

    public function register(string $email): array
    {
        $apiKey = $this->generateApiKey();
        $this->repository->createUser($email, $apiKey);

        return [
            'email'   => $email,
            'apiKey'  => $apiKey,
        ];
    }

    private function generateApiKey(): string
    {
        return $this->apiKeyRepository->generateApiKey();
    }

    public function emailExists(string $email): bool
    {
        return $this->repository->emailExists($email);
    }
}
