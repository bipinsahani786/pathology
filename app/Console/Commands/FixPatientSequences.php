<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\PatientProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixPatientSequences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:patient-sequences';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates the new company_patient_number field for existing patient profiles per company.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting patient sequence migration...');

        $companies = Company::all();

        foreach ($companies as $company) {
            $this->info("Processing Company ID: {$company->id} - {$company->name}");

            DB::transaction(function () use ($company) {
                // Fetch profiles ordered by creation ID
                $profiles = PatientProfile::where('company_id', $company->id)
                    ->orderBy('id', 'asc')
                    ->get();

                $counter = 1;
                $updated = 0;

                foreach ($profiles as $profile) {
                    // Try to extract existing number from string if it exists to preserve current highest
                    if ($profile->patient_id_string) {
                        $parsed = (int) preg_replace('/[^0-9]/', '', substr($profile->patient_id_string, strrpos($profile->patient_id_string, '-')));
                        if ($parsed > 0) {
                            $counter = max($counter, $parsed);
                        }
                    }

                    if (is_null($profile->company_patient_number)) {
                        $profile->company_patient_number = $counter;
                        $profile->save();
                        $updated++;
                    }
                    
                    $counter++;
                }

                $this->info("  -> Updated $updated records. Sequence next up: $counter");
            });
        }

        $this->info('Patient sequences updated successfully.');
    }
}
