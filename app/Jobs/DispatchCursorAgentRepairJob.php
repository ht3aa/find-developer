<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class DispatchCursorAgentRepairJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 600;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public string $fingerprint,
        public array $payload,
    ) {
        if ($queue = config('cursor_agent.queue')) {
            $this->onQueue($queue);
        }
    }

    public function uniqueId(): string
    {
        return $this->fingerprint;
    }

    public function handle(): void
    {
        if (! config('cursor_agent.enabled')) {
            return;
        }

        $dumpId = (string) Str::uuid();
        $relativePath = 'cursor-agent-dumps/'.$dumpId.'.json';
        $trace = (string) ($this->payload['trace'] ?? '');
        $maxBytes = max(1024, (int) config('cursor_agent.trace_max_bytes'));
        if (strlen($trace) > $maxBytes) {
            $trace = substr($trace, 0, $maxBytes)."\n... (trace truncated)";
        }

        $dump = array_merge($this->payload, [
            'trace' => $trace,
            'dump_id' => $dumpId,
            'base_branch' => config('cursor_agent.base_branch'),
        ]);

        Storage::disk('local')->put($relativePath, json_encode($dump, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));

        $absoluteDumpPath = Storage::disk('local')->path($relativePath);
        $workspace = base_path();
        $baseBranch = config('cursor_agent.base_branch');
        $model = config('cursor_agent.model');

        $prompt = <<<PROMPT
You are an automated repair run for this Laravel application.

Read the server error dump JSON at: {$absoluteDumpPath}

Goals (in order):
1. Diagnose and fix the root cause in this repository (workspace: {$workspace}).
2. Follow AGENTS.md and CLAUDE.md if present.
3. Create a git branch named like cursor/YYYY-MM-DD-fix-server-error-{$dumpId} from {$baseBranch}, implement the fix, run the minimal relevant tests (php artisan test with a narrow filter when possible), run vendor/bin/pint --dirty on changed PHP files.
4. Push the branch to origin and open a pull request into {$baseBranch} using gh pr create (non-interactive flags). Title and body should summarize the bug and the fix. If gh is unavailable, commit locally and print exact commands for the human to run.

Use the Cursor Agent tools as needed. Prefer small, correct changes.
PROMPT;

        $binary = config('cursor_agent.binary');

        $result = Process::path($workspace)
            ->timeout($this->timeout)
            ->run([
                $binary,
                '--print',
                '--trust',
                '--force',
                '--model',
                $model,
                '--workspace',
                $workspace,
                $prompt,
            ]);

        if (! $result->successful()) {
            Log::error('cursor_agent.cli_failed', [
                'exit_code' => $result->exitCode(),
                'stderr' => $result->errorOutput(),
                'stdout' => $result->output(),
                'dump' => $relativePath,
            ]);

            $result->throw();
        }

        Log::info('cursor_agent.cli_finished', [
            'dump' => $relativePath,
            'stdout_bytes' => strlen($result->output()),
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('cursor_agent.job_failed', [
            'message' => $exception?->getMessage(),
            'fingerprint' => $this->fingerprint,
        ]);
    }
}
