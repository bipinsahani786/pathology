<?php

namespace App\Services\MachineIntegration;

/**
 * Electrolyte Parser — ST-200 Plus (Sensacore / PSR Biochem)
 *
 * Measures: Na (Sodium), K (Potassium), Cl (Chloride)
 * Also optionally: iCa (Ionized Calcium), pH, pCO2, pO2 (blood gas models)
 *
 * Common output format from ST-200 Plus:
 *   Na=138.5,K=4.2,Cl=102.3
 *   or via ASTM-lite: fixed-width column output
 */
class ElectrolyteParser
{
    /**
     * All parameters this parser recognizes from ST-200 Plus.
     */
    public const ELECTROLYTE_PARAMS = [
        'NA', 'K', 'CL',       // Primary (Na, K, Cl)
        'ICa', 'CA',            // Ionized Calcium (optional module)
        'PH', 'PCO2', 'PO2',   // Blood gas (optional module)
        'HCO3', 'TCO2',        // Bicarbonate
    ];

    /**
     * Display names for each parameter.
     */
    public const PARAM_LABELS = [
        'NA'   => 'Sodium (Na)',
        'K'    => 'Potassium (K)',
        'CL'   => 'Chloride (Cl)',
        'ICa'  => 'Ionized Calcium (iCa)',
        'CA'   => 'Calcium',
        'PH'   => 'pH',
        'PCO2' => 'pCO2',
        'PO2'  => 'pO2',
        'HCO3' => 'HCO3',
        'TCO2' => 'TCO2',
    ];

    /**
     * Aliases — different firmware naming conventions.
     */
    private const ALIASES = [
        'SODIUM'     => 'NA',
        'POTASSIUM'  => 'K',
        'CHLORIDE'   => 'CL',
        'NA+'        => 'NA',
        'K+'         => 'K',
        'CL-'        => 'CL',
    ];

    /**
     * Parse ST-200 Plus output.
     *
     * @return array{sample_id: string|null, results: array<string, array{value: string, unit: string}>}
     */
    public function parse(string $raw): array
    {
        $output = [
            'sample_id' => null,
            'results'   => [],
        ];

        $raw = trim($raw);

        // Format 1: KEY=VALUE pairs (comma or semicolon separated)
        if (str_contains($raw, '=')) {
            $pairs = preg_split('/[,;\s]+/', $raw);
            foreach ($pairs as $pair) {
                if (!str_contains($pair, '=')) continue;
                [$key, $value] = explode('=', $pair, 2);
                $key   = strtoupper(trim($key));
                $value = trim($value);

                if (in_array($key, ['SID', 'ID', 'SAMPLEID', 'ACCESSION'])) {
                    $output['sample_id'] = $value;
                    continue;
                }

                $key = self::ALIASES[$key] ?? $key;
                if (in_array($key, self::ELECTROLYTE_PARAMS) && $value !== '') {
                    $output['results'][$key] = [
                        'value' => $value,
                        'unit'  => $this->getUnit($key),
                    ];
                }
            }
        }

        // Format 2: Fixed-width / line-by-line format
        // e.g.:  "Na   138.5 mmol/L"
        if (empty($output['results'])) {
            $lines = preg_split('/[\r\n]+/', $raw);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                if (preg_match('/^(Na|K|Cl|NA|iCa|CA|pH|pCO2|pO2)\s+([\d.]+)/i', $line, $m)) {
                    $key   = strtoupper(trim($m[1]));
                    $value = trim($m[2]);
                    $key   = self::ALIASES[$key] ?? $key;

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
     * Standard units for each electrolyte parameter.
     */
    private function getUnit(string $param): string
    {
        return match (strtoupper($param)) {
            'NA', 'K', 'CL', 'HCO3', 'TCO2' => 'mmol/L',
            'ICa', 'CA'                       => 'mmol/L',
            'PH'                              => '',
            'PCO2', 'PO2'                     => 'mmHg',
            default                           => 'mmol/L',
        };
    }

    /**
     * Generate simulated electrolyte data for testing.
     */
    public function generateSimulated(string $sampleId): string
    {
        $na = number_format(rand(1350, 1450) / 10, 1);
        $k  = number_format(rand(35, 55) / 10, 1);
        $cl = number_format(rand(980, 1080) / 10, 1);

        return "SID={$sampleId},NA={$na},K={$k},CL={$cl}";
    }
}
