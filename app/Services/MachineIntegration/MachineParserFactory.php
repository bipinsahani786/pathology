<?php

namespace App\Services\MachineIntegration;

use App\Models\MachineIntegration;

/**
 * Factory — picks the correct parser for a given machine type/protocol.
 */
class MachineParserFactory
{
    /**
     * Parse raw machine data using the correct parser for the given machine.
     *
     * @return array{sample_id: string|null, patient_id?: string|null, results: array}
     */
    public static function parse(MachineIntegration $machine, string $rawData): array
    {
        $parser = self::getParser($machine);
        return $parser->parse($rawData);
    }

    /**
     * Get the parser instance for a machine.
     */
    public static function getParser(MachineIntegration $machine): AstmParser|HematologyParser|ElectrolyteParser
    {
        // First check by machine_type, then override by protocol
        return match (true) {
            $machine->machine_type === 'hematology'              => new HematologyParser(),
            $machine->machine_type === 'electrolyte'             => new ElectrolyteParser(),
            $machine->protocol === 'astm'                        => new AstmParser(),
            $machine->machine_type === 'biochemistry'            => new AstmParser(),
            default                                              => new AstmParser(),
        };
    }

    /**
     * Generate simulated data for a machine (for testing without physical machine).
     *
     * @param  MachineIntegration $machine
     * @param  string             $sampleId
     * @param  string             $testType  For biochemistry: 'lft', 'rft', 'lipid'
     * @return string Raw simulated data string
     */
    public static function generateSimulated(
        MachineIntegration $machine,
        string $sampleId,
        string $testType = 'lft'
    ): string {
        return match ($machine->machine_type) {
            'hematology'  => (new HematologyParser())->generateSimulated($sampleId),
            'electrolyte' => (new ElectrolyteParser())->generateSimulated($sampleId),
            default       => (new AstmParser())->generateSimulatedAstm($sampleId, $testType),
        };
    }
}
