<div class="flex flex-col h-[500px]" wire:poll.3s>
    <!-- Messages Box -->
    <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-100 dark:border-gray-800" id="chat-messages-container">
        @forelse($messages as $msg)
            <div class="flex flex-col {{ $msg->sender_type === 'admin' ? 'items-end' : 'items-start' }}">
                <div class="max-w-[75%] rounded-lg px-4 py-2 text-sm {{ $msg->sender_type === 'admin' ? 'bg-amber-500 text-white rounded-br-none' : 'bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-bl-none' }}">
                    <p class="whitespace-pre-wrap break-all">{{ $msg->message }}</p>
                </div>
                <span class="text-[10px] text-gray-400 mt-1">
                    {{ $msg->created_at->format('H:i') }}
                    @if($msg->sender_type === 'admin')
                        <span class="ml-1">{{ $msg->is_read ? '✓✓' : '✓' }}</span>
                    @endif
                </span>
            </div>
        @empty
            <div class="flex items-center justify-center h-full text-gray-400 text-sm">
                No messages yet. Start the conversation!
            </div>
        @endforelse
    </div>

    <!-- Input Form -->
    <form wire:submit.prevent="sendMessage" class="mt-4 flex gap-2">
        <input 
            type="text" 
            wire:model="newMessage" 
            placeholder="Type a message..." 
            class="flex-1 min-w-0 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-amber-500 focus:ring-amber-500 text-sm"
        />
        <button 
            type="submit" 
            class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-semibold transition"
        >
            Send
        </button>
    </form>
    
    <script>
        // Scroll to bottom function
        function scrollToBottom() {
            const container = document.getElementById('chat-messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }
        
        // Scroll to bottom on initial load
        setTimeout(scrollToBottom, 100);
        
        // Scroll to bottom when a message is sent
        window.addEventListener('message-sent', () => {
            setTimeout(scrollToBottom, 50);
        });
    </script>
</div>
