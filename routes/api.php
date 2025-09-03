<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
 
 
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\TransactionController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::controller(UserController::class)->group(function(){
     Route::post('register', 'register'); 
     Route::post('login', 'login'); 
     Route::post('logout', 'logout'); 
});

Route::middleware('auth:sanctum')->group(function(){
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('budgets', BudgetController::class);
    Route::get('/search', [TransactionController::class, 'search']);
});
 
