<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\InvitationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::put('users/{user}', [AuthController::class, 'update']);
    Route::get('employees/{employee}', [AuthController::class, 'getEmployee']);
    Route::post('companies', [CompanyController::class, 'store']);
    Route::get('companies/{company}', [CompanyController::class, 'index']);
    Route::put('companies/{company}', [CompanyController::class, 'update']);
    Route::delete('companies/{company}', [CompanyController::class, 'destroy']);
    Route::get('/companies', [CompanyController::class, 'getCompanies']);
    Route::get('/histories', [HistoryController::class, 'index']);
    Route::get('/invitations', [InvitationController::class, 'index']);
    Route::delete('/invitations/{invitation}', [InvitationController::class, 'destroy']);
    Route::post('invite', [InvitationController::class, 'store']);
    Route::get('search', [CompanyController::class, 'search']);
});
