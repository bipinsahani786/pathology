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

                $updated = 0;
                $highestId = 0;

                // Load config defaults per company
                $pPrefix = \App\Models\Configuration::getFor('patient_id_prefix', 'PAT', $company->id);
                $pDigits = (int) \App\Models\Configuration::getFor('patient_id_digits', 4, $company->id);

                foreach ($profiles as $profile) {
                    // CRITICAL FIX: To match the printed reports given to the first 1000 patients,
                    // we MUST set the company_patient_number equal to their global users.id
                    // because the old code was printing `User::id` on their invoices!
                    $targetNumber = $profile->user_id;
                    $highestId = max($highestId, $targetNumber);

                    if ($profile->company_patient_number !== $targetNumber) {
                        $profile->company_patient_number = $targetNumber;
                    }

                    // Force regenerate patient_id_string to match what was printed on the report
                    $expectedString = $pPrefix . str_pad($targetNumber, $pDigits, '0', STR_PAD_LEFT);
                    
                    if ($profile->patient_id_string !== $expectedString || $profile->isDirty('company_patient_number')) {
                        $profile->patient_id_string = $expectedString;
                        $profile->save();
                        $updated++;
                    }
                }
                
                $nextSequence = $highestId + 1;

                $this->info("  -> Updated $updated records. Sequence next up: $nextSequence");
            });
        }

        $this->info('Patient sequences updated successfully.');
    }
}
