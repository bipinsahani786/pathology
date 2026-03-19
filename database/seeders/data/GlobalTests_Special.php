<?php
// Special Tests, Autoimmune, and PCR
return [
    [
        'test_code' => 'COV19',
        'name' => 'COVID-19 RT-PCR (Qualitative)',
        'category' => 'Molecular Diagnostics',
        'description' => 'Real-time PCR for detection of SARS-CoV-2 RNA.',
        'interpretation' => 'Positive: Viral RNA detected — suggests active infection. Negative: Not detected — if symptoms persist, re-test after 48-72 hours. Note: Result depends on viral load and sample quality (Nasal/Oropharyngeal swalb).',
        'suggested_price' => 1200,
        'default_parameters' => [
            ['name' => 'SARS-CoV-2 RNA', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Not Detected', 'short_code' => 'COV2', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Ct Value (Target 1)', 'unit' => '', 'range_type' => 'general', 'general_range' => '> 35', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'CT1', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'Ct Value (Target 2)', 'unit' => '', 'range_type' => 'general', 'general_range' => '> 35', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'CT2', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'HLAB27',
        'name' => 'HLA-B27 (Flow Cytometry)',
        'category' => 'Immunology',
        'description' => 'Human Leukocyte Antigen B*27. Strongly associated with Ankylosing Spondylitis.',
        'interpretation' => 'B*27 Positive: High association with Spondyloarthropathies (Ankylosing Spondylitis, Reactive Arthritis). Negative: Low probability of association, but does not rule out disease.',
        'suggested_price' => 2500,
        'default_parameters' => [
            ['name' => 'HLA-B*27', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Negative', 'short_code' => 'B27', 'input_type' => 'text', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'HBVDNA-Q',
        'name' => 'HBV DNA Quantitative (Real Time PCR)',
        'category' => 'Molecular Diagnostics',
        'description' => 'Viral load monitoring for Hepatitis B virus.',
        'interpretation' => 'Used to monitor antiviral therapy response and assess infectivity. Lower Limit of Detection: 10 IU/mL.',
        'suggested_price' => 4500,
        'default_parameters' => [
            ['name' => 'Viral Load', 'unit' => 'IU/mL', 'range_type' => 'general', 'general_range' => 'Not Detected', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'HBVQ', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'Log 10 Value', 'unit' => 'log10', 'range_type' => 'general', 'general_range' => '0', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'HBVLOG', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'ALLERGY-P',
        'name' => 'Allergy Panel (Comprehensive)',
        'category' => 'Immunology',
        'description' => 'Measures IgE antibodies against common food and respiratory allergens.',
        'interpretation' => 'Class 0: Absent or undetectable. Class 1-2: Low to moderate. Class 3-4: Strong positive. Class 5-6: Extremely high risk of allergic reaction.',
        'suggested_price' => 5500,
        'default_parameters' => [
            ['name' => 'Total IgE', 'unit' => 'IU/mL', 'range_type' => 'general', 'general_range' => '0 - 100', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'IGE', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'Dust Allergy', 'unit' => 'kU/l', 'range_type' => 'general', 'general_range' => '< 0.35', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'DUST', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'Milk Allergy', 'unit' => 'kU/l', 'range_type' => 'general', 'general_range' => '< 0.35', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'MILK', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'Wheat Allergy', 'unit' => 'kU/l', 'range_type' => 'general', 'general_range' => '< 0.35', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'WHEAT', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
];
