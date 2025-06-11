<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Conversation;
use Illuminate\Http\Request;

class MangeConversation extends Controller
{
    public function conversations()
    {
        $authId = auth()->id();
        $conversations = Conversation::with(['userOne', 'userTwo'])
            ->where('user_one_id', '!=', $authId)
            ->where('user_two_id', '!=', $authId)
            ->paginate(5);

        return view('admin.chat.conversations', compact('conversations'));
    }

    public function showConversation($id)
    {
        $conversation = Conversation::findOrFail($id);

        // Paginate الرسائل فقط
        $messages = $conversation->messages()
            ->with('sender')
            ->latest()
            ->paginate(10);
        return view('admin.chat.show', compact('conversation', 'messages'));
    }

    public function deleteMessage($id)
    {
        $message = Chat::findOrFail($id);
        $message->delete();

        return redirect()->back()->with('success', 'Message deleted successfully.');
    }
}
