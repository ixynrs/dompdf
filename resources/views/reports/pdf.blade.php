<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>{{ $report['symbol'] }} Trial Balance</title>
        <style>
            body {
                font-family: DejaVu Sans, sans-serif;
                color: #111827;
                font-size: 12px;
                line-height: 1.35;
            }

            h1 {
                margin: 0;
                font-size: 18px;
            }

            .subhead {
                margin: 6px 0 14px;
                color: #374151;
                font-size: 12px;
            }

            table.trial-balance {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
            }

            table.trial-balance th,
            table.trial-balance td {
                border: 1px solid #d1d5db;
                padding: 6px 8px;
                vertical-align: middle;
                word-wrap: break-word;
            }

            table.trial-balance th {
                background: #f3f4f6;
                text-align: left;
                font-weight: 700;
            }

            .col-account {
                width: 56%;
            }

            .col-amount {
                width: 22%;
                text-align: right;
            }

            .section-row td {
                background: #f9fafb;
                font-weight: 700;
            }

            .totals-row td {
                font-weight: 700;
                background: #eef2ff;
            }
        </style>
    </head>
    <body>
        @php
            $trialBalance = $report['trial_balance'] ?? [];
            $formatAmount = static fn (mixed $amount): string => number_format((float) $amount, 0, '.', ',');
        @endphp

        <h1>Trial Balance View (As of {{ $trialBalance['as_of_date'] ?? ($report['end_date'] ?? 'N/A') }})</h1>
        <p class="subhead">{{ $report['name'] }} ({{ $report['symbol'] }})</p>

        <table class="trial-balance">
            <thead>
                <tr>
                    <th class="col-account">Account Name</th>
                    <th class="col-amount">Debit ($)</th>
                    <th class="col-amount">Credit ($)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($trialBalance['sections'] ?? [] as $section)
                    <tr class="section-row">
                        <td colspan="3">{{ $section['title'] }}</td>
                    </tr>

                    @foreach ($section['rows'] as $row)
                        <tr>
                            <td>{{ $row['account'] }}</td>
                            <td class="col-amount">{{ $formatAmount($row['debit']) }}</td>
                            <td class="col-amount">{{ $formatAmount($row['credit']) }}</td>
                        </tr>
                    @endforeach
                @endforeach

                <tr class="totals-row">
                    <td>TOTALS</td>
                    <td class="col-amount">${{ $formatAmount($trialBalance['totals']['debit'] ?? 0) }}</td>
                    <td class="col-amount">${{ $formatAmount($trialBalance['totals']['credit'] ?? 0) }}</td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
