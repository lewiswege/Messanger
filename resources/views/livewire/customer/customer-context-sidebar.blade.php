<div style="background: #1e293b; border-radius: 8px; overflow: hidden; border: 1px solid #334155;">
    {{-- Customer Header --}}
    <div style="padding: 16px; border-bottom: 1px solid #334155; text-align: center;">
        <div style="width: 60px; height: 60px; background: #8b5cf6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
            <span style="color: white; font-size: 24px; font-weight: 600;">{{ strtoupper(substr($customer->name ?? 'C', 0, 1)) }}</span>
        </div>
        <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: white;">{{ $customer->name ?? 'Unknown' }}</h3>
        <p style="margin: 4px 0 0; font-size: 13px; color: #94a3b8;">Customer</p>
    </div>

    {{-- Customer Details --}}
    <div style="padding: 16px; border-bottom: 1px solid #334155;">
        <h4 style="margin: 0 0 12px; font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Contact Details</h4>

        @if($customer->email)
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
            <svg style="width: 16px; height: 16px; color: #64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span style="font-size: 14px; color: #e2e8f0;">{{ $customer->email }}</span>
        </div>
        @endif

        @if($customer->phone_primary)
        <div style="display: flex; align-items: center; gap: 10px;">
            <svg style="width: 16px; height: 16px; color: #64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            <span style="font-size: 14px; color: #e2e8f0;">{{ $customer->phone_primary }}</span>
        </div>
        @endif

        @if(!$customer->email && !$customer->phone_primary)
        <p style="margin: 0; font-size: 13px; color: #64748b;">No contact details</p>
        @endif
    </div>

    {{-- Channel Identifiers --}}
    <div style="padding: 16px; border-bottom: 1px solid #334155;">
        <h4 style="margin: 0 0 12px; font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Channel Identifiers</h4>

        @forelse($channelIdentifiers as $identifier)
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px 10px; background: #0f172a; border-radius: 6px; margin-bottom: 8px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                @if($identifier->channel === 'telegram')
                <svg style="width: 18px; height: 18px; color: #0088cc;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295l.213-3.054 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.64-.203-.658-.64.135-.954l11.566-4.458c.538-.196 1.006.128.828.94z"/>
                </svg>
                @elseif($identifier->channel === 'whatsapp')
                <svg style="width: 18px; height: 18px; color: #25d366;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                @else
                <svg style="width: 18px; height: 18px; color: #64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                @endif
                <span style="font-size: 13px; color: #e2e8f0; text-transform: capitalize;">{{ $identifier->channel }}</span>
            </div>
            <span style="font-size: 12px; color: #94a3b8; font-family: monospace;">{{ $identifier->identifier }}</span>
        </div>
        @empty
        <p style="margin: 0; font-size: 13px; color: #64748b;">No channel identifiers</p>
        @endforelse
    </div>

    {{-- Conversation Stats --}}
    <div style="padding: 16px; border-bottom: 1px solid #334155;">
        <h4 style="margin: 0 0 12px; font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Conversations</h4>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <div style="background: #0f172a; border-radius: 6px; padding: 12px; text-align: center;">
                <p style="margin: 0; font-size: 24px; font-weight: 600; color: #8b5cf6;">{{ $totalConversations }}</p>
                <p style="margin: 4px 0 0; font-size: 11px; color: #94a3b8;">Total</p>
            </div>
            <div style="background: #0f172a; border-radius: 6px; padding: 12px; text-align: center;">
                <p style="margin: 0; font-size: 24px; font-weight: 600; color: #10b981;">{{ $openConversations }}</p>
                <p style="margin: 4px 0 0; font-size: 11px; color: #94a3b8;">Open</p>
            </div>
        </div>
    </div>

    {{-- Recent Conversations --}}
    <div style="padding: 16px; border-bottom: 1px solid #334155;">
        <h4 style="margin: 0 0 12px; font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Recent Conversations</h4>

        @forelse($recentConversations as $conversation)
        <a
            href="{{ route('filament.admin.resources.conversations.view', $conversation) }}"
            style="display: block; padding: 10px; background: #0f172a; border-radius: 6px; margin-bottom: 8px; text-decoration: none; transition: background 0.2s;"
            onmouseover="this.style.background='#1e293b'"
            onmouseout="this.style.background='#0f172a'"
        >
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                <span style="font-size: 12px; padding: 2px 8px; border-radius: 4px; text-transform: capitalize;
                    {{ $conversation->status === 'open' ? 'background: #065f46; color: #10b981;' : ($conversation->status === 'pending' ? 'background: #78350f; color: #f59e0b;' : 'background: #374151; color: #9ca3af;') }}">
                    {{ $conversation->status }}
                </span>
                <span style="font-size: 11px; color: #64748b;">{{ $conversation->last_message_at ? \Carbon\Carbon::parse($conversation->last_message_at)->diffForHumans() : 'No messages' }}</span>
            </div>
            <p style="margin: 0; font-size: 13px; color: #94a3b8; text-transform: capitalize;">
                via {{ $conversation->last_inbound_channel ?? 'unknown' }}
                @if($conversation->unread_count > 0)
                <span style="background: #dc2626; color: white; font-size: 10px; padding: 1px 6px; border-radius: 10px; margin-left: 6px;">{{ $conversation->unread_count }}</span>
                @endif
            </p>
        </a>
        @empty
        <p style="margin: 0; font-size: 13px; color: #64748b;">No conversations yet</p>
        @endforelse
    </div>

    {{-- Quick Actions --}}
    <div style="padding: 16px;">
        <h4 style="margin: 0 0 12px; font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Quick Actions</h4>

        <a
            href="{{ route('filament.admin.resources.conversations.create') }}?customer_id={{ $customer->id }}"
            style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 10px; background: #8b5cf6; color: white; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; margin-bottom: 8px; transition: background 0.2s;"
            onmouseover="this.style.background='#7c3aed'"
            onmouseout="this.style.background='#8b5cf6'"
        >
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Conversation
        </a>

        <a
            href="{{ route('filament.admin.resources.customers.edit', $customer) }}"
            style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 10px; background: #334155; color: #e2e8f0; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; transition: background 0.2s;"
            onmouseover="this.style.background='#475569'"
            onmouseout="this.style.background='#334155'"
        >
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Customer
        </a>
    </div>
</div>
