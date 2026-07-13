<?php

namespace App\Filament\Pages;

use App\Models\Message;
use App\Models\User;
use Filament\Pages\Page;
use Livewire\Attributes\On;

use Livewire\WithFileUploads;

class ChatPage extends Page
{
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Chat';
    protected static ?string $title = 'Chat';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.chat-page';

    // The currently selected user ID
    public ?int $selectedUserId = null;

    // Admin reply text
    public string $newMessage = '';
    
    // File upload
    public $attachmentFile;

    public function mount(): void
    {
        // Auto-select first user with messages, if any
        $firstUser = User::has('messages')->orderByDesc('updated_at')->first();
        if ($firstUser) {
            $this->selectedUserId = $firstUser->id;
        }
    }

    /**
     * Select a user to chat with.
     */
    public function selectUser(int $userId): void
    {
        $this->selectedUserId = $userId;
        $this->newMessage = '';
        $this->attachmentFile = null;
        $this->markUserMessagesAsRead($userId);
    }

    /**
     * Mark all incoming user messages as read.
     */
    protected function markUserMessagesAsRead(int $userId): void
    {
        Message::where('user_id', $userId)
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Send an admin message to the selected user.
     */
    public function sendMessage(): void
    {
        if (!$this->selectedUserId || (empty(trim($this->newMessage)) && !$this->attachmentFile)) {
            return;
        }

        $attachmentPath = null;
        $attachmentType = null;

        if ($this->attachmentFile) {
            $path = $this->attachmentFile->store("chat_attachments/{$this->selectedUserId}", 'public');
            $attachmentPath = $path;
            
            $extension = strtolower($this->attachmentFile->getClientOriginalExtension());
            $attachmentType = in_array($extension, ['pdf']) ? 'pdf' : 'image';
        }

        Message::create([
            'user_id' => $this->selectedUserId,
            'sender_type' => 'admin',
            'message' => trim($this->newMessage),
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'is_read' => false,
        ]);

        $this->newMessage = '';
        $this->attachmentFile = null;
    }

    /**
     * Get all users — those with messages first (ordered by latest message), then the rest.
     */
    public function getUsersWithCounts(): \Illuminate\Support\Collection
    {
        return User::withCount([
                'messages as unread_count' => fn ($q) => $q->where('sender_type', 'user')->where('is_read', false),
            ])
            ->withCount('messages as total_messages')
            ->orderByDesc('unread_count')
            ->orderByDesc('total_messages')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get messages for the currently selected user.
     */
    public function getSelectedMessages(): \Illuminate\Support\Collection
    {
        if (!$this->selectedUserId) {
            return collect();
        }

        // Mark as read on every render
        $this->markUserMessagesAsRead($this->selectedUserId);

        return Message::where('user_id', $this->selectedUserId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get the selected user model.
     */
    public function getSelectedUser(): ?User
    {
        if (!$this->selectedUserId) return null;
        return User::find($this->selectedUserId);
    }
}
