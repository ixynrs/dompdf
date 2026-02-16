<?php

use function Pest\Laravel\get;

const AMD_DOWNLOAD_NAME = 'Q1-2025-MAR-JUN-AMD.pdf';
const SRE_DOWNLOAD_NAME = 'SRE-Manila-City-Q1-2025.pdf';

test('report home page is accessible', function () {
    $response = get(route('home'));

    $response->assertStatus(200);
});

test('amd q1 preview endpoint returns the amd q1 pdf file', function () {
    $response = get(route('reports.preview'));

    $response
        ->assertHeaderContains('content-disposition', 'inline')
        ->assertHeaderContains('content-disposition', 'filename=');
});

test('amd q1 download endpoint returns the amd q1 pdf file', function () {
    $response = get(route('reports.download'));

    $response
        ->assertDownload(AMD_DOWNLOAD_NAME);
});

test('sre preview endpoint returns the sre pdf file', function () {
    $response = get(route('sre.preview'));

    $response
        ->assertHeaderContains('content-disposition', 'inline')
        ->assertHeaderContains('content-disposition', 'filename=');
});

test('sre download endpoint returns the sre pdf file', function () {
    $response = get(route('sre.download'));

    $response
        ->assertDownload(SRE_DOWNLOAD_NAME);
});
