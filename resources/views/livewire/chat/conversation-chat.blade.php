<div wire:poll.5s>

<div
    style="display: flex; flex-direction: column; height: 500px; border-radius: 8px; overflow: hidden; border: 1px solid #1e293b; width: 100%;"
    x-data="{ scrollToBottom() { $refs.messages.scrollTop = $refs.messages.scrollHeight } }"
    x-init="scrollToBottom()"
>
    {{-- Header with Customer Name and Status --}}
    <div style="padding: 10px 12px; background: #1e293b; border-bottom: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div style="text-align: left;">
            <p style="margin: 0; font-size: 15px; font-weight: 600; color: white;">{{ $record->customer->name ?? 'Customer' }}</p>
            <span style="font-size: 11px; color: #10b981; text-transform: capitalize;">{{ $record->status }}</span>
        </div>

        @if($record->status !== 'resolved')
        <button
            wire:click="resolve"
            style="background: #10b981; color: white; border: none; padding: 4px 12px; border-radius: 4px; font-size: 12px; cursor: pointer; display: flex; align-items: center; gap: 4px;"
        >
            <svg style="width: 14px; height: 14px; fill: currentColor;" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Resolve
        </button>
        @else
        <div style="display: flex; align-items: center; gap: 4px; color: #10b981; font-size: 12px; font-weight: 500;">
            <svg style="width: 16px; height: 16px; fill: currentColor;" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Resolved
        </div>
        @endif
    </div>

    {{-- Messages Area --}}
    <div
        x-ref="messages"
        style="flex: 1; overflow-y: auto; padding: 12px; background: #0F172B;"
    >
        @forelse($messages as $message)
            @if($message->direction === 'inbound')
                {{-- Inbound: Left side --}}
                <div style="display: flex; justify-content: flex-start; margin-bottom: 8px;">
                    <div style="max-width: 85%; background: #e2e8f0; border-radius: 12px; border-top-left-radius: 0; padding: 8px 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <p style="margin: 0; font-size: 14px; color: #1e293b; word-wrap: break-word;">{{ $message->content }}</p>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px; gap: 8px;">
                            <span style="font-size: 10px; color: #8b5cf6; text-transform: capitalize;">{{ $message->channel }}</span>
                            <span style="font-size: 10px; color: #64748b;">{{ $message->created_at->format('H:i') }}</span>
                        </div>
                    </div>
                </div>
            @else
                {{-- Outbound: Right side --}}
                <div style="display: flex; justify-content: flex-end; margin-bottom: 8px;">
                    <div style="max-width: 85%; background: #8b5cf6; border-radius: 12px; border-top-right-radius: 0; padding: 8px 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                        <p style="margin: 0; font-size: 14px; color: white; word-wrap: break-word;">{{ $message->content }}</p>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px; gap: 8px;">
                            <span style="font-size: 10px; color: rgba(255,255,255,0.7); text-transform: capitalize;">{{ $message->channel }}</span>
                            <span style="font-size: 10px; color: rgba(255,255,255,0.7);">{{ $message->created_at->format('H:i') }} ✓✓</span>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                <p style="color: #94a3b8; font-size: 14px;">No messages yet</p>
            </div>
        @endforelse
    </div>

    {{-- Reply Input --}}
    <form
        wire:submit="sendMessage"
        x-on:submit="setTimeout(() => scrollToBottom(), 100)"
        style="padding: 10px; background: #1e293b; border-top: 1px solid #334155; display: flex; gap: 8px; align-items: center;"
    >
        <input
            type="text"
            wire:model="messageContent"
            placeholder="Type a message..."
            style="flex: 1; min-width: 0; border: 1px solid #334155; border-radius: 24px; padding: 10px 16px; font-size: 14px; outline: none; background: #0F172B; color: white;"
        />
        <button
            type="submit"
            style="width: 40px; height: 40px; min-width: 40px; border-radius: 50%; background: #8b5cf6; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;"
        >
            <svg style="width: 18px; height: 18px; fill: white;" viewBox="0 0 24 24">
                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
            </svg>
        </button>
    </form>
</div>

