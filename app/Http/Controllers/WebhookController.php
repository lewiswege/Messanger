<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use App\Jobs\ProcessWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request, string $channel, ?string $secret = null) {
        $expectedSecret = config('services.telegram.webhook_secret');

        // 1. Verify the URL secret segment
        if ($secret !== $expectedSecret) {
            Log::warning("Unauthorized webhook attempt: Invalid secret path for channel: {$channel}");
            return response()->json(['message' => 'Unauthorized path'], 403);
        }

        // 2. Verify the Telegram-specific secret token header
        if ($channel === 'telegram') {
            $headerSecret = $request->header('X-Telegram-Bot-Api-Secret-Token');
            if ($headerSecret !== $expectedSecret) {
                Log::warning("Unauthorized Telegram webhook: Missing or invalid secret token header.");
                return response()->json(['message' => 'Unauthorized token'], 403);
            }
        }

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
