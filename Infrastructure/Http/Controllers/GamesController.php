<?php

namespace Infrastructure\Http\Controllers;

use Application\UseCase\GamesUseCase;
use Illuminate\Http\Request;
use Infrastructure\Http\Validators\ValidatorInterface;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class GamesController extends Controller
{
    public const string NO_ERRORS = '';
    private GamesUseCase $useCase;
    private ValidatorInterface $validator;
    public function __construct(GamesUseCase $useCase, ValidatorInterface $validator)
    {
        $this->useCase = $useCase;
        $this->validator = $validator;
    }

    public function getTopGames(Request $request)
    {
        $limit = (int) $request->query('limit', '1');

        try {
            if ($this->validator->checkForErrorInInputData(['limit' => $limit]) !== self::NO_ERRORS) {
                throw new InvalidArgumentException($this->validator->checkForErrorInInputData(['limit' => $limit]));
            }
            $result = $this->useCase->getTopGamesWithStats($limit);
            return response()->json($result, 200);
        } catch (InvalidArgumentException) {
            return response()->json(['error' => "Invalid 'limit' parameter. Must be between 1 and 3."], 400);
        } catch (RuntimeException $e) {
            if (str_contains($e->getMessage(), '401')) {
                return response()->json(['error' => 'Unauthorized. Token is invalid or has expired.'], 401);
            }
            return response()->json(['error' => 'Internal server error.'], 500);
        } catch (Throwable) {
            return response()->json(['error' => 'Internal server error.'], 500);
        }
    }
}
