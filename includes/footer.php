<?php
/**
 * Global Footer Include for SDO FAST.
 * Closes page wrapper, loads standard CDNs, and includes the AI chatbot widget.
 */
?>
    </div> <!-- End content-container -->
    
    <footer class="footer mt-auto">
        <div class="container-fluid">
            <span class="text-muted">© 2026 SDO FAST. All rights reserved. SDO Financial Accounting Services & Transactions.</span>
        </div>
    </footer>
</div> <!-- End main-content -->
</div> <!-- End wrapper -->

<!-- Global API Loading Spinner (Managed by api.js) -->
<div id="api-loading-spinner" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(255, 255, 255, 0.6); display: none; align-items: center; justify-content: center; z-index: 2000;">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- =========================================================================
     AI CHATBOT WIDGET
     ========================================================================= -->
<?php if (isLoggedIn()): ?>
<div class="chatbot-widget d-print-none">
    <!-- Float Button -->
    <div class="chatbot-toggle" id="fast-chat-toggle" title="Chat with FAST AI Assistant">
        <i class="bi bi-lightning-charge-fill"></i>
    </div>
    
    <!-- Chat Window -->
    <div class="chatbot-window" id="fast-chat-window">
        <div class="chatbot-header">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge-fill fs-5 text-accent"></i>
                <div>
                    <h6 class="mb-0 fw-bold fs-7">SDO FAST AI Assistant</h6>
                    <small class="text-white-50" style="font-size: 0.65rem;">Always Active</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-link text-white p-0 border-0" id="fast-chat-expand" title="Toggle Fullscreen" style="min-height: auto; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; box-shadow: none;">
                    <i class="bi bi-arrows-angle-expand fs-6"></i>
                </button>
                <button type="button" class="btn-close btn-close-white" id="fast-chat-close" aria-label="Close" style="min-height: auto; width: 14px; height: 14px; margin: 0; box-shadow: none;"></button>
            </div>
        </div>
        
        <!-- Chat Messages Container -->
        <div class="chatbot-messages" id="fast-chat-messages">
            <div class="chat-msg bot">
                Hello! I am your <strong>SDO FAST Financial Accounting Virtual Assistant</strong>. How can I help you today with your accounting transactions, DV status, or tax configurations?
            </div>
        </div>
        
        <!-- Quick Reply Chips -->
        <div class="chatbot-quick-replies">
            <span class="quick-reply-chip" onclick="sendQuickReply('How to track a DV?')">How to track a DV?</span>
            <span class="quick-reply-chip" onclick="sendQuickReply('What are the tax rates?')">What are the tax rates?</span>
            <span class="quick-reply-chip" onclick="sendQuickReply('How long is approval?')">How long is approval?</span>
            <span class="quick-reply-chip" onclick="sendQuickReply('What documents are required?')">What documents are required?</span>
        </div>
        
        <!-- Input Area -->
        <form id="fast-chatbot-form" onsubmit="handleChatSubmit(event)">
            <div class="chatbot-input-container">
                <input type="text" id="fast-chatbot-input" class="form-control chatbot-input" placeholder="Type your financial question..." required autocomplete="off">
                <button type="submit" class="chatbot-send-btn">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Bootstrap 5 Bundle JS (Includes Popper) CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<!-- Chart.js CDN (Loaded only if requested by dashboard) -->
<?php if (isset($loadChartJS) && $loadChartJS): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php endif; ?>

<!-- SDO FAST Custom Core Javascript Utility Files -->
<script src="<?php echo env('APP_URL'); ?>/assets/js/api.js?v=<?php echo time(); ?>"></script>
<script src="<?php echo env('APP_URL'); ?>/assets/js/main.js?v=<?php echo time(); ?>"></script>

<!-- Inline Chatbot Actions -->
<?php if (isLoggedIn()): ?>
<script>
const chatToggle = document.getElementById('fast-chat-toggle');
const chatWindow = document.getElementById('fast-chat-window');
const chatClose = document.getElementById('fast-chat-close');
const chatExpand = document.getElementById('fast-chat-expand');
const chatMessages = document.getElementById('fast-chat-messages');
const chatInput = document.getElementById('fast-chatbot-input');

