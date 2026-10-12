<?php

namespace Infrastructure\Http\Validators;

class TokenRequestValidator implements ValidatorInterface
{
    public const string INVALID_EMAIL_FORMAT = "Invalid email format.";
    public const string INVALID_API_KEY = "Invalid API key.";
    public const string NO_ERRORS = '';

    public function checkForErrorInInputData(array $data): string
    {
        if (empty($data['email']) || !RegisterRequestValidator::isValidEmailFormat($data['email'])) {
            return self::INVALID_EMAIL_FORMAT;
        }
        if (empty($data['api_key']) || !is_string($data['api_key'])) {
            return self::INVALID_API_KEY;
        }
        return self::NO_ERRORS;
    }
}
