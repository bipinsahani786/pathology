<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Branch;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Plan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CompanyRegistrationService
{
    /**
     * Handles the complete onboarding process for a new lab (Tenant)
     * Wraps everything in a DB transaction to ensure data integrity
     */
    public function registerNewLab(array $data)
    {
        return DB::transaction(function () use ($data) {
            
            // 1. Fetch the default Free Plan from the database
            $freePlan = Plan::where('price', 0)->where('is_active', true)->first();
            
            // Fallback to 15 days if no free plan is explicitly defined in the DB
            $trialDays = $freePlan ? $freePlan->duration_in_days : 15; 

            // 2. Create the Company (Tenant)
            $company = Company::create([
                'name' => $data['lab_name'],
                'email' => $data['email'], 
                'phone' => $data['phone'],
                'status' => 'active',
                'plan_id' => $freePlan ? $freePlan->id : null, 
                'trial_ends_at' => Carbon::now()->addDays($trialDays), 
            ]);

            // 3. Create the Main/Default Branch for this Company
            $branch = Branch::create([
                'company_id' => $company->id,
                'name' => 'Main Lab',
                'type' => 'main_lab',
                'contact_number' => $data['phone'],
                'is_active' => true,
            ]);

            // 4. Create the User (Lab Owner/Admin)
            $user = User::create([
                'company_id' => $company->id,
                'branch_id' => $branch->id,
                'name' => $data['owner_name'],
                'email' => $data['email'], 
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'is_active' => true,
            ]);

            $user->assignRole('lab_admin');

            // 5. Save extra User Details
            UserDetail::create([
                'user_id' => $user->id,
                'phone' => $data['phone'],
            ]);

            return $user;
        });
    }
}