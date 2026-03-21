<?php
// Serology, Urine, and Clinical Pathology Tests
return [
    [
        'test_code' => 'WIDAL',
        'name' => 'Widal Test',
        'category' => 'Serology & Immunology',
        'description' => 'Serological test for Typhoid fever (Salmonella Typhi and Paratyphi).',
        'interpretation' => '<table><tr><th>Antigen</th><th>Significant Titre</th><th>Indicates</th></tr><tr><td>TO (Typhi O)</td><td>&ge; 1:160</td><td>Active Typhoid infection</td></tr><tr><td>TH (Typhi H)</td><td>&ge; 1:160</td><td>Past infection or immunization</td></tr><tr><td>AO (Paratyphi A O)</td><td>&ge; 1:160</td><td>Paratyphoid A infection</td></tr><tr><td>BH (Paratyphi B H)</td><td>&ge; 1:160</td><td>Paratyphoid B infection</td></tr></table><br>Rising titre in paired samples (4-fold) is more diagnostic than a single test.',
        'suggested_price' => 250,
        'default_parameters' => [
            [
                'name' => 'S. Typhi O (TO)', 'unit' => '', 'short_code' => 'TO', 'input_type' => 'selection',
                'range_type' => 'flexible', 'formula' => '',
                'options' => ['Negative', '1:20', '1:40', '1:80', '1:160', '1:320'],
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'normal_value' => 'Negative', 'display_range' => 'Negative (< 1:80)']]
            ],
            [
                'name' => 'S. Typhi H (TH)', 'unit' => '', 'short_code' => 'TH', 'input_type' => 'selection',
                'range_type' => 'flexible', 'formula' => '',
                'options' => ['Negative', '1:20', '1:40', '1:80', '1:160', '1:320'],
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'normal_value' => 'Negative', 'display_range' => 'Negative (< 1:80)']]
            ],
            [
                'name' => 'S. Paratyphi AO', 'unit' => '', 'short_code' => 'AO', 'input_type' => 'selection',
                'range_type' => 'flexible', 'formula' => '',
                'options' => ['Negative', '1:20', '1:40', '1:80', '1:160', '1:320'],
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'normal_value' => 'Negative', 'display_range' => 'Negative (< 1:80)']]
            ],
            [
                'name' => 'S. Paratyphi BH', 'unit' => '', 'short_code' => 'BH', 'input_type' => 'selection',
                'range_type' => 'flexible', 'formula' => '',
                'options' => ['Negative', '1:20', '1:40', '1:80', '1:160', '1:320'],
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'normal_value' => 'Negative', 'display_range' => 'Negative (< 1:80)']]
            ],
        ],
    ],
    [
        'test_code' => 'RA',
        'name' => 'Rheumatoid Factor (RA Factor)',
        'category' => 'Serology & Immunology',
        'description' => 'Autoimmune marker for Rheumatoid Arthritis.',
        'interpretation' => '<table><tr><th>Level (IU/mL)</th><th>Significance</th></tr><tr><td>&lt; 14</td><td>Negative</td></tr><tr><td>14 - 50</td><td>Low Positive — Early RA possible</td></tr><tr><td>&gt; 50</td><td>High Positive — RA likely</td></tr></table>',
        'suggested_price' => 350,
        'default_parameters' => [
            [
                'name' => 'RA Factor (Quantitative)', 'unit' => 'IU/mL', 'short_code' => 'RAF', 'input_type' => 'numeric',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '0', 'max_val' => '14', 'display_range' => '0 - 14']]
            ],
        ],
    ],
    [
        'test_code' => 'ASO',
        'name' => 'ASO Titre (Anti-Streptolysin O)',
        'category' => 'Serology & Immunology',
        'description' => 'Detects antibodies against Streptococcal exotoxin. Useful for Rheumatic fever diagnosis.',
        'interpretation' => '<table><tr><th>Level (IU/mL)</th><th>Significance</th></tr><tr><td>&lt; 200</td><td>Normal</td></tr><tr><td>200 - 400</td><td>Mild elevation — Recent Strep infection</td></tr><tr><td>&gt; 400</td><td>Significant — Rheumatic fever, Post-strep GN</td></tr></table>',
        'suggested_price' => 350,
        'default_parameters' => [
            ['name' => 'ASO Titre', 'unit' => 'IU/mL', 'range_type' => 'general', 'general_range' => '0 - 200', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'ASO', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'ANA',
        'name' => 'ANA (Anti-Nuclear Antibody)',
        'category' => 'Serology & Immunology',
        'description' => 'Screening test for autoimmune diseases, especially SLE.',
        'interpretation' => '<table><tr><th>Titre</th><th>Significance</th></tr><tr><td>Negative</td><td>Unlikely SLE</td></tr><tr><td>1:40</td><td>Low — May be normal</td></tr><tr><td>1:80 - 1:160</td><td>Suggestive of autoimmune disease</td></tr><tr><td>&ge; 1:320</td><td>Highly suggestive — SLE, Sjogren, Scleroderma</td></tr></table>',
        'suggested_price' => 800,
        'default_parameters' => [
            [
                'name' => 'ANA (IFA)', 'unit' => '', 'short_code' => 'ANA', 'input_type' => 'selection',
                'range_type' => 'flexible', 'formula' => '',
                'options' => ['Negative', 'Reactive', 'Borderline'],
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'normal_value' => 'Negative', 'display_range' => 'Negative']]
            ],
            [
                'name' => 'ANA Titre', 'unit' => '', 'short_code' => 'ANAT', 'input_type' => 'selection',
                'range_type' => 'flexible', 'formula' => '',
                'options' => ['< 1:40', '1:40', '1:80', '1:160', '1:320', '1:640'],
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'normal_value' => '< 1:40', 'display_range' => '< 1:40']]
            ],
            [
                'name' => 'Pattern', 'unit' => '', 'short_code' => 'ANAP', 'input_type' => 'text',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'normal_value' => 'Not Applicable', 'display_range' => 'n/a']]
            ],
        ],
    ],
    [
        'test_code' => 'HBSAG',
        'name' => 'HBsAg (Hepatitis B Surface Antigen)',
        'category' => 'Serology & Immunology',
        'description' => 'Screening test for Hepatitis B virus infection.',
        'interpretation' => 'Reactive: Active HBV infection. Non-Reactive: No current HBV infection. Confirm reactive results with HBV DNA and HBeAg.',
        'suggested_price' => 300,
        'default_parameters' => [
            ['name' => 'HBsAg', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Non-Reactive', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'ANTIHCV',
        'name' => 'Anti-HCV (Hepatitis C Antibody)',
        'category' => 'Serology & Immunology',
        'description' => 'Screening for Hepatitis C virus exposure.',
        'interpretation' => 'Reactive: Exposure to HCV. Confirm with HCV RNA PCR for active infection.',
        'suggested_price' => 500,
        'default_parameters' => [
            ['name' => 'Anti-HCV', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Non-Reactive', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'HIV',
        'name' => 'HIV I & II (Screening)',
        'category' => 'Serology & Immunology',
        'description' => 'Screening test for Human Immunodeficiency Virus antibodies.',
        'interpretation' => 'Reactive result must be confirmed with Western Blot or HIV RNA PCR. Pre and post-test counseling recommended.',
        'suggested_price' => 400,
        'default_parameters' => [
            ['name' => 'HIV I & II Antibody', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Non-Reactive', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'VDRL',
        'name' => 'VDRL / RPR (Syphilis Screening)',
        'category' => 'Serology & Immunology',
        'description' => 'Non-treponemal screening test for syphilis.',
        'interpretation' => 'Reactive: Possible syphilis — confirm with TPHA/FTA-ABS. Biological false positives in: SLE, Pregnancy, Infections.',
        'suggested_price' => 200,
        'default_parameters' => [
            ['name' => 'VDRL', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Non-Reactive', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'DENGUE',
        'name' => 'Dengue NS1 Ag + IgM/IgG',
        'category' => 'Serology & Immunology',
        'description' => 'Combo test for Dengue virus. NS1 for early detection, IgM/IgG for immune response.',
        'interpretation' => '<table><tr><th>Marker</th><th>Positive Indicates</th><th>Window</th></tr><tr><td>NS1 Antigen</td><td>Active/Early Dengue</td><td>Day 1-7</td></tr><tr><td>IgM Antibody</td><td>Recent/Primary Dengue</td><td>Day 5-35</td></tr><tr><td>IgG Antibody</td><td>Past/Secondary Dengue</td><td>Day 7 onwards</td></tr></table>',
        'suggested_price' => 700,
        'default_parameters' => [
            ['name' => 'Dengue NS1 Antigen', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Negative', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Dengue IgM', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Negative', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Dengue IgG', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Negative', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'MALARIA',
        'name' => 'Malaria Antigen (Rapid Card)',
        'category' => 'Serology & Immunology',
        'description' => 'Rapid antigen detection for P. vivax and P. falciparum malaria.',
        'interpretation' => 'P. vivax positive: Benign malaria. P. falciparum positive: Potentially severe — treat as emergency.',
        'suggested_price' => 350,
        'default_parameters' => [
            ['name' => 'P. Vivax', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Negative', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'P. Falciparum', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Negative', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'UPT',
        'name' => 'Urine Pregnancy Test (UPT)',
        'category' => 'Clinical Pathology',
        'description' => 'Qualitative detection of human chorionic gonadotropin (hCG) in urine.',
        'interpretation' => 'Positive: Pregnancy likely. Negative: Pregnancy unlikely — repeat if period is missed. Can be positive as early as the first day of missed period.',
        'suggested_price' => 100,
        'default_parameters' => [
            ['name' => 'Urine Pregnancy Test', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Negative', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'URE',
        'name' => 'Urine Routine & Microscopy',
        'category' => 'Clinical Pathology',
        'description' => 'Complete urinalysis including physical, chemical and microscopic examination.',
        'interpretation' => '<table><tr><th>Finding</th><th>Possible Cause</th></tr><tr><td>Protein +</td><td>Kidney disease, UTI, Pre-eclampsia</td></tr><tr><td>Glucose +</td><td>Diabetes mellitus, Renal glucosuria</td></tr><tr><td>RBC in urine</td><td>Stones, UTI, Glomerulonephritis</td></tr><tr><td>Pus cells &gt; 5</td><td>Urinary tract infection</td></tr><tr><td>Casts</td><td>Kidney disease</td></tr><tr><td>Crystals</td><td>Stone disease, metabolic disorders</td></tr></table>',
        'suggested_price' => 150,
        'default_parameters' => [
            ['name' => 'Colour', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Pale Yellow', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Appearance', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Clear', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Specific Gravity', 'unit' => '', 'range_type' => 'general', 'general_range' => '1.005 - 1.030', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'SG', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'pH', 'unit' => '', 'range_type' => 'general', 'general_range' => '4.6 - 8.0', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'PH', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'Protein', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Nil', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Glucose', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Nil', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Ketone Bodies', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Nil', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Blood', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Nil', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Bilirubin', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Nil', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Pus Cells', 'unit' => '/HPF', 'range_type' => 'general', 'general_range' => '0 - 5', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'PUS', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'RBCs', 'unit' => '/HPF', 'range_type' => 'general', 'general_range' => '0 - 2', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'URBC', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'Epithelial Cells', 'unit' => '/HPF', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Few', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Casts', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Nil', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Crystals', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Nil', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Bacteria', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Nil', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'STOOL-RE',
        'name' => 'Stool Routine & Microscopy',
        'category' => 'Clinical Pathology',
        'description' => 'Complete stool examination including physical and microscopic analysis.',
        'interpretation' => 'Ova & Parasites: Identify helminth eggs, cysts. Occult blood positive: GI bleed, colorectal cancer screening. Pus cells: Inflammatory bowel, Bacterial dysentery.',
        'suggested_price' => 150,
        'default_parameters' => [
            ['name' => 'Colour', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Brown', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Consistency', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Formed', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Mucus', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Absent', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Occult Blood', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Negative', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Ova / Parasites', 'unit' => '', 'range_type' => 'value', 'general_range' => '', 'male_range' => '', 'female_range' => '', 'normal_value' => 'Not Seen', 'short_code' => '', 'input_type' => 'text', 'formula' => ''],
            ['name' => 'Pus Cells', 'unit' => '/HPF', 'range_type' => 'general', 'general_range' => '0 - 5', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'SPUS', 'input_type' => 'numeric', 'formula' => ''],
            ['name' => 'RBCs', 'unit' => '/HPF', 'range_type' => 'general', 'general_range' => '0 - 2', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'SRBC', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'PSA',
        'name' => 'PSA (Prostate Specific Antigen)',
        'category' => 'Immunology',
        'description' => 'Tumor marker for prostate cancer screening and monitoring.',
        'interpretation' => '<table><tr><th>PSA (ng/mL)</th><th>Significance</th></tr><tr><td>&lt; 4.0</td><td>Normal</td></tr><tr><td>4.0 - 10.0</td><td>Borderline — Biopsy may be needed</td></tr><tr><td>&gt; 10.0</td><td>Elevated — High risk of prostate cancer</td></tr></table>',
        'suggested_price' => 600,
        'default_parameters' => [
            ['name' => 'Total PSA', 'unit' => 'ng/mL', 'range_type' => 'general', 'general_range' => '0 - 4.0', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'PSA', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'PROLACTIN',
        'name' => 'Prolactin',
        'category' => 'Biochemistry',
        'description' => 'Hormone produced by pituitary. Elevated in pituitary tumors and PCOS.',
        'interpretation' => '<table><tr><th>Level (ng/mL)</th><th>Significance</th></tr><tr><td>Male: 2 - 18</td><td>Normal</td></tr><tr><td>Female: 2 - 29</td><td>Normal</td></tr><tr><td>30 - 100</td><td>Medications, PCOS, Hypothyroidism</td></tr><tr><td>&gt; 200</td><td>Prolactinoma likely</td></tr></table>',
        'suggested_price' => 500,
        'default_parameters' => [
            ['name' => 'Prolactin', 'unit' => 'ng/mL', 'range_type' => 'gender', 'general_range' => '', 'male_range' => '2.0 - 18.0', 'female_range' => '2.0 - 29.0', 'normal_value' => '', 'short_code' => 'PRL', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
];
