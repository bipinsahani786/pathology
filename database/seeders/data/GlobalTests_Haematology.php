<?php
// Haematology Tests
return [
    [
        'test_code' => 'CBC',
        'name' => 'Complete Blood Count (CBC)',
        'category' => 'Haematology',
        'description' => 'Evaluates overall health and detects a wide range of disorders including anemia, infection and leukemia.',
        'interpretation' => '<table><tr><th>Parameter</th><th>Low Indicates</th><th>High Indicates</th></tr><tr><td>Hemoglobin</td><td>Anemia, Blood loss</td><td>Polycythemia, Dehydration</td></tr><tr><td>WBC</td><td>Bone marrow failure, Autoimmune</td><td>Infection, Inflammation, Leukemia</td></tr><tr><td>Platelets</td><td>Thrombocytopenia, Dengue</td><td>Thrombocytosis, Infection</td></tr><tr><td>RBC</td><td>Anemia, Hemorrhage</td><td>Polycythemia Vera</td></tr></table>',
        'suggested_price' => 350,
        'default_parameters' => [
            [
                'name' => 'Hemoglobin (Hb)', 'unit' => 'g/dL', 'short_code' => 'HB', 'input_type' => 'numeric',
                'method' => 'Cyanmethemoglobin',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [
                    ['gender' => 'Male', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '13.0', 'max_val' => '17.0', 'display_range' => '13.0 - 17.0'],
                    ['gender' => 'Female', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '12.0', 'max_val' => '15.5', 'display_range' => '12.0 - 15.5'],
                ]
            ],
            [
                'name' => 'Total RBC Count', 'unit' => 'million/cumm', 'short_code' => 'RBC', 'input_type' => 'numeric',
                'method' => 'Electrical Impedance',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [
                    ['gender' => 'Male', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '4.5', 'max_val' => '5.5', 'display_range' => '4.5 - 5.5'],
                    ['gender' => 'Female', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '3.8', 'max_val' => '4.8', 'display_range' => '3.8 - 4.8'],
                ]
            ],
            [
                'name' => 'Packed Cell Volume (PCV)', 'unit' => '%', 'short_code' => 'PCV', 'input_type' => 'numeric',
                'method' => 'Calculated',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [
                    ['gender' => 'Male', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '40', 'max_val' => '50', 'display_range' => '40 - 50'],
                    ['gender' => 'Female', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '36', 'max_val' => '46', 'display_range' => '36 - 46'],
                ]
            ],
            [
                'name' => 'Mean Corpuscular Volume (MCV)', 'unit' => 'fL', 'short_code' => 'MCV', 'input_type' => 'calculated',
                'method' => 'Calculated',
                'range_type' => 'flexible', 'formula' => '({PCV} * 10) / {RBC}',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '83', 'max_val' => '101', 'display_range' => '83 - 101']]
            ],
            [
                'name' => 'MCH', 'unit' => 'pg', 'short_code' => 'MCH', 'input_type' => 'calculated',
                'method' => 'Calculated',
                'range_type' => 'flexible', 'formula' => '({HB} * 10) / {RBC}',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '27', 'max_val' => '32', 'display_range' => '27 - 32']]
            ],
            [
                'name' => 'MCHC', 'unit' => 'g/dL', 'short_code' => 'MCHC', 'input_type' => 'calculated',
                'method' => 'Calculated',
                'range_type' => 'flexible', 'formula' => '({HB} * 100) / {PCV}',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '31.5', 'max_val' => '34.5', 'display_range' => '31.5 - 34.5']]
            ],
            [
                'name' => 'Total WBC Count', 'unit' => 'cells/cumm', 'short_code' => 'WBC', 'input_type' => 'numeric',
                'method' => 'Electrical Impedance',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '4000', 'max_val' => '11000', 'display_range' => '4000 - 11000']]
            ],
            [
                'name' => 'Neutrophils', 'unit' => '%', 'short_code' => 'NEU', 'input_type' => 'numeric',
                'method' => 'Flow Cytometry / Microscopy',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '40', 'max_val' => '80', 'display_range' => '40 - 80']]
            ],
            [
                'name' => 'Lymphocytes', 'unit' => '%', 'short_code' => 'LYM', 'input_type' => 'numeric',
                'method' => 'Flow Cytometry / Microscopy',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '20', 'max_val' => '45', 'display_range' => '20 - 45']]
            ],
            [
                'name' => 'Monocytes', 'unit' => '%', 'short_code' => 'MONO', 'input_type' => 'numeric',
                'method' => 'Flow Cytometry / Microscopy',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '2', 'max_val' => '10', 'display_range' => '2 - 10']]
            ],
            [
                'name' => 'Eosinophils', 'unit' => '%', 'short_code' => 'EOS', 'input_type' => 'numeric',
                'method' => 'Flow Cytometry / Microscopy',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '1', 'max_val' => '6', 'display_range' => '1 - 6']]
            ],
            [
                'name' => 'Basophils', 'unit' => '%', 'short_code' => 'BASO', 'input_type' => 'numeric',
                'method' => 'Flow Cytometry / Microscopy',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '0', 'max_val' => '1', 'display_range' => '0 - 1']]
            ],
            [
                'name' => 'Platelet Count', 'unit' => 'lakhs/cumm', 'short_code' => 'PLT', 'input_type' => 'numeric',
                'method' => 'Electrical Impedance',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '1.5', 'max_val' => '4.5', 'display_range' => '1.5 - 4.5']]
            ],
        ],
    ],
    [
        'test_code' => 'BG-RH',
        'name' => 'Blood Group & Rh Type',
        'category' => 'Haematology',
        'description' => 'Determination of ABO blood group and Rhesus factor.',
        'interpretation' => 'Blood group is determined by the presence of antigens on red blood cells. Rh factor is important for transfusion compatibility and pregnancy.',
        'suggested_price' => 150,
        'default_parameters' => [
            [
                'name' => 'Blood Group', 'unit' => '', 'short_code' => 'BG', 'input_type' => 'selection',
                'method' => 'Slide Agglutination',
                'options' => ['A', 'B', 'AB', 'O'],
                'range_type' => 'flexible', 'formula' => '', 'ranges' => []
            ],
            [
                'name' => 'Rh Factor', 'unit' => '', 'short_code' => 'RH', 'input_type' => 'selection',
                'method' => 'Slide Agglutination',
                'options' => ['Positive', 'Negative'],
                'range_type' => 'flexible', 'formula' => '', 'ranges' => []
            ],
        ],
    ],
    [
        'test_code' => 'ESR',
        'name' => 'Erythrocyte Sedimentation Rate (ESR)',
        'category' => 'Haematology',
        'description' => 'Non-specific measure of inflammation. Westergren method.',
        'interpretation' => 'Elevated in: Infections, Autoimmune disorders, Malignancy. Westergren method is standard.',
        'suggested_price' => 100,
        'default_parameters' => [
            [
                'name' => 'ESR (Westergren)', 'unit' => 'mm/1st hr', 'short_code' => 'ESR', 'input_type' => 'numeric',
                'method' => 'Westergren Method',
                'range_type' => 'flexible', 'formula' => '',
                'ranges' => [
                    ['gender' => 'Male', 'age_min' => 0, 'age_max' => 50, 'age_unit' => 'Years', 'min_val' => '0', 'max_val' => '15', 'display_range' => '0 - 15'],
                    ['gender' => 'Male', 'age_min' => 51, 'age_max' => 150, 'age_unit' => 'Years', 'min_val' => '0', 'max_val' => '20', 'display_range' => '0 - 20'],
                    ['gender' => 'Female', 'age_min' => 0, 'age_max' => 50, 'age_unit' => 'Years', 'min_val' => '0', 'max_val' => '20', 'display_range' => '0 - 20'],
                    ['gender' => 'Female', 'age_min' => 51, 'age_max' => 150, 'age_unit' => 'Years', 'min_val' => '0', 'max_val' => '30', 'display_range' => '0 - 30'],
                ]
            ],
        ],
    ],
    [
        'test_code' => 'PS',
        'name' => 'Peripheral Blood Smear',
        'category' => 'Haematology',
        'description' => 'Microscopic examination of blood film for RBC morphology, WBC differential and platelet estimation.',
        'interpretation' => 'RBC morphology assessment helps differentiate types of anemia. WBC morphology helps identify blast cells or abnormal forms.',
        'suggested_price' => 200,
        'default_parameters' => [
            ['name' => 'RBC Morphology', 'unit' => '', 'short_code' => 'RBCM', 'input_type' => 'text', 'method' => 'Leishman Stain / Microscopy', 'range_type' => 'flexible', 'formula' => '', 'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'normal_value' => 'Normocytic Normochromic', 'display_range' => 'n/a']]],
            ['name' => 'WBC Morphology', 'unit' => '', 'short_code' => 'WBCM', 'input_type' => 'text', 'method' => 'Leishman Stain / Microscopy', 'range_type' => 'flexible', 'formula' => '', 'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'normal_value' => 'Normal', 'display_range' => 'n/a']]],
            ['name' => 'Platelet Estimate', 'unit' => '', 'short_code' => 'PLTE', 'input_type' => 'text', 'method' => 'Leishman Stain / Microscopy', 'range_type' => 'flexible', 'formula' => '', 'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'normal_value' => 'Adequate', 'display_range' => 'n/a']]],
            ['name' => 'Parasites', 'unit' => '', 'short_code' => 'PARA', 'input_type' => 'text', 'method' => 'Leishman Stain / Microscopy', 'range_type' => 'flexible', 'formula' => '', 'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'normal_value' => 'Not Seen', 'display_range' => 'n/a']]],
        ],
    ],
    [
        'test_code' => 'RETIC',
        'name' => 'Reticulocyte Count',
        'category' => 'Haematology',
        'description' => 'Measures immature red blood cells. Useful in evaluating bone marrow response.',
        'interpretation' => '<table><tr><th>Level</th><th>Significance</th></tr><tr><td>Low (&lt; 0.5%)</td><td>Bone marrow suppression, Aplastic anemia</td></tr><tr><td>Normal (0.5 - 2.5%)</td><td>Normal bone marrow function</td></tr><tr><td>High (&gt; 2.5%)</td><td>Hemolytic anemia, Blood loss recovery</td></tr></table>',
        'suggested_price' => 200,
        'default_parameters' => [
            ['name' => 'Reticulocyte Count', 'unit' => '%', 'short_code' => 'RETIC', 'input_type' => 'numeric', 'method' => 'Supravital Staining (Brilliant Cresyl Blue)', 'range_type' => 'flexible', 'formula' => '', 'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '0.5', 'max_val' => '2.5', 'display_range' => '0.5 - 2.5']]],
        ],
    ],
    [
        'test_code' => 'BT-CT',
        'name' => 'Bleeding Time & Clotting Time',
        'category' => 'Haematology',
        'description' => 'Screening tests for platelet function and coagulation pathway.',
        'interpretation' => 'Prolonged BT: Platelet disorders, Von Willebrand disease. Prolonged CT: Factor deficiency, Hemophilia, Anticoagulant therapy.',
        'suggested_price' => 150,
        'default_parameters' => [
            ['name' => 'Bleeding Time (BT)', 'unit' => 'minutes', 'short_code' => 'BT', 'input_type' => 'numeric', 'method' => 'Duke Method', 'range_type' => 'flexible', 'formula' => '', 'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '1', 'max_val' => '6', 'display_range' => '1 - 6']]],
            ['name' => 'Clotting Time (CT)', 'unit' => 'minutes', 'short_code' => 'CT', 'input_type' => 'numeric', 'method' => 'Capillary Tube Method', 'range_type' => 'flexible', 'formula' => '', 'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '4', 'max_val' => '9', 'display_range' => '4 - 9']]],
        ],
    ],
    [
        'test_code' => 'PT-INR',
        'name' => 'Prothrombin Time (PT/INR)',
        'category' => 'Haematology',
        'description' => 'Evaluates extrinsic and common coagulation pathways. Essential for warfarin monitoring.',
        'interpretation' => '<table><tr><th>INR Value</th><th>Interpretation</th></tr><tr><td>&lt; 1.1</td><td>Normal coagulation</td></tr><tr><td>2.0 - 3.0</td><td>Target for most indications on Warfarin</td></tr><tr><td>2.5 - 3.5</td><td>Target for mechanical heart valves</td></tr><tr><td>&gt; 4.0</td><td>High bleeding risk</td></tr></table>',
        'suggested_price' => 350,
        'default_parameters' => [
            ['name' => 'Prothrombin Time (PT)', 'unit' => 'seconds', 'short_code' => 'PT', 'input_type' => 'numeric', 'method' => 'Coagulometry (Clot Detection)', 'range_type' => 'flexible', 'formula' => '', 'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '11', 'max_val' => '16', 'display_range' => '11 - 16']]],
            ['name' => 'Control', 'unit' => 'seconds', 'short_code' => 'CTRL', 'input_type' => 'numeric', 'method' => 'Coagulometry (Clot Detection)', 'range_type' => 'flexible', 'formula' => '', 'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '12', 'max_val' => '16', 'display_range' => '12 - 16']]],
            ['name' => 'INR', 'unit' => '', 'short_code' => 'INR', 'input_type' => 'numeric', 'method' => 'Calculated', 'range_type' => 'flexible', 'formula' => '', 'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '0.8', 'max_val' => '1.1', 'display_range' => '0.8 - 1.1']]],
        ],
    ],
    [
        'test_code' => 'APTT',
        'name' => 'Activated Partial Thromboplastin Time (APTT)',
        'category' => 'Haematology',
        'description' => 'Evaluates intrinsic coagulation pathway. Used for heparin monitoring.',
        'interpretation' => 'Prolonged APTT: Hemophilia A/B, Von Willebrand, Heparin therapy, Lupus anticoagulant, DIC.',
        'suggested_price' => 400,
        'default_parameters' => [
            ['name' => 'APTT (Patient)', 'unit' => 'seconds', 'short_code' => 'APTT', 'input_type' => 'numeric', 'method' => 'Coagulometry (Clot Detection)', 'range_type' => 'flexible', 'formula' => '', 'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '25', 'max_val' => '35', 'display_range' => '25 - 35']]],
            ['name' => 'APTT (Control)', 'unit' => 'seconds', 'short_code' => 'APTTC', 'input_type' => 'numeric', 'method' => 'Coagulometry (Clot Detection)', 'range_type' => 'flexible', 'formula' => '', 'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '25', 'max_val' => '35', 'display_range' => '25 - 35']]],
        ],
    ],
    [
        'test_code' => 'DDIMER',
        'name' => 'D-Dimer',
        'category' => 'Haematology',
        'description' => 'Fibrin degradation product. Used to rule out DVT, PE, and DIC.',
        'interpretation' => '<table><tr><th>Range</th><th>Significance</th></tr><tr><td>&lt; 0.5 µg/mL</td><td>Normal — DVT/PE unlikely</td></tr><tr><td>0.5 - 1.0 µg/mL</td><td>Borderline — Correlate clinically</td></tr><tr><td>&gt; 1.0 µg/mL</td><td>Elevated — Consider DVT, PE, DIC</td></tr></table>',
        'suggested_price' => 800,
        'default_parameters' => [
            ['name' => 'D-Dimer', 'unit' => 'µg/mL FEU', 'short_code' => 'DDIM', 'input_type' => 'numeric', 'method' => 'Immunoturbidimetry', 'range_type' => 'flexible', 'formula' => '', 'ranges' => [['gender' => 'Both', 'age_min' => 0, 'age_max' => 120, 'age_unit' => 'Years', 'min_val' => '0', 'max_val' => '0.5', 'display_range' => '0 - 0.5']]],
        ],
    ],
];
