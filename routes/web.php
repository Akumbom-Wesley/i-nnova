<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\CaseStudyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KickstarterController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/work', [CaseStudyController::class, 'index'])->name('work.index');
Route::get('/work/{caseStudy:slug}', [CaseStudyController::class, 'show'])->name('work.show');

Route::get('/about', AboutController::class)->name('about');
Route::get('/kickstarter', KickstarterController::class)->name('kickstarter');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('contact.store');
