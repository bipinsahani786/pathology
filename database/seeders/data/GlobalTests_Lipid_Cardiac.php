<?php
// Lipid & Cardiac Tests
return [
    [
        'test_code' => 'LIPID',
        'name' => 'Lipid Profile',
        'category' => 'Biochemistry',
        'description' => 'Complete lipid panel. 10-12 hours fasting required.',
        'interpretation' => '<table><tr><th>Parameter</th><th>Desirable</th><th>Borderline</th><th>High Risk</th></tr><tr><td>Total Cholesterol</td><td>&lt; 200</td><td>200 - 239</td><td>&ge; 240 mg/dL</td></tr><tr><td>LDL Cholesterol</td><td>&lt; 100</td><td>130 - 159</td><td>&ge; 160 mg/dL</td></tr><tr><td>HDL Cholesterol</td><td>&gt; 60</td><td>40 - 60</td><td>&lt; 40 mg/dL</td></tr><tr><td>Triglycerides</td><td>&lt; 150</td><td>150 - 199</td><td>&ge; 200 mg/dL</td></tr><tr><td>VLDL</td><td>&lt; 30</td><td>30 - 40</td><td>&gt; 40 mg/dL</td></tr></table>',
        'suggested_price' => 450,
        'default_parameters' => [
            ['name' => 'Total Cholesterol', 'unit' => 'mg/dL', 'range_type' => 'general', 'general_range' => '125 - 200', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'TC', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'Triglycerides', 'unit' => 'mg/dL', 'range_type' => 'general', 'general_range' => '25 - 150', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'TG', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'HDL Cholesterol', 'unit' => 'mg/dL', 'range_type' => 'gender', 'general_range' => '', 'male_range' => '35 - 80', 'female_range' => '40 - 88', 'normal_value' => '', 'short_code' => 'HDL', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'VLDL Cholesterol', 'unit' => 'mg/dL', 'range_type' => 'general', 'general_range' => '5 - 30', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'VLDL', 'input_type' => 'calculated', 'formula' => '{TG} / 5'],
            ['name' => 'LDL Cholesterol', 'unit' => 'mg/dL', 'range_type' => 'general', 'general_range' => '0 - 100', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'LDL', 'input_type' => 'calculated', 'formula' => '{TC} - {HDL} - {VLDL}'],
            ['name' => 'TC/HDL Ratio', 'unit' => '', 'range_type' => 'general', 'general_range' => '0 - 5.0', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'TCHDL', 'input_type' => 'calculated', 'formula' => '{TC} / {HDL}'],
            ['name' => 'LDL/HDL Ratio', 'unit' => '', 'range_type' => 'general', 'general_range' => '0 - 3.5', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'LDLHDL', 'input_type' => 'calculated', 'formula' => '{LDL} / {HDL}'],
        ],
    ],
    [
        'test_code' => 'TROP-I',
        'name' => 'Troponin I',
        'category' => 'Biochemistry',
        'description' => 'Cardiac biomarker. Gold standard for myocardial infarction diagnosis.',
        'interpretation' => '<table><tr><th>Level (ng/mL)</th><th>Significance</th></tr><tr><td>&lt; 0.04</td><td>Normal (No myocardial injury)</td></tr><tr><td>0.04 - 0.39</td><td>Elevated — Myocardial injury likely</td></tr><tr><td>&ge; 0.40</td><td>Strongly positive — MI likely</td></tr></table><br>Rises 3-6 hrs after onset, peaks at 12-24 hrs, remains elevated for 7-14 days.',
        'suggested_price' => 900,
        'default_parameters' => [
            ['name' => 'Troponin I', 'unit' => 'ng/mL', 'range_type' => 'general', 'general_range' => '0 - 0.04', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'TROPI', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'CPK-MB',
        'name' => 'CPK-MB (CK-MB)',
        'category' => 'Biochemistry',
        'description' => 'Creatine Kinase MB fraction. Cardiac isoenzyme for MI diagnosis.',
        'interpretation' => '<table><tr><th>Level</th><th>Significance</th></tr><tr><td>&lt; 25 U/L</td><td>Normal</td></tr><tr><td>25 - 100 U/L</td><td>Mild myocardial injury</td></tr><tr><td>&gt; 100 U/L</td><td>Significant myocardial injury / MI</td></tr></table>',
        'suggested_price' => 500,
        'default_parameters' => [
            ['name' => 'CPK-Total', 'unit' => 'U/L', 'range_type' => 'gender', 'general_range' => '', 'male_range' => '39 - 308', 'female_range' => '26 - 192', 'normal_value' => '', 'short_code' => 'CPK', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'CPK-MB', 'unit' => 'U/L', 'range_type' => 'general', 'general_range' => '0 - 25', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'CPKMB', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'LDH',
        'name' => 'Lactate Dehydrogenase (LDH)',
        'category' => 'Biochemistry',
        'description' => 'Present in many tissues. Marker for tissue damage, hemolysis, and lymphoma.',
        'interpretation' => 'Elevated in: MI, Hemolytic anemia, Liver disease, Lymphoma, Pulmonary embolism, Muscle injury.',
        'suggested_price' => 350,
        'default_parameters' => [
            ['name' => 'LDH', 'unit' => 'U/L', 'range_type' => 'general', 'general_range' => '140 - 280', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'LDH', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'BNP',
        'name' => 'NT-proBNP',
        'category' => 'Biochemistry',
        'description' => 'Natriuretic peptide. Used for diagnosis and monitoring of heart failure.',
        'interpretation' => '<table><tr><th>Age</th><th>Heart Failure Unlikely</th><th>Heart Failure Likely</th></tr><tr><td>&lt; 50 years</td><td>&lt; 300 pg/mL</td><td>&gt; 450 pg/mL</td></tr><tr><td>50-75 years</td><td>&lt; 300 pg/mL</td><td>&gt; 900 pg/mL</td></tr><tr><td>&gt; 75 years</td><td>&lt; 300 pg/mL</td><td>&gt; 1800 pg/mL</td></tr></table>',
        'suggested_price' => 1500,
        'default_parameters' => [
            ['name' => 'NT-proBNP', 'unit' => 'pg/mL', 'range_type' => 'general', 'general_range' => '0 - 125', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'BNP', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'HOMOCYS',
        'name' => 'Homocysteine',
        'category' => 'Biochemistry',
        'description' => 'Risk factor for cardiovascular disease. Elevated in B12/folate deficiency.',
        'interpretation' => '<table><tr><th>Level (µmol/L)</th><th>Risk</th></tr><tr><td>&lt; 15</td><td>Normal</td></tr><tr><td>15 - 30</td><td>Moderate risk</td></tr><tr><td>30 - 100</td><td>Intermediate hyperhomocysteinemia</td></tr><tr><td>&gt; 100</td><td>Severe — usually genetic</td></tr></table>',
        'suggested_price' => 900,
        'default_parameters' => [
            ['name' => 'Homocysteine', 'unit' => 'µmol/L', 'range_type' => 'general', 'general_range' => '5 - 15', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'HCY', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
];
