<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    Plan, Company, Branch, CollectionCenter, PaymentMode,
    GlobalTest, LabTest, Membership, Voucher, Configuration,
    User, PatientProfile, DoctorProfile, AgentProfile,
    Invoice, InvoiceItem, Payment
};
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Starting Demo Seeder...');

        // ============================================================
        // 1. PLANS
        // ============================================================
        $basicPlan = Plan::firstOrCreate(['name' => 'Basic'], [
            'price' => 0,
            'duration_in_days' => 365,
            'features' => ['tests' => 50, 'patients' => 200, 'branches' => 1, 'staff' => 2],
            'is_active' => true,
        ]);
        $proPlan = Plan::firstOrCreate(['name' => 'Professional'], [
            'price' => 4999,
            'duration_in_days' => 365,
            'features' => ['tests' => 500, 'patients' => 5000, 'branches' => 5, 'staff' => 10, 'reports' => true],
            'is_active' => true,
        ]);
        $enterprisePlan = Plan::firstOrCreate(['name' => 'Enterprise'], [
            'price' => 14999,
            'duration_in_days' => 365,
            'features' => ['tests' => -1, 'patients' => -1, 'branches' => -1, 'staff' => -1, 'reports' => true, 'api' => true, 'whatsapp' => true],
            'is_active' => true,
        ]);
        $this->command->info('✅ Plans created');

        // ============================================================
        // 2. DEMO COMPANY (Lab)
        // ============================================================
        $company = Company::firstOrCreate(['email' => 'info@sahanipathology.in'], [
            'name' => 'Sahani Pathology & Diagnostic Center',
            'phone' => '+91 9876543210',
            'address' => '123, Main Road, Kankarbagh, Patna - 800020, Bihar',
            'status' => 'active',
            'plan_id' => $proPlan->id,
            'trial_ends_at' => now()->addDays(30),
            'website' => 'https://www.sahanipathology.in',
            'gst_number' => '10AABCS1234F1Z5',
            'tagline' => 'Trusted Diagnostics Since 2010',
        ]);
        $this->command->info('✅ Company created: ' . $company->name);

        // ============================================================
        // 3. LAB ADMIN USER
        // ============================================================
        $labAdminRole = Role::firstOrCreate(['name' => 'lab_admin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $patientRole = Role::firstOrCreate(['name' => 'patient']);
        $doctorRole = Role::firstOrCreate(['name' => 'doctor']);
        $agentRole = Role::firstOrCreate(['name' => 'agent']);

        $labAdmin = User::firstOrCreate(['email' => 'lab@sahanipathology.in'], [
            'name' => 'Bipin Sahani',
            'password' => Hash::make('password123'),
            'phone' => '9876543210',
            'company_id' => $company->id,
            'is_active' => true,
        ]);
        if (!$labAdmin->hasRole('lab_admin')) {
            $labAdmin->assignRole($labAdminRole);
        }

        // Lab Staff
        $staff1 = User::firstOrCreate(['email' => 'rahul@sahanipathology.in'], [
            'name' => 'Rahul Kumar',
            'password' => Hash::make('password123'),
            'phone' => '9876543211',
            'company_id' => $company->id,
            'is_active' => true,
        ]);
        if (!$staff1->hasRole('staff')) {
            $staff1->assignRole($staffRole);
        }

        $this->command->info('✅ Lab Admin & Staff created');

        // ============================================================
        // 4. BRANCHES
        // ============================================================
        $mainBranch = Branch::firstOrCreate(['company_id' => $company->id, 'name' => 'Main Lab - Kankarbagh'], [
            'address' => '123, Main Road, Kankarbagh, Patna - 800020',
        ]);
        $branch2 = Branch::firstOrCreate(['company_id' => $company->id, 'name' => 'Boring Road Branch'], [
            'address' => '45, Boring Road, Patna - 800001',
        ]);
        $branch3 = Branch::firstOrCreate(['company_id' => $company->id, 'name' => 'Danapur Branch'], [
            'address' => '78, Station Road, Danapur, Patna - 801503',
        ]);

        // Assign branch to admin
        $labAdmin->update(['branch_id' => $mainBranch->id]);
        $staff1->update(['branch_id' => $mainBranch->id]);

        $this->command->info('✅ Branches created');

        // ============================================================
        // 5. COLLECTION CENTERS
        // ============================================================
        $mainCenter = CollectionCenter::firstOrCreate(['company_id' => $company->id, 'name' => 'Main Lab - Kankarbagh'], [
            'center_code' => 'CENTER-001',
            'address' => '123, Main Road, Kankarbagh, Patna - 800020',
            'is_main_lab' => true,
            'is_active' => true,
        ]);
        CollectionCenter::firstOrCreate(['company_id' => $company->id, 'name' => 'Boring Road Center'], [
            'center_code' => 'CENTER-002',
            'address' => '45, Boring Road, Patna',
            'is_main_lab' => false,
            'is_active' => true,
        ]);
        CollectionCenter::firstOrCreate(['company_id' => $company->id, 'name' => 'Rajiv Nagar Center'], [
            'center_code' => 'CENTER-003',
            'address' => '12, Rajiv Nagar, Patna',
            'is_main_lab' => false,
            'is_active' => true,
        ]);
        CollectionCenter::firstOrCreate(['company_id' => $company->id, 'name' => 'City Mall Kiosk'], [
            'center_code' => 'CENTER-004',
            'address' => 'Ground Floor, City Mall, Kankarbagh',
            'is_main_lab' => false,
            'is_active' => true,
        ]);
        $this->command->info('✅ Collection Centers created');

        // ============================================================
        // 6. PAYMENT MODES
        // ============================================================
        foreach (['Cash', 'UPI', 'Credit Card', 'Debit Card', 'Net Banking', 'Paytm', 'PhonePe'] as $mode) {
            PaymentMode::firstOrCreate(['company_id' => $company->id, 'name' => $mode], [
                'is_active' => true,
            ]);
        }
        $cashMode = PaymentMode::where('company_id', $company->id)->where('name', 'Cash')->first();
        $upiMode = PaymentMode::where('company_id', $company->id)->where('name', 'UPI')->first();
        $this->command->info('✅ Payment Modes created');

        // ============================================================
        // 7. GLOBAL TESTS (Master Catalog)
        // ============================================================
        $globalTests = [
            ['test_code' => 'CBC-001', 'name' => 'Complete Blood Count (CBC)', 'category' => 'Haematology', 'suggested_price' => 350, 'default_parameters' => [
                ['param' => 'Hemoglobin', 'unit' => 'g/dL', 'male_range' => '13.0-17.0', 'female_range' => '12.0-15.5'],
                ['param' => 'WBC Count', 'unit' => 'cells/µL', 'male_range' => '4500-11000', 'female_range' => '4500-11000'],
                ['param' => 'RBC Count', 'unit' => 'million/µL', 'male_range' => '4.5-5.5', 'female_range' => '4.0-5.0'],
                ['param' => 'Platelet Count', 'unit' => 'lac/µL', 'male_range' => '1.5-4.0', 'female_range' => '1.5-4.0'],
                ['param' => 'PCV/HCT', 'unit' => '%', 'male_range' => '38-50', 'female_range' => '36-44'],
                ['param' => 'MCV', 'unit' => 'fL', 'male_range' => '80-100', 'female_range' => '80-100'],
                ['param' => 'MCH', 'unit' => 'pg', 'male_range' => '27-33', 'female_range' => '27-33'],
                ['param' => 'MCHC', 'unit' => 'g/dL', 'male_range' => '32-36', 'female_range' => '32-36'],
            ]],
            ['test_code' => 'LFT-001', 'name' => 'Liver Function Test (LFT)', 'category' => 'Biochemistry', 'suggested_price' => 600, 'default_parameters' => [
                ['param' => 'SGPT (ALT)', 'unit' => 'U/L', 'male_range' => '7-56', 'female_range' => '7-45'],
                ['param' => 'SGOT (AST)', 'unit' => 'U/L', 'male_range' => '10-40', 'female_range' => '9-32'],
                ['param' => 'Total Bilirubin', 'unit' => 'mg/dL', 'male_range' => '0.1-1.2', 'female_range' => '0.1-1.2'],
                ['param' => 'Direct Bilirubin', 'unit' => 'mg/dL', 'male_range' => '0.0-0.3', 'female_range' => '0.0-0.3'],
                ['param' => 'Alkaline Phosphatase', 'unit' => 'U/L', 'male_range' => '44-147', 'female_range' => '44-147'],
                ['param' => 'Total Protein', 'unit' => 'g/dL', 'male_range' => '6.0-8.3', 'female_range' => '6.0-8.3'],
                ['param' => 'Albumin', 'unit' => 'g/dL', 'male_range' => '3.5-5.0', 'female_range' => '3.5-5.0'],
            ]],
            ['test_code' => 'KFT-001', 'name' => 'Kidney Function Test (KFT)', 'category' => 'Biochemistry', 'suggested_price' => 500, 'default_parameters' => [
                ['param' => 'Blood Urea', 'unit' => 'mg/dL', 'male_range' => '15-40', 'female_range' => '15-40'],
                ['param' => 'Serum Creatinine', 'unit' => 'mg/dL', 'male_range' => '0.7-1.3', 'female_range' => '0.6-1.1'],
                ['param' => 'Uric Acid', 'unit' => 'mg/dL', 'male_range' => '3.4-7.0', 'female_range' => '2.4-6.0'],
                ['param' => 'BUN', 'unit' => 'mg/dL', 'male_range' => '7-20', 'female_range' => '7-20'],
                ['param' => 'Sodium', 'unit' => 'mEq/L', 'male_range' => '136-145', 'female_range' => '136-145'],
                ['param' => 'Potassium', 'unit' => 'mEq/L', 'male_range' => '3.5-5.0', 'female_range' => '3.5-5.0'],
            ]],
            ['test_code' => 'TSH-001', 'name' => 'Thyroid Profile (TSH, T3, T4)', 'category' => 'Endocrinology', 'suggested_price' => 700, 'default_parameters' => [
                ['param' => 'TSH', 'unit' => 'µIU/mL', 'male_range' => '0.4-4.0', 'female_range' => '0.4-4.0'],
                ['param' => 'T3', 'unit' => 'ng/dL', 'male_range' => '80-200', 'female_range' => '80-200'],
                ['param' => 'T4', 'unit' => 'µg/dL', 'male_range' => '5.1-14.1', 'female_range' => '5.1-14.1'],
            ]],
            ['test_code' => 'LPD-001', 'name' => 'Lipid Profile', 'category' => 'Biochemistry', 'suggested_price' => 500, 'default_parameters' => [
                ['param' => 'Total Cholesterol', 'unit' => 'mg/dL', 'male_range' => '<200', 'female_range' => '<200'],
                ['param' => 'Triglycerides', 'unit' => 'mg/dL', 'male_range' => '<150', 'female_range' => '<150'],
                ['param' => 'HDL Cholesterol', 'unit' => 'mg/dL', 'male_range' => '>40', 'female_range' => '>50'],
                ['param' => 'LDL Cholesterol', 'unit' => 'mg/dL', 'male_range' => '<100', 'female_range' => '<100'],
                ['param' => 'VLDL', 'unit' => 'mg/dL', 'male_range' => '5-40', 'female_range' => '5-40'],
            ]],
            ['test_code' => 'BGL-001', 'name' => 'Blood Glucose Fasting', 'category' => 'Biochemistry', 'suggested_price' => 100, 'default_parameters' => [
                ['param' => 'Fasting Blood Sugar', 'unit' => 'mg/dL', 'male_range' => '70-100', 'female_range' => '70-100'],
            ]],
            ['test_code' => 'BGL-002', 'name' => 'Blood Glucose PP', 'category' => 'Biochemistry', 'suggested_price' => 100, 'default_parameters' => [
                ['param' => 'PP Blood Sugar', 'unit' => 'mg/dL', 'male_range' => '70-140', 'female_range' => '70-140'],
            ]],
            ['test_code' => 'HBA-001', 'name' => 'HbA1c (Glycosylated Hemoglobin)', 'category' => 'Biochemistry', 'suggested_price' => 500, 'default_parameters' => [
                ['param' => 'HbA1c', 'unit' => '%', 'male_range' => '<5.7', 'female_range' => '<5.7'],
            ]],
            ['test_code' => 'URI-001', 'name' => 'Urine Routine & Microscopy', 'category' => 'Clinical Pathology', 'suggested_price' => 200, 'default_parameters' => [
                ['param' => 'Color', 'unit' => '', 'male_range' => 'Pale Yellow', 'female_range' => 'Pale Yellow'],
                ['param' => 'Specific Gravity', 'unit' => '', 'male_range' => '1.005-1.030', 'female_range' => '1.005-1.030'],
                ['param' => 'pH', 'unit' => '', 'male_range' => '4.5-8.0', 'female_range' => '4.5-8.0'],
                ['param' => 'Protein', 'unit' => '', 'male_range' => 'Nil', 'female_range' => 'Nil'],
                ['param' => 'Sugar', 'unit' => '', 'male_range' => 'Nil', 'female_range' => 'Nil'],
                ['param' => 'Pus Cells', 'unit' => '/HPF', 'male_range' => '0-5', 'female_range' => '0-5'],
            ]],
            ['test_code' => 'ESR-001', 'name' => 'ESR (Erythrocyte Sedimentation Rate)', 'category' => 'Haematology', 'suggested_price' => 100, 'default_parameters' => [
                ['param' => 'ESR', 'unit' => 'mm/hr', 'male_range' => '0-15', 'female_range' => '0-20'],
            ]],
            ['test_code' => 'VIT-001', 'name' => 'Vitamin D (25-OH)', 'category' => 'Immunology', 'suggested_price' => 1200, 'default_parameters' => [
                ['param' => 'Vitamin D', 'unit' => 'ng/mL', 'male_range' => '30-100', 'female_range' => '30-100'],
            ]],
            ['test_code' => 'VIT-002', 'name' => 'Vitamin B12', 'category' => 'Immunology', 'suggested_price' => 800, 'default_parameters' => [
                ['param' => 'Vitamin B12', 'unit' => 'pg/mL', 'male_range' => '200-900', 'female_range' => '200-900'],
            ]],
            ['test_code' => 'WDL-001', 'name' => 'Widal Test', 'category' => 'Serology', 'suggested_price' => 250, 'default_parameters' => [
                ['param' => 'S. Typhi O', 'unit' => '', 'male_range' => '<1:80', 'female_range' => '<1:80'],
                ['param' => 'S. Typhi H', 'unit' => '', 'male_range' => '<1:80', 'female_range' => '<1:80'],
                ['param' => 'S. Para-Typhi AH', 'unit' => '', 'male_range' => '<1:80', 'female_range' => '<1:80'],
                ['param' => 'S. Para-Typhi BH', 'unit' => '', 'male_range' => '<1:80', 'female_range' => '<1:80'],
            ]],
            ['test_code' => 'CRP-001', 'name' => 'C-Reactive Protein (CRP)', 'category' => 'Immunology', 'suggested_price' => 500, 'default_parameters' => [
                ['param' => 'CRP', 'unit' => 'mg/L', 'male_range' => '<6', 'female_range' => '<6'],
            ]],
            ['test_code' => 'HIV-001', 'name' => 'HIV 1 & 2 (ELISA)', 'category' => 'Serology', 'suggested_price' => 500, 'default_parameters' => [
                ['param' => 'HIV 1 & 2 Antibody', 'unit' => '', 'male_range' => 'Non-Reactive', 'female_range' => 'Non-Reactive'],
            ]],
            ['test_code' => 'HBS-001', 'name' => 'HBsAg (Hepatitis B)', 'category' => 'Serology', 'suggested_price' => 350, 'default_parameters' => [
                ['param' => 'HBsAg', 'unit' => '', 'male_range' => 'Non-Reactive', 'female_range' => 'Non-Reactive'],
            ]],
            ['test_code' => 'DNG-001', 'name' => 'Dengue NS1 Antigen', 'category' => 'Serology', 'suggested_price' => 800, 'default_parameters' => [
                ['param' => 'Dengue NS1 Antigen', 'unit' => '', 'male_range' => 'Negative', 'female_range' => 'Negative'],
            ]],
            ['test_code' => 'MAL-001', 'name' => 'Malaria (MP) Test', 'category' => 'Parasitology', 'suggested_price' => 200, 'default_parameters' => [
                ['param' => 'Malaria Parasite', 'unit' => '', 'male_range' => 'Not Detected', 'female_range' => 'Not Detected'],
            ]],
            ['test_code' => 'PT-001', 'name' => 'Prothrombin Time (PT/INR)', 'category' => 'Haematology', 'suggested_price' => 400, 'default_parameters' => [
                ['param' => 'PT', 'unit' => 'seconds', 'male_range' => '11-13.5', 'female_range' => '11-13.5'],
                ['param' => 'INR', 'unit' => '', 'male_range' => '0.8-1.2', 'female_range' => '0.8-1.2'],
            ]],
            ['test_code' => 'STL-001', 'name' => 'Stool Routine & Microscopy', 'category' => 'Clinical Pathology', 'suggested_price' => 150, 'default_parameters' => [
                ['param' => 'Color', 'unit' => '', 'male_range' => 'Brown', 'female_range' => 'Brown'],
                ['param' => 'Consistency', 'unit' => '', 'male_range' => 'Formed', 'female_range' => 'Formed'],
                ['param' => 'Ova', 'unit' => '', 'male_range' => 'Not Found', 'female_range' => 'Not Found'],
                ['param' => 'Cyst', 'unit' => '', 'male_range' => 'Not Found', 'female_range' => 'Not Found'],
            ]],
        ];

        foreach ($globalTests as $gt) {
            GlobalTest::firstOrCreate(['test_code' => $gt['test_code']], $gt);
        }
        $this->command->info('✅ ' . count($globalTests) . ' Global Tests created');

        // ============================================================
        // 8. LAB TESTS (Company-specific, mapped from Global Tests)
        // ============================================================
        $deptMap = [
            'Haematology' => 'Haematology', 'Biochemistry' => 'Biochemistry',
            'Endocrinology' => 'Endocrinology', 'Clinical Pathology' => 'Clinical Pathology',
            'Immunology' => 'Immunology', 'Serology' => 'Serology',
            'Parasitology' => 'Parasitology',
        ];

        $createdLabTests = [];
        foreach (GlobalTest::all() as $gt) {
            $labTest = LabTest::firstOrCreate(['company_id' => $company->id, 'test_code' => $gt->test_code], [
                'global_test_id' => $gt->id,
                'name' => $gt->name,
                'department' => $deptMap[$gt->category] ?? $gt->category,
                'mrp' => $gt->suggested_price ?? 300,
                'b2b_price' => ($gt->suggested_price ?? 300) * 0.7,
                'sample_type' => in_array($gt->category, ['Clinical Pathology']) ? 'Urine/Stool' : 'Blood',
                'tat_hours' => $gt->category === 'Immunology' ? 24 : 6,
                'parameters' => $gt->default_parameters,
                'is_active' => true,
                'is_package' => false,
                'linked_test_ids' => null,
            ]);
            $createdLabTests[$gt->test_code] = $labTest;
        }

        // Packages
        $cbcId = $createdLabTests['CBC-001']->id ?? null;
        $lftId = $createdLabTests['LFT-001']->id ?? null;
        $kftId = $createdLabTests['KFT-001']->id ?? null;
        $tshId = $createdLabTests['TSH-001']->id ?? null;
        $lipidId = $createdLabTests['LPD-001']->id ?? null;
        $fbsId = $createdLabTests['BGL-001']->id ?? null;
        $hbaId = $createdLabTests['HBA-001']->id ?? null;
        $urineId = $createdLabTests['URI-001']->id ?? null;
        $vitDId = $createdLabTests['VIT-001']->id ?? null;
        $vitB12Id = $createdLabTests['VIT-002']->id ?? null;

        $packages = [
            ['test_code' => 'PKG-FULL', 'name' => 'Full Body Checkup', 'mrp' => 2499, 'linked' => array_filter([$cbcId, $lftId, $kftId, $tshId, $lipidId, $fbsId, $urineId])],
            ['test_code' => 'PKG-DM', 'name' => 'Diabetes Panel', 'mrp' => 999, 'linked' => array_filter([$fbsId, $hbaId, $kftId, $lipidId])],
            ['test_code' => 'PKG-FEVER', 'name' => 'Fever Panel', 'mrp' => 1299, 'linked' => array_filter([$cbcId, $createdLabTests['WDL-001']->id ?? null, $createdLabTests['MAL-001']->id ?? null, $createdLabTests['DNG-001']->id ?? null])],
            ['test_code' => 'PKG-VIT', 'name' => 'Vitamin Panel', 'mrp' => 1799, 'linked' => array_filter([$vitDId, $vitB12Id, $cbcId])],
            ['test_code' => 'PKG-LKC', 'name' => 'Liver & Kidney Combo', 'mrp' => 899, 'linked' => array_filter([$lftId, $kftId])],
        ];

        foreach ($packages as $pkg) {
            LabTest::firstOrCreate(['company_id' => $company->id, 'test_code' => $pkg['test_code']], [
                'name' => $pkg['name'],
                'department' => 'Packages',
                'mrp' => $pkg['mrp'],
                'b2b_price' => $pkg['mrp'] * 0.6,
                'sample_type' => 'Blood',
                'tat_hours' => 24,
                'parameters' => null,
                'is_active' => true,
                'is_package' => true,
                'linked_test_ids' => array_values($pkg['linked']),
            ]);
        }
        $this->command->info('✅ Lab Tests & Packages created');

        // ============================================================
        // 9. DOCTORS (with profiles)
        // ============================================================
        $doctorsData = [
            ['name' => 'Dr. Amit Sharma', 'phone' => '9800100001', 'spec' => 'General Medicine', 'clinic' => 'Sharma Clinic', 'comm' => 15],
            ['name' => 'Dr. Priya Singh', 'phone' => '9800100002', 'spec' => 'Cardiology', 'clinic' => 'Heart Care Center', 'comm' => 20],
            ['name' => 'Dr. Ravi Patel', 'phone' => '9800100003', 'spec' => 'Orthopedics', 'clinic' => 'Bone & Joint Clinic', 'comm' => 15],
            ['name' => 'Dr. Anjali Gupta', 'phone' => '9800100004', 'spec' => 'Gynecology', 'clinic' => 'Women Care Hospital', 'comm' => 18],
            ['name' => 'Dr. Suresh Yadav', 'phone' => '9800100005', 'spec' => 'Pediatrics', 'clinic' => 'Child Care Hospital', 'comm' => 12],
            ['name' => 'Dr. Meena Kumari', 'phone' => '9800100006', 'spec' => 'Dermatology', 'clinic' => 'Skin & Hair Clinic', 'comm' => 10],
            ['name' => 'Dr. Rajesh Verma', 'phone' => '9800100007', 'spec' => 'ENT', 'clinic' => 'ENT Specialist Center', 'comm' => 15],
            ['name' => 'Dr. Neha Agarwal', 'phone' => '9800100008', 'spec' => 'Ophthalmology', 'clinic' => 'Eye Care Hospital', 'comm' => 12],
        ];

        $doctorUsers = [];
        foreach ($doctorsData as $doc) {
            $user = User::firstOrCreate(['phone' => $doc['phone']], [
                'name' => $doc['name'],
                'email' => strtolower(str_replace([' ', 'Dr.'], ['', ''], $doc['name'])) . '@doctor.com',
                'password' => Hash::make('password123'),
                'company_id' => $company->id,
                'is_active' => true,
            ]);
            DoctorProfile::firstOrCreate(['user_id' => $user->id], [
                'company_id' => $company->id,
                'specialization' => $doc['spec'],
                'clinic_name' => $doc['clinic'],
                'commission_percentage' => $doc['comm'],
            ]);
            $doctorUsers[] = $user;
        }
        $this->command->info('✅ Doctors created');

        // ============================================================
        // 10. AGENTS
        // ============================================================
        $agentsData = [
            ['name' => 'Vikram Health Services', 'phone' => '9800200001', 'agency' => 'Vikram Health Services', 'comm' => 25],
            ['name' => 'MedConnect Agency', 'phone' => '9800200002', 'agency' => 'MedConnect Pvt Ltd', 'comm' => 20],
            ['name' => 'Patna Diagnostics Partner', 'phone' => '9800200003', 'agency' => 'Patna Diagnostics', 'comm' => 22],
            ['name' => 'HealthFirst Collection', 'phone' => '9800200004', 'agency' => 'HealthFirst India', 'comm' => 18],
        ];

        $agentUsers = [];
        foreach ($agentsData as $agt) {
            $user = User::firstOrCreate(['phone' => $agt['phone']], [
                'name' => $agt['name'],
                'email' => strtolower(str_replace(' ', '', $agt['agency'])) . '@agent.com',
                'password' => Hash::make('password123'),
                'company_id' => $company->id,
                'is_active' => true,
            ]);
            AgentProfile::firstOrCreate(['user_id' => $user->id], [
                'company_id' => $company->id,
                'agency_name' => $agt['agency'],
                'commission_percentage' => $agt['comm'],
            ]);
            $agentUsers[] = $user;
        }
        $this->command->info('✅ Agents created');

        // ============================================================
        // 11. PATIENTS
        // ============================================================
        $patientsData = [
            ['name' => 'Rajesh Kumar', 'phone' => '9800300001', 'age' => 45, 'gender' => 'Male', 'blood' => 'B+', 'addr' => 'Kankarbagh, Patna'],
            ['name' => 'Sunita Devi', 'phone' => '9800300002', 'age' => 38, 'gender' => 'Female', 'blood' => 'A+', 'addr' => 'Boring Road, Patna'],
            ['name' => 'Amit Sinha', 'phone' => '9800300003', 'age' => 52, 'gender' => 'Male', 'blood' => 'O+', 'addr' => 'Danapur, Patna'],
            ['name' => 'Pooja Kumari', 'phone' => '9800300004', 'age' => 28, 'gender' => 'Female', 'blood' => 'AB+', 'addr' => 'Rajiv Nagar, Patna'],
            ['name' => 'Manoj Prasad', 'phone' => '9800300005', 'age' => 60, 'gender' => 'Male', 'blood' => 'O-', 'addr' => 'Exhibition Road, Patna'],
            ['name' => 'Kavita Singh', 'phone' => '9800300006', 'age' => 33, 'gender' => 'Female', 'blood' => 'B-', 'addr' => 'Bailey Road, Patna'],
            ['name' => 'Sunil Mehta', 'phone' => '9800300007', 'age' => 72, 'gender' => 'Male', 'blood' => 'A-', 'addr' => 'Gandhi Maidan, Patna'],
            ['name' => 'Rina Gupta', 'phone' => '9800300008', 'age' => 41, 'gender' => 'Female', 'blood' => 'B+', 'addr' => 'Patliputra, Patna'],
            ['name' => 'Deepak Yadav', 'phone' => '9800300009', 'age' => 25, 'gender' => 'Male', 'blood' => 'O+', 'addr' => 'Saguna More, Patna'],
            ['name' => 'Anita Kumari', 'phone' => '9800300010', 'age' => 48, 'gender' => 'Female', 'blood' => 'A+', 'addr' => 'Ashiana, Patna'],
            ['name' => 'Ravi Shankar', 'phone' => '9800300011', 'age' => 55, 'gender' => 'Male', 'blood' => 'AB-', 'addr' => 'Phulwari Sharif, Patna'],
            ['name' => 'Meera Devi', 'phone' => '9800300012', 'age' => 35, 'gender' => 'Female', 'blood' => 'O+', 'addr' => 'Sampatchak, Patna'],
            ['name' => 'Arjun Thakur', 'phone' => '9800300013', 'age' => 30, 'gender' => 'Male', 'blood' => 'B+', 'addr' => 'Kumhrar, Patna'],
            ['name' => 'Sarita Kumari', 'phone' => '9800300014', 'age' => 22, 'gender' => 'Female', 'blood' => 'A+', 'addr' => 'Mithapur, Patna'],
            ['name' => 'Gopal Prasad', 'phone' => '9800300015', 'age' => 67, 'gender' => 'Male', 'blood' => 'O-', 'addr' => 'Khajpura, Patna'],
        ];

        $patientUsers = [];
        $patCounter = 1001;
        foreach ($patientsData as $pat) {
            $user = User::firstOrCreate(['phone' => $pat['phone']], [
                'name' => $pat['name'],
                'email' => strtolower(str_replace(' ', '', $pat['name'])) . '@patient.com',
                'password' => Hash::make('password123'),
                'company_id' => $company->id,
                'is_active' => true,
            ]);
            PatientProfile::firstOrCreate(['user_id' => $user->id], [
                'company_id' => $company->id,
                'patient_id_string' => 'PAT-' . $patCounter,
                'age' => $pat['age'],
                'age_type' => 'Years',
                'gender' => $pat['gender'],
                'blood_group' => $pat['blood'],
                'address' => $pat['addr'],
            ]);
            $patientUsers[] = $user;
            $patCounter++;
        }
        $this->command->info('✅ ' . count($patientsData) . ' Patients created');

        // ============================================================
        // 12. MEMBERSHIPS
        // ============================================================
        Membership::firstOrCreate(['company_id' => $company->id, 'name' => 'Silver'], [
            'price' => 499, 'discount_percentage' => 10, 'validity_days' => 180,
            'color_code' => '#C0C0C0', 'description' => '10% off on all tests for 6 months', 'is_active' => true,
        ]);
        Membership::firstOrCreate(['company_id' => $company->id, 'name' => 'Gold'], [
            'price' => 999, 'discount_percentage' => 20, 'validity_days' => 365,
            'color_code' => '#FFD700', 'description' => '20% off on all tests for 1 year', 'is_active' => true,
        ]);
        Membership::firstOrCreate(['company_id' => $company->id, 'name' => 'Platinum'], [
            'price' => 1999, 'discount_percentage' => 30, 'validity_days' => 365,
            'color_code' => '#E5E4E2', 'description' => '30% off on all tests for 1 year + priority reports', 'is_active' => true,
        ]);
        $this->command->info('✅ Memberships created');

        // ============================================================
        // 13. VOUCHERS
        // ============================================================
        Voucher::firstOrCreate(['company_id' => $company->id, 'code' => 'WELCOME10'], [
            'discount_type' => 'percentage', 'discount_value' => 10,
            'min_bill_amount' => 500, 'max_discount_amount' => 200,
            'valid_from' => now()->subDays(7), 'valid_until' => now()->addMonths(3),
            'usage_limit' => 100, 'used_count' => 0, 'is_active' => true,
        ]);
        Voucher::firstOrCreate(['company_id' => $company->id, 'code' => 'FLAT200'], [
            'discount_type' => 'flat', 'discount_value' => 200,
            'min_bill_amount' => 1000, 'max_discount_amount' => 200,
            'valid_from' => now(), 'valid_until' => now()->addMonth(),
            'usage_limit' => 50, 'used_count' => 0, 'is_active' => true,
        ]);
        Voucher::firstOrCreate(['company_id' => $company->id, 'code' => 'SUMMER25'], [
            'discount_type' => 'percentage', 'discount_value' => 25,
            'min_bill_amount' => 800, 'max_discount_amount' => 500,
            'valid_from' => now(), 'valid_until' => now()->addMonths(2),
            'usage_limit' => 200, 'used_count' => 0, 'is_active' => true,
        ]);
        $this->command->info('✅ Vouchers created');

        // ============================================================
        // 14. CONFIGURATIONS (Default Invoice Settings)
        // ============================================================
        $configs = [
            'invoice_prefix' => 'INV',
            'invoice_separator' => '-',
            'invoice_date_format' => 'ym',
            'invoice_counter_digits' => '4',
            'invoice_counter_reset' => 'monthly',
            'bill_template' => 'classic',
            'pdf_show_header' => '1',
            'pdf_show_footer' => '1',
        ];
        foreach ($configs as $key => $val) {
            Configuration::firstOrCreate(['company_id' => $company->id, 'config_key' => $key], [
                'config_value' => $val,
            ]);
        }
        $this->command->info('✅ Configurations created');

        // ============================================================
        // 15. SAMPLE INVOICES (Recent bills for listing demo)
        // ============================================================
        $labTestModels = LabTest::where('company_id', $company->id)->where('is_package', false)->take(10)->get();
        $collectionTypes = ['Center', 'Home Collection', 'Hospital'];

        for ($i = 0; $i < 20; $i++) {
            $patient = $patientUsers[array_rand($patientUsers)];
            $doctor = rand(0, 1) ? $doctorUsers[array_rand($doctorUsers)] : null;
            $testCount = rand(1, 4);
            $selectedTests = $labTestModels->random(min($testCount, $labTestModels->count()));
            $subtotal = $selectedTests->sum('mrp');
            $discount = rand(0, 3) === 0 ? round($subtotal * 0.1, 2) : 0;
            $total = $subtotal - $discount;
            $isPaid = rand(0, 3) > 0; // 75% chance paid
            $paidAmount = $isPaid ? $total : round($total * rand(3, 8) / 10, 2);
            $due = round($total - $paidAmount, 2);

            $invoiceDate = now()->subDays(rand(0, 30))->subHours(rand(0, 12));
            $invoiceNumber = 'INV-' . $invoiceDate->format('ym') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $barcode = 'INV' . $invoiceDate->format('ymd') . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'company_id' => $company->id,
                'collection_center_id' => $mainCenter->id,
                'branch_id' => $mainBranch->id,
                'patient_id' => $patient->id,
                'created_by' => $labAdmin->id,
                'referred_by_doctor_id' => $doctor?->id,
                'referred_by_agent_id' => null,
                'invoice_number' => $invoiceNumber,
                'barcode' => $barcode,
                'invoice_date' => $invoiceDate,
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'membership_discount_amount' => 0,
                'voucher_discount_amount' => 0,
                'total_amount' => $total,
                'paid_amount' => $paidAmount,
                'due_amount' => max($due, 0),
                'payment_status' => $due <= 0 ? 'Paid' : ($paidAmount > 0 ? 'Partial' : 'Unpaid'),
                'status' => 'Pending',
                'collection_type' => $collectionTypes[array_rand($collectionTypes)],
                'expected_report_time' => $invoiceDate->copy()->addHours(24),
                'doctor_commission_amount' => 0,
                'agent_commission_amount' => 0,
            ]);

            // Invoice Items
            foreach ($selectedTests as $test) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'lab_test_id' => $test->id,
                    'test_name' => $test->name,
                    'mrp' => $test->mrp,
                    'is_package' => $test->is_package,
                ]);
            }

            // Payment record
            if ($paidAmount > 0 && $cashMode) {
                Payment::create([
                    'company_id' => $company->id,
                    'invoice_id' => $invoice->id,
                    'patient_id' => $patient->id,
                    'collected_by' => $labAdmin->id,
                    'payment_mode_id' => rand(0, 1) ? $cashMode->id : ($upiMode->id ?? $cashMode->id),
                    'amount' => $paidAmount,
                    'transaction_id' => rand(0, 1) ? 'TXN' . strtoupper(substr(md5(rand()), 0, 8)) : null,
                ]);
            }
        }
        $this->command->info('✅ 20 Sample Invoices created');

        // ============================================================
        // DONE!
        // ============================================================
        $this->command->newLine();
        $this->command->info('🎉 Demo data seeded successfully!');
        $this->command->info('   Lab Admin Login: lab@sahanipathology.in / password123');
        $this->command->info('   Staff Login: rahul@sahanipathology.in / password123');
        $this->command->newLine();
    }
}
