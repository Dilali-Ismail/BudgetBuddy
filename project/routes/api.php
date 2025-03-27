<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GroupController;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\GroupExpenseController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login'])->name('login');

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/user',[AuthController::class,'user']);
    Route::post('/expenses',[ExpenseController::class,'store']);
    Route::get('/expenses',[ExpenseController::class,'index']);
    Route::get('/expenses/{expense}',[ExpenseController::class,'show']);
    Route::put('/expenses/{expense}',[ExpenseController::class,'update']);
    Route::delete('/expenses/{expense}',[ExpenseController::class,'destroy']);

    Route::post('/tags',[TagController::class,'store']);
    Route::get('/tags',[TagController::class,'index']);
    Route::get('/tags/{tag}',[TagController::class,'show']);
    Route::put('/tags/{tag}',[TagController::class,'update']);
    Route::delete('/tags/{tag}',[TagController::class,'destroy']);
    Route::post('/expenses/{expense}/tags', [ExpenseController::class, 'attachTags']);
    Route::post('/groupes',[GroupController::class,'store']);
    Route::get('/groupes',[GroupController::class,'index']);
    Route::get('/groupes/{id}',[GroupController::class,'show']);
    Route::delete('/groupes/{id}',[GroupController::class,'destroy']);
    Route::post('/groups/{id}/expenses', [GroupExpenseController::class,'store']);
    Route::get('/groups/{id}/expenses', [GroupExpenseController::class,'index']);
    Route::delete('/groups/{id}/expenses/{expenseId}', [GroupExpenseController::class,'destroy']);
    Route::get('/groups/{id}/balances', [GroupController::class, 'balances']);

});
