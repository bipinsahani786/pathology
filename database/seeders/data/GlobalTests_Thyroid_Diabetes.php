<?php
// Thyroid & Diabetes Tests
return [
    [
        'test_code' => 'TFT',
        'name' => 'Thyroid Function Test (T3, T4, TSH)',
        'category' => 'Biochemistry',
        'description' => 'Complete thyroid profile. Early morning sample preferred.',
        'interpretation' => '<table><tr><th>Pattern</th><th>TSH</th><th>T3/T4</th><th>Diagnosis</th></tr><tr><td>Primary Hypothyroid</td><td>High</td><td>Low</td><td>Hashimoto, Iodine deficiency</td></tr><tr><td>Primary Hyperthyroid</td><td>Low</td><td>High</td><td>Graves disease, Toxic nodule</td></tr><tr><td>Subclinical Hypothyroid</td><td>High</td><td>Normal</td><td>Early thyroid failure</td></tr><tr><td>Subclinical Hyperthyroid</td><td>Low</td><td>Normal</td><td>Early excess</td></tr><tr><td>Secondary Hypothyroid</td><td>Low</td><td>Low</td><td>Pituitary dysfunction</td></tr></table>',
        'suggested_price' => 500,
        'default_parameters' => [
            ['name' => 'Total T3', 'unit' => 'ng/dL', 'range_type' => 'general', 'general_range' => '60 - 200', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'T3', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'Total T4', 'unit' => 'µg/dL', 'range_type' => 'general', 'general_range' => '4.5 - 12.5', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'T4', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'TSH', 'unit' => 'µIU/mL', 'range_type' => 'general', 'general_range' => '0.30 - 5.50', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'TSH', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'FT3',
        'name' => 'Free T3',
        'category' => 'Biochemistry',
        'description' => 'Free triiodothyronine — active thyroid hormone.',
        'interpretation' => 'Elevated: Hyperthyroidism, T3 thyrotoxicosis. Low: Hypothyroidism, Sick euthyroid syndrome.',
        'suggested_price' => 350,
        'default_parameters' => [
            ['name' => 'Free T3', 'unit' => 'pg/mL', 'range_type' => 'general', 'general_range' => '2.0 - 4.4', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'FT3', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'FT4',
        'name' => 'Free T4',
        'category' => 'Biochemistry',
        'description' => 'Free thyroxine — biologically active form not bound to proteins.',
        'interpretation' => 'Elevated: Hyperthyroidism, Thyroiditis. Low: Hypothyroidism, Pituitary failure.',
        'suggested_price' => 350,
        'default_parameters' => [
            ['name' => 'Free T4', 'unit' => 'ng/dL', 'range_type' => 'general', 'general_range' => '0.93 - 1.70', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'FT4', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'GTT',
        'name' => 'Glucose Tolerance Test (OGTT)',
        'category' => 'Biochemistry',
        'description' => 'Oral Glucose Tolerance Test with 75g glucose load. Gold standard for gestational diabetes screening.',
        'interpretation' => '<table><tr><th>Time Point</th><th>Normal</th><th>GDM (IADPSG)</th></tr><tr><td>Fasting</td><td>&lt; 92 mg/dL</td><td>&ge; 92 mg/dL</td></tr><tr><td>1 Hour</td><td>&lt; 180 mg/dL</td><td>&ge; 180 mg/dL</td></tr><tr><td>2 Hours</td><td>&lt; 153 mg/dL</td><td>&ge; 153 mg/dL</td></tr></table><br>One or more abnormal values = Gestational Diabetes Mellitus.',
        'suggested_price' => 300,
        'default_parameters' => [
            ['name' => 'Fasting Glucose', 'unit' => 'mg/dL', 'range_type' => 'general', 'general_range' => '70 - 92', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'GTTF', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => '1 Hour Glucose', 'unit' => 'mg/dL', 'range_type' => 'general', 'general_range' => '< 180', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'GTT1', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => '2 Hour Glucose', 'unit' => 'mg/dL', 'range_type' => 'general', 'general_range' => '< 153', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'GTT2', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'INSULIN-F',
        'name' => 'Fasting Insulin',
        'category' => 'Biochemistry',
        'description' => 'Measures fasting serum insulin. Used with glucose for HOMA-IR calculation.',
        'interpretation' => '<table><tr><th>Level (µIU/mL)</th><th>Significance</th></tr><tr><td>&lt; 25</td><td>Normal</td></tr><tr><td>25 - 30</td><td>Borderline insulin resistance</td></tr><tr><td>&gt; 30</td><td>Insulin Resistance / PCOS</td></tr></table>',
        'suggested_price' => 600,
        'default_parameters' => [
            ['name' => 'Fasting Glucose (for HOMA)', 'unit' => 'mg/dL', 'range_type' => 'general', 'general_range' => '70 - 100', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'GHOMA', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'Fasting Insulin', 'unit' => 'µIU/mL', 'range_type' => 'general', 'general_range' => '2.6 - 24.9', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'INSF', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'HOMA-IR (Insulin Resistance)', 'unit' => 'index', 'range_type' => 'general', 'general_range' => '< 2.5', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'HOMAIR', 'input_type' => 'calculated', 'formula' => '({INSF} * {GHOMA}) / 405'],
        ],
    ],
    [
        'test_code' => 'CRP',
        'name' => 'C-Reactive Protein (CRP)',
        'category' => 'Biochemistry',
        'description' => 'Acute phase reactant. Non-specific marker of inflammation and infection.',
        'interpretation' => '<table><tr><th>Level (mg/L)</th><th>Significance</th></tr><tr><td>&lt; 5</td><td>Normal</td></tr><tr><td>5 - 10</td><td>Mild inflammation</td></tr><tr><td>10 - 50</td><td>Moderate inflammation / Infection</td></tr><tr><td>&gt; 50</td><td>Severe infection / Sepsis</td></tr></table>',
        'suggested_price' => 500,
        'default_parameters' => [
            ['name' => 'CRP (Quantitative)', 'unit' => 'mg/L', 'range_type' => 'general', 'general_range' => '0 - 5', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'CRP', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'ELEC',
        'name' => 'Serum Electrolytes (Na, K, Cl)',
        'category' => 'Biochemistry',
        'description' => 'Essential electrolyte panel. Critical for fluid balance assessment.',
        'interpretation' => '<table><tr><th>Electrolyte</th><th>Low</th><th>High</th></tr><tr><td>Sodium</td><td>Hyponatremia: SIADH, Diuretics</td><td>Hypernatremia: Dehydration, DI</td></tr><tr><td>Potassium</td><td>Hypokalemia: Diarrhea, Diuretics</td><td>Hyperkalemia: CKD, ACE inhibitors</td></tr><tr><td>Chloride</td><td>Vomiting, Burns</td><td>CKD, Metabolic acidosis</td></tr></table>',
        'suggested_price' => 400,
        'default_parameters' => [
            ['name' => 'Sodium (Na)', 'unit' => 'mEq/L', 'range_type' => 'general', 'general_range' => '136 - 146', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'NA', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'Potassium (K)', 'unit' => 'mEq/L', 'range_type' => 'general', 'general_range' => '3.5 - 5.1', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'K', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'Chloride (Cl)', 'unit' => 'mEq/L', 'range_type' => 'general', 'general_range' => '98 - 106', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'CL', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'Bicarbonate (HCO3)', 'unit' => 'mEq/L', 'range_type' => 'general', 'general_range' => '22 - 28', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'HCO3', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'Anion Gap', 'unit' => 'mEq/L', 'range_type' => 'general', 'general_range' => '8 - 14', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'AGAP', 'input_type' => 'calculated', 'formula' => '{NA} - ({CL} + {HCO3})'],
        ],
    ],
];