function appendMessage(text, sender) {
    const msg = document.createElement('div');
    msg.className = `chat-msg ${sender}`;
    msg.innerHTML = text;
    chatMessages.appendChild(msg);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function sendQuickReply(text) {
    chatInput.value = text;
    document.getElementById('fast-chatbot-form').dispatchEvent(new Event('submit'));
}

async function loadChatHistory() {
    try {
        const response = await API.request('<?php echo env('APP_URL'); ?>/api/chatbot/get-history.php');
        if (response && response.success && response.history && response.history.length > 0) {
            chatMessages.innerHTML = '';
            response.history.forEach(log => {
                appendMessage(log.user_message, 'user');
                appendMessage(log.bot_response, 'bot');
            });
        }
    } catch (e) {
        console.error('Failed to load chat history:', e);
    }
}

// Restore state from localStorage
function restoreChatState() {
    const chatActive = localStorage.getItem('fast_chat_active') === 'true';
    const chatFullscreen = localStorage.getItem('fast_chat_fullscreen') === 'true';

    if (chatActive && chatWindow) {
        chatWindow.classList.add('active');
    }
    
    if (chatFullscreen && chatWindow) {
        chatWindow.classList.add('fullscreen');
        const icon = chatExpand.querySelector('i');
        if (icon) {
            icon.className = 'bi bi-arrows-angle-contract fs-6';
        }
    }
}

// Toggle Chat Window
if (chatToggle && chatWindow) {
    chatToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        chatWindow.classList.toggle('active');
        const isActive = chatWindow.classList.contains('active');
        localStorage.setItem('fast_chat_active', isActive);
    });
}

// Close Chat Window
if (chatClose && chatWindow) {
    chatClose.addEventListener('click', function(e) {
        e.stopPropagation();
        chatWindow.classList.remove('active');
        localStorage.setItem('fast_chat_active', false);
    });
}

// Expand/Fullscreen Toggle
if (chatExpand && chatWindow) {
    chatExpand.addEventListener('click', function(e) {
        e.stopPropagation();
        chatWindow.classList.toggle('fullscreen');
        const isFullscreen = chatWindow.classList.contains('fullscreen');
        localStorage.setItem('fast_chat_fullscreen', isFullscreen);
        
        const icon = chatExpand.querySelector('i');
        if (icon) {
            if (isFullscreen) {
                icon.className = 'bi bi-arrows-angle-contract fs-6';
            } else {
                icon.className = 'bi bi-arrows-angle-expand fs-6';
            }
        }
    });
}

// Close on outside click only if NOT in fullscreen mode
document.addEventListener('click', function(e) {
    if (chatWindow && chatWindow.classList.contains('active') && !chatWindow.classList.contains('fullscreen')) {
        if (!chatWindow.contains(e.target) && e.target !== chatToggle && !chatToggle.contains(e.target)) {
            chatWindow.classList.remove('active');
            localStorage.setItem('fast_chat_active', false);
        }
    }
});

function getActiveTrackingCode() {
    // 1. Try to get from URL parameter "tracking"
    const urlParams = new URLSearchParams(window.location.search);
    let tracking = urlParams.get('tracking');
    if (tracking && /^FAST-\d{4}-\d+$/i.test(tracking)) {
        return tracking.toUpperCase();
    }
    
    // 2. Try to search the page DOM for any tracking number pattern
    const bodyText = document.body.innerText;
    const match = bodyText.match(/FAST-\d{4}-\d+/i);
    if (match) {
        return match[0].toUpperCase();
    }
    
    return null;
}

async function handleChatSubmit(e) {
    e.preventDefault();
    const text = chatInput.value.trim();
    if (!text) return;

    // Append user message
    appendMessage(text, 'user');
    chatInput.value = '';

    // Append loading bubble
    const loadingId = 'loading_' + Date.now();
    const loadingBubble = document.createElement('div');
    loadingBubble.id = loadingId;
    loadingBubble.className = 'chat-msg bot';
    loadingBubble.innerHTML = '<span class="spinner-grow spinner-grow-sm text-primary" role="status"></span> Thinking...';
    chatMessages.appendChild(loadingBubble);
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Send API Request
    const response = await API.request('<?php echo env('APP_URL'); ?>/api/chatbot/chat-handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            message: text,
            active_tracking: getActiveTrackingCode()
        })
    });

    // Remove loading bubble
    const spinnerBubble = document.getElementById(loadingId);
    if (spinnerBubble) spinnerBubble.remove();

    if (response && response.success) {
        appendMessage(response.message, 'bot');
    } else {
        appendMessage('Sorry, I encountered an error. Please try again or ask another question.', 'bot');
    }
}

// Initialize history and state
loadChatHistory().then(() => {
    restoreChatState();
});
</script>
<?php endif; ?>
</body>
</html>
