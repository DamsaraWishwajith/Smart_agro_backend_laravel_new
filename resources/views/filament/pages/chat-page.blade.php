<x-filament-panels::page>

<style>
    /* Override Filament page padding so chat fills full height */
    .fi-main { padding: 0 !important; }
    .fi-page-header { display: none !important; }

    .chat-wrap {
        display: flex;
        height: calc(100vh - 64px);
        overflow: hidden;
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }

    /* ── SIDEBAR ─────────────────────────────────────────────── */
    .chat-sidebar {
        width: 300px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        background: #1a1f2e;
        border-right: 1px solid #2d3449;
    }

    .sidebar-header {
        padding: 20px 16px 14px;
        border-bottom: 1px solid #2d3449;
    }

    .sidebar-header h2 {
        font-size: 18px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sidebar-header h2 svg {
        width: 20px;
        height: 20px;
        color: #f59e0b;
    }

    .sidebar-header p {
        font-size: 12px;
        color: #64748b;
        margin: 0;
    }

    .user-list {
        flex: 1;
        overflow-y: auto;
    }

    .user-list::-webkit-scrollbar { width: 4px; }
    .user-list::-webkit-scrollbar-track { background: transparent; }
    .user-list::-webkit-scrollbar-thumb { background: #2d3449; border-radius: 4px; }

    .user-item {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: none;
        border: none;
        cursor: pointer;
        transition: background 0.15s;
        border-left: 3px solid transparent;
        text-align: left;
    }

    .user-item:hover { background: #252a3a; }
    .user-item.active { background: #1e2d45; border-left-color: #f59e0b; }

    .user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
        position: relative;
    }

    .user-avatar-online {
        position: absolute;
        bottom: 1px;
        right: 1px;
        width: 10px;
        height: 10px;
        background: #22c55e;
        border-radius: 50%;
        border: 2px solid #1a1f2e;
    }

    .user-info { flex: 1; min-width: 0; }
    .user-name { font-size: 14px; font-weight: 600; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .user-meta { font-size: 12px; color: #64748b; margin-top: 2px; }

    .unread-badge {
        background: #ef4444;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        border-radius: 999px;
        min-width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 5px;
        flex-shrink: 0;
    }

    /* ── CHAT PANEL ──────────────────────────────────────────── */
    .chat-panel {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #0f1117;
        overflow: hidden;
    }

    .chat-header {
        padding: 14px 20px;
        background: #1a1f2e;
        border-bottom: 1px solid #2d3449;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    }

    .chat-header-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        font-weight: 700;
        color: #fff;
    }

    .chat-header-info { flex: 1; }
    .chat-header-name { font-size: 15px; font-weight: 700; color: #e2e8f0; }
    .chat-header-sub { font-size: 12px; color: #64748b; margin-top: 1px; }

    /* ── MESSAGES ────────────────────────────────────────────── */
    .messages-area {
        flex: 1;
        overflow-y: auto;
        padding: 24px 20px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .messages-area::-webkit-scrollbar { width: 4px; }
    .messages-area::-webkit-scrollbar-track { background: transparent; }
    .messages-area::-webkit-scrollbar-thumb { background: #2d3449; border-radius: 4px; }

    .msg-row { display: flex; width: 100%; }
    .msg-row.from-user { justify-content: flex-start; }
    .msg-row.from-admin { justify-content: flex-end; }

    .msg-bubble {
        display: flex;
        flex-direction: column;
        width: max-content;
        max-width: 100%;
        padding: 10px 14px;
        border-radius: 18px;
        font-size: 14px;
        line-height: 1.5;
        overflow-wrap: break-word;
        word-break: break-word;
        white-space: pre-wrap;
        position: relative;
    }

    .from-user .msg-bubble {
        background: #1e2535;
        color: #cbd5e1;
        border-bottom-left-radius: 4px;
    }

    .from-admin .msg-bubble {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .msg-meta {
        font-size: 10px;
        color: #475569;
        margin-top: 3px;
        padding: 0 4px;
    }

    .from-admin .msg-meta { text-align: right; color: #64748b; }

    .tick { font-size: 11px; }
    .tick.read { color: #f59e0b; }

    /* date divider */
    .date-divider {
        text-align: center;
        margin: 12px 0;
    }

    .date-divider span {
        background: #1e2535;
        color: #64748b;
        font-size: 11px;
        padding: 3px 12px;
        border-radius: 999px;
    }

    /* ── INPUT ───────────────────────────────────────────────── */
    .chat-input-bar {
        padding: 14px 20px;
        background: #1a1f2e;
        border-top: 1px solid #2d3449;
        flex-shrink: 0;
    }

    .input-inner {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #252a3a;
        border-radius: 999px;
        padding: 6px 6px 6px 18px;
    }

    .input-inner input {
        flex: 1;
        background: none;
        border: none;
        outline: none;
        color: #e2e8f0;
        font-size: 14px;
    }

    .input-inner input::placeholder { color: #475569; }

    .send-btn {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.1s, opacity 0.1s;
        flex-shrink: 0;
    }

    .send-btn:hover { opacity: 0.9; transform: scale(1.05); }
    .send-btn svg { width: 16px; height: 16px; color: #fff; }

    /* ── EMPTY STATE ─────────────────────────────────────────── */
    .empty-state {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        color: #3a4156;
    }

    .empty-state svg { width: 64px; height: 64px; }
    .empty-state p { font-size: 15px; font-weight: 600; color: #475569; }
    .empty-state span { font-size: 13px; color: #3a4156; }

    /* ── EMPTY USER LIST ─────────────────────────────────────── */
    .no-users {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex: 1;
        padding: 32px;
        gap: 8px;
        color: #3a4156;
    }
    .no-users svg { width: 40px; height: 40px; }
    .no-users p { font-size: 13px; color: #475569; text-align: center; }
</style>

<div class="chat-wrap" wire:poll.3s>

    {{-- ── SIDEBAR ──────────────────────────────────────────────── --}}
    <div class="chat-sidebar">
        <div class="sidebar-header">
            <h2>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                Messages
            </h2>
            <p>{{ $this->getUsersWithCounts()->count() }} user(s)</p>
        </div>

        <div class="user-list">
            @forelse($this->getUsersWithCounts() as $user)
                <button
                    type="button"
                    wire:click="selectUser({{ $user->id }})"
                    class="user-item {{ $selectedUserId === $user->id ? 'active' : '' }}"
                >
                    <div class="user-avatar">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                        <div class="user-avatar-online"></div>
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ $user->name }}</div>
                        <div class="user-meta">{{ $user->total_messages }} message(s)</div>
                    </div>
                    @if($user->unread_count > 0)
                        <div class="unread-badge">{{ $user->unread_count }}</div>
                    @endif
                </button>
            @empty
                <div class="no-users">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                    <p>No conversations yet.<br>Wait for users to send a message.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ── CHAT PANEL ───────────────────────────────────────────── --}}
    <div class="chat-panel">

        @if($selectedUserId && $this->getSelectedUser())
            @php
                $selectedUser = $this->getSelectedUser();
                $messages = $this->getSelectedMessages();
            @endphp

            {{-- Header --}}
            <div class="chat-header">
                <div class="chat-header-avatar">
                    {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                </div>
                <div class="chat-header-info">
                    <div class="chat-header-name">{{ $selectedUser->name }}</div>
                    <div class="chat-header-sub">{{ $selectedUser->email }} &nbsp;·&nbsp; {{ ucfirst($selectedUser->status ?? 'pending') }}</div>
                </div>
            </div>

            {{-- Messages --}}
            <div class="messages-area" id="chat-messages-box">
                @if($messages->isEmpty())
                    <div class="empty-state" style="flex:1;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <p>No messages yet</p>
                        <span>Send a message to start the conversation</span>
                    </div>
                @else
                    @php $prevDate = null; @endphp
                    @foreach($messages as $msg)
                        @php $msgDate = $msg->created_at->format('d M Y'); @endphp
                        @if($msgDate !== $prevDate)
                            <div class="date-divider"><span>{{ $msgDate }}</span></div>
                            @php $prevDate = $msgDate; @endphp
                        @endif

                        <div class="msg-row {{ $msg->sender_type === 'admin' ? 'from-admin' : 'from-user' }}">
                            <div style="display: flex; flex-direction: column; align-items: {{ $msg->sender_type === 'admin' ? 'flex-end' : 'flex-start' }}; max-width: 75%;">
                                <div class="msg-bubble">
                                    @if($msg->attachment_path)
                                        @if($msg->attachment_type === 'image')
                                            <a href="{{ asset('storage/' . $msg->attachment_path) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $msg->attachment_path) }}" class="max-w-[200px] rounded mb-2" alt="Attachment" />
                                            </a>
                                        @elseif($msg->attachment_type === 'pdf')
                                            <a href="{{ url('api/chat/attachment/' . $msg->id) }}" target="_blank" class="flex items-center gap-2 p-2 bg-black/20 rounded mb-2 text-white no-underline hover:bg-black/30 transition">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                <span class="text-xs truncate max-w-[150px]">PDF Document</span>
                                            </a>
                                        @endif
                                    @endif
                                    @if($msg->message)
                                        <span>{{ $msg->message }}</span>
                                    @endif
                                </div>
                                <div class="msg-meta">
                                    {{ $msg->created_at->format('H:i') }}
                                    @if($msg->sender_type === 'admin')
                                        <span class="tick {{ $msg->is_read ? 'read' : '' }}">
                                            {{ $msg->is_read ? ' ✓✓' : ' ✓' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Input --}}
            <div class="chat-input-bar">
                @if ($attachmentFile)
                    <div class="mb-2 px-3 py-1 bg-amber-500/20 text-amber-500 rounded text-xs inline-flex items-center gap-2">
                        <span>File selected: {{ $attachmentFile->getClientOriginalName() }}</span>
                        <button type="button" wire:click="$set('attachmentFile', null)" class="text-amber-500 hover:text-amber-700">
                            &times;
                        </button>
                    </div>
                @endif
                <form wire:submit.prevent="sendMessage">
                    <div class="input-inner">
                        <label class="cursor-pointer p-2 rounded-full hover:bg-gray-700 transition">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            <input type="file" wire:model="attachmentFile" class="hidden" accept="image/*,.pdf" />
                        </label>
                        <input
                            type="text"
                            wire:model="newMessage"
                            placeholder="Type a reply..."
                            autocomplete="off"
                        />
                        <button type="submit" class="send-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </div>
                </form>
            </div>

        @else
            {{-- Empty state (no user selected) --}}
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <p>Select a conversation</p>
                <span>Choose a user from the list to view their messages</span>
            </div>
        @endif

    </div>
</div>

<script>
    function scrollChatToBottom() {
        const box = document.getElementById('chat-messages-box');
        if (box) box.scrollTop = box.scrollHeight;
    }
    scrollChatToBottom();
    document.addEventListener('livewire:update', scrollChatToBottom);
</script>

</x-filament-panels::page>
