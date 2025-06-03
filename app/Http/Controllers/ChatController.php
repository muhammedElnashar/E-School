<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\Chat;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function createOrGetConversation(Request $request)
    {
        $user1 = Auth::id();
        $user2 = $request->user_id;

        if ($user1 == $user2) {
            return response()->json(['message' => 'Cannot start chat with yourself'], 400);
        }

        $conversation = Conversation::where(function ($q) use ($user1, $user2) {
            $q->where('user_one_id', $user1)->where('user_two_id', $user2);
        })->orWhere(function ($q) use ($user1, $user2) {
            $q->where('user_one_id', $user2)->where('user_two_id', $user1);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => $user1,
                'user_two_id' => $user2,
            ]);
        }

        return response()->json(['conversation' => $conversation]);
    }

    // إرسال رسالة
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required|string',
        ]);

        $chat = Chat::create([
            'conversation_id' => $request->conversation_id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        broadcast(new NewMessage($chat))->toOthers();

        return response()->json(['message' => $chat]);
    }


    public function getMessages($conversation_id)
    {
        $conversation = Conversation::findOrFail($conversation_id);

        if (!in_array(Auth::id(), [$conversation->user_one_id, $conversation->user_two_id])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = $conversation->messages()->with('sender')->orderBy('created_at')->get();

        return response()->json(['messages' => $messages]);
    }


    public function userConversations()
    {
        $userId = Auth::id();

        $conversations = Conversation::with(['userOne', 'userTwo'])
            ->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->get();

        return response()->json(['conversations' => $conversations]);
    }
}
