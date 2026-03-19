<?php
// Biochemistry General Tests
return [
    [
        'test_code' => 'BSF',
        'name' => 'Blood Sugar Fasting',
        'category' => 'Biochemistry',
        'description' => 'Fasting blood glucose. 8-12 hours overnight fast required.',
        'interpretation' => '<table><tr><th>Level (mg/dL)</th><th>Category</th></tr><tr><td>&lt; 100</td><td>Normal</td></tr><tr><td>100 - 125</td><td>Impaired Fasting Glucose (Pre-Diabetes)</td></tr><tr><td>&ge; 126</td><td>Diabetes Mellitus</td></tr></table>',
        'suggested_price' => 80,
        'default_parameters' => [
            ['name' => 'Blood Sugar (Fasting)', 'unit' => 'mg/dL', 'range_type' => 'general', 'general_range' => '70 - 100', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'BSF', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'BSPP',
        'name' => 'Blood Sugar Post Prandial',
        'category' => 'Biochemistry',
        'description' => 'Post meal blood glucose. Sample taken 2 hours after standard meal.',
        'interpretation' => '<table><tr><th>Level (mg/dL)</th><th>Category</th></tr><tr><td>&lt; 140</td><td>Normal</td></tr><tr><td>140 - 199</td><td>Impaired Glucose Tolerance</td></tr><tr><td>&ge; 200</td><td>Diabetes Mellitus</td></tr></table>',
        'suggested_price' => 80,
        'default_parameters' => [
            ['name' => 'Blood Sugar (PP)', 'unit' => 'mg/dL', 'range_type' => 'general', 'general_range' => '70 - 140', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'BSPP', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'BSR',
        'name' => 'Blood Sugar Random',
        'category' => 'Biochemistry',
        'description' => 'Random blood glucose. No fasting required.',
        'interpretation' => 'Random glucose >= 200 mg/dL with symptoms is diagnostic of diabetes mellitus.',
        'suggested_price' => 80,
        'default_parameters' => [
            ['name' => 'Blood Sugar (Random)', 'unit' => 'mg/dL', 'range_type' => 'general', 'general_range' => '70 - 140', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'BSR', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'HBA1C',
        'name' => 'Glycosylated Hemoglobin (HbA1c)',
        'category' => 'Biochemistry',
        'description' => 'Reflects average blood glucose over the past 2-3 months. Gold standard for diabetes monitoring.',
        'interpretation' => '<table><tr><th>HbA1c (%)</th><th>Category</th><th>Estimated Average Glucose</th></tr><tr><td>&lt; 5.7</td><td>Normal</td><td>&lt; 117 mg/dL</td></tr><tr><td>5.7 - 6.4</td><td>Pre-Diabetes</td><td>117 - 137 mg/dL</td></tr><tr><td>&ge; 6.5</td><td>Diabetes Mellitus</td><td>&ge; 140 mg/dL</td></tr><tr><td>&lt; 7.0</td><td>Good Diabetic Control</td><td>&lt; 154 mg/dL</td></tr><tr><td>7.0 - 8.0</td><td>Fair Control</td><td>154 - 183 mg/dL</td></tr><tr><td>&gt; 8.0</td><td>Poor Control</td><td>&gt; 183 mg/dL</td></tr></table>',
        'suggested_price' => 450,
        'default_parameters' => [
            ['name' => 'HbA1c', 'unit' => '%', 'range_type' => 'general', 'general_range' => '4.0 - 5.6', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'HBA1C', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'Estimated Average Glucose', 'unit' => 'mg/dL', 'range_type' => 'general', 'general_range' => '68 - 114', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'EAG', 'input_type' => 'calculated', 'formula' => '({HBA1C} * 28.7) - 46.7'],
        ],
    ],
    [
        'test_code' => 'URIC',
        'name' => 'Uric Acid',
        'category' => 'Biochemistry',
        'description' => 'End product of purine metabolism. Elevated in gout and renal disease.',
        'interpretation' => '<table><tr><th>Condition</th><th>Significance</th></tr><tr><td>Elevated</td><td>Gout, Renal failure, Leukemia, Pre-eclampsia</td></tr><tr><td>Low</td><td>Wilson disease, Fanconi syndrome</td></tr></table>',
        'suggested_price' => 200,
        'default_parameters' => [
            ['name' => 'Uric Acid', 'unit' => 'mg/dL', 'range_type' => 'gender', 'general_range' => '', 'male_range' => '3.4 - 7.0', 'female_range' => '2.4 - 6.0', 'normal_value' => '', 'short_code' => 'URIC', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'CALC',
        'name' => 'Serum Calcium',
        'category' => 'Biochemistry',
        'description' => 'Total serum calcium. Essential for bone, nerve and muscle function.',
        'interpretation' => '<table><tr><th>Level</th><th>Significance</th></tr><tr><td>Low (&lt; 8.5)</td><td>Hypoparathyroidism, Vit D deficiency, CKD</td></tr><tr><td>Normal (8.5 - 10.5)</td><td>Normal calcium homeostasis</td></tr><tr><td>High (&gt; 10.5)</td><td>Hyperparathyroidism, Malignancy, Vit D excess</td></tr></table>',
        'suggested_price' => 200,
        'default_parameters' => [
            ['name' => 'Serum Calcium', 'unit' => 'mg/dL', 'range_type' => 'general', 'general_range' => '8.5 - 10.5', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'CA', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'PHOS',
        'name' => 'Serum Phosphorus',
        'category' => 'Biochemistry',
        'description' => 'Inorganic phosphorus. Important for bone metabolism.',
        'interpretation' => 'Elevated: CKD, Hypoparathyroidism, Tumor lysis. Low: Hyperparathyroidism, Rickets, Malnutrition.',
        'suggested_price' => 200,
        'default_parameters' => [
            ['name' => 'Serum Phosphorus', 'unit' => 'mg/dL', 'range_type' => 'general', 'general_range' => '2.5 - 4.5', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'PHOS', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'IRON',
        'name' => 'Iron Studies',
        'category' => 'Biochemistry',
        'description' => 'Comprehensive iron profile including Serum Iron, TIBC, Transferrin Saturation and Ferritin.',
        'interpretation' => '<table><tr><th>Pattern</th><th>Iron</th><th>TIBC</th><th>Ferritin</th><th>Diagnosis</th></tr><tr><td>Iron Deficiency</td><td>Low</td><td>High</td><td>Low</td><td>IDA</td></tr><tr><td>Chronic Disease</td><td>Low</td><td>Low</td><td>High</td><td>ACD</td></tr><tr><td>Overload</td><td>High</td><td>Low</td><td>High</td><td>Hemochromatosis</td></tr></table>',
        'suggested_price' => 600,
        'default_parameters' => [
            ['name' => 'Serum Iron', 'unit' => 'µg/dL', 'range_type' => 'gender', 'general_range' => '', 'male_range' => '65 - 175', 'female_range' => '50 - 170', 'normal_value' => '', 'short_code' => 'FE', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'TIBC', 'unit' => 'µg/dL', 'range_type' => 'general', 'general_range' => '250 - 370', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'TIBC', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'Transferrin Saturation', 'unit' => '%', 'range_type' => 'general', 'general_range' => '20 - 50', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'TSAT', 'input_type' => 'calculated', 'formula' => '({FE} / {TIBC}) * 100'],
            ['name' => 'Serum Ferritin', 'unit' => 'ng/mL', 'range_type' => 'gender', 'general_range' => '', 'male_range' => '20 - 250', 'female_range' => '10 - 120', 'normal_value' => '', 'short_code' => 'FERR', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'VITD',
        'name' => 'Vitamin D (25-OH)',
        'category' => 'Biochemistry',
        'description' => '25-Hydroxyvitamin D. Gold standard for vitamin D status assessment.',
        'interpretation' => '<table><tr><th>Level (ng/mL)</th><th>Status</th></tr><tr><td>&lt; 10</td><td>Severe Deficiency</td></tr><tr><td>10 - 20</td><td>Deficiency</td></tr><tr><td>20 - 30</td><td>Insufficiency</td></tr><tr><td>30 - 100</td><td>Sufficient</td></tr><tr><td>&gt; 100</td><td>Toxicity Risk</td></tr></table>',
        'suggested_price' => 1200,
        'default_parameters' => [
            ['name' => '25-OH Vitamin D', 'unit' => 'ng/mL', 'range_type' => 'general', 'general_range' => '30 - 100', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'VITD', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'VITB12',
        'name' => 'Vitamin B12',
        'category' => 'Biochemistry',
        'description' => 'Serum cobalamin level. Deficiency causes megaloblastic anemia and neuropathy.',
        'interpretation' => '<table><tr><th>Level (pg/mL)</th><th>Status</th></tr><tr><td>&lt; 200</td><td>Deficiency</td></tr><tr><td>200 - 300</td><td>Borderline / Possible deficiency</td></tr><tr><td>300 - 900</td><td>Normal</td></tr><tr><td>&gt; 900</td><td>Elevated (Liver disease, CML)</td></tr></table>',
        'suggested_price' => 800,
        'default_parameters' => [
            ['name' => 'Vitamin B12', 'unit' => 'pg/mL', 'range_type' => 'general', 'general_range' => '200 - 900', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'B12', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
];
