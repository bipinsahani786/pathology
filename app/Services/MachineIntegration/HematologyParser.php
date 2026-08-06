<?php

namespace App\Services\MachineIntegration;

/**
 * Hematology Parser — Cellomax 5 (5-Part CBC Analyzer)
 * by INDO-MEDX Healthcare
 *
 * Cellomax 5 typically outputs a delimited text format over TCP/IP or serial.
 * Common formats:
 *   KEY=VALUE,KEY=VALUE,...
 *   or pipe-delimited with fixed columns
 *
 * Parameters: WBC, RBC, HGB, HCT, MCV, MCH, MCHC, RDW, PLT, MPV,
 *             NEUT%, LYMPH%, MONO%, EO%, BASO%,
 *             NEUT#, LYMPH#, MONO#, EO#, BASO#
 */
class HematologyParser
{
    /**
     * All standard 5-part CBC parameters this parser recognizes.
     */
    public const CBC_PARAMS = [
        // Complete Blood Count
        'WBC', 'RBC', 'HGB', 'HCT', 'MCV', 'MCH', 'MCHC', 'RDW', 'PLT', 'MPV',
        // Differential (Percentage)
        'NEUT%', 'LYMPH%', 'MONO%', 'EO%', 'BASO%',
        // Differential (Absolute)
        'NEUT#', 'LYMPH#', 'MONO#', 'EO#', 'BASO#',
    ];

    /**
     * Alternate / alias names that different Cellomax firmware versions use.
     */
    private const ALIASES = [
        'NEUTROPHIL%' => 'NEUT%',
        'LYMPHOCYTE%' => 'LYMPH%',
        'MONOCYTE%'   => 'MONO%',
        'EOSINOPHIL%' => 'EO%',
        'BASOPHIL%'   => 'BASO%',
        'NEUTROPHIL#' => 'NEUT#',
        'LYMPHOCYTE#' => 'LYMPH#',
        'MONOCYTE#'   => 'MONO#',
        'EOSINOPHIL#' => 'EO#',
        'BASOPHIL#'   => 'BASO#',
        'HEMOGLOBIN'  => 'HGB',
        'HEMATOCRIT'  => 'HCT',
        'PLATELETS'   => 'PLT',
    ];

    /**
     * Parse Cellomax output string.
     *
     * @param  string $raw
     * @return array{sample_id: string|null, results: array<string, array{value: string, unit: string}>}
     */
    public function parse(string $raw): array
    {
        $output = [
            'sample_id' => null,
            'results'   => [],
        ];

        // Try key=value comma-separated format first (most common for Cellomax 5)
        if (str_contains($raw, '=')) {
            $output = $this->parseKeyValue($raw, $output);
        }

        // Try pipe-delimited format (some firmware versions)
        if (empty($output['results']) && str_contains($raw, '|')) {
            $output = $this->parsePipeDelimited($raw, $output);
        }

        return $output;
    }

    /**
     * Parse KEY=VALUE,KEY=VALUE format.
     * Example: "SID=LAB001,WBC=7.2,RBC=4.85,HGB=14.2,HCT=42.1,MCV=86.8,PLT=245"
     */
    private function parseKeyValue(string $raw, array $output): array
    {
        $pairs = preg_split('/[,;\s]+/', trim($raw));

        foreach ($pairs as $pair) {
            if (!str_contains($pair, '=')) continue;

            [$key, $value] = explode('=', $pair, 2);
            $key   = strtoupper(trim($key));
            $value = trim($value);

            // Sample ID
            if (in_array($key, ['SID', 'SAMPLEID', 'ACCESSION', 'BARCODE', 'ID'])) {
                $output['sample_id'] = $value;
                continue;
            }

            // Normalize aliases
            $key = self::ALIASES[$key] ?? $key;

            if (in_array($key, self::CBC_PARAMS) && $value !== '') {
                $output['results'][$key] = [
                    'value' => $value,
                    'unit'  => $this->getUnit($key),
                ];
            }
        }

        return $output;
    }

    /**
     * Parse pipe-delimited format.
     */
    private function parsePipeDelimited(string $raw, array $output): array
    {
        $lines = preg_split('/[\r\n]+/', trim($raw));
        foreach ($lines as $line) {
            $fields = explode('|', $line);
            if (count($fields) >= 2) {
                $key   = strtoupper(trim($fields[0]));
                $value = trim($fields[1]);

                if ($key === 'SID') {
                    $output['sample_id'] = $value;
                    continue;
                }

                $key = self::ALIASES[$key] ?? $key;
                if (in_array($key, self::CBC_PARAMS) && $value !== '') {
                    $output['results'][$key] = [
                        'value' => $value,
                        'unit'  => $this->getUnit($key),
                    ];
                }
            }
        }
        return $output;
    }

    /**
     * Standard units for each CBC parameter.
     */
    private function getUnit(string $param): string
    {
        return match ($param) {
            'WBC'           => '10³/µL',
            'RBC'           => '10⁶/µL',
            'HGB'           => 'g/dL',
            'HCT'           => '%',
            'MCV'           => 'fL',
            'MCH'           => 'pg',
            'MCHC'          => 'g/dL',
            'RDW'           => '%',
            'PLT'           => '10³/µL',
            'MPV'           => 'fL',
            'NEUT%', 'LYMPH%', 'MONO%', 'EO%', 'BASO%' => '%',
            'NEUT#', 'LYMPH#', 'MONO#', 'EO#', 'BASO#' => '10³/µL',
            default         => '',
        };
    }

    /**
     * Generate simulated CBC data for testing (realistic normal values).
     */
    public function generateSimulated(string $sampleId): string
    {
        $data = [
            'SID'    => $sampleId,
            'WBC'    => number_format(rand(45, 110) / 10, 1),
            'RBC'    => number_format(rand(380, 550) / 100, 2),
            'HGB'    => number_format(rand(110, 170) / 10, 1),
            'HCT'    => number_format(rand(330, 500) / 10, 1),
            'MCV'    => number_format(rand(780, 980) / 10, 1),
            'MCH'    => number_format(rand(250, 340) / 10, 1),
            'MCHC'   => number_format(rand(310, 360) / 10, 1),
            'RDW'    => number_format(rand(115, 145) / 10, 1),
            'PLT'    => (string) rand(150, 400),
            'MPV'    => number_format(rand(75, 125) / 10, 1),
            'NEUT%'  => number_format(rand(450, 700) / 10, 1),
            'LYMPH%' => number_format(rand(200, 400) / 10, 1),
            'MONO%'  => number_format(rand(40, 100) / 10, 1),
            'EO%'    => number_format(rand(10, 50) / 10, 1),
            'BASO%'  => number_format(rand(2, 10) / 10, 1),
        ];

        return implode(',', array_map(
            fn($k, $v) => "$k=$v",
            array_keys($data),
            array_values($data)
        ));
    }
}
