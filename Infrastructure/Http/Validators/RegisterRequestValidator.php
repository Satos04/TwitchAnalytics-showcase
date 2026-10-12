<?php

namespace Infrastructure\Http\Validators;

class RegisterRequestValidator implements ValidatorInterface
{
    private const string FIELD_EMAIL = 'email';
    public const string BAD_REQUEST_ERROR = "Invalid or missing 'email' parameter.";
    public const string NO_ERRORS = '';

    public function checkForErrorInInputData(array $data): string
    {
        if (!isset($data[self::FIELD_EMAIL]) || empty(trim($data[self::FIELD_EMAIL]))) {
            return self::BAD_REQUEST_ERROR;
        }

        $email = trim($data[self::FIELD_EMAIL]);

        if (!$this->isValidEmailFormat($email)) {
            return self::BAD_REQUEST_ERROR;
        }

        return self::NO_ERRORS;
    }

    public static function isValidEmailFormat(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
