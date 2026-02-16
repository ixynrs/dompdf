<?php

use function Pest\Laravel\get;

const SAMPLE_DOWNLOAD_NAME = 'Q1-2025-MAR-JUN-AMD.pdf';

test('report home page is accessible', function () {
    $response = get(route('reports.home'));

    $response
        ->assertSuccessful()
        ->assertSee('AMD Q1 Report Downloader')
        ->assertSee('public/financial-reports/amd-q1.json');
});

test('download endpoint returns the amd q1 pdf file', function () {
    $response = get(route('reports.download'));

    $response
        ->assertDownload(SAMPLE_DOWNLOAD_NAME);
});
