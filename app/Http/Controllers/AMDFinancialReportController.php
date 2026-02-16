<?php

namespace App\Http\Controllers;

use App\Services\AMDFinancialReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\ValidationException;
use JsonException;
use RuntimeException;
use UnexpectedValueException;

class AMDFinancialReportController extends Controller
{
    public function __construct(
        private AMDFinancialReportService $financialReportService
    ) {}

    public function preview()
    {
        try {
            $reportPayload = $this->financialReportService->readAmdQ1Payload();
        } catch (RuntimeException|JsonException|UnexpectedValueException $e) {
            abort(500, $e->getMessage());
        }

        $report = $this->financialReportService->prepareReportData($reportPayload);

        return Pdf::loadView('reports.pdf', [
            'report' => $report,
        ])
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    public function download()
    {
        try {
            $reportPayload = $this->financialReportService->readAmdQ1Payload();
        } catch (RuntimeException) {
            throw ValidationException::withMessages([
                'file' => 'Failed to read public/financial-reports/amd-q1.json.',
            ]);
        } catch (JsonException) {
            throw ValidationException::withMessages([
                'file' => 'public/financial-reports/amd-q1.json contains invalid JSON.',
            ]);
        } catch (UnexpectedValueException) {
            throw ValidationException::withMessages([
                'file' => 'public/financial-reports/amd-q1.json has an unsupported structure.',
            ]);
        }

        $report = $this->financialReportService->prepareReportData($reportPayload);
        $downloadName = $this->financialReportService->generateDownloadFileName($reportPayload);

        return Pdf::loadView('reports.pdf', [
            'report' => $report,
        ])
            ->setPaper('a4', 'portrait')
            ->download($downloadName);
    }
}
