<?php

namespace Infrastructure\Http\Validators;

class SearchRequestValidator implements ValidatorInterface
{
    public const string INVALID_QUERY_PARAMETER = "Invalid or missing 'query' parameter.";
    public const string NO_ERRORS = '';

    public function checkForErrorInInputData(array $data): string
    {
        if (empty($data['query']) || !is_string($data['query'])) {
            return self::INVALID_QUERY_PARAMETER;
        }
        return self::NO_ERRORS;
    }
}
