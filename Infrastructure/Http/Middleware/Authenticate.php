<?php

namespace Infrastructure\Http\Middleware;

use Closure;
use Domain\Authentication\JWTCipherInterface;
use Domain\Repository\SecretKeyRepositoryInterface;
use Exception;

class Authenticate
{
    public const UNAUTHORIZED_MESSAGE = 'Unauthorized. Token is invalid or has expired.';

    public function __construct(
        private JWTCipherInterface $cipher,
        private SecretKeyRepositoryInterface $secretKeyRepository
    ) {
    }

    public function handle($request, Closure $next, $guard = null)
    {
        $authHeader = $request->header('Authorization') ?? '';

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return response()->json(['error' => self::UNAUTHORIZED_MESSAGE], 401);
        }

        try {
            $secretKey = $this->secretKeyRepository->getSecretKey();
            $this->cipher->decode($matches[1], $secretKey);
        } catch (Exception $e) {
            return response()->json(['error' => self::UNAUTHORIZED_MESSAGE], 401);
        }

        return $next($request);
    }
}
