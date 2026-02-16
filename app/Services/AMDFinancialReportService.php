<?php

namespace App\Services;

use RuntimeException;
use UnexpectedValueException;

class AMDFinancialReportService
{
    private const REPORT_SOURCE_PATH = 'financial-reports/amd-q1.json';

    public function reportSourcePath(): string
    {
        return self::REPORT_SOURCE_PATH;
    }

    private const ACCOUNT_DEFINITIONS = [
        ['ASSETS', 'Cash and Cash Equivalents', 'CashAndCashEquivalentsAtCarryingValue', 'max', 'debit', false],
        ['ASSETS', 'Short-term Investments', 'ShortTermInvestments', 'max', 'debit', false],
        ['ASSETS', 'Accounts Receivable, net', 'AccountsReceivableNetCurrent', 'max', 'debit', false],
        ['ASSETS', 'Inventories', 'InventoryNet', 'max', 'debit', false],
        ['ASSETS', 'Prepaid Expenses and Other Current Assets', 'PrepaidExpenseAndOtherAssetsCurrent', 'max', 'debit', false],
        ['ASSETS', 'Property and Equipment, net', 'PropertyPlantAndEquipmentNet', 'max', 'debit', false],
        ['ASSETS', 'Goodwill', 'Goodwill', 'max', 'debit', false],
        ['ASSETS', 'Acquisition-related Intangibles, net', 'IntangibleAssetsNetExcludingGoodwill', 'latest', 'debit', false],
        ['ASSETS', 'Deferred Tax Assets', 'DeferredIncomeTaxAssetsNet', 'max', 'debit', false],
        ['ASSETS', 'Other Non-current Assets', 'OtherAssetsNoncurrent', 'max', 'debit', false],
        ['LIABILITIES', 'Accounts Payable', 'AccountsPayableCurrent', 'latest', 'credit', false],
        ['LIABILITIES', 'Short-term Borrowings', 'ShortTermBorrowings', 'max', 'credit', false],
        ['LIABILITIES', 'Accrued Liabilities', 'AccruedLiabilitiesCurrentAndNoncurrent', 'max', 'credit', false],
        ['LIABILITIES', 'Other Current Liabilities', 'OtherLiabilitiesCurrent', 'max', 'credit', false],
        ['LIABILITIES', 'Long-term Debt', 'LongTermDebtNoncurrent', 'latest', 'credit', false],
        ['LIABILITIES', 'Long-term Operating Lease Liabilities', 'OperatingLeaseLiabilityNoncurrent', 'latest', 'credit', false],
        ['LIABILITIES', 'Deferred Tax Liabilities', 'DeferredIncomeTaxLiabilitiesNet', 'latest', 'credit', false],
        ['LIABILITIES', 'Other Long-term Liabilities', 'OtherLiabilitiesNoncurrent', 'latest', 'credit', false],
        ["STOCKHOLDERS' EQUITY", 'Common Stock, par value', 'CommonStockValue', 'latest', 'credit', false],
        ["STOCKHOLDERS' EQUITY", 'Additional Paid-in Capital', 'AdditionalPaidInCapitalCommonStock', 'latest', 'credit', false],
        ["STOCKHOLDERS' EQUITY", 'Retained Earnings', 'RetainedEarningsAccumulatedDeficit', 'latest', 'credit', false],
        ["STOCKHOLDERS' EQUITY", 'Treasury Stock (Contra-Equity)', 'TreasuryStockValue', 'max_abs', 'debit', true],
        ["STOCKHOLDERS' EQUITY", 'Accumulated Other Comprehensive Loss', 'AccumulatedOtherComprehensiveIncomeLossNetOfTax', 'latest', 'debit', true],
    ];

    public function readAmdQ1Payload(): array
    {
        $rawJson = file_get_contents(public_path(self::REPORT_SOURCE_PATH));

        if ($rawJson === false) {
            throw new RuntimeException('Failed to read report file.');
        }

        $payload = json_decode($rawJson, true);

        if (! is_array($payload)) {
            throw new UnexpectedValueException('Unsupported JSON structure.');
        }

        return $payload;
    }

