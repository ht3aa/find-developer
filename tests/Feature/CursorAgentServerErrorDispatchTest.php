<?php

use App\Jobs\DispatchCursorAgentRepairJob;
use App\Support\CursorAgentServerErrorClassifier;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('dispatches cursor repair job when reporting a runtime exception and agent is enabled', function () {
    Config::set('cursor_agent.enabled', true);
    Queue::fake();

    report(new RuntimeException('boom'));

    Queue::assertPushed(DispatchCursorAgentRepairJob::class);
});

it('does not dispatch when cursor agent is disabled', function () {
    Config::set('cursor_agent.enabled', false);
    Queue::fake();

    report(new RuntimeException('boom'));

    Queue::assertNotPushed(DispatchCursorAgentRepairJob::class);
});

it('dispatches for http 500 after exception reporting rules allow it', function () {
    Config::set('cursor_agent.enabled', true);
    Queue::fake();

    report(new HttpException(500, 'broken'));

    Queue::assertPushed(DispatchCursorAgentRepairJob::class);
});

it('does not treat validation as server-side in classifier', function () {
    expect(CursorAgentServerErrorClassifier::shouldTrigger(ValidationException::withMessages(['x' => 'bad'])))->toBeFalse();
});

it('does not dispatch for http 404', function () {
    Config::set('cursor_agent.enabled', true);
    Queue::fake();

    report(new HttpException(404, 'missing'));

    Queue::assertNotPushed(DispatchCursorAgentRepairJob::class);
});

it('deduplicates identical errors within the rate limit window', function () {
    Config::set('cursor_agent.enabled', true);
    Config::set('cursor_agent.rate_limit_seconds', 3600);
    Queue::fake();

    $e = new RuntimeException('same message');
    report($e);
    report($e);

    Queue::assertPushed(DispatchCursorAgentRepairJob::class, 1);
});
