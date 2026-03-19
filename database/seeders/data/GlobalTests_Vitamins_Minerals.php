<?php
// Vitamins & Minerals
return [
    [
        'test_code' => 'FOLATE',
        'name' => 'Serum Folate (Folic Acid)',
        'category' => 'Biochemistry',
        'description' => 'Measures Vitamin B9 level. Vital for DNA synthesis and RBC formation.',
        'interpretation' => '<table><tr><th>Level (ng/mL)</th><th>Status</th></tr><tr><td>&lt; 3.0</td><td>Deficiency</td></tr><tr><td>3.0 - 5.8</td><td>Borderline</td></tr><tr><td>&gt; 5.9</td><td>Normal</td></tr></table><br>Low levels seen in: Malnutrition, Alcoholism, Malabsorption, Pregnancy.',
        'suggested_price' => 700,
        'default_parameters' => [
            ['name' => 'Folic Acid', 'unit' => 'ng/mL', 'range_type' => 'general', 'general_range' => '5.9 - 24.8', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'FOL', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'MAG',
        'name' => 'Serum Magnesium',
        'category' => 'Biochemistry',
        'description' => 'Essential mineral for muscle and nerve function, and heart rhythm.',
        'interpretation' => 'Low (Hypomagnesemia): Malabsorption, Chronic diarrhea, Alcoholism, Diuretics. High (Hypermagnesemia): Renal failure, Antacid overuse.',
        'suggested_price' => 250,
        'default_parameters' => [
            ['name' => 'Magnesium', 'unit' => 'mg/dL', 'range_type' => 'general', 'general_range' => '1.7 - 2.5', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'MG', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'ZINC',
        'name' => 'Serum Zinc',
        'category' => 'Biochemistry',
        'description' => 'Trace element vital for immunity, wound healing and DNA synthesis.',
        'interpretation' => 'Low levels associated with: Poor wound healing, Hair loss, Immune dysfunction, Growth retardation in children.',
        'suggested_price' => 800,
        'default_parameters' => [
            ['name' => 'Serum Zinc', 'unit' => 'µg/dL', 'range_type' => 'general', 'general_range' => '60 - 120', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'ZN', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'COPPER',
        'name' => 'Serum Copper',
        'category' => 'Biochemistry',
        'description' => 'Essential trace mineral. Evaluates Wilson disease and nutritional status.',
        'interpretation' => 'Low: Wilson disease (with low ceruloplasmin), Menkes syndrome. High: Pregnancy, Oral contraceptives, Infections, Liver disease.',
        'suggested_price' => 800,
        'default_parameters' => [
            ['name' => 'Serum Copper', 'unit' => 'µg/dL', 'range_type' => 'general', 'general_range' => '70 - 140', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'CU', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
    [
        'test_code' => 'VITA',
        'name' => 'Vitamin A (Retinol)',
        'category' => 'Biochemistry',
        'description' => 'Fat-soluble vitamin essential for vision, immune function and reproduction.',
        'interpretation' => 'Deficiency causes night blindness and xerophthalmia. Toxicity (hypervitaminosis A) causes liver damage and CNS pressure.',
        'suggested_price' => 1500,
        'default_parameters' => [
            ['name' => 'Vitamin A', 'unit' => 'µg/dL', 'range_type' => 'general', 'general_range' => '30 - 80', 'male_range' => '', 'female_range' => '', 'normal_value' => '', 'short_code' => 'VITA', 'input_type' => 'numeric', 'formula' => ''],
        ],
    ],
];
