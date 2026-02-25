<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;

class PolicyPermissionService
{
    private const GUARD = 'web';

    /**
     * Discover permission names from policy files (can('Ability:Resource') pattern)
     * and return them grouped by resource, with DB permission records created.
     *
     * @return array<string, list<array{id: int, name: string, ability: string, ability_label: string}>>
     */
    public function getPermissionsGroupedByResource(): array
    {
        $permissionNames = $this->discoverPermissionNamesFromPolicies();
        $grouped = $this->groupByResource($permissionNames);
        $result = [];

        foreach ($grouped as $resource => $names) {
            $result[$resource] = [];
            foreach ($names as $name) {
                $permission = Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => self::GUARD],
                    ['name' => $name, 'guard_name' => self::GUARD]
                );
                $result[$resource][] = [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'ability' => $this->abilityFromName($name),
                    'ability_label' => $this->abilityLabel($name),
                ];
            }
        }

        ksort($result);

        return $result;
    }

    /**
     * @return list<string>
     */
    private function discoverPermissionNamesFromPolicies(): array
    {
        $policiesPath = app_path('Policies');
        if (! File::isDirectory($policiesPath)) {
            return [];
        }

        $names = [];
        $files = File::files($policiesPath);

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }
            $content = File::get($file->getPathname());
            // Match ->can('Ability:Resource') or ->can("Ability:Resource"), skip commented lines
            if (preg_match_all("/->can\s*\(\s*['\"]([^'\"]+)['\"]\s*\)/", $content, $matches)) {
                foreach ($matches[1] as $name) {
                    $name = trim($name);
                    if ($name !== '' && str_contains($name, ':')) {
                        $names[$name] = true;
                    }
                }
            }
        }

        return array_keys($names);
    }

    /**
     * @param  list<string>  $permissionNames
     * @return array<string, list<string>>
     */
    private function groupByResource(array $permissionNames): array
    {
        $grouped = [];
        foreach ($permissionNames as $name) {
            $parts = explode(':', $name, 2);
            if (count($parts) === 2) {
                $resource = trim($parts[1]);
                if (! isset($grouped[$resource])) {
                    $grouped[$resource] = [];
                }
                if (! in_array($name, $grouped[$resource], true)) {
                    $grouped[$resource][] = $name;
                }
            }
        }

        foreach ($grouped as $resource => $names) {
            usort($grouped[$resource], fn (string $a, string $b) => strcmp($a, $b));
        }

        return $grouped;
    }

    private function abilityFromName(string $permissionName): string
    {
        $parts = explode(':', $permissionName, 2);

        return trim($parts[0] ?? '');
    }

    private function abilityLabel(string $permissionName): string
    {
        $ability = $this->abilityFromName($permissionName);

        return $this->abilityLabelForAbility($ability);
    }

    private function abilityLabelForAbility(string $ability): string
    {
        $labels = [
            'ViewAny' => 'View any',
            'View' => 'View',
            'Create' => 'Create',
            'Update' => 'Update',
            'Delete' => 'Delete',
            'DeleteAny' => 'Delete any',
            'Restore' => 'Restore',
            'ForceDelete' => 'Force delete',
        ];

        return $labels[$ability] ?? preg_replace('/([a-z])([A-Z])/', '$1 $2', $ability);
    }
}
