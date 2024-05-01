<?php 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoGroupController;
use App\Http\Controllers\TodoController;

Route::get('/todoGroup', [TodoGroupController::class, 'index']);
Route::post('/todoGroup', [TodoGroupController::class, 'store']);
Route::patch('/todoGroup/{id}', [TodoGroupController::class, 'update']);
Route::delete('/todoGroup/{id}', [TodoGroupController::class, 'destroy']);
Route::post('/todo', [TodoController::class, 'store']);
Route::patch('/todo/{id}', [TodoController::class, 'update']);
Route::post('/deleteTodo', [TodoController::class, 'destroy']);
?>