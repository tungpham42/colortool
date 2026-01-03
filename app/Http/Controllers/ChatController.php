<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GroqService;

class ChatController extends Controller
{
    public function sendMessage(Request $request, GroqService $groq)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'array', // History helps the bot remember context
        ]);

        // Format user input for the API
        // In a real app, you might validate the 'history' structure strictly
        $userMessage = ['role' => 'user', 'content' => $request->input('message')];

        // Combine previous history with new message
        $messages = array_merge($request->input('history', []), [$userMessage]);

        // Get response from Groq
        $botReply = $groq->chat($messages);

        return response()->json([
            'reply' => $botReply,
            // Return the new history state to the frontend
            'history' => array_merge($messages, [['role' => 'assistant', 'content' => $botReply]])
        ]);
    }
}
