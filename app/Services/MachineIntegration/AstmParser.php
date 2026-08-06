<?php

namespace App\Services\MachineIntegration;

/**
 * ASTM E1394 Protocol Parser
 *
 * Handles biochemistry analyzers like:
 * - SRP Healthcare Analyzer
 * - AURA Chem 120
 *
 * ASTM message format:
 * H|\^&|||MACHINE_NAME|||||||P|1|timestamp
 * P|1||PAT_ID|Patient Name
 * O|1|SAMPLE_ID||^^^TEST_NAME|||||||R
 * R|1|^^^PARAM_NAME|VALUE|UNIT|REF_RANGE||||F
 * L|1|N
 */
class AstmParser
{
    /**
     * Parse a full ASTM message string into structured data.
     *
     * @param  string $raw  Raw ASTM string (may contain \r or \n delimiters)
     * @return array{
     *   sample_id: string|null,
     *   patient_id: string|null,
     *   patient_name: string|null,
     *   results: array<string, array{value: string, unit: string, ref_range: string}>
     * }
     */
    public function parse(string $raw): array
    {
        $output = [
            'sample_id'    => null,
            'patient_id'   => null,
            'patient_name' => null,
            'results'      => [],
        ];

        // Normalize line endings — ASTM uses \r (CR) as record separator
        $lines = preg_split('/[\r\n]+/', trim($raw));

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $recordType = substr($line, 0, 1);
            $fields     = explode('|', $line);

            switch ($recordType) {
                // Patient Record
                case 'P':
                    $output['patient_id']   = $this->clean($fields[2] ?? '');
                    $output['patient_name'] = $this->cleanPatientName($fields[5] ?? $fields[4] ?? '');
                    break;

                // Order Record — contains sample/accession ID
                case 'O':
                    if (!empty($fields[2])) {
                        $output['sample_id'] = $this->clean($fields[2]);
                    }
                    break;

                // Result Record — each parameter result
                case 'R':
                    $paramRaw  = $fields[2] ?? '';
                    $value     = $this->clean($fields[3] ?? '');
                    $unit      = $this->clean($fields[4] ?? '');
                    $refRange  = $this->clean($fields[5] ?? '');

                    // ASTM param format: ^^^PARAM_NAME or ^TEST^PARAM
                    $paramName = $this->extractParamName($paramRaw);

                    if ($paramName && $value !== '') {
                        $output['results'][$paramName] = [
                            'value'     => $value,
                            'unit'      => $unit,
                            'ref_range' => $refRange,
                        ];
                    }
                    break;
            }
        }

        return $output;
    }

    /**
     * Extract the parameter name from ASTM ^^^PARAM or ^TEST^PARAM format.
     */
    private function extractParamName(string $raw): string
    {
        // Remove leading ^ characters and get last meaningful segment
        $parts = explode('^', $raw);
        foreach (array_reverse($parts) as $part) {
            $part = trim($part);
            if (!empty($part)) {
                return strtoupper($part);
            }
        }
        return '';
    }

    private function clean(string $value): string
    {
        return trim(strip_tags($value));
    }

    private function cleanPatientName(string $raw): string
    {
        // ASTM name format: "Last^First^Middle" → "First Last"
        $parts = explode('^', $raw);
        $last  = trim($parts[0] ?? '');
        $first = trim($parts[1] ?? '');
        return trim("$first $last") ?: $raw;
    }

    /**
     * Generate a realistic fake ASTM message for simulation/testing.
     */
    public function generateSimulatedAstm(string $sampleId, string $testType = 'lft'): string
    {
        $ts = now()->format('YmdHis');

        $results = match ($testType) {
            'lft' => [
                'SGOT'        => ['45.2', 'U/L', '10-40'],
                'SGPT'        => ['38.7', 'U/L', '7-56'],
                'ALP'         => ['89.0', 'U/L', '44-147'],
                'BILIRUBIN-T' => ['0.8', 'mg/dL', '0.2-1.2'],
                'BILIRUBIN-D' => ['0.3', 'mg/dL', '0-0.4'],
                'PROTEIN-T'   => ['7.2', 'g/dL', '6.0-8.3'],
                'ALBUMIN'     => ['4.1', 'g/dL', '3.5-5.2'],
                'GLOBULIN'    => ['3.1', 'g/dL', '2.0-3.5'],
            ],
            'rft' => [
                'UREA'        => ['28.5', 'mg/dL', '15-45'],
                'CREATININE'  => ['1.1', 'mg/dL', '0.6-1.2'],
                'URIC-ACID'   => ['5.4', 'mg/dL', '3.5-7.2'],
                'GLUCOSE'     => ['95.0', 'mg/dL', '70-110'],
            ],
            'lipid' => [
                'CHOLESTEROL' => ['185.0', 'mg/dL', '<200'],
                'TRIGLYCERIDE'=> ['140.0', 'mg/dL', '<150'],
                'HDL'         => ['48.0', 'mg/dL', '>40'],
                'LDL'         => ['109.0', 'mg/dL', '<130'],
                'VLDL'        => ['28.0', 'mg/dL', '<30'],
            ],
            default => [
                'GLUCOSE' => ['95.0', 'mg/dL', '70-110'],
            ],
        };

        $lines   = [];
        $lines[] = "H|\\^&|||SIMULATOR^ASTM|||||||P|1|$ts";
        $lines[] = "P|1||PT001|Simulated^Patient";
        $lines[] = "O|1|$sampleId||^^^{$testType}|||||||R";

        $i = 1;
        foreach ($results as $param => [$val, $unit, $ref]) {
            $lines[] = "R|$i|^^^$param|$val|$unit|$ref||||F";
            $i++;
        }

        $lines[] = "L|1|N";

        return implode("\r", $lines);
    }
}
