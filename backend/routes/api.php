<?php

use App\Http\Controllers\AddonController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderTrackingController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);


/*
|--------------------------------------------------------------------------
| Customer Feedback
|--------------------------------------------------------------------------
|
| Customers do not have accounts, so submitting feedback
| is intentionally public.
|
*/

Route::post('/feedbacks', [FeedbackController::class, 'store']);

Route::get('/track-order/{orderNumber}', [OrderTrackingController::class, 'show']);


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Services
    |--------------------------------------------------------------------------
    */

    Route::get('/services', [
        ServiceController::class,
        'index'
    ]);

    Route::post('/services', [
        ServiceController::class,
        'store'
    ]);

    Route::patch('/services/{service}', [
        ServiceController::class,
        'update'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Add-ons
    |--------------------------------------------------------------------------
    */

    Route::get('/addons', [
        AddonController::class,
        'index'
    ]);

    Route::post('/addons', [
        AddonController::class,
        'store'
    ]);

    Route::patch('/addons/{addon}', [
        AddonController::class,
        'update'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */

    Route::get('/orders', [
        OrderController::class,
        'index'
    ]);

    Route::get('/orders/{order}', [
        OrderController::class,
        'show'
    ]);

    Route::post('/orders', [
        OrderController::class,
        'store'
    ]);

    Route::patch('/orders/{order}/status', [
        OrderController::class,
        'updateStatus'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    Route::get('/reports', [
        ReportController::class,
        'index'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Customer Feedback Reports
    |--------------------------------------------------------------------------
    |
    | Admin/staff can view feedback.
    |
    */

    Route::get('/feedbacks', [
        FeedbackController::class,
        'index'
    ]);

});