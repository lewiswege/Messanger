<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/webhook/{channel}', [WebhookController::class, 'handle'])
    ->whereIn('channel', ['telegram', 'simulator', 'whatsapp']);
