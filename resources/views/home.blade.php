<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Financial Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="bg-body-tertiary">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <h1 class="h3 mb-4 text-center">Financial Reports</h1>

                <div class="row g-4">
                    {{-- AMD Report --}}
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div
                                class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">
                                <h5 class="card-title mb-2">AMD Q1 Report</h5>
                                <p class="text-body-secondary mb-4">Download the AMD Q1 Financial Report as PDF.</p>
                                <div class="d-flex gap-2 w-100">
                                    <a href="{{ route('reports.preview') }}" target="_blank"
                                        class="btn btn-outline-primary w-50">
                                        Preview
                                    </a>
                                    <a href="{{ route('reports.download') }}" class="btn btn-primary w-50">
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SRE Report --}}
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div
                                class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">
                                <h5 class="card-title mb-2">SRE Report</h5>
                                <p class="text-body-secondary mb-4">Download the Statement of Receipts and Expenditures
                                    as PDF.</p>
                                <div class="d-flex gap-2 w-100">
                                    <a href="{{ route('sre.preview') }}" target="_blank"
                                        class="btn btn-outline-success w-50">
                                        Preview
                                    </a>
                                    <a href="{{ route('sre.download') }}" class="btn btn-success w-50">
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
