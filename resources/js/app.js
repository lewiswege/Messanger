import './echo';

// Real-time updates are handled by Filament/Livewire components
// These listeners are for debugging purposes only
if (window.Echo) {
    window.Echo.channel('conversations')
        .listen('.NewMessage', (e) => {
            console.log('NewMessage event received:', e);
        })
        .listen('.ConversationRead', (e) => {
            console.log('ConversationRead event received:', e);
        })
        .listen('.ConversationAssigned', (e) => {
            console.log('ConversationAssigned event received:', e);
        });
}