{{-- Conversation Details --}}
<div style="width: 100%; background: #1e293b; border-radius: 8px; border: 1px solid #334155; overflow: hidden; margin-top: 16px;">
    <div style="padding: 16px; border-bottom: 1px solid #334155;">
        <h3 style="margin: 0; font-size: 14px; font-weight: 600; color: white; text-transform: uppercase; letter-spacing: 0.5px;">Conversation Details</h3>
    </div>

    <div style="padding: 16px;">
        {{-- Customer Info --}}
        <div style="margin-bottom: 20px;">
            <p style="margin: 0 0 4px; font-size: 11px; color: #64748b; text-transform: uppercase;">Customer</p>
            <p style="margin: 0; font-size: 14px; color: #e2e8f0;">{{ $record->customer->name ?? 'Unknown' }}</p>
        </div>

        @if($record->customer->email)
        <div style="margin-bottom: 20px;">
            <p style="margin: 0 0 4px; font-size: 11px; color: #64748b; text-transform: uppercase;">Email</p>
            <p style="margin: 0; font-size: 14px; color: #e2e8f0;">{{ $record->customer->email }}</p>
        </div>
        @endif

        @if($record->customer->phone_primary)
        <div style="margin-bottom: 20px;">
            <p style="margin: 0 0 4px; font-size: 11px; color: #64748b; text-transform: uppercase;">Phone</p>
            <p style="margin: 0; font-size: 14px; color: #e2e8f0;">{{ $record->customer->phone_primary }}</p>
        </div>
        @endif

        {{-- Status --}}
        <div style="margin-bottom: 20px;">
            <p style="margin: 0 0 4px; font-size: 11px; color: #64748b; text-transform: uppercase;">Status</p>
            <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 500; text-transform: capitalize;
                {{ $record->status === 'in_progress' ? 'background: #1e3a8a; color: #60a5fa;' : ($record->status === 'waiting_response' ? 'background: #78350f; color: #fbbf24;' : ($record->status === 'resolved' ? 'background: #064e3b; color: #34d399;' : 'background: #374151; color: #9ca3af;')) }}">
                {{ $record->status }}
            </span>
        </div>

        {{-- Channel --}}
        <div style="margin-bottom: 20px;">
            <p style="margin: 0 0 4px; font-size: 11px; color: #64748b; text-transform: uppercase;">Channel</p>
            <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 500; background: #1e3a5f; color: #38bdf8; text-transform: capitalize;">
                {{ $record->last_inbound_channel ?? 'Unknown' }}
            </span>
        </div>

        {{-- Assigned Agent --}}
        <div style="margin-bottom: 20px;">
            <p style="margin: 0 0 4px; font-size: 11px; color: #64748b; text-transform: uppercase;">Assigned To</p>
            <p style="margin: 0; font-size: 14px; color: #e2e8f0;">{{ $record->assignedAgent->name ?? 'Unassigned' }}</p>
        </div>

        {{-- Unread Count --}}
        <div style="margin-bottom: 20px;">
            <p style="margin: 0 0 4px; font-size: 11px; color: #64748b; text-transform: uppercase;">Unread Messages</p>
            <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 500;
                {{ $record->unread_count > 0 ? 'background: #7f1d1d; color: #fca5a5;' : 'background: #374151; color: #9ca3af;' }}">
                {{ $record->unread_count }}
            </span>
        </div>

        {{-- Last Activity --}}
        <div style="margin-bottom: 20px;">
            <p style="margin: 0 0 4px; font-size: 11px; color: #64748b; text-transform: uppercase;">Last Activity</p>
            <p style="margin: 0; font-size: 14px; color: #e2e8f0;">{{ $record->last_message_at ? \Carbon\Carbon::parse($record->last_message_at)->diffForHumans() : '-' }}</p>
        </div>

        {{-- Created --}}
        <div style="margin-bottom: 20px;">
            <p style="margin: 0 0 4px; font-size: 11px; color: #64748b; text-transform: uppercase;">Created</p>
            <p style="margin: 0; font-size: 14px; color: #e2e8f0;">{{ \Carbon\Carbon::parse($record->created_at)->format('M d, Y H:i') }}</p>
        </div>

        {{-- Resolved At (if resolved) --}}
        @if($record->status === 'resolved' && $record->resolved_at)
        <div style="margin-bottom: 20px;">
            <p style="margin: 0 0 4px; font-size: 11px; color: #64748b; text-transform: uppercase;">Resolved</p>
            <p style="margin: 0; font-size: 14px; color: #e2e8f0;">{{ \Carbon\Carbon::parse($record->resolved_at)->format('M d, Y H:i') }}</p>
        </div>
        @endif
    </div>
</div>

</div>
