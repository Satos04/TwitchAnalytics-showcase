<?php

namespace Infrastructure\Http\Controllers;

use Application\UseCase\SearchUseCase;
use Illuminate\Http\Request;
use Infrastructure\Http\Validators\ValidatorInterface;

class SearchController extends Controller
{
    public const string NO_ERRORS = '';
    private SearchUseCase $useCase;
    private ValidatorInterface $validator;

    public function __construct(SearchUseCase $useCase, ValidatorInterface $validator)
    {
        $this->useCase = $useCase;
        $this->validator = $validator;
    }

    public function searchCategories(Request $request)
    {
        $query = $request->query('query');

        if ($this->validator->checkForErrorInInputData(['query' => $query]) !== self::NO_ERRORS) {
            return response()->json(['error' => "Invalid or missing 'query' parameter."], 400);
        }
        try {
            $categories = $this->useCase->searchCategories(trim($query));
            return response()->json($categories, 200);
        } catch (\RuntimeException $e) {
            if (str_contains($e->getMessage(), '401')) {
                return response()->json(['error' => 'Unauthorized. Token is invalid or has expired.'], 401);
            }
            return response()->json(['error' => 'Internal server error.'], 500);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Internal server error.'], 500);
        }
    }
}
