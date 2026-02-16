<?php

use App\Http\Controllers\AMDFinancialReportController;
use App\Http\Controllers\StatementOfReceiptsAndExpidenturesController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');

Route::get('/reports/preview', [AMDFinancialReportController::class, 'preview'])->name('reports.preview');
Route::get('/reports/download', [AMDFinancialReportController::class, 'download'])->name('reports.download');

Route::get('/sre/preview', [StatementOfReceiptsAndExpidenturesController::class, 'preview'])->name('sre.preview');
Route::get('/sre/download', [StatementOfReceiptsAndExpidenturesController::class, 'download'])->name('sre.download');
