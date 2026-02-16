<?php

namespace App\Services;

use RuntimeException;
use UnexpectedValueException;

class StatementOfReceiptsAndExpidenturesServices
{
    public function __construct()
    {
        //
    }

    public function readSrePayload(): array
    {
        $rawJson = file_get_contents(public_path('financial-reports/blgf-sre.json'));

        if ($rawJson === false) {
            throw new RuntimeException('Failed to read BLGF SRE file.');
        }

        $payload = json_decode($rawJson, true);

        if (! is_array($payload)) {
            throw new UnexpectedValueException('Unsupported JSON structure.');
        }

        return $payload;
    }

    /**
     * Return the default signature blocks for the report.
     */
    public function getDefaultSignatures(): array
    {
        return [
            'prepared_by' => [
                'name' => 'torrefiel, erena marie oliva',
                'title' => 'administrative officer 5',
                'office' => 'Office of the City Treasurer',
            ],
            'certified_by' => [
                'name' => 'TALEGON, MA. JAZMIN MURILLO',
                'title' => 'OIC - Asst. City Treasurer',
                'office' => 'Office of the City Treasurer',
            ],
            'budget_officer' => [
                'name' => 'GALLOS, SHYLLINE ARCEO',
                'title' => 'ADMINISTRATIVE OFFICER V (BUDGET)',
                'office' => 'Office of the City Budget Officer',
            ],
            'budget_certifier' => [
                'name' => 'CHUA, MARY GRACE RESPALL',
                'title' => '',
                'office' => 'Office of the City Budget Officer',
            ],
        ];
    }
}
