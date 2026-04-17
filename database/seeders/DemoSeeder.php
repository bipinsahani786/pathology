<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    Plan, Company, Branch, CollectionCenter, PaymentMode,
    GlobalTest, LabTest, Membership, Voucher, Configuration,
    User, PatientProfile, DoctorProfile, AgentProfile,
    Invoice, InvoiceItem, Payment, TestReport, ReportResult
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
        $basicPlan = Plan::updateOrCreate(['name' => 'Basic'], [
            'price' => 0,
            'duration_in_days' => 365,
            'features' => [
                'tests' => 50, 'patients' => 200, 'branches' => 1, 'staff' => 2,
                'inventory' => false, 'custom_invoice' => false, 'collection_centers' => 1,
                'doctors' => 5, 'agents' => 0, 'reports' => true
            ],
            'is_active' => true,
        ]);
        $proPlan = Plan::updateOrCreate(['name' => 'Professional'], [
            'price' => 4999,
            'duration_in_days' => 365,
            'features' => [
                'tests' => 500, 'patients' => 5000, 'branches' => 5, 'staff' => 10,
                'inventory' => true, 'custom_invoice' => true, 'collection_centers' => 5,
                'doctors' => 50, 'agents' => 10, 'reports' => true
            ],
            'is_active' => true,
        ]);
        $enterprisePlan = Plan::updateOrCreate(['name' => 'Enterprise'], [
            'price' => 14999,
            'duration_in_days' => 365,
            'features' => [
                'tests' => -1, 'patients' => -1, 'branches' => -1, 'staff' => -1,
                'inventory' => true, 'custom_invoice' => true, 'collection_centers' => -1,
                'doctors' => -1, 'agents' => -1, 'reports' => true, 'api' => true, 'whatsapp' => true
            ],
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

        // Create a Branch Admin for Boring Road
        $branchAdminRole = Role::firstOrCreate(['name' => 'branch_admin']);
        $boringAdmin = User::firstOrCreate(['email' => 'boring.admin@sahanipathology.in'], [
            'name' => 'Amit Sharma (Boring Road)',
            'password' => Hash::make('password123'),
            'phone' => '9876543215',
            'company_id' => $company->id,
            'branch_id' => $branch2->id,
            'is_active' => true,
        ]);
        if (!$boringAdmin->hasRole('branch_admin')) {
            $boringAdmin->assignRole($branchAdminRole);
        }

        $this->command->info('✅ Branches & Branch Admin created');

        // ============================================================
        // 5. COLLECTION CENTERS
        // ============================================================
        $mainCenter = CollectionCenter::firstOrCreate(['company_id' => $company->id, 'name' => 'Main Lab - Kankarbagh'], [
            'center_code' => 'CENTER-001',
            'address' => '123, Main Road, Kankarbagh, Patna - 800020',
            'is_main_lab' => true,
            'branch_id' => $mainBranch->id,
            'is_active' => true,
        ]);
        $boringCenter = CollectionCenter::firstOrCreate(['company_id' => $company->id, 'name' => 'Boring Road Center'], [
            'center_code' => 'CENTER-002',
            'address' => '45, Boring Road, Patna',
            'is_main_lab' => false,
            'branch_id' => $branch2->id,
            'is_active' => true,
        ]);
        $rajivCenter = CollectionCenter::firstOrCreate(['company_id' => $company->id, 'name' => 'Rajiv Nagar Center'], [
            'center_code' => 'CENTER-003',
            'address' => '12, Rajiv Nagar, Patna',
            'is_main_lab' => false,
            'branch_id' => $branch2->id,
            'is_active' => true,
        ]);
        CollectionCenter::firstOrCreate(['company_id' => $company->id, 'name' => 'City Mall Kiosk'], [
            'center_code' => 'CENTER-004',
            'address' => 'Ground Floor, City Mall, Kankarbagh',
            'is_main_lab' => false,
            'branch_id' => $mainBranch->id,
            'is_active' => true,
        ]);

        // Create Collection Center Users
        $ccRole = Role::firstOrCreate(['name' => 'collection_center']);
        $ccUser1 = User::firstOrCreate(['email' => 'boring@center.com'], [
            'name' => 'Boring Road CC Admin',
            'password' => Hash::make('password123'),
            'phone' => '9988776655',
            'company_id' => $company->id,
            'branch_id' => $branch2->id,
            'collection_center_id' => $boringCenter->id,
            'is_active' => true,
        ]);
        if (!$ccUser1->hasRole('collection_center')) $ccUser1->assignRole($ccRole);

        $ccUser2 = User::firstOrCreate(['email' => 'rajiv@center.com'], [
            'name' => 'Rajiv Nagar CC Admin',
            'password' => Hash::make('password123'),
            'phone' => '9988776654',
            'company_id' => $company->id,
            'branch_id' => $branch2->id,
            'collection_center_id' => $rajivCenter->id,
            'is_active' => true,
        ]);
        if (!$ccUser2->hasRole('collection_center')) $ccUser2->assignRole($ccRole);

        $this->command->info('✅ Collection Centers & CC Users created');

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
        $this->call(GlobalTestSeeder::class);
        $this->command->info('✅ Global Master Tests seeded');

        // ============================================================
        // 8. LAB TESTS (Company-specific, mapped from Global Tests)
        // ============================================================

        $createdLabTests = [];
        foreach (GlobalTest::all() as $gt) {
            $labTest = LabTest::updateOrCreate(['company_id' => $company->id, 'test_code' => $gt->test_code], [
                'global_test_id' => $gt->id,
                'name' => $gt->name,
                'department_id' => $gt->department_id,
                'mrp' => $gt->mrp ?? 300,
                'b2b_price' => ($gt->mrp ?? 300) * 0.7,
                'sample_type' => $gt->sample_type ?? (in_array($gt->category, ['Clinical Pathology']) ? 'Urine/Stool' : 'Blood'),
                'tat_hours' => $gt->tat_hours ?? 24,
                'parameters' => $gt->default_parameters,
                'description' => $gt->description,
                'interpretation' => $gt->interpretation,
                'method' => $gt->method,
                'is_active' => true,
                'is_package' => false,
            ]);
            $createdLabTests[$gt->test_code] = $labTest;
        }

        // Packages
        $cbcId = $createdLabTests['CBC']->id ?? null;
        $lftId = $createdLabTests['LFT']->id ?? null;
        $kftId = $createdLabTests['KFT']->id ?? null;
        $tshId = $createdLabTests['TFT']->id ?? null;
        $lipidId = $createdLabTests['LIPID']->id ?? null;
        $fbsId = $createdLabTests['BSF']->id ?? null;
        $hbaId = $createdLabTests['HBA1C']->id ?? null;
        $urineId = $createdLabTests['URE']->id ?? null;
        $vitDId = $createdLabTests['VITD']->id ?? null;
        $vitB12Id = $createdLabTests['VITB12']->id ?? null;

        $packages = [
            ['test_code' => 'PKG-FULL', 'name' => 'Full Body Checkup', 'mrp' => 2499, 'linked' => array_filter([$cbcId, $lftId, $kftId, $tshId, $lipidId, $fbsId, $urineId])],
            ['test_code' => 'PKG-DM', 'name' => 'Diabetes Panel', 'mrp' => 999, 'linked' => array_filter([$fbsId, $hbaId, $kftId, $lipidId])],
            ['test_code' => 'PKG-FEVER', 'name' => 'Fever Panel', 'mrp' => 1299, 'linked' => array_filter([$cbcId, $createdLabTests['WIDAL']->id ?? null, $createdLabTests['MALARIA']->id ?? null, $createdLabTests['DENGUE']->id ?? null])],
            ['test_code' => 'PKG-VIT', 'name' => 'Vitamin Panel', 'mrp' => 1799, 'linked' => array_filter([$vitDId, $vitB12Id, $cbcId])],
            ['test_code' => 'PKG-LKC', 'name' => 'Liver & Kidney Combo', 'mrp' => 899, 'linked' => array_filter([$lftId, $kftId])],
        ];

        $otherDeptId = \App\Models\Department::where('name', 'Other')->where('is_system', true)->first()?->id;

        foreach ($packages as $pkg) {
            LabTest::firstOrCreate(['company_id' => $company->id, 'test_code' => $pkg['test_code']], [
                'name' => $pkg['name'],
                'department_id' => $otherDeptId,
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
                'branch_id' => (rand(0, 1) ? $mainBranch->id : $branch2->id),
                'is_active' => true,
            ]);
            DoctorProfile::firstOrCreate(['user_id' => $user->id], [
                'company_id' => $company->id,
                'specialization' => $doc['spec'],
                'clinic_name' => $doc['clinic'],
                'commission_percentage' => $doc['comm'],
            ]);
            if (!$user->hasRole('doctor')) {
                $user->assignRole($doctorRole);
            }
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
                'branch_id' => (rand(0, 1) ? $mainBranch->id : $branch3->id),
                'is_active' => true,
            ]);
            AgentProfile::firstOrCreate(['user_id' => $user->id], [
                'company_id' => $company->id,
                'agency_name' => $agt['agency'],
                'commission_percentage' => $agt['comm'],
            ]);
            if (!$user->hasRole('agent')) {
                $user->assignRole($agentRole);
            }
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
                'branch_id' => (rand(0, 2) === 0 ? $mainBranch->id : (rand(0, 1) ? $branch2->id : $branch3->id)),
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

            $selectedBranch = rand(0, 2) === 0 ? $mainBranch : (rand(0, 1) ? $branch2 : $branch3);
            $selectedCC = $selectedBranch->id === $mainBranch->id ? $mainCenter : (rand(0, 1) ? $boringCenter : $rajivCenter);
            $totalB2b = $selectedTests->sum('b2b_price');
            $ccProfit = max(0, $total - $totalB2b);

            $invoice = Invoice::updateOrCreate(
                ['company_id' => $company->id, 'invoice_number' => $invoiceNumber],
                [
                    'collection_center_id' => $selectedCC->id,
                'branch_id' => $selectedBranch->id,
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
                'total_b2b_amount' => $totalB2b,
                'cc_profit_amount' => $ccProfit,
                'paid_amount' => $paidAmount,
                'due_amount' => max($due, 0),
                'payment_status' => $due <= 0 ? 'Paid' : ($paidAmount > 0 ? 'Partial' : 'Unpaid'),
                'status' => 'Pending',
                'sample_status' => ['Pending', 'Collected', 'Dispatched', 'Received', 'Ready'][rand(0, 4)],
                'collection_type' => $collectionTypes[array_rand($collectionTypes)],
                'expected_report_time' => $invoiceDate->copy()->addHours(24),
                'doctor_commission_amount' => 0,
                'agent_commission_amount' => 0,
            ]);

            // Invoice Items
            foreach ($selectedTests as $test) {
                $isCompleted = rand(0, 1); // 50% chance the test is done
                $item = InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'lab_test_id' => $test->id,
                    'test_name' => $test->name,
                    'mrp' => $test->mrp,
                    'is_package' => $test->is_package,
                    'status' => $isCompleted ? 'Completed' : 'Pending',
                ]);

                if ($isCompleted) {
                    // Create a TestReport if not exists
                    $testReport = TestReport::firstOrCreate(
                        ['invoice_id' => $invoice->id],
                        [
                            'company_id' => $company->id,
                            'patient_id' => $patient->id,
                            'status' => 'Approved',
                            'approved_by' => $labAdmin->id,
                            'approved_at' => now(),
                        ]
                    );

                    // Create results based on test parameters
                    $params = is_string($test->parameters) ? json_decode($test->parameters, true) : $test->parameters;
                    if (is_array($params)) {
                        foreach ($params as $param) {
                            ReportResult::create([
                                'test_report_id' => $testReport->id,
                                'invoice_item_id' => $item->id,
                                'lab_test_id' => $test->id,
                                'parameter_name' => $param['name'] ?? 'Result',
                                'short_code' => $param['short_code'] ?? null,
                                'result_value' => $param['input_type'] === 'numeric' ? (string)rand(10, 100) : 'Normal',
                                'unit' => $param['unit'] ?? '',
                                'reference_range' => $param['general_range'] ?? '',
                                'status' => 'Normal',
                            ]);
                        }
                    }
                }
            }

            // Payment record
            if ($paidAmount > 0 && $cashMode) {
                Payment::create([
                    'company_id' => $company->id,
                    'invoice_id' => $invoice->id,
                    'patient_id' => $patient->id,
                    'collected_by' => $selectedBranch->id === $mainBranch->id ? $labAdmin->id : $boringAdmin->id,
                    'payment_mode_id' => rand(0, 1) ? $cashMode->id : ($upiMode->id ?? $cashMode->id),
                    'amount' => $paidAmount,
                    'transaction_id' => rand(0, 1) ? 'TXN' . strtoupper(substr(md5(rand()), 0, 8)) : null,
                ]);
            }
        }
        $this->command->info('✅ 20 Sample Invoices created');
 
        // ============================================================
        // 16. SAMPLE SETTLEMENTS
        // ============================================================
        $pendingSettlement = \App\Models\Settlement::create([
            'company_id' => $company->id,
            'branch_id' => $branch2->id,
            'user_id' => $ccUser1->id,
            'collection_center_id' => $boringCenter->id,
            'amount' => 1500,
            'payment_date' => now()->subDays(1),
            'payment_mode' => 'UPI',
            'reference_no' => 'UPI123456789',
            'type' => 'CollectionCenter',
            'status' => 'Pending',
            'notes' => 'Bulk payment for last week'
        ]);

        $approvedSettlement = \App\Models\Settlement::create([
            'company_id' => $company->id,
            'branch_id' => $branch2->id,
            'user_id' => $ccUser2->id,
            'collection_center_id' => $rajivCenter->id,
            'amount' => 2500,
            'payment_date' => now()->subDays(5),
            'payment_mode' => 'Bank Transfer',
            'reference_no' => 'TXN987654321',
            'type' => 'CollectionCenter',
            'status' => 'Approved',
            'notes' => 'Monthly settlement'
        ]);
        $this->command->info('✅ Sample Settlements created');

        // ============================================================
        // DONE!
        // ============================================================
        $this->command->newLine();
        $this->command->info('🎉 Demo data seeded successfully!');
        $this->command->info('   Lab Admin Login: lab@sahanipathology.in / password123');
        $this->command->info('   Branch Admin Login: boring.admin@sahanipathology.in / password123');
        $this->command->info('   Staff Login: rahul@sahanipathology.in / password123');
        $this->command->newLine();
    }
}
