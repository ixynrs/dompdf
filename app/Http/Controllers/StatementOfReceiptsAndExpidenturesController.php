<?php

namespace App\Http\Controllers;

use App\Services\StatementOfReceiptsAndExpidenturesServices;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use UnexpectedValueException;

class StatementOfReceiptsAndExpidenturesController extends Controller
{
    public function __construct(
        private StatementOfReceiptsAndExpidenturesServices $sreService
    ) {}

    /**
     * Stream the SRE report inline in the browser.
     */
    public function preview()
    {
        try {
            $payload = $this->sreService->readSrePayload();
        } catch (RuntimeException|UnexpectedValueException $e) {
            abort(500, $e->getMessage());
        }

        $signatures = $this->sreService->getDefaultSignatures();

        return Pdf::loadView('sre.pdf', [
            'rows' => $payload,
            'signatures' => $signatures,
            'lgu' => 'Manila City',
            'period' => 'Q1, 2025',
        ])
            ->setPaper('legal', 'landscape')
            ->stream('SRE-Manila-City-Q1-2025.pdf');
    }

    /**
     * Download the SRE report as PDF.
     */
    public function download()
    {
        try {
            $payload = $this->sreService->readSrePayload();
        } catch (RuntimeException) {
            throw ValidationException::withMessages([
                'file' => 'Failed to read public/financial-reports/blgf-sre.json.',
            ]);
        } catch (UnexpectedValueException) {
            throw ValidationException::withMessages([
                'file' => 'public/financial-reports/blgf-sre.json has an unsupported structure.',
            ]);
        }

        $signatures = $this->sreService->getDefaultSignatures();

        return Pdf::loadView('sre.pdf', [
            'rows' => $payload,
            'signatures' => $signatures,
            'lgu' => 'Manila City',
            'period' => 'Q1, 2025',
        ])
            ->setPaper('legal', 'landscape')
            ->download('SRE-Manila-City-Q1-2025.pdf');
    }
}
