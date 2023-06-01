<?php

namespace App\Contracts;

interface VerificationServiceInterface
{
    public function verifyFile(array $fileData): array;
}
