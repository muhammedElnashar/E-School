<?php

namespace App\Http\Controllers\Admin;

use App\Events\NewMessage;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $conversations = Conversation::with(['userOne', 'userTwo'])->where('user_one_id', Auth::id())->orWhere('user_two_id', Auth::id())
            ->latest('updated_at')
            ->paginate(10);

        return view('admin.chat.admin-chat', compact('conversations'));
    }

    public function search(Request $request)
    {
        $request->validate(['user_code' => 'required|string']);

        $user = User::where('user_code', $request->user_code)->first();

        if (!$user) {
            return redirect()->back()->withErrors('User Not Found');
        }

        return redirect()->route('admin.chat.withUser', $user->id);
    }

    public function chatWithUser($userId)
    {
        $admin = auth()->user();

        $conversation = Conversation::firstOrCreate(
            [
                ['user_one_id', '=', $admin->id],
                ['user_two_id', '=', $userId],
            ],
            [
                'user_one_id' => $admin->id,
                'user_two_id' => $userId,
            ]
        );

        $messages = Chat::where('conversation_id', $conversation->id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        $targetUser = User::findOrFail($userId);

        return view('admin.chat.chat', compact('conversation', 'messages', 'targetUser'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required|string',
        ]);

        $chat = Chat::create([
            'conversation_id' => $request->conversation_id,
            'sender_id' => auth()->id(),
            'message' => $request->message,
        ]);

        $chat->conversation->update(['last_message' => $request->message]);
        broadcast(new NewMessage($chat))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully'
        ]);
    }
    public function deleteConversation($id)
    {
        $conversations = Conversation::findOrFail($id);
        $conversations->delete();
        return redirect()->back()->with('success', 'Conversations deleted successfully.');
    }

}
