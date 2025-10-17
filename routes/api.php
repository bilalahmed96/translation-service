<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ExportController;

Route::prefix('v1')->middleware('auth.token')->group(function () {

    // Translations CRUD + search
    Route::get('/translations/search', [TranslationController::class, 'search']);
    Route::apiResource('translations', TranslationController::class);

    // Locales
    Route::apiResource('locales', LocaleController::class)->only(['index', 'store']);

    // Tags
    Route::apiResource('tags', TagController::class)->only(['index', 'store']);

    // JSON Export
    Route::get('/export/{locale}', [ExportController::class, 'export']);
});
