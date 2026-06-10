<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\HeartbeatController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Routes chat (bảo vệ bằng auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/chats', [ChatController::class, 'getConversations']);
    Route::post('/chats/create', [ChatController::class, 'createChat']);
    Route::get('/chats/{chatId}/messages', [ChatController::class, 'getMessages']);
    Route::post('/chats/{chatId}/messages', [ChatController::class, 'storeMessage']);
 Route::delete('/messages/{id}', [ChatController::class, 'revokeMessage']); // thu hồi mềm
 Route::patch('/chats/hide', [ChatController::class, 'hideConversations']);

     Route::post('/heartbeat', [HeartbeatController::class, 'ping']);



});