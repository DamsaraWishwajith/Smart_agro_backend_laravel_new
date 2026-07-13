<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Message;

class UserChat extends Component
{
    public User $user;
    public string $newMessage = '';

    public function mount(User $user)
    {
        $this->user = $user;
        $this->markAsRead();
    }

    /**
     * Mark all incoming user messages as read.
     */
    public function markAsRead()
    {
        Message::where('user_id', $this->user->id)
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Send message from the admin to the user.
     */
    public function sendMessage()
    {
        if (empty(trim($this->newMessage))) {
            return;
        }

        Message::create([
            'user_id' => $this->user->id,
            'sender_type' => 'admin',
            'message' => trim($this->newMessage),
            'is_read' => false,
        ]);

        $this->newMessage = '';
        $this->dispatch('message-sent');
    }

    public function render()
    {
        // Mark as read whenever the chat renders (in case user sends new messages while open)
        $this->markAsRead();

        $messages = Message::where('user_id', $this->user->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('livewire.user-chat', [
            'messages' => $messages,
        ]);
    }
}
