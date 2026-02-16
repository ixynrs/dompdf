<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Statement of Receipts and Expenditures</title>
    <style>
        @page {
            margin: 30px 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #000;
            font-size: 8px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        /* ---- Header ---- */
        .header-block {
            margin-bottom: 5px;
        }

        .header-block p {
            margin: 0;
            font-size: 8px;
        }

        .report-title {
            text-align: center;
            font-size: 10px;
            margin: 10px 0 10px 0;
        }

        /* ---- Meta (LGU / Period) ---- */
        .meta-table {
            margin-bottom: 6px;
        }

        .meta-table td {
            border: none;
            padding: 1px 4px;
            font-size: 8px;
        }

        /* ---- Data table ---- */
        .sre-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 7px;
        }

        .sre-table th,
        .sre-table td {
            border: 1px solid #000;
            padding: 2px 4px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .sre-table th {
            text-align: center;
        }

        .col-particulars {
            width: 30%;
            text-align: left;
        }

        .col-amount {
            width: 14%;
            text-align: right;
        }

        .col-percent {
            width: 14%;
            text-align: right;
        }

        /* Sub-items get an indent */
        .indent td:first-child {
            padding-left: 18px;
        }

        /* ---- Signature section ---- */
        .sig-section {
            margin-top: 30px;
        }

        .sig-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sig-table td {
            border: none;
            vertical-align: top;
            width: 50%;
            padding: 4px 20px;
            text-align: center;
        }

        .sig-label {
            font-size: 8px;
            margin-bottom: 15px;
        }

        .sig-name {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 1px;
        }

        .sig-title {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 0;
        }

        .sig-underline {
            width: 60%;
            margin: 2px auto 0 auto;
            border-top: 1px solid #000;
        }

        .sig-office {
            font-size: 8px;
            padding-top: 3px;
            margin: 0;
        }

        .sig-gap {
            height: 25px;
        }
    </style>
</head>

<body>

    {{-- ===== HEADER ===== --}}
    <div class="header-block">
        <p>BUREAU OF LOCAL GOVERNMENT FINANCE</p>
        <p>DEPARTMENT OF FINANCE</p>
        <p>http://blgf.gov.ph/</p>
    </div>

    <div class="report-title">STATEMENT OF RECEIPTS AND EXPENDITURES</div>

    {{-- ===== META ===== --}}
    <table class="meta-table">
        <tr>
            <td>LGU:</td>
            <td>{{ $lgu ?? 'Manila City' }}</td>
        </tr>
        <tr>
            <td>Period Covered:</td>
            <td>{{ $period ?? 'Q1, 2025' }}</td>
        </tr>
    </table>

    {{-- ===== DATA TABLE ===== --}}
    @php
        $categoryRows = ['LOCAL SOURCES', 'TAX REVENUE', 'NON-TAX REVENUE', 'EXTERNAL SOURCES'];
        $totalRows = [
            'TOTAL CURRENT OPERATING INCOME',
            'ADD: SUPPLEMENTAL BUDGET (UNAPPROPRIATED SURPLUS) FOR CURRENT OPERATING EXPENDITURES',
            'TOTAL AVAILABLE FOR CURRENT OPERATING EXPENDITURES',
            'LESS: CURRENT OPERATING EXPENDITURES (PS + MOOE + FE)',
        ];
    @endphp

    <table class="sre-table">
        <thead>
            <tr>
                <th class="col-particulars">Particulars</th>
                <th class="col-amount">Income/Target Budget<br>Appropriation</th>
                <th class="col-amount">General Fund</th>
                <th class="col-amount">SEF</th>
                <th class="col-amount">Total</th>
                <th class="col-percent">% of General + SEF to<br>Total Income(GF+SEF)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                @php
                    $p = $row['particulars'];
                    $isCategory = in_array($p, $categoryRows);
                    $isTotal = in_array($p, $totalRows);
                    $isSub = !$isCategory && !$isTotal;
                @endphp
                <tr @if ($isSub) class="indent" @endif>
                    <td>{{ $row['particulars'] }}</td>
                    <td class="col-amount" style="{{ $isCategory || $isTotal }}">
                        {{ $row['income_target_budget_appropriation'] ?? '' }}</td>
                    <td class="col-amount" style="{{ $isCategory || $isTotal }}">
                        {{ $row['general_fund'] ?? '' }}</td>
                    <td class="col-amount" style="{{ $isCategory || $isTotal }}">
                        {{ $row['sef'] ?? '' }}</td>
                    <td class="col-amount" style="{{ $isCategory || $isTotal }}">
                        {{ $row['total'] ?? '' }}</td>
                    <td class="col-percent" style="{{ $isCategory || $isTotal }}">
                        {{ $row['percent_to_total_income'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ===== SIGNATURES ===== --}}
    <div class="sig-section">
        <table class="sig-table">
            {{-- Row 1: Prepared by / Certified by --}}
            <tr>
                <td>
                    <p class="sig-label" style="text-align: left;">Prepared by:</p>
                    <p class="sig-name">{{ $signatures['prepared_by']['name'] }}</p>
                    <p class="sig-title">( {{ $signatures['prepared_by']['title'] }} )</p>
                    <div class="sig-underline"></div>
                    <p class="sig-office">{{ $signatures['prepared_by']['office'] }}</p>
                </td>
                <td>
                    <p class="sig-label" style="text-align: left;">Certified by:</p>
                    <p class="sig-name">{{ $signatures['certified_by']['name'] }}</p>
                    <p class="sig-title">( {{ $signatures['certified_by']['title'] }} )</p>
                    <div class="sig-underline"></div>
                    <p class="sig-office">{{ $signatures['certified_by']['office'] }}</p>
                </td>
            </tr>
            {{-- Row 2: Budget officers --}}
            <tr>
                <td>
                    <div class="sig-gap"></div>
                    <p class="sig-name">{{ $signatures['budget_officer']['name'] }}</p>
                    <p class="sig-title">( {{ $signatures['budget_officer']['title'] }} )</p>
                    <div class="sig-underline"></div>
                    <p class="sig-office">{{ $signatures['budget_officer']['office'] }}</p>
                </td>
                <td>
                    <div class="sig-gap"></div>
                    <p class="sig-name">{{ $signatures['budget_certifier']['name'] }}</p>
                    <p class="sig-title">&nbsp;</p>
                    <div class="sig-underline"></div>
                    <p class="sig-office">{{ $signatures['budget_certifier']['office'] }}</p>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
