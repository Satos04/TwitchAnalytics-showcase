<?php

namespace Infrastructure\Http\Validators;

class GamesRequestValidator implements ValidatorInterface
{
    public const int MINIMUM_NUMBER_OF_GAMES = 1;
    public const int MAXIMUM_NUMBER_OF_GAMES = 3;
    public const string NO_ERRORS = '';
    public const string LIMIT_MUST_BE_BETWEEN_1_AND_3 = "Limit must be between 1 and 3.";

    public function checkForErrorInInputData(array $data): string
    {
        $limit = $data['limit'] ?? null;
        if (is_null($limit) || $limit < self::MINIMUM_NUMBER_OF_GAMES || $limit > self::MAXIMUM_NUMBER_OF_GAMES) {
            return self::LIMIT_MUST_BE_BETWEEN_1_AND_3;
        }
        return self::NO_ERRORS;
    }
}
