<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cursor Agent CLI — automatic repair on server errors
    |--------------------------------------------------------------------------
    |
    | When enabled, reportable 5xx-class errors enqueue a job that runs the
    | Cursor Agent CLI (see https://cursor.com/docs/cli/overview) with a fix
    | prompt. The agent must have git + GitHub CLI configured on the worker
    | host to open a pull request against the base branch (usually main).
    |
    | Set CURSOR_API_KEY (or run `agent login`) and queue workers on the machine
    | that can reach Cursor APIs and push to GitHub.
    |
    */

    'enabled' => (bool) env('CURSOR_AGENT_ON_500', true),

    'binary' => env('CURSOR_AGENT_BINARY', 'agent'),

    /*
    | Model passed to `agent --model` (e.g. gpt-5-mini for a faster tier).
    | Run `agent --list-models` to see IDs for your account.
    */
    'model' => env('CURSOR_AGENT_MODEL', 'composer-2-fast'),

    'base_branch' => env('CURSOR_AGENT_BASE_BRANCH', 'main'),

    'queue' => env('CURSOR_AGENT_QUEUE'),

    'rate_limit_seconds' => (int) env('CURSOR_AGENT_RATE_LIMIT_SECONDS', 3600),

    'trace_max_bytes' => (int) env('CURSOR_AGENT_TRACE_MAX_BYTES', 50_000),

];
