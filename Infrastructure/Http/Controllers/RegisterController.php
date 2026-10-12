<?php

namespace Infrastructure\Http\Controllers;

use Application\UseCase\RegisterUseCase;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infrastructure\Http\Validators\ValidatorInterface;

class RegisterController extends Controller
{
    public const string NO_ERRORS = '';
    private RegisterUseCase $useCase;
    private ValidatorInterface $validator;

    public function __construct(RegisterUseCase $useCase, ValidatorInterface $validator)
    {
        $this->useCase = $useCase;
        $this->validator = $validator;
    }

    public function handle(Request $request): JsonResponse
    {
        try {
            $requestData = $request->all();
            $errors      = $this->validator->checkForErrorInInputData($requestData);
            if ($errors !== self::NO_ERRORS) {
                throw new Exception($errors, 400);
            }
            $responseData = $this->useCase->registerNewUser($request);
            return response()->json($responseData, 201);
        } catch (Exception $exception) {
            return $this->transformExceptionIntoJsonResponse($exception);
        }
    }
    public function transformExceptionIntoJsonResponse(Exception $exception): JsonResponse
    {
        $statusCode = $this->isValidHttpStatusCode($exception->getCode())
            ? $exception->getCode()
            : 500;

        return response()->json(
            ['error' => $exception->getMessage()],
            $statusCode,
            ['Content-Type' => 'application/json']
        );
    }

    private function isValidHttpStatusCode(int $code): bool
    {
        return $code >= 100 && $code <= 599;
    }
}
