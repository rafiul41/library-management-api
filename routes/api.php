<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;

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

Route::resource('books', BookController::class);
Route::resource('users', UserController::class);

Route::post('books/issue', [BookController::class, 'issueBook']);
Route::post('books/submit', [BookController::class, 'submitBook']);

Route::get('books/{book_id}/holding-users', [BookController::class, 'listHoldingUsers']);
Route::get('users/{user_id}/issued-books', [UserController::class, 'listIssuedBooks']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
