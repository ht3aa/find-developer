<?php

namespace App\Services;

use App\Models\CompanyJob;
use App\Models\CompanyJobApplication;
use App\Models\Developer;
use App\Models\User;
use Illuminate\Support\Str;
use RuntimeException;

class CompanyJobGiteaProvisioner
{
    public function __construct(
        protected GiteaService $gitea,
    ) {}

    /**
     * Ensure the job owner has a Gitea account and a private repository for this post.
     */
    public function provisionRepositoryForApprovedJob(CompanyJob $job): void
    {
        if ($job->gitea_provisioned_at !== null) {
            return;
        }

        $user = $job->user;
        if ($user === null) {
            throw new RuntimeException('Remote work post has no owner user.');
        }

        $this->ensureUserHasGiteaAccount($user);

        $user->refresh();

        $login = (string) $user->gitea_username;
        if ($login === '') {
            throw new RuntimeException('Could not determine Gitea username for the post owner.');
        }

        $repoName = $this->repositoryNameForJob($job);

        $this->gitea->createRepositoryForUser(
            $login,
            $repoName,
            $job->title,
            true,
        );

        $job->forceFill([
            'gitea_owner' => $login,
            'gitea_repo_name' => $repoName,
            'gitea_provisioned_at' => now(),
        ])->save();
    }

    /**
     * Grant an accepted developer access to the job's private repository.
     */
    public function addDeveloperToJobRepository(CompanyJob $job, Developer $developer): void
    {
        $owner = $job->gitea_owner;
        $repo = $job->gitea_repo_name;

        if ($owner === null || $repo === null || $job->gitea_provisioned_at === null) {
            throw new RuntimeException('Remote work post has no Gitea repository yet.');
        }

        $user = $developer->user;
        if ($user === null) {
            throw new RuntimeException('Developer has no linked user account.');
        }

        $this->ensureUserHasGiteaAccount($user);

        $user->refresh();

        $collaboratorLogin = (string) $user->gitea_username;
        if ($collaboratorLogin === '') {
            throw new RuntimeException('Could not determine Gitea username for the developer.');
        }

        $this->gitea->addCollaborator($owner, $repo, $collaboratorLogin, 'write');
    }

    public function ensureUserHasGiteaAccount(User $user): void
    {
        if ($user->gitea_username !== null && $user->gitea_username !== '') {
            return;
        }

        $username = $this->gitea->suggestedUsernameFromEmail($user->email);
        $password = (string) Str::uuid();

        try {
            $response = $this->gitea->createUser(
                username: $username,
                email: $user->email,
                password: $password,
                fullName: $user->name,
                mustChangePassword: true,
            );
        } catch (RuntimeException $e) {
            $login = $this->gitea->findLoginByEmail($user->email);
            if ($login !== null) {
                $user->forceFill(['gitea_username' => $login])->save();

                return;
            }

            throw $e;
        }

        $login = (string) ($response['login'] ?? $response['username'] ?? '');
        if ($login === '') {
            throw new RuntimeException('Gitea did not return a login for the new user.');
        }

        $user->forceFill(['gitea_username' => $login])->save();
    }

    /**
     * Used when accepting an application (same as addDeveloperToJobRepository with application model).
     */
    public function addAcceptedApplication(CompanyJobApplication $application): void
    {
        $job = $application->companyJob;
        $developer = $application->developer;

        $this->addDeveloperToJobRepository($job, $developer);
    }

    /**
     * Gitea repository name matches the job's public slug, max 100 chars per API limits.
     */
    private function repositoryNameForJob(CompanyJob $job): string
    {
        $slug = trim((string) $job->slug);

        if ($slug === '') {
            return 'remote-work-'.$job->id;
        }

        return mb_substr($slug, 0, 100);
    }
}
