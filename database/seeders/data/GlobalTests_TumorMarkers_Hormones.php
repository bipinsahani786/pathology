<?php
// Tumor Markers & Hormones
return [
    [
        'test_code' => 'AFP',
        'name' => 'Alpha-Fetoprotein (AFP)',
        'category' => 'Biochemistry',
        'description' => 'Tumor marker for liver, germ cell, and certain other cancers.',
        'interpretation' => '<table><tr><th>Level (ng/mL)</th><th>Significance</th></tr><tr><td>&lt; 10</td><td>Normal</td></tr><tr><td>10 - 200</td><td>Elevated — Pregnancy, Liver cirrhosis, Hepatitis</td></tr><tr><td>&gt; 400</td><td>Very High — Strongly suggestive of Hepatocellular Carcinoma or Germ cell tumor</td></tr></table>',
        'suggested_price' => 800,
        'default_parameters' => [
            [
                'name' => 'AFP (Tumor Marker)', 'unit' => 'ng/mL', 'short_code' => 'AFP', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '0', 'max_val' => '10', 'display_range' => '0 - 10']]
            ],
        ],
    ],
    [
        'test_code' => 'CEA',
        'name' => 'Carcinoembryonic Antigen (CEA)',
        'category' => 'Biochemistry',
        'description' => 'Cancer marker.',
        'interpretation' => 'Monitoring of colorectal cancer.',
        'suggested_price' => 800,
        'default_parameters' => [
            [
                'name' => 'CEA (Serum)', 'unit' => 'ng/mL', 'short_code' => 'CEA', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '0', 'max_val' => '5.0', 'display_range' => '0 - 5.0']]
            ],
        ],
    ],
    [
        'test_code' => 'CA125',
        'name' => 'Cancer Antigen 125 (CA-125)',
        'category' => 'Biochemistry',
        'description' => 'Ovarian cancer screening.',
        'interpretation' => 'Elevated in various conditions.',
        'suggested_price' => 1200,
        'default_parameters' => [
            [
                'name' => 'CA-125', 'unit' => 'U/mL', 'short_code' => 'CA125', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '0', 'max_val' => '35', 'display_range' => '0 - 35']]
            ],
        ],
    ],
    [
        'test_code' => 'T-TESTO',
        'name' => 'Testosterone (Total)',
        'category' => 'Hormones',
        'description' => 'Total serum testosterone.',
        'interpretation' => 'Low in males: Hypogonadism.',
        'suggested_price' => 800,
        'default_parameters' => [
            [
                'name' => 'Total Testosterone', 'unit' => 'ng/dL', 'short_code' => 'TESTO', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [
                    ['gender' => 'Male', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '250', 'max_val' => '1100', 'display_range' => '250 - 1100'],
                    ['gender' => 'Female', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '2', 'max_val' => '45', 'display_range' => '2 - 45'],
                ]
            ],
        ],
    ],
    [
        'test_code' => 'LH',
        'name' => 'Luteinizing Hormone (LH)',
        'category' => 'Hormones',
        'description' => 'Pituitary hormone.',
        'interpretation' => 'Regulates reproductive function.',
        'suggested_price' => 450,
        'default_parameters' => [
            [
                'name' => 'LH (Serum)', 'unit' => 'mIU/mL', 'short_code' => 'LH', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [
                    ['gender' => 'Male', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '1.5', 'max_val' => '9.3', 'display_range' => '1.5 - 9.3'],
                    ['gender' => 'Female', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '1.9', 'max_val' => '76', 'display_range' => '1.9 - 76'],
                ]
            ],
        ],
    ],
    [
        'test_code' => 'FSH',
        'name' => 'Follicle Stimulating Hormone (FSH)',
        'category' => 'Hormones',
        'description' => 'Pituitary hormone.',
        'interpretation' => 'Essential for reproductive function.',
        'suggested_price' => 450,
        'default_parameters' => [
            [
                'name' => 'FSH (Serum)', 'unit' => 'mIU/mL', 'short_code' => 'FSH', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [
                    ['gender' => 'Male', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '1.4', 'max_val' => '18.1', 'display_range' => '1.4 - 18.1'],
                    ['gender' => 'Female', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '2.5', 'max_val' => '116', 'display_range' => '2.5 - 116'],
                ]
            ],
        ],
    ],
    [
        'test_code' => 'AMH',
        'name' => 'Anti-Mullerian Hormone (AMH)',
        'category' => 'Hormones',
        'description' => 'Ovarian reserve marker.',
        'interpretation' => 'Optimal: 1.0 - 3.0 ng/mL.',
        'suggested_price' => 2500,
        'default_parameters' => [
            [
                'name' => 'AMH', 'unit' => 'ng/mL', 'short_code' => 'AMH', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '1.0', 'max_val' => '3.0', 'display_range' => '1.0 - 3.0']]
            ],
        ],
    ],
];
