<?php

namespace Infrastructure\Http\Controllers;

use Application\UseCase\TokenUseCase;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Infrastructure\Http\Validators\ValidatorInterface;

class TokenController extends Controller
{
    public const string INVALID_PARAMETERS_ERROR = "Invalid or missing parameters.";
    public const string NO_ERRORS = '';
    private TokenUseCase $tokenUseCase;
    private ValidatorInterface $validator;

    public function __construct(TokenUseCase $useCase, ValidatorInterface $validator)
    {
        $this->tokenUseCase = $useCase;
        $this->validator = $validator;
    }

    public function handleTokenRequest(Request $request): JsonResponse
    {
        try {
            if ($this->validator->checkForErrorInInputData($request->all()) !== self::NO_ERRORS) {
                throw new Exception(self::INVALID_PARAMETERS_ERROR, 400);
            }
            $tokenData = $this->tokenUseCase->createTokenJwt(
                $request->input('email') ?? '',
                $request->input('api_key') ?? '',
                $request->ip() ?? ''
            );
            return response()->json($tokenData, 200);
        } catch (Exception $exception) {
            return $this->transformExceptionIntoJsonResponse($exception);
        }
    }
    public function transformExceptionIntoJsonResponse(Exception $exception): JsonResponse
    {
        $code = $exception->getCode();
        $status = ($code >= 400 && $code < 600) ? $code : 500;
        return response()->json(
            ['error' => $exception->getMessage()],
            $status
        );
    }
}
