<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class FixRolesSync extends Command
{
    protected $signature = 'roles:fix-sync {--dry-run : Preview changes without applying them}';

    protected $description = 'Clean up duplicate tenant-prefixed roles and restore missing roles for all users';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE — No changes will be made to the database.');
            $this->info('');
        }

        $this->info('=== Step 1/3: Cleaning duplicate tenant-prefixed roles ===');
        $this->cleanDuplicateRoles($isDryRun);

        $this->info('');
        $this->info('=== Step 2/3: Restoring missing roles for users ===');
        $this->restoreMissingRoles($isDryRun);

        $this->info('');
        $this->info('=== Step 3/3: Clearing permission cache ===');
        if (! $isDryRun) {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            $this->info('Permission cache cleared.');
        } else {
            $this->line('  [DRY RUN] Would clear permission cache.');
        }

        $this->info('');
        if ($isDryRun) {
            $this->warn('🔍 DRY RUN complete. Run without --dry-run to apply changes.');
        } else {
            $this->info('✅ Role sync fix completed successfully!');
        }

        return 0;
    }

    private function cleanDuplicateRoles(bool $isDryRun)
    {
        $callback = function () use ($isDryRun) {
            $roles = Role::all();
            $cleaned = 0;

            foreach ($roles as $role) {
                // Match patterns like: lab_9_doctor, lab_2_staff, lab_2_collection_center
                if (preg_match('/^lab_\d+_(staff|lab_admin|collection_center|branch_admin|doctor|agent)$/', $role->name, $matches)) {
                    $systemRoleName = $matches[1];
                    $this->warn("  Found duplicate role: {$role->name} → should be: {$systemRoleName}");

                    $users = User::role($role->name)->get();
                    $this->line("    Has {$users->count()} user(s)");

                    if (! $isDryRun) {
                        // Ensure system role exists
                        $systemRole = Role::firstOrCreate(['name' => $systemRoleName, 'guard_name' => 'web']);

                        foreach ($users as $user) {
                            if (! $user->hasRole($systemRoleName)) {
                                $user->assignRole($systemRoleName);
                            }
                            $user->removeRole($role->name);
                        }

                        $role->delete();
                    }

                    $cleaned++;
                }
            }

            if ($cleaned === 0) {
                $this->info('  No duplicate roles found. Database is clean.');
            } else {
                $prefix = $isDryRun ? '[DRY RUN] Would clean' : 'Cleaned';
                $this->info("  {$prefix} {$cleaned} duplicate role(s).");
            }
        };

        if ($isDryRun) {
            $callback();
        } else {
            DB::transaction($callback);
        }
    }

    private function restoreMissingRoles(bool $isDryRun)
    {
        // Only fetch users who have NO roles at all
        $usersWithoutRoles = User::whereDoesntHave('roles')->get();

        if ($usersWithoutRoles->isEmpty()) {
            $this->info('  All users have roles. Nothing to restore.');

            return;
        }

        $this->info("  Found {$usersWithoutRoles->count()} user(s) without any role.");

        // Ensure all system roles exist
        if (! $isDryRun) {
            Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'agent', 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'collection_center', 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        }

        $callback = function () use ($usersWithoutRoles, $isDryRun) {
            $fixedCount = 0;
            $skippedCount = 0;

            foreach ($usersWithoutRoles as $user) {
                $assignedRole = null;

                // 1. Check by Profile existence (most reliable — no guessing)
                if ($user->doctorProfile) {
                    $assignedRole = 'doctor';
                } elseif ($user->patientProfile) {
                    $assignedRole = 'patient';
                } elseif ($user->agentProfile) {
                    $assignedRole = 'agent';
                }
                // 2. Check Collection Center link
                elseif ($user->collection_center_id) {
                    $assignedRole = 'collection_center';
                }
                // 3. Skip users we can't confidently identify
                // (No profile, no CC link — we don't guess by name or fallback to staff)
                else {
                    $skippedCount++;
                    $this->warn("  ⚠ SKIPPED: User #{$user->id} ({$user->name}) — No profile found, needs manual assignment");

                    continue;
                }

                if ($assignedRole && ! $isDryRun) {
                    $user->assignRole($assignedRole);
                }

                $fixedCount++;
                $prefix = $isDryRun ? '[DRY RUN] Would assign' : 'Restored';
                $this->line("  {$prefix}: User #{$user->id} ({$user->name}) → {$assignedRole}");
            }

            $this->info("  Total fixed: {$fixedCount}");
            if ($skippedCount > 0) {
                $this->warn("  Total skipped (need manual check): {$skippedCount}");
            }
        };

        if ($isDryRun) {
            $callback();
        } else {
            DB::transaction($callback);
        }
    }
}
