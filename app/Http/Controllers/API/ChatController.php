<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Services\GroqChatService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    protected GroqChatService $groqService;

    public function __construct(GroqChatService $groqService)
    {
        $this->groqService = $groqService;
    }

    /**
     * Start a new chat session
     */
    public function startSession(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            $forceNew = $request->input('force_new', false);

            // If force_new is true, end all existing active sessions
            if ($forceNew) {
                ChatSession::where('user_id', $userId)
                    ->where('status', 'active')
                    ->update(['status' => 'ended']);

                Log::info('Forced new chat session - ended previous active sessions', [
                    'user_id' => $userId
                ]);
            } else {
                // Check for existing active session (original behavior)
                $existingSession = ChatSession::where('user_id', $userId)
                    ->where('status', 'active')
                    ->latest()
                    ->first();

                if ($existingSession && !$existingSession->hasTimedOut()) {
                    return response()->json([
                        'success' => true,
                        'session_token' => $existingSession->session_token,
                        'session_id' => $existingSession->id,
                        'message' => 'Resumed existing session'
                    ]);
                }
            }

            // Create new session
            $session = ChatSession::create([
                'user_id' => $userId,
                'user_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'active'
            ]);

            // Send welcome message
            $welcomeMessage = $this->getWelcomeMessage($userId);

            ChatMessage::create([
                'chat_session_id' => $session->id,
                'role' => 'assistant',
                'message' => $welcomeMessage
            ]);

            return response()->json([
                'success' => true,
                'session_token' => $session->session_token,
                'session_id' => $session->id,
                'welcome_message' => $welcomeMessage,
                'is_new_session' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to start chat session', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to start chat session',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Send a message and get AI response
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_token' => 'required|string',
            'message' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = auth()->id();

            $session = ChatSession::where('session_token', $request->session_token)
                ->where('user_id', $userId) // Ensure user owns this session
                ->where('status', 'active')
                ->firstOrFail();

            // Check if session timed out
            if ($session->hasTimedOut()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session has timed out. Please start a new session.',
                    'timeout' => true
                ], 410);
            }

            // Update session activity
            $session->touchActivity();

            // Save user message
            $userMessage = ChatMessage::create([
                'chat_session_id' => $session->id,
                'role' => 'user',
                'message' => $request->message
            ]);

            // Build context
            $context = $this->groqService->buildRestaurantContext($session->user_id);

            // Get chat history (last 10 exchanges)
            $chatHistory = $session->messages()
                ->where('id', '<', $userMessage->id)
                ->latest()
                ->take(20)
                ->get()
                ->reverse()
                ->map(function ($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->message
                    ];
                })
                ->toArray();

            // Get AI response
            $aiResponse = $this->groqService->sendMessage(
                $request->message,
                $context,
                $chatHistory
            );

            if (!$aiResponse['success']) {
                return response()->json([
                    'success' => false,
                    'error' => 'AI service unavailable. Please try again later.'
                ], 503);
            }

            // Save AI response
            $assistantMessage = ChatMessage::create([
                'chat_session_id' => $session->id,
                'role' => 'assistant',
                'message' => $aiResponse['message'],
                'metadata' => [
                    'model' => $aiResponse['model'] ?? null,
                    'usage' => $aiResponse['usage'] ?? null
                ]
            ]);

            return response()->json([
                'success' => true,
                'user_message' => $userMessage->toApiFormat(),
                'assistant_message' => $assistantMessage->toApiFormat(),
                'session_active' => true
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid session. Please start a new chat.'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Failed to send chat message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_token' => $request->session_token
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to process message',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get chat history for a session
     */
    public function getHistory(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = auth()->id();

            $session = ChatSession::where('session_token', $request->session_token)
                ->where('user_id', $userId) // Ensure user owns this session
                ->firstOrFail();

            $messages = $session->messages()
                ->get()
                ->map(fn($msg) => $msg->toApiFormat());

            return response()->json([
                'success' => true,
                'messages' => $messages,
                'session_status' => $session->status,
                'is_active' => $session->isActive()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Session not found'
            ], 404);
        }
    }

    /**
     * End a chat session
     */
    public function endSession(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = auth()->id();

            $session = ChatSession::where('session_token', $request->session_token)
                ->where('user_id', $userId) // Ensure user owns this session
                ->firstOrFail();

            $session->endSession();

            return response()->json([
                'success' => true,
                'message' => 'Chat session ended successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Session not found'
            ], 404);
        }
    }

    /**
     * Clear chat history for current session
     */
    public function clearHistory(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = auth()->id();

            $session = ChatSession::where('session_token', $request->session_token)
                ->where('user_id', $userId) // Ensure user owns this session
                ->firstOrFail();

            // Delete all messages for this session
            $deletedCount = ChatMessage::where('chat_session_id', $session->id)->delete();

            Log::info('Chat history cleared', [
                'session_id' => $session->id,
                'messages_deleted' => $deletedCount,
                'user_id' => $session->user_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chat history cleared successfully',
                'deleted_count' => $deletedCount
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Session not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to clear chat history', [
                'error' => $e->getMessage(),
                'session_token' => $request->session_token
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to clear chat history'
            ], 500);
        }
    }

    /**
     * Get welcome message based on user
     */
    private function getWelcomeMessage(?int $userId): string
    {
        if ($userId) {
            $user = \App\Models\User::find($userId);
            $name = $user ? $user->name : 'there';
            return "Hi {$name}! 👋 Welcome to The Stag SmartDine AI Assistant. I can help you with our menu, orders, reservations, and any questions about our restaurant. What can I help you with today?";
        }

        return "Hi there! 👋 Welcome to The Stag SmartDine AI Assistant. I can help you with our menu, orders, reservations, and any questions about our restaurant. What can I help you with today?";
    }

    /**
     * Get all chat sessions for current user
     */
    public function getAllSessions(): JsonResponse
    {
        try {
            $userId = auth()->id();

            $sessions = ChatSession::where('user_id', $userId)
                ->withCount('messages')
                ->with(['messages' => function ($query) {
                    $query->latest()->take(1); // Get last message
                }])
                ->orderBy('created_at', 'desc')
                ->limit(20) // Limit to recent 20 sessions
                ->get()
                ->map(function ($session) {
                    return [
                        'id' => $session->id,
                        'session_token' => $session->session_token,
                        'status' => $session->status,
                        'created_at' => $session->created_at->toIso8601String(),
                        'created_at_human' => $session->created_at->diffForHumans(),
                        'message_count' => $session->messages_count,
                        'last_message' => $session->messages->first() ? [
                            'role' => $session->messages->first()->role,
                            'message' => \Str::limit($session->messages->first()->message, 100),
                            'created_at' => $session->messages->first()->created_at->toIso8601String()
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'sessions' => $sessions
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get chat sessions', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to load chat history'
            ], 500);
        }
    }

    /**
     * Delete specific chat session
     */
    public function deleteSession($sessionId): JsonResponse
    {
        try {
            $userId = auth()->id();

            $session = ChatSession::where('id', $sessionId)
                ->where('user_id', $userId)
                ->firstOrFail();

            // Delete all messages
            ChatMessage::where('chat_session_id', $session->id)->delete();

            // Delete session
            $session->delete();

            return response()->json([
                'success' => true,
                'message' => 'Chat session deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Session not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Failed to delete chat session', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to delete session'
            ], 500);
        }
    }

    /**
     * Health check for chat service
     */
    public function healthCheck(): JsonResponse
    {
        $groqAvailable = $this->groqService->healthCheck();

        return response()->json([
            'success' => true,
            'groq_service' => $groqAvailable ? 'available' : 'unavailable',
            'timestamp' => now()->toIso8601String()
        ]);
    }
}
