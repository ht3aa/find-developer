<?php

use App\Jobs\DispatchCursorAgentRepairJob;
use Carbon\Carbon;

it('builds remote branch name for dump id', function () {
    Carbon::setTestNow('2026-04-02 12:00:00');

    expect(DispatchCursorAgentRepairJob::remoteBranchNameForDump('a1b2c3d4-e5f6-7890-abcd-ef1234567890'))
        ->toBe('cursor/2026-04-02-fix-server-error-a1b2c3d4');

    Carbon::setTestNow();
});
