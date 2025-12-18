<?php

use Illuminate\Support\Facades\Route;
use App\Models\QuoteRequest;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('pages.dashboard');
})->name('dashboard');

Route::get('/quote-request/create', function () {
    return view('pages.quote-request-create');
})->name('quote-request.create');

Route::get('/quote-request/{quoteRequest}', function (QuoteRequest $quoteRequest) {
    return view('pages.quote-request-show', ['quoteRequest' => $quoteRequest]);
})->name('quote-request.show');
