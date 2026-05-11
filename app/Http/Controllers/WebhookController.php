<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use App\Jobs\ProcessWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request, string $channel) {

        $webhook = Webhook::create([
            'channel' => $channel,
            'payload' => $request->all(),
        ]);

        Log::info("webhook stored with ID: " . $webhook->id);


        ProcessWebhook::dispatch($webhook)
            ->onQueue('webhook');
            // ->afterResponse(); caused queue issues

        return response() ->json(['message' => 'Received'], 202);
    }
}
