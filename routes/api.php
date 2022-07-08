<?php

use App\Http\Controllers\API\LinkController;
use App\Http\Controllers\API\StatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('links', LinkController::class);
Route::get('/stats/{link}', [StatController ::class, 'show']);
