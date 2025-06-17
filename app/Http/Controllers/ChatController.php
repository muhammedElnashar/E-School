<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'user_code' => 'required|exists:users,user_code',
            'message' => 'required|string',
        ]);

        $sender = auth()->user();
        $receiver = User::where('user_code', $request->user_code)->first();

        if ($sender->id === $receiver->id) {
            return response()->json(['error' => 'Can Not Chat With Yourself'], 422);
        }
        $userOne = min($sender->id, $receiver->id);
        $userTwo = max($sender->id, $receiver->id);

        $conversation = Conversation::firstOrCreate(
            [
                'user_one_id' => $userOne,
                'user_two_id' => $userTwo,
            ],
            [
                'user_one_id' => $userOne,
                'user_two_id' => $userTwo,
                'last_message' => null,
            ]
        );
        $chat = Chat::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'message' => $request->message,
        ]);

        $conversation->update(['last_message' => $request->message]);

        broadcast(new NewMessage($chat))->toOthers();

        return response()->json(['message' => 'Message Send Successfully', 'conversation_id' => $conversation->id]);
    }

    // جلب كل المحادثات للمستخدم
    public function getConversations()
    {
        $userId = auth()->id();

        $conversations = Conversation::with(['userOne', 'userTwo'])
            ->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($conv) use ($userId) {
                $otherUser = $conv->user_one_id === $userId ? $conv->userTwo : $conv->userOne;
                return [
                    'id' => $conv->id,
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'user_code' => $otherUser->user_code,
                        'image' => asset('images/' . $otherUser->image),

                    ],
                    'updated_at' => $conv->updated_at->diffForHumans(),
                ];
            });

        return response()->json($conversations);
    }


    public function getMessages($conversationId)
    {
        $conversation = Conversation::with('messages.sender')->findOrFail($conversationId);

        $userId = auth()->id();
        if (!in_array($userId, [$conversation->user_one_id, $conversation->user_two_id])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'Message' => MessageResource::collection($conversation->messages)
        ], 201);
    }

    public function searchUser(Request $request)
    {
        $request->validate(['query' => 'required|string']);

        $query = $request->input('query');

        $user = User::where('user_code', $query)
            ->orWhere('name', 'LIKE', '%' . $query . '%')
            ->first();
        if (!$user) {
            return response()->json(['message' => 'User Not Found'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'user_code' => $user->user_code,
            'image' => asset('images/' . $user->image),
        ], 201);
    }

    public function destroyChat($id)
    {
        $user = auth()->user();
        $conversation = Conversation::where(function ($query) use ($user) {
            $query->where('user_one_id', $user->id)
                ->orWhere('user_two_id', $user->id);
        })->findOrFail($id);
        if ($conversation->user_one_id !== $user->id && $conversation->user_two_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $conversation->delete();
        return response()->json(['message' => 'Chat deleted successfully'], 200);
    }
}