    public function retrieveFinancialOverview(array $reportPayload): array
    {
        return [
            'symbol' => (string) ($reportPayload['symbol'] ?? ''),
            'name' => (string) ($reportPayload['name'] ?? ''),
            'quarter' => (string) ($reportPayload['quarter'] ?? ''),
            'year' => (int) ($reportPayload['year'] ?? 0),
            'start_date' => (string) ($reportPayload['startDate'] ?? ''),
            'end_date' => (string) ($reportPayload['endDate'] ?? ''),
        ];
    }

    public function prepareReportData(array $reportPayload): array
    {
        $balanceSheetRows = $reportPayload['data']['bs'] ?? [];
        if (! is_array($balanceSheetRows)) {
            $balanceSheetRows = [];
        }

        return [
            'symbol' => (string) ($reportPayload['symbol'] ?? ''),
            'name' => (string) ($reportPayload['name'] ?? ''),
            'end_date' => (string) ($reportPayload['endDate'] ?? ''),
            'trial_balance' => $this->generateTrialBalance(
                $balanceSheetRows,
                (string) ($reportPayload['endDate'] ?? '')
            ),
        ];
    }

    private function generateTrialBalance(array $balanceSheetRows, string $asOfDate): array
    {
        $sectionRows = ['ASSETS' => [], 'LIABILITIES' => [], "STOCKHOLDERS' EQUITY" => []];
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        foreach (self::ACCOUNT_DEFINITIONS as [$section, $account, $concept, $selection, $side, $forceSide]) {
            $value = $this->extractConceptValue($balanceSheetRows, $concept, $selection);

            if ($value === null) {
                continue;
            }

            $debit = 0.0;
            $credit = 0.0;

            if ($forceSide) {
                $side === 'debit' ? $debit = abs($value) : $credit = abs($value);
            } elseif ($side === 'debit') {
                $value >= 0 ? $debit = $value : $credit = abs($value);
            } else {
                $value >= 0 ? $credit = $value : $debit = abs($value);
            }

            $sectionRows[$section][] = ['account' => $account, 'debit' => $debit, 'credit' => $credit];
            $totalDebit += $debit;
            $totalCredit += $credit;
        }

        return [
            'as_of_date' => $asOfDate,
            'sections' => [
                ['title' => 'ASSETS', 'rows' => $sectionRows['ASSETS']],
                ['title' => 'LIABILITIES', 'rows' => $sectionRows['LIABILITIES']],
                ['title' => "STOCKHOLDERS' EQUITY", 'rows' => $sectionRows["STOCKHOLDERS' EQUITY"]],
            ],
            'totals' => ['debit' => $totalDebit, 'credit' => $totalCredit],
        ];
    }

    private function extractConceptValue(array $balanceSheetRows, string $concept, string $selection): ?float
    {
        $latestValue = null;
        $maxValue = null;
        $maxAbsoluteValue = null;

        foreach ($balanceSheetRows as $row) {
            if (! is_array($row) || (string) ($row['concept'] ?? '') !== $concept) {
                continue;
            }

            $value = $row['value'] ?? null;
            $unit = strtoupper((string) ($row['unit'] ?? ''));

            if (! is_numeric($value) || $unit !== 'USD') {
                continue;
            }

            $value = (float) $value;
            $latestValue = $value;
            $maxValue = $maxValue === null || $value > $maxValue ? $value : $maxValue;
            $maxAbsoluteValue = $maxAbsoluteValue === null || abs($value) > abs($maxAbsoluteValue) ? $value : $maxAbsoluteValue;
        }

        if ($latestValue === null) {
            return null;
        }

        return match ($selection) {
            'max' => $maxValue,
            'max_abs' => $maxAbsoluteValue,
            default => $latestValue,
        };
    }

    public function generateDownloadFileName(array $payload): string
    {
        $quarter = strtoupper((string) ($payload['quarter'] ?? 'Q1'));
        $fiscalYear = (string) ($payload['year'] ?? date('Y'));
        $startMonth = $this->toMonth((string) ($payload['startDate'] ?? ''));
        $endMonth = $this->toMonth((string) ($payload['endDate'] ?? ''));
        $symbol = strtoupper((string) ($payload['symbol'] ?? 'AMD'));

        return sprintf('%s-%s-%s-%s-%s.pdf', $quarter, $fiscalYear, $startMonth, $endMonth, $symbol);
    }

    private function toMonth(string $date): string
    {
        $timestamp = strtotime($date);

        if ($timestamp === false) {
            return 'UNK';
        }

        return strtoupper(date('M', $timestamp));
    }
}
