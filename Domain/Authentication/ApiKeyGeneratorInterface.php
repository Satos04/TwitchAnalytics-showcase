<?php

namespace Domain\Authentication;

interface ApiKeyGeneratorInterface
{
    public function generateApiKey(): string;
}
