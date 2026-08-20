<?php

namespace App\Services\Import;

use App\Models\AgentProfile;
use App\Models\Company;
use App\Models\Configuration;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BulkImportService
{
    /**
     * Last parsing error message if file could not be read.
     *
     * @var string|null
     */
    protected ?string $lastParseError = null;

    /**
     * Get the last parse error message.
     */
    public function getLastParseError(): ?string
    {
        return $this->lastParseError;
    }

    /**
     * Parse any spreadsheet (.xlsx, .xls) or CSV file into an array of associative rows.
     *
     * @param string $filePath
     * @param array $expectedKeywords
     * @return array
     */
    public function parseFile(string $filePath, array $expectedKeywords = []): array
    {
        $this->lastParseError = null;

        if (!file_exists($filePath) || !is_readable($filePath)) {
            $this->lastParseError = "Uploaded file could not be read from disk.";
            return [];
        }

        // Check file magic bytes to determine actual file format
        $handle = @fopen($filePath, 'rb');
        $magic = $handle ? fread($handle, 8) : '';
        if ($handle) {
            fclose($handle);
        }

        $isZipXlsx = str_starts_with($magic, "PK\x03\x04") || str_starts_with($magic, "PK");
        $isOldXls = str_starts_with($magic, "\xD0\xCF\x11\xE0");

        // 1. Binary Excel Spreadsheets (XLSX / XLS)
        if ($isZipXlsx || $isOldXls) {
            if ($isZipXlsx && !class_exists('ZipArchive')) {
                $this->lastParseError = "PHP Zip extension ('ZipArchive') is not active in your web server. Please restart your local dev server ('php artisan serve') or save your Excel file as CSV (.csv) to import without ZipArchive.";
                return [];
            }

            if (!class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
                $this->lastParseError = "PhpSpreadsheet library is not available to read Excel files.";
                return [];
            }

            try {
                $readerType = $isZipXlsx ? 'Xlsx' : 'Xls';
                $reader = IOFactory::createReader($readerType);
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($filePath);

                // Find the first worksheet that actually has content
                $sheet = null;
                foreach ($spreadsheet->getAllSheets() as $candidateSheet) {
                    $highestRow = $candidateSheet->getHighestDataRow();
                    if ($highestRow > 1 || !empty(trim((string)$candidateSheet->getCell('A1')->getValue()))) {
                        $sheet = $candidateSheet;
                        break;
                    }
                }

                if (!$sheet) {
                    $sheet = $spreadsheet->getActiveSheet();
                }

                $rawRows = $sheet->toArray(null, true, true, false);

                if (empty($rawRows)) {
                    $this->lastParseError = "The Excel sheet appears to be empty.";
                    return [];
                }

                return $this->extractDataFromRows($rawRows, $expectedKeywords);
            } catch (\Throwable $e) {
                $this->lastParseError = "Failed to parse Excel spreadsheet: " . $e->getMessage();
                return []; // Strictly DO NOT fallback to CSV on a binary file!
            }
        }

        // 2. CSV / Plain Text Spreadsheets
        $csvResult = $this->parseCsv($filePath, $expectedKeywords);
        if (empty($csvResult) && !$this->lastParseError) {
            $this->lastParseError = "No valid data rows found in the uploaded CSV file.";
        }

        return $csvResult;
    }

    /**
     * Extract structured associative array from raw spreadsheet 2D array.
     */
    protected function extractDataFromRows(array $rawRows, array $expectedKeywords = []): array
    {
        // Filter out completely empty rows
        $cleanRows = [];
        foreach ($rawRows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $nonEmpty = array_filter($row, fn($v) => !is_null($v) && trim((string)$v) !== '');
            if (!empty($nonEmpty)) {
                $cleanRows[] = $row;
            }
        }

        if (empty($cleanRows)) {
            return [];
        }

        // Detect header row index
        $headerIndex = $this->detectHeaderRowIndex($cleanRows, $expectedKeywords);
        $rawHeader = $cleanRows[$headerIndex];

        $headers = [];
        foreach ($rawHeader as $index => $col) {
            $headers[$index] = $this->normalizeKey((string)$col);
        }

        $data = [];
        for ($i = $headerIndex + 1; $i < count($cleanRows); $i++) {
            $row = $cleanRows[$i];
            $mappedRow = [];
            $hasAnyValue = false;

            foreach ($headers as $colIdx => $key) {
                if ($key !== '') {
                    $rawVal = isset($row[$colIdx]) ? trim((string)$row[$colIdx]) : null;
                    $val = $rawVal !== null && $rawVal !== '' ? $this->sanitizeUtf8($rawVal) : null;
                    if ($val !== '' && !is_null($val)) {
                        $hasAnyValue = true;
                    }
                    $mappedRow[$key] = $val;
                }
            }

            if ($hasAnyValue) {
                $data[] = $mappedRow;
            }
        }

        return $data;
    }

    /**
     * Detect the row index that contains column headers.
     */
    /**
     * Detect the row index that contains column headers using score-based matching.
     */
    protected function detectHeaderRowIndex(array $rows, array $expectedKeywords = []): int
    {
        $knownHeaders = [
            'name' => 4, 'patient_name' => 5, 'full_name' => 5, 'doctor_name' => 5, 'dr_name' => 5, 'agent_name' => 5,
            'mobile' => 4, 'phone' => 4, 'contact' => 4, 'phone_number' => 5, 'mobile_number' => 5, 'contact_number' => 5,
            'age' => 4, 'patient_age' => 5, 'age_type' => 4, 'gender' => 4, 'sex' => 4,
            'blood_group' => 4, 'bloodgroup' => 4, 'blood' => 3, 'email' => 4, 'address' => 4,
            'specialization' => 4, 'clinic_name' => 4, 'clinic' => 3, 'agency_name' => 4, 'agency' => 3,
            'commission_percentage' => 4, 'commission' => 4
        ];

        foreach ($expectedKeywords as $kw) {
            $normKw = $this->normalizeKey($kw);
            if ($normKw !== '') {
                $knownHeaders[$normKw] = 4;
            }
        }

        $bestIndex = 0;
        $maxScore = 0;

        foreach ($rows as $rowIndex => $row) {
            $score = 0;
            foreach ($row as $cell) {
                $normalized = $this->normalizeKey((string)$cell);
                if ($normalized === '') {
                    continue;
                }

                if (isset($knownHeaders[$normalized])) {
                    $score += $knownHeaders[$normalized];
                } else {
                    foreach (array_keys($knownHeaders) as $kh) {
                        if ($normalized === $kh || str_starts_with($normalized, $kh . '_') || str_ends_with($normalized, '_' . $kh)) {
                            $score += 2;
                            break;
                        }
                    }
                }
            }

            if ($score > $maxScore) {
                $maxScore = $score;
                $bestIndex = $rowIndex;
            }
        }

        return $bestIndex;
    }

    /**
     * Robust CSV reader with BOM stripping and delimiter auto-detection.
     */
    protected function parseCsv(string $filePath, array $expectedKeywords = []): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return [];
        }

        $content = file_get_contents($filePath);
        if ($content === false || trim($content) === '') {
            return [];
        }

        // Remove UTF-8 BOM if present
        $bom = pack('H*', 'EFBBBF');
        $content = preg_replace("/^$bom/", '', $content);

        // Detect line separator
        $lines = preg_split("/\r\n|\n|\r/", $content);
        $lines = array_filter($lines, fn($line) => trim($line) !== '');

        if (empty($lines)) {
            return [];
        }

        // Detect delimiter (comma, semicolon, tab)
        $firstLine = reset($lines);
        $delimiters = [',', ';', "\t", '|'];
        $bestDelimiter = ',';
        $maxCount = 0;

        foreach ($delimiters as $delim) {
            $count = substr_count($firstLine, $delim);
            if ($count > $maxCount) {
                $maxCount = $count;
                $bestDelimiter = $delim;
            }
        }

        $rawRows = [];
        foreach ($lines as $line) {
            $row = str_getcsv($line, $bestDelimiter);
            if (!empty($row)) {
                $rawRows[] = $row;
            }
        }

        return $this->extractDataFromRows($rawRows, $expectedKeywords);
    }

    /**
     * Safely sanitize any string or nested array to ensure 100% valid UTF-8.
     *
     * @param mixed $value
     * @return mixed
     */
    public function sanitizeUtf8($value)
    {
        if (is_array($value)) {
            $cleaned = [];
            foreach ($value as $k => $v) {
                $cleanKey = is_string($k) ? mb_convert_encoding($k, 'UTF-8', 'UTF-8') : $k;
                $cleaned[$cleanKey] = $this->sanitizeUtf8($v);
            }
            return $cleaned;
        }

        if (is_string($value)) {
            // Strip BOM
            $bom = pack('H*', 'EFBBBF');
            $clean = preg_replace("/^$bom/", '', $value);
            // Ensure valid UTF-8
            return mb_convert_encoding($clean, 'UTF-8', 'UTF-8');
        }

        return $value;
    }

    /**
     * Normalize header column key into standardized snake_case slug safely.
     */
    protected function normalizeKey(string $key): string
    {
        $clean = $this->sanitizeUtf8($key);
        $clean = mb_strtolower(trim($clean), 'UTF-8');
        $clean = preg_replace('/[^\p{L}\p{N}]+/u', '_', $clean);
        return trim($clean, '_');
    }

    /**
     * Get a value from a row using possible alias keys with exact, normalized, and fuzzy matching.
     *
     * @param array $row
     * @param array $aliases
     * @param mixed $default
     * @param array $exclusions
     * @return mixed
     */
    protected function getValue(array $row, array $aliases, $default = null, array $exclusions = [])
    {
        // 1. Direct exact & normalized key match
        foreach ($aliases as $alias) {
            $norm = $this->normalizeKey($alias);
            if (array_key_exists($norm, $row) && !is_null($row[$norm]) && trim((string)$row[$norm]) !== '') {
                return trim((string)$row[$norm]);
            }
            if (array_key_exists($alias, $row) && !is_null($row[$alias]) && trim((string)$row[$alias]) !== '') {
                return trim((string)$row[$alias]);
            }
        }

        // 2. Fuzzy substring match across actual row keys
        foreach ($row as $key => $value) {
            if (is_null($value) || trim((string)$value) === '') {
                continue;
            }

            // Check exclusions
            $isExcluded = false;
            foreach ($exclusions as $ex) {
                $normEx = $this->normalizeKey($ex);
                if ($normEx !== '' && ($key === $normEx || str_contains($key, $normEx))) {
                    $isExcluded = true;
                    break;
                }
            }
            if ($isExcluded) {
                continue;
            }

            foreach ($aliases as $alias) {
                $normAlias = $this->normalizeKey($alias);
                if ($normAlias !== '' && ($key === $normAlias || str_contains($key, $normAlias))) {
                    return trim((string)$value);
                }
            }
        }

        return $default;
    }

    /**
     * Fallback to find the first non-numeric/ID column for a name if no header matched.
     */
    protected function getPositionalName(array $row): ?string
    {
        foreach ($row as $key => $val) {
            if (is_null($val) || trim((string)$val) === '') {
                continue;
            }
            $cleanVal = trim((string)$val);
            // Skip pure numbers (e.g. S.No, ID, Phone, Age) or Date strings
            if (is_numeric($cleanVal) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $cleanVal) || preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $cleanVal)) {
                continue;
            }
            // Skip common non-name values
            if (in_array(strtolower($cleanVal), ['male', 'female', 'other', 'years', 'months', 'days', 'a+', 'a-', 'b+', 'b-', 'o+', 'o-', 'ab+', 'ab-'])) {
                continue;
            }
            return $cleanVal;
        }
        return null;
    }

    /**
     * Bulk import patients from an uploaded spreadsheet file.
     *
     * @param string $filePath
     * @param int $companyId
     * @param int|null $branchId
     * @return array
     */
    public function importPatients(string $filePath, int $companyId, ?int $branchId = null): array
    {
        $rows = $this->parseFile($filePath);
        $total = count($rows);
        $success = 0;
        $skipped = 0;
        $errors = [];

        if ($total === 0) {
            $msg = $this->lastParseError ?: 'No data rows found in the uploaded file or column headers could not be matched. Please ensure your file contains columns like "Patient Name", "Age", "Phone", etc. You can download our sample Excel template for guidance.';
            return [
                'total' => 0,
                'success' => 0,
                'skipped' => 0,
                'errors' => [$msg],
            ];
        }

        $pPrefix = Configuration::getFor('patient_id_prefix', 'PAT');
        $pDigits = (int) Configuration::getFor('patient_id_digits', 4);

        $maxLocalId = PatientProfile::where('company_id', $companyId)->max('company_patient_number') ?? 0;
        if ($maxLocalId == 0) {
            $lastProfile = PatientProfile::where('company_id', $companyId)
                ->whereNotNull('patient_id_string')
                ->orderBy('id', 'desc')
                ->first();
            if ($lastProfile && strpos($lastProfile->patient_id_string, '-') !== false) {
                $parsed = (int) preg_replace('/[^0-9]/', '', substr($lastProfile->patient_id_string, strrpos($lastProfile->patient_id_string, '-')));
                if ($parsed > 0) {
                    $maxLocalId = $parsed;
                }
            }
        }

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 for Excel (1-based + 1 header row)

            $name = $this->getValue($row, [
                'patient_name', 'name', 'full_name', 'fullname', 'patientname', 'pt_name', 'p_name', 'patient',
                'customer_name', 'client_name', 'person_name', 'beneficiary_name', 'beneficiary', 'party_name',
                'first_name', 'user_name', 'username', 'contact_person', 'member_name', 'member', 'pt'
            ], null, ['doctor', 'dr', 'clinic', 'agent', 'agency', 'hospital', 'company', 'branch', 'father', 'husband', 'mother', 'guardian']);

            if (empty($name)) {
                $name = $this->getPositionalName($row);
            }

            $phone = $this->getValue($row, [
                'phone', 'mobile', 'contact', 'mob', 'cell', 'tel', 'whatsapp', 'phone_number', 'mobile_number',
                'contact_number', 'mobile_no', 'phone_no', 'contact_no', 'cell_no', 'phone_num', 'mobile_num'
            ]);

            $email = $this->getValue($row, ['email', 'email_id', 'e_mail', 'mail_id', 'email_address'], null, ['address', 'city', 'location', 'residence', 'area']);
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = null;
            }

            $ageRaw = $this->getValue($row, ['age', 'patient_age', 'pt_age', 'years', 'yr', 'yrs', 'umar', 'age_years', 'age_in_years', 'age_yrs']);
            if (is_null($ageRaw) || trim((string)$ageRaw) === '') {
                foreach ($row as $k => $v) {
                    if (is_numeric($v) && (int)$v >= 0 && (int)$v <= 120 && strlen((string)(int)$v) <= 3) {
                        $ageRaw = $v;
                        break;
                    }
                }
            }

            $ageTypeRaw = $this->getValue($row, ['age_type', 'unit', 'age_unit', 'type'], 'Years');
            $genderRaw = $this->getValue($row, ['gender', 'sex', 'm_f', 'gender_m_f', 'sex_m_f'], 'Male');
            $bloodGroup = $this->getValue($row, ['blood_group', 'bloodgroup', 'blood_grp', 'blood', 'bg', 'b_group']);
            $address = $this->getValue($row, ['address', 'location', 'city', 'full_address', 'residence', 'area', 'addr']);

            // Validate name
            if (empty($name)) {
                $skipped++;
                $errors[] = "Row {$rowNumber}: Patient Name could not be identified.";
                continue;
            }

            // Validate / Normalize Age
            $age = (is_numeric($ageRaw) && (float)$ageRaw >= 0 && (float)$ageRaw <= 150) ? (float)$ageRaw : 30.0;

            // Normalize Age Type
            $ageTypeLower = strtolower(trim((string)$ageTypeRaw));
            if (str_starts_with($ageTypeLower, 'm')) {
                $ageType = 'Months';
            } elseif (str_starts_with($ageTypeLower, 'd')) {
                $ageType = 'Days';
            } else {
                $ageType = 'Years';
            }

            // Normalize Gender
            $genderLower = strtolower(trim((string)$genderRaw));
            if (str_starts_with($genderLower, 'f')) {
                $gender = 'Female';
            } elseif (str_starts_with($genderLower, 'o')) {
                $gender = 'Other';
            } else {
                $gender = 'Male';
            }

            // Normalize Phone (clean non-digits)
            if (!empty($phone)) {
                $cleanedPhone = preg_replace('/[^0-9]/', '', (string)$phone);
                // Handle optional +91 or 91 country code prefix if 12 digits
                if (strlen($cleanedPhone) === 12 && str_starts_with($cleanedPhone, '91')) {
                    $cleanedPhone = substr($cleanedPhone, 2);
                }
                $phone = $cleanedPhone;
            }

            // Check Email uniqueness if provided
            if (!empty($email)) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skipped++;
                    $errors[] = "Row {$rowNumber} ({$name}): Invalid email format '{$email}'.";
                    continue;
                }

                if (User::where('company_id', $companyId)->where('email', $email)->exists()) {
                    $skipped++;
                    $errors[] = "Row {$rowNumber} ({$name}): Email '{$email}' is already registered.";
                    continue;
                }
            }

            // Normalize Blood Group
            if (!empty($bloodGroup)) {
                $bgClean = strtoupper(trim(str_replace(' ', '', $bloodGroup)));
                $validBgs = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                if (in_array($bgClean, $validBgs)) {
                    $bloodGroup = $bgClean;
                }
            }

            DB::beginTransaction();
            try {
                // Generate next Unique Patient ID
                $maxLocalId++;
                $patientIdString = $pPrefix . str_pad($maxLocalId, $pDigits, '0', STR_PAD_LEFT);
                while (PatientProfile::where('company_id', $companyId)->where('patient_id_string', $patientIdString)->exists()) {
                    $maxLocalId++;
                    $patientIdString = $pPrefix . str_pad($maxLocalId, $pDigits, '0', STR_PAD_LEFT);
                }

                // 1. Create User
                $user = User::create([
                    'name' => $name,
                    'phone' => !empty($phone) ? $phone : null,
                    'email' => !empty($email) ? $email : null,
                    'password' => Hash::make(!empty($phone) ? $phone : '12345678'),
                    'is_active' => true,
                    'company_id' => $companyId,
                    'branch_id' => $branchId,
                ]);

                // 2. Create Profile
                PatientProfile::create([
                    'company_id' => $companyId,
                    'user_id' => $user->id,
                    'patient_id_string' => $patientIdString,
                    'company_patient_number' => $maxLocalId,
                    'age' => $age,
                    'age_type' => $ageType,
                    'gender' => $gender,
                    'blood_group' => !empty($bloodGroup) ? $bloodGroup : null,
                    'address' => !empty($address) ? $address : null,
                ]);

                // 3. Role
                $user->assignRole('patient');

                DB::commit();
                $success++;
            } catch (\Throwable $e) {
                DB::rollBack();
                $skipped++;
                $errors[] = "Row {$rowNumber} ({$name}): Failed to save - " . $e->getMessage();
            }
        }

        return $this->sanitizeUtf8([
            'total' => $total,
            'success' => $success,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);
    }

    /**
     * Bulk import referring doctors from an uploaded spreadsheet file.
     *
     * @param string $filePath
     * @param int $companyId
     * @param int|null $branchId
     * @return array
     */
    public function importDoctors(string $filePath, int $companyId, ?int $branchId = null): array
    {
        $rows = $this->parseFile($filePath);
        $total = count($rows);
        $success = 0;
        $skipped = 0;
        $errors = [];

        if ($total === 0) {
            $msg = $this->lastParseError ?: 'No data rows found in the uploaded file or column headers could not be matched. Please ensure your file contains columns like "Doctor Name", "Phone", "Specialization", etc. You can download our sample Excel template for guidance.';
            return [
                'total' => 0,
                'success' => 0,
                'skipped' => 0,
                'errors' => [$msg],
            ];
        }

        $company = Company::find($companyId);
        $maxDoctors = $company->plan->features['doctors'] ?? -1;
        $currentDoctorCount = DoctorProfile::where('company_id', $companyId)->count();

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            if ($maxDoctors != -1 && $currentDoctorCount >= $maxDoctors) {
                $skipped++;
                $errors[] = "Row {$rowNumber}: Plan limit reached! Your plan allows maximum {$maxDoctors} referring doctors.";
                continue;
            }

            $name = $this->getValue($row, [
                'doctor_name', 'dr_name', 'name', 'doctor', 'dr', 'full_name', 'fullname', 'physician_name',
                'physician', 'doc_name', 'consultant_name', 'consultant', 'customer_name', 'client_name'
            ], null, ['clinic', 'agent', 'agency', 'hospital', 'company', 'branch']);

            if (empty($name)) {
                $name = $this->getPositionalName($row);
            }

            $phone = $this->getValue($row, [
                'phone', 'mobile', 'contact', 'mob', 'cell', 'tel', 'whatsapp', 'phone_number', 'mobile_number',
                'contact_number', 'mobile_no', 'phone_no', 'contact_no', 'cell_no'
            ]);
            $email = $this->getValue($row, ['email', 'email_address', 'mail', 'email_id', 'e_mail']);
            $specialization = $this->getValue($row, ['specialization', 'specialty', 'speciality', 'qualification', 'degree', 'department', 'designation']);
            $clinicName = $this->getValue($row, ['clinic_name', 'clinic', 'hospital', 'hospital_name', 'chamber', 'practice', 'workplace', 'center_name']);
            $commissionRaw = $this->getValue($row, ['commission_percentage', 'commission', 'commission_percent', 'commission_%', 'comm_%', 'cut', 'share', 'comm', 'percentage'], 0);

            if (empty($name)) {
                $skipped++;
                $errors[] = "Row {$rowNumber}: Doctor Name could not be identified.";
                continue;
            }

            // Prefix Dr. if not present
            $finalName = str_starts_with(strtolower($name), 'dr') ? $name : 'Dr. ' . $name;

            // Commission
            $commission = is_numeric($commissionRaw) ? (float)$commissionRaw : 0;
            if ($commission < 0 || $commission > 100) {
                $commission = 0;
            }

            // Phone cleanup
            if (!empty($phone)) {
                $cleanedPhone = preg_replace('/[^0-9]/', '', (string)$phone);
                if (strlen($cleanedPhone) === 12 && str_starts_with($cleanedPhone, '91')) {
                    $cleanedPhone = substr($cleanedPhone, 2);
                }
                $phone = $cleanedPhone;
            }

            // Email check
            if (!empty($email)) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skipped++;
                    $errors[] = "Row {$rowNumber} ({$name}): Invalid email format '{$email}'.";
                    continue;
                }

                if (User::where('company_id', $companyId)->where('email', $email)->exists()) {
                    $skipped++;
                    $errors[] = "Row {$rowNumber} ({$name}): Email '{$email}' is already registered.";
                    continue;
                }
            }

            DB::beginTransaction();
            try {
                $user = User::create([
                    'company_id' => $companyId,
                    'branch_id' => $branchId,
                    'name' => $finalName,
                    'phone' => !empty($phone) ? $phone : null,
                    'email' => !empty($email) ? $email : null,
                    'password' => Hash::make(!empty($phone) ? $phone : 'password123'),
                    'is_active' => true,
                ]);

                DoctorProfile::create([
                    'company_id' => $companyId,
                    'user_id' => $user->id,
                    'specialization' => !empty($specialization) ? $specialization : null,
                    'clinic_name' => !empty($clinicName) ? $clinicName : null,
                    'commission_percentage' => $commission,
                ]);

                $user->assignRole('doctor');

                DB::commit();
                $success++;
                $currentDoctorCount++;
            } catch (\Throwable $e) {
                DB::rollBack();
                $skipped++;
                $errors[] = "Row {$rowNumber} ({$name}): Failed to save - " . $e->getMessage();
            }
        }

        return $this->sanitizeUtf8([
            'total' => $total,
            'success' => $success,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);
    }

    /**
     * Bulk import referral agents from an uploaded spreadsheet file.
     *
     * @param string $filePath
     * @param int $companyId
     * @param int|null $branchId
     * @return array
     */
    public function importAgents(string $filePath, int $companyId, ?int $branchId = null): array
    {
        $rows = $this->parseFile($filePath);
        $total = count($rows);
        $success = 0;
        $skipped = 0;
        $errors = [];

        if ($total === 0) {
            $msg = $this->lastParseError ?: 'No data rows found in the uploaded file or column headers could not be matched. Please ensure your file contains columns like "Agent Name", "Phone", "Agency Name", etc. You can download our sample Excel template for guidance.';
            return [
                'total' => 0,
                'success' => 0,
                'skipped' => 0,
                'errors' => [$msg],
            ];
        }

        $company = Company::find($companyId);
        $maxAgents = $company->plan->features['agents'] ?? -1;
        $currentAgentCount = AgentProfile::where('company_id', $companyId)->count();

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            if ($maxAgents != -1 && $currentAgentCount >= $maxAgents) {
                $skipped++;
                $errors[] = "Row {$rowNumber}: Plan limit reached! Your plan allows maximum {$maxAgents} marketing agents.";
                continue;
            }

            $name = $this->getValue($row, [
                'agent_name', 'name', 'full_name', 'fullname', 'agent', 'partner_name', 'partner',
                'associate_name', 'associate', 'representative', 'rep_name', 'customer_name', 'client_name'
            ], null, ['agency', 'clinic', 'doctor', 'hospital', 'company', 'branch']);

            if (empty($name)) {
                $name = $this->getPositionalName($row);
            }

            $phone = $this->getValue($row, [
                'phone', 'mobile', 'contact', 'mob', 'cell', 'tel', 'whatsapp', 'phone_number', 'mobile_number',
                'contact_number', 'mobile_no', 'phone_no', 'contact_no', 'cell_no'
            ]);
            $email = $this->getValue($row, ['email', 'email_address', 'mail', 'email_id', 'e_mail']);
            $agencyName = $this->getValue($row, ['agency_name', 'agency', 'company_name', 'organization', 'center_name', 'firm_name', 'firm']);
            $commissionRaw = $this->getValue($row, ['commission_percentage', 'commission', 'commission_percent', 'commission_%', 'comm_%', 'cut', 'share', 'comm', 'percentage'], 0);

            if (empty($name)) {
                $skipped++;
                $errors[] = "Row {$rowNumber}: Agent Name could not be identified.";
                continue;
            }

            // Commission
            $commission = is_numeric($commissionRaw) ? (float)$commissionRaw : 0;
            if ($commission < 0 || $commission > 100) {
                $commission = 0;
            }

            // Phone cleanup
            if (!empty($phone)) {
                $cleanedPhone = preg_replace('/[^0-9]/', '', (string)$phone);
                if (strlen($cleanedPhone) === 12 && str_starts_with($cleanedPhone, '91')) {
                    $cleanedPhone = substr($cleanedPhone, 2);
                }
                $phone = $cleanedPhone;
            }

            // Email check
            if (!empty($email)) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skipped++;
                    $errors[] = "Row {$rowNumber} ({$name}): Invalid email format '{$email}'.";
                    continue;
                }

                if (User::where('company_id', $companyId)->where('email', $email)->exists()) {
                    $skipped++;
                    $errors[] = "Row {$rowNumber} ({$name}): Email '{$email}' is already registered.";
                    continue;
                }
            }

            DB::beginTransaction();
            try {
                $user = User::create([
                    'company_id' => $companyId,
                    'branch_id' => $branchId,
                    'name' => $name,
                    'phone' => !empty($phone) ? $phone : null,
                    'email' => !empty($email) ? $email : null,
                    'password' => Hash::make(!empty($phone) ? $phone : 'password123'),
                    'is_active' => true,
                ]);

                AgentProfile::create([
                    'company_id' => $companyId,
                    'user_id' => $user->id,
                    'agency_name' => !empty($agencyName) ? $agencyName : null,
                    'commission_percentage' => $commission,
                ]);

                $user->assignRole('agent');

                DB::commit();
                $success++;
                $currentAgentCount++;
            } catch (\Throwable $e) {
                DB::rollBack();
                $skipped++;
                $errors[] = "Row {$rowNumber} ({$name}): Failed to save - " . $e->getMessage();
            }
        }

        return $this->sanitizeUtf8([
            'total' => $total,
            'success' => $success,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);
    }

    /**
     * Generate and stream a styled Sample Excel (.xlsx) file.
     *
     * @param string $type 'patients' | 'doctors' | 'agents'
     * @return StreamedResponse
     */
    public function downloadSampleTemplate(string $type): StreamedResponse
    {
        $headers = [];
        $sampleData = [];
        $filename = "sample_{$type}_import.xlsx";

        if ($type === 'patients') {
            $headers = ['Patient Name', 'Phone Number', 'Age', 'Age Type', 'Gender', 'Blood Group', 'Email', 'Address'];
            $sampleData = [
                ['Rahul Sharma', '9876543210', '32', 'Years', 'Male', 'B+', 'rahul.sharma@example.com', 'Flat 402, Green Park, Patna'],
                ['Priya Kumari', '9876543211', '26', 'Years', 'Female', 'O+', 'priya.k@example.com', 'Boring Road, Patna'],
                ['Aarav Singh', '9876543212', '6', 'Months', 'Male', 'A+', '', 'Kankarbagh, Patna'],
                ['Sunita Devi', '9876543213', '55', 'Years', 'Female', 'AB+', '', 'Bailey Road, Patna'],
            ];
        } elseif ($type === 'doctors') {
            $headers = ['Doctor Name', 'Phone Number', 'Specialization', 'Clinic Name', 'Commission %', 'Email'];
            $sampleData = [
                ['Dr. Rajesh Verma', '9876543220', 'Cardiologist', 'Verma Heart Care', '15', 'dr.verma@example.com'],
                ['Dr. Sneha Gupta', '9876543221', 'General Physician', 'City Health Clinic', '10', 'dr.sneha@example.com'],
                ['Dr. Amit Patel', '9876543222', 'Orthopedic Surgeon', 'Patel Ortho Care', '12.5', 'dr.amit@example.com'],
            ];
        } elseif ($type === 'agents') {
            $headers = ['Agent Name', 'Phone Number', 'Agency Name', 'Commission %', 'Email'];
            $sampleData = [
                ['Vikram Kumar', '9876543230', 'LifeCare Diagnostics Referral', '10', 'vikram.agent@example.com'],
                ['Manoj Tiwari', '9876543231', 'Seva Health Network', '8', 'manoj.tiwari@example.com'],
                ['Ramesh Yadav', '9876543232', 'Yadav Health Associates', '12', 'ramesh.yadav@example.com'],
            ];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(ucfirst($type) . ' Template');

        // Set Headers & Sample Rows
        $fullTable = array_merge([$headers], $sampleData);
        $sheet->fromArray($fullTable, null, 'A1');

        // Styling
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $headerRange = "A1:{$lastColLetter}1";

        // Header Background & Font
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'], // Primary blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);

        // Auto size columns
        for ($i = 1; $i <= count($headers); $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Apply borders
        $totalRows = count($sampleData) + 1;
        $fullRange = "A1:{$lastColLetter}{$totalRows}";
        $sheet->getStyle($fullRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
        ]);

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
