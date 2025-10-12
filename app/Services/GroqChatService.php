<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class GroqChatService
{
    private string $apiKey;
    private string $baseUrl;
    private string $model;
    private int $timeout;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
        $this->baseUrl = config('services.groq.base_url', 'https://api.groq.com/openai/v1');
        $this->model = config('services.groq.model', 'llama3-8b-8192');
        $this->timeout = config('services.groq.timeout', 30);
    }

    /**
     * Send chat message to Groq AI with restaurant context
     */
    public function sendMessage(string $message, array $context = [], array $chatHistory = []): array
    {
        try {
            $systemPrompt = $this->buildSystemPrompt($context);
            $messages = $this->buildMessageHistory($systemPrompt, $chatHistory, $message);

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->baseUrl}/chat/completions", [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 500,
                    'stream' => false
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'message' => $data['choices'][0]['message']['content'] ?? 'No response generated',
                    'usage' => $data['usage'] ?? [],
                    'model' => $data['model'] ?? $this->model
                ];
            }

            Log::error('Groq API error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to get response from AI service',
                'status_code' => $response->status()
            ];

        } catch (Exception $e) {
            Log::error('Groq service error', [
                'message' => $e->getMessage(),
                'context_size' => count($context),
                'history_size' => count($chatHistory)
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Build system prompt with restaurant context
     */
    private function buildSystemPrompt(array $context): string
    {
        $menuItems = $context['menu_items'] ?? [];
        $userProfile = $context['user_profile'] ?? [];
        $currentCart = $context['current_cart'] ?? [];

        $prompt = "You are The Stag SmartDine AI Assistant, a restaurant chatbot helping customers. ";
        $prompt .= "You must respond ONLY in English.\n\n";

        $prompt .= "RESTAURANT INFORMATION:\n";
        $prompt .= "- Name: The Stag SmartDine\n";
        $prompt .= "- Services: Dine-in, Takeaway, QR Menu Ordering\n";
        $prompt .= "- Payment: Online payment (Toyyibpay)\n";
        $prompt .= "- Features: AI recommendations, table reservations, order tracking\n\n";

        if (!empty($menuItems) && $menuItems->count() > 0) {
            $prompt .= "AVAILABLE MENU (TOP ITEMS):\n";
            foreach ($menuItems->take(15) as $item) {
                $status = $item->availability ? '✓' : '✗';
                $prompt .= "- {$status} {$item->name} ({$item->category}) - RM{$item->price}";
                if ($item->is_featured) {
                    $prompt .= " [POPULAR]";
                }
                $prompt .= "\n";
            }
            $prompt .= "\n";
        } else {
            $prompt .= "⚠️ MENU STATUS: Currently NO menu items in database.\n";
            $prompt .= "CRITICAL: When asked about menu, tell customer:\n";
            $prompt .= "- Menu is being updated\n";
            $prompt .= "- Ask them to check back later or contact staff\n";
            $prompt .= "- DO NOT make up, suggest, or list any specific food/drink items\n";
            $prompt .= "- DO NOT mention prices or menu names that don't exist\n\n";
        }

        if (!empty($userProfile)) {
            $prompt .= "CUSTOMER PROFILE:\n";
            $prompt .= "- Name: " . ($userProfile['name'] ?? 'Guest') . "\n";
            if (!empty($userProfile['preferences'])) {
                $prompt .= "- Favorites: " . implode(', ', $userProfile['preferences']) . "\n";
            }
            $prompt .= "\n";
        }

        if (!empty($currentCart) && $currentCart->count() > 0) {
            $prompt .= "CURRENT CART:\n";
            foreach ($currentCart as $item) {
                $prompt .= "- {$item->quantity}x {$item->name} (RM{$item->unit_price})\n";
            }
            $prompt .= "\n";
        }

        $prompt .= "⚠️ STRICT SCOPE RULES (CRITICAL - SAVE TOKENS!):\n";
        $prompt .= "You ONLY answer questions about The Stag SmartDine restaurant:\n";
        $prompt .= "✅ ALLOWED: Menu, food, drinks, prices, orders, reservations, services, location, payment\n";
        $prompt .= "❌ FORBIDDEN: Politics, sports, weather, news, coding, math, science, personal advice, general knowledge\n\n";

        $prompt .= "IF OUT-OF-SCOPE QUESTION DETECTED:\n";
        $prompt .= "Respond with this exact template (short & polite):\n";
        $prompt .= "\"Sorry, I can only help with questions about The Stag SmartDine restaurant. Is there anything you'd like to know about our menu or reservations?\"\n";
        $prompt .= "DO NOT elaborate. DO NOT answer the question. Just redirect.\n\n";

        $prompt .= "IMPORTANT INSTRUCTIONS:\n";
        $prompt .= "1. Answer friendly in ENGLISH ONLY\n";
        $prompt .= "2. STRICT scope - reject all non-restaurant topics immediately\n";
        $prompt .= "3. Recommend menu based on preferences\n";
        $prompt .= "4. Help with orders & reservations\n";
        $prompt .= "5. Keep responses SHORT (max 4-5 lines)\n";
        $prompt .= "6. Use RM for prices\n";
        $prompt .= "7. If unsure, ask customer to contact staff\n\n";

        $prompt .= "RESPONSE FORMAT (IMPORTANT!):\n";
        $prompt .= "- Use **bold** to highlight important items (example: **Nasi Lemak**, **RM12.90**)\n";
        $prompt .= "- Use numbered lists (1. 2. 3.) for multiple items\n";
        $prompt .= "- Short paragraphs - max 2-3 sentences\n";
        $prompt .= "- Add line breaks between sections\n";
        $prompt .= "- Format: Greeting → Info → Helpful question\n\n";

        return $prompt;
    }

    /**
     * Build message history for API request
     */
    private function buildMessageHistory(string $systemPrompt, array $chatHistory, string $currentMessage): array
    {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Add recent history (last 10 exchanges = 20 messages)
        $recentHistory = array_slice($chatHistory, -20);
        foreach ($recentHistory as $msg) {
            $messages[] = [
                'role' => $msg['role'] ?? 'user',
                'content' => $msg['content'] ?? $msg['message'] ?? ''
            ];
        }

        // Add current message
        $messages[] = [
            'role' => 'user',
            'content' => $currentMessage
        ];

        return $messages;
    }

    /**
     * Build restaurant context for chat
     */
    public function buildRestaurantContext(?int $userId = null): array
    {
        return [
            'menu_items' => $this->getAvailableMenuItems(),
            'user_profile' => $userId ? $this->getUserProfile($userId) : [],
            'current_cart' => $userId ? $this->getCurrentCart($userId) : [],
            'operating_hours' => $this->getOperatingHours()
        ];
    }

    /**
     * Get available menu items for context
     */
    private function getAvailableMenuItems()
    {
        try {
            return DB::table('menu_items')
                ->leftJoin('categories', 'menu_items.category_id', '=', 'categories.id')
                ->whereNull('menu_items.deleted_at')
                ->select([
                    'menu_items.id',
                    'menu_items.name',
                    'menu_items.description',
                    'menu_items.price',
                    'menu_items.availability',
                    'categories.name as category',
                    'menu_items.is_featured',
                    'menu_items.allergens'
                ])
                ->orderByDesc('menu_items.availability')
                ->orderByDesc('menu_items.is_featured')
                ->orderBy('categories.name')
                ->limit(30)
                ->get();
        } catch (Exception $e) {
            Log::warning('Failed to get menu items for chat context', ['error' => $e->getMessage()]);
            return collect([]);
        }
    }

    /**
     * Get current cart items for user
     */
    private function getCurrentCart(int $userId)
    {
        try {
            return DB::table('user_carts')
                ->join('menu_items', 'user_carts.menu_item_id', '=', 'menu_items.id')
                ->where('user_carts.user_id', $userId)
                ->whereNull('user_carts.deleted_at')
                ->select([
                    'menu_items.name',
                    'user_carts.quantity',
                    'user_carts.unit_price',
                    'user_carts.special_notes'
                ])
                ->get();
        } catch (Exception $e) {
            Log::warning('Failed to get current cart', ['user_id' => $userId, 'error' => $e->getMessage()]);
            return collect([]);
        }
    }

    /**
     * Get user profile for personalization
     */
    private function getUserProfile(int $userId): array
    {
        try {
            $user = DB::table('users')
                ->leftJoin('customer_profiles', 'users.id', '=', 'customer_profiles.user_id')
                ->where('users.id', $userId)
                ->select([
                    'users.name',
                    'users.email',
                    'customer_profiles.phone_number'
                ])
                ->first();

            if (!$user) {
                return [];
            }

            // Get recent order preferences (categories ordered most)
            $recentOrders = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
                ->join('categories', 'menu_items.category_id', '=', 'categories.id')
                ->where('orders.user_id', $userId)
                ->where('orders.order_status', '!=', 'cancelled')
                ->where('orders.created_at', '>=', now()->subDays(60))
                ->whereNull('orders.deleted_at')
                ->select('categories.name as category')
                ->groupBy('categories.name')
                ->orderByRaw('COUNT(*) DESC')
                ->limit(5)
                ->pluck('category')
                ->toArray();

            return [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone_number,
                'preferences' => $recentOrders
            ];

        } catch (Exception $e) {
            Log::warning('Failed to get user profile for chat', ['user_id' => $userId, 'error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get restaurant operating hours
     */
    private function getOperatingHours(): array
    {
        return [
            'monday' => '09:00 - 22:00',
            'tuesday' => '09:00 - 22:00',
            'wednesday' => '09:00 - 22:00',
            'thursday' => '09:00 - 22:00',
            'friday' => '09:00 - 23:00',
            'saturday' => '09:00 - 23:00',
            'sunday' => '10:00 - 21:00'
        ];
    }

    /**
     * Check if Groq service is available
     */
    public function healthCheck(): bool
    {
        try {
            if (empty($this->apiKey)) {
                return false;
            }

            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])
                ->get("{$this->baseUrl}/models");

            return $response->successful();

        } catch (Exception $e) {
            Log::warning('Groq service health check failed', [
                'message' => $e->getMessage()
            ]);

            return false;
        }
    }
}
