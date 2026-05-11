<?php

namespace App\Jobs;

use App\Events\ConversationAssigned;
use App\Events\NewMessage;
use App\Models\Webhook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\CustomerChannelIdentifier;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ConversationAssignmentService;

class ProcessWebhook implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;

    public function __construct(public Webhook $webhook)
    {
        $this->onQueue('webhook');

    }

    public function handle(): void
    {
        $this->webhook->update([
            'status' => 'processing',
        ]);
        $this->webhook->increment('attempts', 1);

        try {
            $channel = $this->webhook->channel;
            $payload = $this->webhook->payload;

            if ($channel === 'telegram') {
                $identifier = (string) ($payload['message']['from']['id'] ?? '');
                $channelMessageId = (string) ($payload['message']['message_id'] ?? '');
                $content = $payload['message']['text'] ?? '';
                $firstName = $payload['message']['from']['first_name'] ?? '';
                $lastName = $payload['message']['from']['last_name'] ?? '';
                $customerName = trim($firstName . ' ' . $lastName) ?: 'Unknown';
            } else {
                throw new \Exception("Webhook channel not supported: {$channel}");
            }

            if (empty($identifier)) {
                throw new \Exception("Invalid channel Identifier: {$channel}");
            }

            // Database Transaction. All or nothing
            $result = DB::transaction(function () use($identifier, $channel, $channelMessageId, $customerName, $content) {
                // Linking the Identity to a customer plus creating one if they dont exist.
                $customerChannel = CustomerChannelIdentifier::firstOrCreate(
                    ['channel' => $channel, 'identifier' => $identifier]
                );

                if(!$customerChannel->customer_id) {
                    $customer = Customer::create(['name' => $customerName]);
                    $customerChannel->update(['customer_id' => $customer->id]);
                } else {
                    $customer = $customerChannel->customer;
                }

                // Find an existing conversation that is NOT archived.
                $conversation = Conversation::query()
                    ->where('customer_id', '=', $customer->id)
                    ->where('status', '!=', 'archived')
                    ->first();

                if (!$conversation) {
                    $conversation = Conversation::create([
                        'customer_id' => $customer->id,
                        'status' => 'new',
                    ]);
                }

                $message = Message::create([
                    'conversation_id'    => $conversation->id,
                    'direction'          => 'inbound',
                    'channel'            => $channel,
                    'channel_message_id' => $channelMessageId,
                    'content_type'       => 'text',
                    'content'            => $content,
                    'status'             => 'delivered',
                    'status_updated_at'  => now(),
                ]);

                // Refresh the conversation to get the updated fields from the Observer
                $conversation->refresh();

                return ['conversation' => $conversation, 'message' => $message];
            });

            $conversation = $result['conversation'];
            $message = $result['message'];

            // Broadcast after transaction is committed
            broadcast(new NewMessage($message));

            // Auto-assign only if conversation is not already assigned
            if (!$conversation->assigned_to) {
                $assignmentService = new ConversationAssignmentService();
                $agent = $assignmentService->findAvailableAgent();

                if ($assignmentService->assignConversation($conversation, $agent)) {
                    $conversation->refresh();
                    broadcast(new ConversationAssigned($conversation));
                }
            }

            $this->webhook->update([
                'status' => 'success',
                'processed_at' => now(),
                'error_log' => null,
            ]);

        } catch (\Throwable $e) {
            $this->webhook->update([
                'status' => 'failed',
                'error_log' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
