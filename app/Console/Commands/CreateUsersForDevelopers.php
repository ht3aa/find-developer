<?php

namespace App\Console\Commands;

use App\Enums\UserType;
use App\Helpers\DeveloperMessageHelper;
use App\Models\Developer;
use App\Models\User;
use App\Notifications\MailtrapNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CreateUsersForDevelopers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'developers:create-users
                            {--dry-run : List developers that would get a user without creating them}
                            {--role= : Role name to assign (default: first available or "developer")}
                            {--number= : Maximum number of developers to process (default: all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user account for each developer that does not have one';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $roleName = $this->option('role');
        $number = $this->option('number');

        $query = Developer::withoutGlobalScopes()
            ->whereNull('user_id')
            ->orderBy('id');

        if ($number !== null && $number !== '') {
            $limit = (int) $number;
            if ($limit > 0) {
                $query->limit($limit);
            }
        }

        $developers = $query->get();

        if ($developers->isEmpty()) {
            $this->info('No developers without a user account found.');

            return self::SUCCESS;
        }

        $this->info("Found {$developers->count()} developer(s) without a user account.");

        $role = $this->resolveRole($roleName);
        if (! $role && ! $dryRun) {
            $this->warn('No role found. Users will be created without a role. Use --role= to specify a role name.');
        }

        $created = 0;
        $skipped = 0;

        foreach ($developers as $developer) {
            if (empty($developer->email)) {
                $this->warn("  Skipped: Developer #{$developer->id} ({$developer->name}) has no email.");
                $skipped++;

                continue;
            }

            if (User::where('email', $developer->email)->exists()) {
                $this->warn("  Skipped: Developer #{$developer->id} ({$developer->name}) — email already in use: {$developer->email}");
                $skipped++;

                continue;
            }

            if ($dryRun) {
                $this->line("  Would create user for: #{$developer->id} {$developer->name} ({$developer->email})");
                $created++;

                continue;
            }

            $password = Str::uuid()->toString();
            $user = User::create([
                'name' => $developer->name,
                'email' => $developer->email,
                'password' => $password,
                'linkedin_url' => $developer->linkedin_url,
                'user_type' => UserType::DEVELOPER,
                'can_access_admin_panel' => true,
            ]);

            if ($role) {
                $user->assignRole($role);
            }

            $developer->update(['user_id' => $user->id]);

            try {
                $developer->notify(new MailtrapNotification(
                    subject: 'User Credentials Created',
                    message: DeveloperMessageHelper::userCredentialsCreatedMessage($developer->name, $developer->email, $password),
                    category: 'User Credentials'
                ));
            } catch (\Throwable $e) {
                $this->warn("  Credentials email failed for {$developer->email}: {$e->getMessage()}");
            }

            $this->line("  Created user for: #{$developer->id} {$developer->name} ({$developer->email})");
            $created++;
        }

        if ($dryRun) {
            $this->newLine();
            $this->info("[Dry run] Would create {$created} user(s). Run without --dry-run to create them.");
        } else {
            $this->newLine();
            $this->info("Created {$created} user(s). Skipped {$skipped}.");
        }

        return self::SUCCESS;
    }

    /**
     * Resolve the role by name or return a default (first "developer" role or first role).
     */
    private function resolveRole(?string $name): ?Role
    {
        if ($name !== null && $name !== '') {
            return Role::where('name', $name)->first();
        }

        return Role::where('name', 'developer')->first()
            ?? Role::where('name', 'like', '%developer%')->first()
            ?? Role::query()->first();
    }
}
