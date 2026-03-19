<?php
// Tumor Markers & Hormones
return [
    [
        'test_code' => 'AFP',
        'name' => 'Alpha-Fetoprotein (AFP)',
        'category' => 'Tumor Markers',
        'description' => 'Tumor marker for liver, germ cell, and certain other cancers.',
        'interpretation' => '<table><tr><th>Level (ng/mL)</th><th>Significance</th></tr><tr><td>&lt; 10</td><td>Normal</td></tr><tr><td>10 - 200</td><td>Elevated — Pregnancy, Liver cirrhosis, Hepatitis</td></tr><tr><td>&gt; 400</td><td>Very High — Strongly suggestive of Hepatocellular Carcinoma or Germ cell tumor</td></tr></table>',
        'suggested_price' => 800,
        'default_parameters' => [
            ['name' => 'AFP (Tumor Marker)', 'unit' => 'ng/mL', 'range_type' => 'general', 'general_range' => '0 - 10', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'AFP', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'CEA',
        'name' => 'Carcinoembryonic Antigen (CEA)',
        'category' => 'Tumor Markers',
        'description' => 'Cancer marker used for diagnosis and monitoring of colorectal and other cancers.',
        'interpretation' => '<table><tr><th>Level (ng/mL)</th><th>Status</th></tr><tr><td>&lt; 3.0</td><td>Non-smokers (Normal)</td></tr><tr><td>&lt; 5.0</td><td>Smokers (Normal)</td></tr><tr><td>&gt; 5.0</td><td>Elevated — GI cancer, Chronic inflammation</td></tr></table>',
        'suggested_price' => 800,
        'default_parameters' => [
            ['name' => 'CEA (Serum)', 'unit' => 'ng/mL', 'range_type' => 'general', 'general_range' => '0 - 5.0', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'CEA', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'CA125',
        'name' => 'Cancer Antigen 125 (CA-125)',
        'category' => 'Tumor Markers',
        'description' => 'Tumor marker used for screening and monitoring of ovarian cancer.',
        'interpretation' => '<table><tr><th>Level (U/mL)</th><th>Significance</th></tr><tr><td>&lt; 35</td><td>Normal</td></tr><tr><td>&gt; 35</td><td>Elevated — Ovarian cancer, Endometriosis, Pelvic inflammatory disease, Pregnancy</td></tr></table>',
        'suggested_price' => 1200,
        'default_parameters' => [
            ['name' => 'CA-125', 'unit' => 'U/mL', 'range_type' => 'general', 'general_range' => '0 - 35', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'CA125', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'T-TESTO',
        'name' => 'Testosterone (Total)',
        'category' => 'Hormones',
        'description' => 'Measures total serum testosterone level.',
        'interpretation' => '<table><tr><th>Gender</th><th>Normal Range (ng/dL)</th></tr><tr><td>Male</td><td>250 - 1100</td></tr><tr><td>Female</td><td>2 - 45</td></tr></table><br>Low in males: Hypogonadism, Aging. High in females: PCOS, Adrenal hyperplasia.',
        'suggested_price' => 800,
        'default_parameters' => [
            ['name' => 'Total Testosterone', 'unit' => 'ng/dL', 'range_type' => 'gender', 'general_range' => '', 'male_range' => '250 - 1100', 'female_range' => '2 - 45', 'normal_value' => '', 'short_code' => 'TESTO', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'LH',
        'name' => 'Luteinizing Hormone (LH)',
        'category' => 'Hormones',
        'description' => 'Pituitary hormone regulating reproductive function.',
        'interpretation' => 'Elevated in: PCOS, Primary ovarian/testicular failure, Menopause. Low in: Pituitary/Hypothalamic dysfunction.',
        'suggested_price' => 450,
        'default_parameters' => [
            ['name' => 'LH (Serum)', 'unit' => 'mIU/mL', 'range_type' => 'gender', 'general_range' => '', 'male_range' => '1.5 - 9.3', 'female_range' => 'Follicular: 1.9-12.5, Peak: 8.7-76, Luteal: 0.5-16.9', 'normal_value' => '', 'short_code' => 'LH', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'FSH',
        'name' => 'Follicle Stimulating Hormone (FSH)',
        'category' => 'Hormones',
        'description' => 'Pituitary hormone essential for pubertal development and reproductive function.',
        'interpretation' => 'Elevated FSH in menopause and primary gonadal failure. Useful in infertility evaluation.',
        'suggested_price' => 450,
        'default_parameters' => [
            ['name' => 'FSH (Serum)', 'unit' => 'mIU/mL', 'range_type' => 'gender', 'general_range' => '', 'male_range' => '1.4 - 18.1', 'female_range' => 'Follicular: 2.5-10.2, Peak: 3.4-33.4, Luteal: 1.5-9.1, Post-menop: 23-116', 'normal_value' => '', 'short_code' => 'FSH', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'AMH',
        'name' => 'Anti-Mullerian Hormone (AMH)',
        'category' => 'Hormones',
        'description' => 'Marker for ovarian reserve and egg count.',
        'interpretation' => '<table><tr><th>Level (ng/mL)</th><th>Ovarian Reserve</th></tr><tr><td>&gt; 3.0</td><td>High (Likely PCOS)</td></tr><tr><td>1.0 - 3.0</td><td>Normal / Optimal</td></tr><tr><td>0.7 - 0.9</td><td>Low / Diminished</td></tr><tr><td>&lt; 0.3</td><td>Very Low / Severely diminished</td></tr></table>',
        'suggested_price' => 2500,
        'default_parameters' => [
            ['name' => 'AMH', 'unit' => 'ng/mL', 'range_type' => 'general', 'general_range' => '1.0 - 3.0', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'AMH', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
];
