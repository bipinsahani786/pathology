<?php
// Special Tests, Autoimmune, and PCR
return [
    [
        'test_code' => 'COV19',
        'name' => 'COVID-19 RT-PCR (Qualitative)',
        'category' => 'Molecular Biology',
        'description' => 'Real-time PCR for detection of SARS-CoV-2 RNA.',
        'interpretation' => 'Positive: Viral RNA detected — suggests active infection. Negative: Not detected — if symptoms persist, re-test after 48-72 hours. Note: Result depends on viral load and sample quality (Nasal/Oropharyngeal swalb).',
        'suggested_price' => 1200,
        'default_parameters' => [
            [
                'name' => 'SARS-CoV-2 RNA', 'unit' => '', 'short_code' => 'COV2', 'input_type' => 'selection',
                'range_type' => 'flexible', 'formula' => '',
                'options' => ['Not Detected', 'Detected', 'Inconclusive'],
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'normal_value' => 'Not Detected', 'display_range' => 'Not Detected']]
            ],
            [
                'name' => 'Ct Value (Target 1)', 'unit' => '', 'short_code' => 'CT1', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '35', 'max_val' => '50', 'display_range' => '> 35']]
            ],
            [
                'name' => 'Ct Value (Target 2)', 'unit' => '', 'short_code' => 'CT2', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '35', 'max_val' => '50', 'display_range' => '> 35']]
            ],
        ],
    ],
    [
        'test_code' => 'HLAB27',
        'name' => 'HLA-B27 (Flow Cytometry)',
        'category' => 'Serology & Immunology',
        'description' => 'Human Leukocyte Antigen B*27.',
        'interpretation' => 'B*27 Positive: High association with Spondyloarthropathies.',
        'suggested_price' => 2500,
        'default_parameters' => [
            [
                'name' => 'HLA-B*27', 'unit' => '', 'short_code' => 'B27', 'input_type' => 'selection',
                'range_type' => 'flexible', 'formula' => '',
                'options' => ['Negative', 'Positive'],
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'normal_value' => 'Negative', 'display_range' => 'Negative']]
            ],
        ],
    ],
    [
        'test_code' => 'HBVDNA-Q',
        'name' => 'HBV DNA Quantitative (Real Time PCR)',
        'category' => 'Molecular Diagnostics',
        'description' => 'Viral load monitoring.',
        'interpretation' => 'Lower Limit of Detection: 10 IU/mL.',
        'suggested_price' => 4500,
        'default_parameters' => [
            [
                'name' => 'Viral Load', 'unit' => 'IU/mL', 'short_code' => 'HBVQ', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'normal_value' => 'Not Detected', 'display_range' => 'Not Detected']]
            ],
        ],
    ],
    [
        'test_code' => 'ALLERGY-P',
        'name' => 'Allergy Panel (Comprehensive)',
        'category' => 'Immunology',
        'description' => 'IgE antibodies panel.',
        'interpretation' => 'Class 0-6 risk assessment.',
        'suggested_price' => 5500,
        'default_parameters' => [
            [
                'name' => 'Total IgE', 'unit' => 'IU/mL', 'short_code' => 'IGE', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '0', 'max_val' => '100', 'display_range' => '0 - 100']]
            ],
        ],
    ],
];
