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

    /**
     * Remote branch name for `git push origin HEAD:refs/heads/...` (stable prefix + first 8 chars of dump UUID).
     */
    public static function remoteBranchNameForDump(string $dumpId): string
    {
        return 'cursor/'.now()->format('Y-m-d').'-fix-server-error-'.substr($dumpId, 0, 8);
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

        $remoteBranch = self::remoteBranchNameForDump($dumpId);

        $prompt = <<<PROMPT
You are an automated repair run for this Laravel application.

Read the server error dump JSON at: {$absoluteDumpPath}

Goals (in order):
1. Diagnose and fix the root cause in this repository (workspace: {$workspace}).
2. Follow AGENTS.md and CLAUDE.md if present.
3. From {$baseBranch} (e.g. git fetch origin && git checkout {$baseBranch} && git pull --ff-only, then git checkout -b a short-lived local branch): implement the fix, run the minimal relevant tests (php artisan test with a narrow filter when possible), run vendor/bin/pint --dirty on changed PHP files, and commit. You may keep any local branch name; the next step sets the remote branch name explicitly.
4. Publish the fix **without renaming the current local branch**: push HEAD to a **new remote branch** using exactly this refspec pattern (replace nothing except using the branch name below):
   git push -u origin HEAD:refs/heads/{$remoteBranch}
   This updates origin with a new branch named {$remoteBranch} while leaving your checked-out local branch name unchanged.
5. Open a pull request into {$baseBranch} from that remote branch, non-interactively, for example:
   gh pr create --base {$baseBranch} --head {$remoteBranch} --title "..." --body "..."
   Title and body should summarize the bug and the fix. If gh is unavailable, print the exact commands for a human to run.

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
