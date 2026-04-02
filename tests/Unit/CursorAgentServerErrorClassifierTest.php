<?php

use App\Support\CursorAgentServerErrorClassifier;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('treats generic errors as server-side', function () {
    expect(CursorAgentServerErrorClassifier::shouldTrigger(new RuntimeException('fail')))->toBeTrue();
});

it('treats http 5xx as server-side', function () {
    expect(CursorAgentServerErrorClassifier::shouldTrigger(new HttpException(500, 'broken')))->toBeTrue();
    expect(CursorAgentServerErrorClassifier::shouldTrigger(new HttpException(503, 'down')))->toBeTrue();
});

it('does not treat http 4xx as server-side', function () {
    expect(CursorAgentServerErrorClassifier::shouldTrigger(new HttpException(404, 'missing')))->toBeFalse();
    expect(CursorAgentServerErrorClassifier::shouldTrigger(new HttpException(422, 'invalid')))->toBeFalse();
});

it('ignores auth and model not found', function () {
    expect(CursorAgentServerErrorClassifier::shouldTrigger(new AuthenticationException))->toBeFalse();
    expect(CursorAgentServerErrorClassifier::shouldTrigger(new ModelNotFoundException))->toBeFalse();
});

it('builds a stable fingerprint for the same exception instance', function () {
    $e = new RuntimeException('x');
    expect(CursorAgentServerErrorClassifier::fingerprint($e))->toBe(CursorAgentServerErrorClassifier::fingerprint($e));
});
