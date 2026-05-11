<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Conversation;
use App\Events\NewMessage;
use Illuminate\Support\Facades\Http;
use Telegram\Bot\Laravel\Facades\Telegram;

class OutboundMessage
{
    public function sendReply(Conversation $conversation, string $content) {
        $message = $conversation->messages()->create([
            'direction' => 'outbound',
            'content' => $content,
            'channel' => $conversation->last_inbound_channel ?? 'telegram',
            'status' => 'pending',
            'channel_message_id' => uniqid('out_'),
        ]);

        broadcast(new NewMessage($message))->toOthers();

        return match($message->channel) {
            'telegram' => $this->sendToTelegram($message),
            // 'simulator' => $this->sendToSimulator($message)
            default => throw new \Exception('channel not supported yet'),
        };

    }

    protected function sendToTelegram(Message $message) {
        $chatId = $message->conversation->customer->getIndentifierFor('telegram');

        if(!$chatId) {
            $message->update(['status' => 'failed']);
            return false;
        }

        try {
            $response = Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $message->content,
                'parse_mode' => 'HTML',
            ]);

            $message->update([
                'status' => 'sent',
                'status_updated_at' => now(),
                'channel_message_id' => (string) $response->getMessageId(),
            ]);
            return true;

        } catch (\Exception $e) {
            logger("Telegram Send Failed: " . $e->getMessage());

            $message->update(['status' => 'failed']);
            return false;
        }
    }

}
