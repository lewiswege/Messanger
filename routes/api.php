<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/webhook/{channel}/{secret?}', [WebhookController::class, 'handle'])
    ->whereIn('channel', ['telegram', 'simulator', 'whatsapp'])
    ->middleware('throttle:60,1');
