<?php

namespace Infrastructure\Http\Validators;

interface ValidatorInterface
{
    public function checkForErrorInInputData(array $data): string;
}
