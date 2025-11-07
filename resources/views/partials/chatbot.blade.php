@auth
<!-- Floating Chatbot Button (Only for Logged-in Users) -->
<div id="chatbot-toggle" class="chatbot-toggle">
    <i class="fas fa-comments"></i>
    <span class="chatbot-badge" id="chatbot-badge" style="display: none;">1</span>
</div>

<!-- Chatbot Window -->
<div id="chatbot-window" class="chatbot-window hidden">
    <div class="chatbot-header">
        <div class="chatbot-header-content">
            <div class="chatbot-title">
                <img src="{{ asset('images/logo.png') }}" alt="logo" class="chatbot-logo">
                <div class="chatbot-title-text">The Stag AI Assistant</div>
            </div>
            <span class="chatbot-status" id="chatbot-status">
                <span class="status-dot"></span>
                <span class="status-text">Online</span>
            </span>
        </div>
        <div class="chatbot-header-actions">
            <button id="chatbot-clear" class="chatbot-action-btn" title="Clear current chat">
                <i class="fas fa-trash-alt"></i>
            </button>
            <button id="chatbot-close" class="chatbot-close-btn">&times;</button>
        </div>
    </div>
    <div id="chatbot-body" class="chatbot-body">
        <!-- Welcome Screen -->
        <div class="chatbot-welcome" id="chatbot-welcome">
            <h2 class="welcome-title">Welcome to Stag AI</h2>
            <p class="welcome-subtitle">I'm here to help you with our menu, orders, and reservations. Ask me anything!</p>

            <div class="welcome-options">
                <button class="welcome-option" data-prompt="Getting Started">
                    <span class="option-icon">📋</span>
                    <span class="option-text">Getting Started</span>
                </button>
                <button class="welcome-option" data-prompt="Features">
                    <span class="option-icon">⭐</span>
                    <span class="option-text">Our Features</span>
                </button>
                <button class="welcome-option" data-prompt="Examples">
                    <span class="option-icon">💡</span>
                    <span class="option-text">What Can You Do?</span>
                </button>
            </div>
        </div>

        <!-- Loading Screen -->
        <div class="chatbot-loading hidden" id="chatbot-loading">
            <div class="loading-spinner"></div>
            <p>Starting chat...</p>
        </div>
    </div>
    <div class="chatbot-footer">
        <input id="chatbot-input" type="text" placeholder="Type your message..." autocomplete="off" disabled>
        <button id="chatbot-send" class="chatbot-send-btn" disabled>
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<style>
    .chatbot-toggle {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #5f48ff, #7c3aed);
        box-shadow: 0 4px 15px rgba(95, 72, 255, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 1000;
        transition: all 0.3s ease;
        border: none;
        color: white;
        font-size: 22px;
    }

    .chatbot-toggle:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(95, 72, 255, 0.6);
    }

    .chatbot-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ef4444;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }

    .chatbot-window {
        position: fixed;
        bottom: 85px;
        right: 20px;
        width: 340px;
        height: 480px;
        background: #fff;
        border-radius: 14px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        z-index: 1001;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .hidden {
        display: none !important;
    }

    .chatbot-header {
        background: linear-gradient(135deg, #5f48ff, #7c3aed);
        color: #fff;
        padding: 14px 16px;
        border-radius: 14px 14px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .chatbot-header-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .chatbot-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chatbot-logo {
        width: 28px;
        height: 28px;
        object-fit: contain;
        border-radius: 6px;
        flex-shrink: 0;
    }

    .chatbot-title-text {
        font-weight: 600;
        font-size: 14px;
        line-height: 28px;
    }

    .chatbot-status {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        opacity: 0.9;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    .chatbot-header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chatbot-action-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        font-size: 13px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .chatbot-action-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.05);
    }

    .chatbot-close-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        font-size: 22px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .chatbot-close-btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .chatbot-body {
        flex: 1;
        padding: 16px;
        overflow-y: auto;
        background: #f9fafb;
        position: relative;
    }

    .chatbot-body::-webkit-scrollbar {
        width: 6px;
    }

    .chatbot-body::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }

    .chatbot-welcome {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        padding: 20px 16px;
        text-align: center;
    }

    .welcome-icon {
        font-size: 64px;
        margin-bottom: 20px;
        animation: bounce 2s infinite;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .welcome-icon .logo {
        width: 100px;
        height: 100px;
        object-fit: contain;
        border-radius: 16px;
    }

    @keyframes bounce {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    .welcome-title {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 10px;
    }

    .welcome-subtitle {
        font-size: 13px;
        color: #6b7280;
        line-height: 1.6;
        margin-bottom: 24px;
        max-width: 280px;
    }

    .welcome-options {
        display: flex;
        flex-direction: column;
        gap: 12px;
        width: 100%;
        max-width: 280px;
    }

    .welcome-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 16px;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: left;
    }

    .welcome-option:hover {
        border-color: #5f48ff;
        background: #f9f7ff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(95, 72, 255, 0.15);
    }

    .option-icon {
        font-size: 20px;
    }

    .option-text {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
    }

    .chatbot-loading {
        display: none;
        /* Hidden by default */
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        gap: 12px;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #f9fafb;
        z-index: 10;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e5e7eb;
        border-top-color: #5f48ff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .message {
        margin-bottom: 16px;
        display: flex;
        flex-direction: column;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .message.user {
        align-items: flex-end;
    }

    .message.bot {
        align-items: flex-start;
    }

    .message-bubble {
        max-width: 75%;
        padding: 10px 14px;
        border-radius: 14px;
        font-size: 13px;
        line-height: 1.5;
        word-wrap: break-word;
    }

    .message.user .message-bubble {
        background: linear-gradient(135deg, #5f48ff, #7c3aed);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .message.bot .message-bubble {
        background: white;
        color: #1f2937;
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .message-bubble p {
        margin: 0 0 8px 0;
    }

    .message-bubble p:last-child {
        margin-bottom: 0;
    }

    .message-bubble strong {
        font-weight: 700;
        color: #5f48ff;
    }

    .message-bubble .message-list {
        margin: 8px 0;
        padding-left: 20px;
    }

    .message-bubble .message-list li {
        margin: 6px 0;
        line-height: 1.6;
    }

    .message-bubble .message-list li strong {
        display: block;
        margin-bottom: 2px;
    }

    .message-time {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 4px;
        padding: 0 4px;
    }

    .chatbot-footer {
        border-top: 1px solid #e5e7eb;
        padding: 14px;
        background: white;
        border-radius: 0 0 14px 14px;
        display: flex;
        gap: 8px;
    }

    .chatbot-footer input {
        flex: 1;
        padding: 10px 12px;
        border-radius: 20px;
        border: 1px solid #e5e7eb;
        font-size: 13px;
        outline: none;
        transition: all 0.2s;
    }

    .chatbot-footer input:focus {
        border-color: #5f48ff;
        box-shadow: 0 0 0 3px rgba(95, 72, 255, 0.1);
    }

    .chatbot-footer input:disabled {
        background: #f3f4f6;
        cursor: not-allowed;
    }

    .chatbot-send-btn {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, #5f48ff, #7c3aed);
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .chatbot-send-btn:hover:not(:disabled) {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(95, 72, 255, 0.4);
    }

    .chatbot-send-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .typing-indicator {
        display: flex;
        gap: 4px;
        padding: 10px 14px;
        background: white;
        border-radius: 14px;
        width: fit-content;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .typing-dot {
        width: 8px;
        height: 8px;
        background: #9ca3af;
        border-radius: 50%;
        animation: typing 1.4s infinite;
    }

    .typing-dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {

        0%,
        60%,
        100% {
            transform: translateY(0);
        }

        30% {
            transform: translateY(-10px);
        }
    }

    /* Typing Animation Cursor */
    .typing-message::after {
        content: '▌';
        color: #5f48ff;
        animation: blink-cursor 0.8s infinite;
        margin-left: 2px;
    }

    @keyframes blink-cursor {

        0%,
        49% {
            opacity: 1;
        }

        50%,
        100% {
            opacity: 0;
        }
    }

    .typing-message {
        cursor: pointer;
        position: relative;
    }

    .typing-message:hover::before {
        content: 'Click to show full text';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        white-space: nowrap;
        pointer-events: none;
        margin-bottom: 4px;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .typing-message:hover::before {
        opacity: 1;
    }



    /* Mobile Top Arrow for easier navigation */
    .mobile-top-arrow {
        display: none;
        /* Will be shown via media query */
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 1000;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #5f48ff, #7c3aed);
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(95, 72, 255, 0.4);
        cursor: pointer;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .mobile-top-arrow:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(95, 72, 255, 0.6);
    }

    @media (max-width: 768px) {
        .chatbot-window {
            width: calc(100% - 30px);
            height: 350px; /* Smaller height so button stays visible */
            max-height: calc(100vh - 180px); /* Ensure space for button below */
            top: auto;
            right: 15px;
            bottom: 150px; /* Position well above the chatbot button (85px + 50px + 15px gap) */
            left: 15px;
            border-radius: 12px;
        }

        .chatbot-toggle {
            /* Match scroll-to-top button size and positioning */
            width: 50px;
            height: 50px;
            font-size: 20px;
            bottom: 85px;
            /* Same as scroll-to-top: above bottom navbar (70px) + buffer */
            right: 15px;
            z-index: 1000;
        }

        .mobile-top-arrow {
            display: block;
        }
    }

    @media (max-width: 480px) {
        .chatbot-window {
            width: calc(100% - 24px);
            height: 320px; /* Even smaller on small screens */
            max-height: calc(100vh - 170px); /* Ensure button visibility */
            right: 12px;
            left: 12px;
            bottom: 145px; /* Keep well above button (85px + 45px + 15px gap) */
            border-radius: 10px;
        }

        .chatbot-toggle {
            /* Consistent size across all pages at 480px */
            width: 45px;
            height: 45px;
            font-size: 18px;
            bottom: 85px;
            right: 12px;
        }

        .chatbot-header {
            padding: 10px 12px;
        }

        .chatbot-logo {
            width: 22px;
            height: 22px;
        }

        .chatbot-title-text {
            font-size: 12px;
            line-height: 22px;
        }

        .chatbot-status {
            font-size: 10px;
        }
        
        .chatbot-action-btn,
        .chatbot-close-btn {
            width: 26px;
            height: 26px;
            font-size: 11px;
        }

        .chatbot-body {
            padding: 10px;
        }

        .welcome-title {
            font-size: 16px;
            margin-bottom: 8px;
        }

        .welcome-subtitle {
            font-size: 11px;
            margin-bottom: 18px;
        }
        
        .welcome-options {
            gap: 10px;
        }

        .welcome-option {
            padding: 10px 12px;
            gap: 8px;
        }
        
        .option-icon {
            font-size: 18px;
        }

        .option-text {
            font-size: 12px;
        }

        .message-bubble {
            font-size: 12px;
            padding: 8px 11px;
            max-width: 85%;
        }
        
        .message-time {
            font-size: 10px;
        }

        .chatbot-footer {
            padding: 10px;
        }

        .chatbot-footer input {
            font-size: 12px;
            padding: 8px 10px;
        }
        
        .chatbot-send-btn {
            width: 34px;
            height: 34px;
            font-size: 14px;
        }
    }
</style>


<script>
    // Chatbot State Management
    const ChatBot = {
        sessionToken: null,
        isOpen: false,
        isLoading: false,

        elements: {
            toggle: document.getElementById('chatbot-toggle'),
            window: document.getElementById('chatbot-window'),
            close: document.getElementById('chatbot-close'),
            clear: document.getElementById('chatbot-clear'),
            body: document.getElementById('chatbot-body'),
            input: document.getElementById('chatbot-input'),
            sendBtn: document.getElementById('chatbot-send'),
            loading: document.getElementById('chatbot-loading'),
            welcome: document.getElementById('chatbot-welcome'),
            status: document.getElementById('chatbot-status')
        },

        init() {
            console.log('ChatBot.init() called');
            console.log('Elements check:', {
                toggle: !!this.elements.toggle,
                window: !!this.elements.window,
                close: !!this.elements.close,
                body: !!this.elements.body,
                input: !!this.elements.input,
                sendBtn: !!this.elements.sendBtn
            });
            this.attachEventListeners();
            this.loadSessionFromStorage();
            console.log('ChatBot initialized successfully');
        },

        attachEventListeners() {
            this.elements.toggle.addEventListener('click', () => this.toggleWindow());
            this.elements.close.addEventListener('click', () => this.closeWindow());
            this.elements.clear.addEventListener('click', () => this.confirmClearHistory());
            this.elements.sendBtn.addEventListener('click', () => this.sendMessage());
            this.elements.input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });



            // Welcome option buttons
            document.querySelectorAll('.welcome-option').forEach(button => {
                button.addEventListener('click', (e) => {
                    const prompt = e.currentTarget.dataset.prompt;
                    this.handleWelcomeOption(prompt);
                });
            });
        },

        toggleWindow() {
            if (this.isOpen) {
                this.closeWindow();
            } else {
                this.openWindow();
            }
        },

        async openWindow() {
            this.elements.window.classList.remove('hidden');
            this.isOpen = true;

            if (!this.sessionToken) {
                await this.startSession();
            } else {
                // Try to load existing session history
                await this.loadSessionHistory();
                this.enableInput();
            }

            // Always scroll to bottom when chatbot is opened
            this.scrollToBottom();
        },

        closeWindow() {
            this.elements.window.classList.add('hidden');
            this.isOpen = false;
        },

        async startSession() {
            try {
                this.showLoading(true);
                console.log('Starting chatbot session...');

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                console.log('CSRF Token:', csrfToken ? 'Found' : 'Missing');

                const response = await fetch('/api/chatbot/start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                console.log('Response status:', response.status);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                console.log('Start session response:', data);

                if (data.success) {
                    console.log('Session started successfully!');
                    this.sessionToken = data.session_token;
                    localStorage.setItem('chatbot_session', this.sessionToken);

                    // Hide welcome screen
                    this.showWelcome(false);

                    // Display welcome message with typing effect
                    if (data.welcome_message) {
                        console.log('Showing welcome message:', data.welcome_message);
                        this.clearBody();
                        this.addMessageWithTypingEffect(data.welcome_message, 'bot', 25);
                    } else {
                        console.warn('No welcome message received');
                        this.clearBody();
                    }

                    this.enableInput();
                    this.updateStatus('online');
                    console.log('Chatbot ready for use');

                    // Scroll to bottom after session starts
                    this.scrollToBottom();
                } else {
                    const errorMsg = data.debug || data.error || 'Failed to start chat session';
                    console.error('Start session failed:', errorMsg);
                    this.clearBody();
                    this.showError(errorMsg);
                    this.updateStatus('offline');
                }
            } catch (error) {
                console.error('Chat session error:', error);
                this.showError('Unable to connect to chat service. Check console for details.');
                this.updateStatus('offline');
            } finally {
                this.showLoading(false);
            }
        },

        async sendMessage() {
            const message = this.elements.input.value.trim();

            if (!message || this.isLoading) return;

            if (!this.sessionToken) {
                await this.startSession();
                return;
            }

            try {
                this.isLoading = true;
                this.disableInput();

                // Display user message
                this.addMessage(message, 'user');
                this.elements.input.value = '';

                // Show typing indicator
                this.showTypingIndicator();

                const response = await fetch('/api/chatbot/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        session_token: this.sessionToken,
                        message: message
                    })
                });

                const data = await response.json();
                console.log('Send message response:', data);

                this.removeTypingIndicator();

                if (data.success) {
                    // Use typing animation for bot responses
                    this.addMessageWithTypingEffect(data.assistant_message.content, 'bot', 30);
                } else if (data.timeout) {
                    this.showError('Session timeout. Starting new session...');
                    this.sessionToken = null;
                    localStorage.removeItem('chatbot_session');
                    await this.startSession();
                } else if (response.status === 404 || (data.error && data.error.includes('Invalid session'))) {
                    // Handle invalid session - auto clear and start fresh
                    console.warn('Invalid session detected, clearing and starting fresh...');
                    this.sessionToken = null;
                    localStorage.removeItem('chatbot_session');
                    this.clearBody();
                    this.showWelcome(false);
                    this.showError('Session expired. Starting fresh session...');
                    await this.startSession();
                } else {
                    const errorMsg = data.debug || data.error || 'Failed to send message';
                    console.error('Send message failed:', errorMsg);
                    this.showError(errorMsg);
                }
            } catch (error) {
                console.error('Send message error:', error);
                this.removeTypingIndicator();
                this.showError('Failed to send message. Check console for details.');
            } finally {
                this.isLoading = false;
                this.enableInput();
            }
        },

        addMessage(content, role, autoScroll = true) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${role}`;

            const bubble = document.createElement('div');
            bubble.className = 'message-bubble';

            // Format the message content
            bubble.innerHTML = this.formatMessageContent(content);

            const time = document.createElement('div');
            time.className = 'message-time';
            time.textContent = this.formatTime(new Date());

            messageDiv.appendChild(bubble);
            messageDiv.appendChild(time);

            this.elements.body.appendChild(messageDiv);

            if (autoScroll) {
                this.scrollToBottom();
            }
        },

        addMessageWithTypingEffect(content, role, speed = 30) {
            // Create message container
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${role}`;

            const bubble = document.createElement('div');
            bubble.className = 'message-bubble typing-message';
            bubble.textContent = ''; // Start empty

            const time = document.createElement('div');
            time.className = 'message-time';
            time.textContent = this.formatTime(new Date());

            messageDiv.appendChild(bubble);
            messageDiv.appendChild(time);
            this.elements.body.appendChild(messageDiv);

            // Variables for typing animation
            let charIndex = 0;
            let isSkipped = false;

            // Allow clicking to skip animation and show full text
            bubble.addEventListener('click', () => {
                if (!isSkipped && charIndex < content.length) {
                    isSkipped = true;
                    clearInterval(typingInterval);
                    bubble.classList.remove('typing-message');
                    bubble.innerHTML = this.formatMessageContent(content);
                    this.scrollToBottom();
                }
            });

            // Typing animation
            const typingInterval = setInterval(() => {
                if (charIndex < content.length) {
                    // Add next character
                    bubble.textContent += content[charIndex];
                    charIndex++;

                    // Auto-scroll as text appears
                    this.scrollToBottom();
                } else {
                    // Animation complete
                    clearInterval(typingInterval);
                    bubble.classList.remove('typing-message');

                    // Now apply formatting (bold, lists, etc.)
                    bubble.innerHTML = this.formatMessageContent(content);
                    this.scrollToBottom();
                }
            }, speed);

            return messageDiv;
        },

        formatMessageContent(content) {
            // Convert markdown-style formatting to HTML
            let formatted = content;

            // Convert **bold** to <strong>
            formatted = formatted.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');

            // Convert numbered lists (1. Item) to proper list
            if (formatted.match(/^\d+\.\s/m)) {
                const lines = formatted.split('\n');
                let inList = false;
                let result = [];

                lines.forEach(line => {
                    if (line.match(/^\d+\.\s/)) {
                        if (!inList) {
                            result.push('<ol class="message-list">');
                            inList = true;
                        }
                        const text = line.replace(/^\d+\.\s/, '');
                        result.push(`<li>${text}</li>`);
                    } else {
                        if (inList) {
                            result.push('</ol>');
                            inList = false;
                        }
                        if (line.trim()) {
                            result.push(`<p>${line}</p>`);
                        }
                    }
                });

                if (inList) {
                    result.push('</ol>');
                }

                formatted = result.join('');
            } else {
                // Just convert line breaks to <br> for simple messages
                formatted = formatted.replace(/\n/g, '<br>');
            }

            return formatted;
        },

        showTypingIndicator() {
            const indicator = document.createElement('div');
            indicator.className = 'message bot';
            indicator.id = 'typing-indicator';

            const typingDiv = document.createElement('div');
            typingDiv.className = 'typing-indicator';
            typingDiv.innerHTML = '<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>';

            indicator.appendChild(typingDiv);
            this.elements.body.appendChild(indicator);
            this.scrollToBottom();
        },

        removeTypingIndicator() {
            const indicator = document.getElementById('typing-indicator');
            if (indicator) {
                indicator.remove();
            }
        },

        showError(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'message bot';
            errorDiv.innerHTML = `
            <div class="message-bubble" style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
                ${message}
            </div>
        `;
            this.elements.body.appendChild(errorDiv);
            this.scrollToBottom();
        },

        showLoading(show) {
            console.log('showLoading:', show);
            if (this.elements.loading) {
                this.elements.loading.classList.toggle('hidden', !show);
                console.log('Loading display set to:', show ? 'flex' : 'none');
            } else {
                console.warn('Loading element not found');
            }
        },

        showWelcome(show) {
            console.log('showWelcome called:', show);
            console.log('Welcome element exists:', !!this.elements.welcome);

            if (this.elements.welcome) {
                this.elements.welcome.style.display = show ? 'flex' : 'none';
                console.log('Welcome screen display set to:', this.elements.welcome.style.display);
            } else {
                console.error('Welcome element not found!');
            }
        },

        handleWelcomeOption(option) {
            console.log('Welcome option selected:', option);

            // Hide welcome screen
            this.showWelcome(false);

            // Map options to predefined prompts
            const prompts = {
                'Getting Started': 'How do I get started with ordering food?',
                'Features': 'What features does The Stag SmartDine offer?',
                'Examples': 'Can you show me some examples of what you can help me with?'
            };

            const prompt = prompts[option] || option;

            // Manually set input and send
            this.elements.input.value = prompt;
            this.sendMessage();
        },

        clearBody() {
            console.log('clearBody called');

            // Clear messages and info banners, keep welcome and loading screens
            const messages = this.elements.body.querySelectorAll('.message');
            messages.forEach(msg => msg.remove());

            // Also remove session-ended-info banners
            const endedInfos = this.elements.body.querySelectorAll('.session-ended-info');
            endedInfos.forEach(info => info.remove());

            console.log('Body cleared (messages and info banners removed)');
        },

        enableInput() {
            this.elements.input.disabled = false;
            this.elements.sendBtn.disabled = false;
            this.elements.input.focus();
        },

        disableInput() {
            this.elements.input.disabled = true;
            this.elements.sendBtn.disabled = true;
        },

        scrollToBottom() {
            // Use requestAnimationFrame for better timing with DOM rendering
            requestAnimationFrame(() => {
                this.elements.body.scrollTop = this.elements.body.scrollHeight;
            });

            // Also set a fallback timeout to ensure scroll happens
            setTimeout(() => {
                this.elements.body.scrollTop = this.elements.body.scrollHeight;
            }, 150);
        },

        loadSessionFromStorage() {
            const stored = localStorage.getItem('chatbot_session');
            if (stored) {
                this.sessionToken = stored;
            }
        },

        async loadSessionHistory() {
            try {
                console.log('Loading session history...');
                this.showWelcome(false);
                this.showLoading(true);

                const response = await fetch('/api/chatbot/history', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        session_token: this.sessionToken
                    })
                });

                const data = await response.json();
                console.log('History loaded:', data);

                // Handle invalid session
                if (response.status === 404 || (data.error && data.error.includes('Session not found'))) {
                    console.warn('Invalid/expired session, starting fresh...');
                    this.sessionToken = null;
                    localStorage.removeItem('chatbot_session');
                    await this.startSession();
                    return;
                }

                if (data.success && data.messages && data.messages.length > 0) {
                    this.clearBody();

                    // Display all previous messages
                    data.messages.forEach(msg => {
                        if (msg.role === 'user' || msg.role === 'assistant') {
                            this.addMessage(msg.content, msg.role === 'user' ? 'user' : 'bot', false);
                        }
                    });

                    console.log(`Loaded ${data.messages.length} messages from history`);

                    // Check if session is still active
                    if (!data.is_active) {
                        // Session ended/timeout - clear session and start fresh
                        console.log('Session has ended/timeout - starting fresh session');
                        this.sessionToken = null;
                        localStorage.removeItem('chatbot_session');
                        this.clearBody();
                        await this.startSession();
                    } else {
                        // Session active - enable input
                        this.enableInput();
                    }

                    // Always scroll to bottom after loading all messages
                    this.scrollToBottom();
                } else if (data.error) {
                    console.warn('Could not load history:', data.error);
                    // Start fresh session
                    this.sessionToken = null;
                    localStorage.removeItem('chatbot_session');
                    await this.startSession();
                } else {
                    console.log('No previous messages found - showing welcome screen');
                    // No messages in history, show welcome screen
                    this.showWelcome(true);
                    this.enableInput();
                }

            } catch (error) {
                console.error('Failed to load history:', error);
                // Start fresh session on error
                this.sessionToken = null;
                localStorage.removeItem('chatbot_session');
                await this.startSession();
            } finally {
                this.showLoading(false);
            }
        },

        updateStatus(status) {
            const statusDot = this.elements.status.querySelector('.status-dot');
            const statusText = this.elements.status.querySelector('.status-text');

            if (status === 'online') {
                statusDot.style.background = '#10b981';
                statusText.textContent = 'Online';
            } else {
                statusDot.style.background = '#ef4444';
                statusText.textContent = 'Offline';
            }
        },

        formatTime(date) {
            return date.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        async confirmClearHistory() {
            if (!this.sessionToken) {
                if (typeof Toast !== 'undefined') {
                    Toast.warning('No Session', 'No active chat session to clear.');
                } else {
                    alert('No active chat session to clear.');
                }
                return;
            }

            // Use modern confirmation modal
            if (typeof showConfirm === 'function') {
                const confirmed = await showConfirm(
                    'Clear Chat History?',
                    'Are you sure you want to clear all chat history? This action cannot be undone.',
                    'warning',
                    'Clear History',
                    'Cancel'
                );
                if (confirmed) {
                    this.clearHistory();
                }
            } else {
                // Fallback to native confirm
                if (confirm('Are you sure you want to clear all chat history? This action cannot be undone.')) {
                    this.clearHistory();
                }
            }
        },

        async clearHistory() {
            try {
                console.log('Clearing chat history...');

                const response = await fetch('/api/chatbot/clear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        session_token: this.sessionToken
                    })
                });

                const data = await response.json();
                console.log('Clear history response:', data);

                if (data.success) {
                    // Clear the chat body (remove messages from view)
                    this.clearBody();

                    // Show welcome screen again
                    this.showWelcome(true);

                    // Enable input (session still active)
                    this.enableInput();

                    console.log(`Successfully cleared ${data.deleted_count} messages from view`);

                    // Show toast notification
                    setTimeout(() => {
                        if (typeof Toast !== 'undefined') {
                            Toast.success('Chat Cleared!', 'You can start a fresh conversation.');
                        } else {
                            alert(`✓ Chat cleared! You can start a fresh conversation.`);
                        }
                    }, 300);
                } else {
                    this.showError(data.error || 'Failed to clear chat history');
                }

            } catch (error) {
                console.error('Failed to clear history:', error);
                this.showError('Failed to clear chat history. Please try again.');
            }
        }
    };

    // Initialize chatbot when DOM is ready
    (function() {
        console.log('Chatbot script loaded');

        function initChatBot() {
            try {
                console.log('DOM ready, initializing chatbot...');
                console.log('ChatBot object:', typeof ChatBot);

                if (typeof ChatBot === 'undefined') {
                    console.error('ChatBot object is undefined!');
                    return;
                }

                ChatBot.init();
            } catch (error) {
                console.error('Failed to initialize ChatBot:', error);
            }
        }

        if (document.readyState === 'loading') {
            console.log('Waiting for DOM...');
            document.addEventListener('DOMContentLoaded', initChatBot);
        } else {
            console.log('DOM already ready...');
            initChatBot();
        }
    })();
</script>
@endauth