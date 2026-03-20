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
            [
                'name' => 'Total Cholesterol', 'unit' => 'mg/dL', 'short_code' => 'TC', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '125', 'max_val' => '200', 'display_range' => '125 - 200']]
            ],
            [
                'name' => 'Triglycerides', 'unit' => 'mg/dL', 'short_code' => 'TG', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '25', 'max_val' => '150', 'display_range' => '25 - 150']]
            ],
            [
                'name' => 'HDL Cholesterol', 'unit' => 'mg/dL', 'short_code' => 'HDL', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [
                    ['gender' => 'Male', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '35', 'max_val' => '80', 'display_range' => '35 - 80'],
                    ['gender' => 'Female', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '40', 'max_val' => '88', 'display_range' => '40 - 88'],
                ]
            ],
            [
                'name' => 'VLDL Cholesterol', 'unit' => 'mg/dL', 'short_code' => 'VLDL', 'input_type' => 'calculated',
                'range_type' => 'flexible', 'formula' => '{TG} / 5',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '5', 'max_val' => '30', 'display_range' => '5 - 30']]
            ],
            [
                'name' => 'LDL Cholesterol', 'unit' => 'mg/dL', 'short_code' => 'LDL', 'input_type' => 'calculated',
                'range_type' => 'flexible', 'formula' => '{TC} - {HDL} - {VLDL}',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '0', 'max_val' => '100', 'display_range' => '< 100']]
            ],
            [
                'name' => 'TC/HDL Ratio', 'unit' => '', 'short_code' => 'TCHDL', 'input_type' => 'calculated',
                'range_type' => 'flexible', 'formula' => '{TC} / {HDL}',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '0', 'max_val' => '5.0', 'display_range' => '< 5.0']]
            ],
        ],
    ],
    [
        'test_code' => 'TROP-I',
        'name' => 'Troponin I',
        'category' => 'Biochemistry',
        'description' => 'Cardiac biomarker.',
        'interpretation' => 'Rises after myocardial injury.',
        'suggested_price' => 900,
        'default_parameters' => [
            [
                'name' => 'Troponin I', 'unit' => 'ng/mL', 'short_code' => 'TROPI', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '0', 'max_val' => '0.04', 'display_range' => '0 - 0.04']]
            ],
        ],
    ],
    [
        'test_code' => 'CPK-MB',
        'name' => 'CPK-MB (CK-MB)',
        'category' => 'Biochemistry',
        'description' => 'Cardiac isoenzyme.',
        'interpretation' => 'Elevated in MI.',
        'suggested_price' => 500,
        'default_parameters' => [
            [
                'name' => 'CPK-Total', 'unit' => 'U/L', 'short_code' => 'CPK', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [
                    ['gender' => 'Male', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '39', 'max_val' => '308', 'display_range' => '39 - 308'],
                    ['gender' => 'Female', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '26', 'max_val' => '192', 'display_range' => '26 - 192'],
                ]
            ],
            [
                'name' => 'CPK-MB', 'unit' => 'U/L', 'short_code' => 'CPKMB', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '0', 'max_val' => '25', 'display_range' => '< 25']]
            ],
        ],
    ],
    [
        'test_code' => 'LDH',
        'name' => 'Lactate Dehydrogenase (LDH)',
        'category' => 'Biochemistry',
        'description' => 'Marker for tissue damage.',
        'interpretation' => 'Elevated in various conditions.',
        'suggested_price' => 350,
        'default_parameters' => [
            [
                'name' => 'LDH', 'unit' => 'U/L', 'short_code' => 'LDH', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '140', 'max_val' => '280', 'display_range' => '140 - 280']]
            ],
        ],
    ],
    [
        'test_code' => 'BNP',
        'name' => 'NT-proBNP',
        'category' => 'Biochemistry',
        'description' => 'Natriuretic peptide.',
        'interpretation' => 'Heart failure marker.',
        'suggested_price' => 1500,
        'default_parameters' => [
            [
                'name' => 'NT-proBNP', 'unit' => 'pg/mL', 'short_code' => 'BNP', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [
                    ['gender' => 'Both', 'age_min' => 0, 'age_max' => 50, 'age_unit' => 'Years', 'min_val' => '0', 'max_val' => '300', 'display_range' => '< 300'],
                    ['gender' => 'Both', 'age_min' => 51, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '0', 'max_val' => '900', 'display_range' => '< 900'],
                ]
            ],
        ],
    ],
    [
        'test_code' => 'HOMOCYS',
        'name' => 'Homocysteine',
        'category' => 'Biochemistry',
        'description' => 'Risk factor for heart disease.',
        'interpretation' => 'Elevated in B12/folate deficiency.',
        'suggested_price' => 900,
        'default_parameters' => [
            [
                'name' => 'Homocysteine', 'unit' => 'µmol/L', 'short_code' => 'HCY', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '5', 'max_val' => '15', 'display_range' => '5 - 15']]
            ],
        ],
    ],
];
