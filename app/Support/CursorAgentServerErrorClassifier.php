<?php

namespace App\Support;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

final class CursorAgentServerErrorClassifier
{
    public static function shouldTrigger(Throwable $e): bool
    {
        if ($e instanceof ValidationException) {
            return false;
        }

        if ($e instanceof AuthenticationException) {
            return false;
        }

        if ($e instanceof ModelNotFoundException) {
            return false;
        }

        if ($e instanceof HttpExceptionInterface) {
            return $e->getStatusCode() >= 500;
        }

        return true;
    }

    public static function fingerprint(Throwable $e): string
    {
        return hash('sha256', $e::class.'|'.$e->getMessage().'|'.$e->getFile().':'.$e->getLine());
    }
}
