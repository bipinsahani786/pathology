<?php

namespace App\Console\Commands;

use App\Models\GlobalTest;
use App\Models\LabTest;
use App\Models\Department;
use Illuminate\Console\Command;

class SyncCbcParameters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cbc:sync-parameters {--company= : The ID of the company to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates CBC test parameters in global_tests and propagates them to a specific or all company lab_tests.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companyId = $this->option('company');
        $this->info('Starting CBC parameters sync...');

        $haematologyTests = require database_path('seeders/data/GlobalTests_Haematology.php');
        
        $cbcData = null;
        foreach ($haematologyTests as $test) {
            if (($test['test_code'] ?? '') === 'CBC') {
                $cbcData = $test;
                break;
            }
        }

        if (!$cbcData) {
            $this->error('CBC test data not found in GlobalTests_Haematology seeder!');
            return Command::FAILURE;
        }

        // Find department
        $department = Department::where('name', $cbcData['category'] ?? 'Other')
            ->where('is_system', true)
            ->first();

        // 1. Update GlobalTest CBC
        $globalTest = GlobalTest::updateOrCreate(
            ['test_code' => 'CBC'],
            [
                'name' => $cbcData['name'],
                'category' => $cbcData['category'] ?? 'Other',
                'department_id' => $department?->id,
                'description' => $cbcData['description'] ?? null,
                'interpretation' => $cbcData['interpretation'] ?? null,
                'mrp' => $cbcData['suggested_price'] ?? 0,
                'method' => $cbcData['method'] ?? null,
                'sample_type' => $cbcData['sample_type'] ?? null,
                'tat_hours' => $cbcData['tat_hours'] ?? 24,
                'default_parameters' => $cbcData['default_parameters'] ?? [],
                'is_active' => true,
            ]
        );

        $this->info('✓ Updated CBC in global_tests table.');

        // 2. Update existing LabTest CBC records
        $query = LabTest::where('test_code', 'CBC');
        if ($companyId) {
            $query->where('company_id', $companyId);
            $this->info("Filtering update for Company ID: {$companyId}");
        }
        $labTests = $query->get();
        $updatedCount = 0;

        foreach ($labTests as $labTest) {
            $labTest->update([
                'parameters' => $globalTest->default_parameters,
            ]);
            $updatedCount++;
        }

        $this->info("✓ Propagated updated parameters to {$updatedCount} company-specific lab_tests.");
        $this->info('CBC parameters sync completed successfully!');

        return Command::SUCCESS;
    }
}
