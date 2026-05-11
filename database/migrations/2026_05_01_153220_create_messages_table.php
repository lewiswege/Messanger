<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->ulid('id');
            $table->foreignId('conversation_id')->index();
            $table->enum('direction', ["inbound","outbound"]);
            $table->enum('channel', ["whatsapp","telegram","sms"]);
            $table->string('channel_message_id')->index();
            $table->enum('content_type', ["text","image","document","audio","location","template"]);
            $table->text('content')->nullable();
            $table->json('metadata')->nullable();
            $table->enum('status', ["pending","queued","sent","delivered","read","failed"])->default('pending');
            $table->timestamp('status_updated_at')->nullable();
            $table->string('sent_by_agent_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
