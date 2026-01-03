<script src="https://cdnjs.cloudflare.com/ajax/libs/marked/16.3.0/lib/marked.umd.min.js" integrity="sha512-V6rGY7jjOEUc7q5Ews8mMlretz1Vn2wLdMW/qgABLWunzsLfluM0FwHuGjGQ1lc8jO5vGpGIGFE+rTzB+63HdA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.2.7/purify.min.js" integrity="sha512-78KH17QLT5e55GJqP76vutp1D2iAoy06WcYBXB6iBCsmO6wWzx0Qdg8EDpm8mKXv68BcvHOyeeP4wxAL0twJGQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<style>
    #send-btn:hover {
        opacity: 0.9;
        transform: translateX(2px);
    }

    /* 2. Custom Styles for Markdown Content inside Chat Bubbles */
    /* Added text wrapping for the bot container */
    .chat-markdown {
        overflow-wrap: break-word;
        word-wrap: break-word;
        word-break: break-word;
    }

    .chat-markdown p {
        margin-bottom: 0.5rem;
    }
    .chat-markdown p:last-child {
        margin-bottom: 0;
    }
    .chat-markdown ul, .chat-markdown ol {
        margin-bottom: 0.5rem;
        padding-left: 1.2rem;
    }
    .chat-markdown pre {
        background-color: #f1f3f5; /* Light gray for code blocks */
        padding: 8px;
        border-radius: 5px;
        overflow-x: auto;
        font-size: 0.85em;
        margin-bottom: 0.5rem;
        border: 1px solid #dee2e6;
        /* Ensure code blocks scroll instead of wrapping aggressively */
        white-space: pre;
        word-wrap: normal;
        word-break: normal;
    }
    .chat-markdown code {
        background-color: rgba(175, 184, 193, 0.2);
        padding: 0.2em 0.4em;
        border-radius: 6px;
        font-family: monospace;
        font-size: 0.9em;
        color: #d63384;
    }
    .chat-markdown pre code {
        background-color: transparent; /* Reset for block code */
        color: inherit;
        padding: 0;
    }
    .chat-markdown a {
        color: #0d6efd;
        text-decoration: underline;
        overflow-wrap: break-word;
        word-break: break-all; /* Ensure links break if too long */
    }
    .chat-markdown h1, .chat-markdown h2, .chat-markdown h3 {
        font-size: 1.1rem;
        font-weight: bold;
        margin-top: 0.5rem;
        margin-bottom: 0.3rem;
    }
</style>

<div id="chat-widget-container" style="position: fixed; bottom: 70px; right: 20px; z-index: 1050;">
    <div id="chat-window" class="card shadow d-none mb-3" style="width: 350px; height: 450px; border-radius: 15px;">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center"
             style="border-top-left-radius: 15px; border-top-right-radius: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;">
            <div class="d-flex align-items-center">
                <i class="fas fa-robot me-2"></i>
                <span class="fw-bold">Color Assistant</span>
            </div>
            <button type="button" class="btn-close btn-close-white" onclick="toggleChat()" aria-label="Close"></button>
        </div>
        <div class="card-body d-flex flex-column p-0" style="overflow: hidden;">
            <div id="chat-box" class="flex-grow-1 p-3 overflow-auto" style="background-color: #f8f9fa;">
                <div class="text-center text-muted small mt-4">
                    <div class="mb-2">
                        <i class="fas fa-palette fa-2x text-secondary opacity-50"></i>
                    </div>
                    Hello! Ask me about hex codes, color harmonies, or color psychology.
                </div>
            </div>
            <div class="p-3 bg-white border-top">
                <div class="input-group shadow-sm" style="border-radius: 25px; overflow: hidden; border: 1px solid #e2e8f0;">
                    <input type="text" id="user-input"
                        class="form-control border-0 shadow-none px-3 py-2"
                        placeholder="Type a message..."
                        aria-label="User message"
                        style="background-color: #fff;">
                    <button onclick="sendChat()"
                            class="btn border-0 text-white px-3 d-flex align-items-center justify-content-center"
                            type="button"
                            id="send-btn"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); transition: all 0.3s ease;">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <button id="chat-toggle-btn" onclick="toggleChat()"
            class="btn btn-primary position-fixed rounded-circle shadow-lg d-flex align-items-center justify-content-center"
            style="width: 60px; height: 60px; bottom: 20px; right: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
        <i class="fas fa-comments fa-lg text-white"></i>
    </button>
</div>

<script>
    // 3. Configure Marked.js options
    // Ensure marked is available before running options
    if (typeof marked !== 'undefined') {
        marked.use({
            breaks: true, // Convert \n to <br>
            gfm: true     // GitHub Flavored Markdown
        });
    }

    let chatHistory = [];
    const chatWindow = document.getElementById('chat-window');
    const toggleBtn = document.getElementById('chat-toggle-btn');
    const chatBox = document.getElementById('chat-box');
    const userInput = document.getElementById('user-input');

    // Toggle Visibility Logic
    function toggleChat() {
        if (chatWindow.classList.contains('d-none')) {
            chatWindow.classList.remove('d-none');
            // Focus input when opening
            setTimeout(() => userInput.focus(), 100);
            scrollToBottom();
        } else {
            chatWindow.classList.add('d-none');
        }
    }

    async function sendChat() {
        const message = userInput.value;
        if (!message) return;

        // 1. UI: User Message
        // We use textContent for user input to prevent HTML injection from the user side immediately
        const userDiv = document.createElement('div');
        userDiv.className = 'd-flex justify-content-end mb-2';
        // Added overflow-wrap, word-wrap, and word-break to force line breaks on long text
        userDiv.innerHTML = `
            <div class="p-2 px-3 rounded-3 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); max-width: 80%; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
            </div>
        `;
        // Safely set text content
        userDiv.querySelector('div').textContent = message;
        chatBox.appendChild(userDiv);

        userInput.value = '';
        scrollToBottom();

        // Show loading indicator
        const loadingId = 'loading-' + Date.now();
        chatBox.innerHTML += `
            <div id="${loadingId}" class="d-flex justify-content-start mb-2">
                <div class="p-2 px-3 rounded-3 bg-white border text-muted small shadow-sm">
                    <i class="fas fa-circle-notch fa-spin me-1"></i> Thinking...
                </div>
            </div>
        `;
        scrollToBottom();

        // 2. API Call
        try {
            const response = await fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    message: message,
                    history: chatHistory
                })
            });

            const data = await response.json();

            // Remove loading indicator
            document.getElementById(loadingId)?.remove();

            // 3. UI: Bot Response
            chatHistory = data.history;

            // Render Markdown
            const botHtml = parseMarkdown(data.reply);

            // Added overflow-wrap via CSS class .chat-markdown (defined above)
            chatBox.innerHTML += `
                <div class="d-flex justify-content-start mb-2">
                    <div class="chat-markdown p-2 px-3 rounded-3 bg-white border shadow-sm text-dark" style="max-width: 85%;">
                        ${botHtml}
                    </div>
                </div>
            `;

            scrollToBottom();

        } catch (error) {
            document.getElementById(loadingId)?.remove();
            console.error('Error:', error);
            chatBox.innerHTML += `<div class="text-center text-danger small mt-2">Connection failed.</div>`;
        }
    }

    function scrollToBottom() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // 4. Updated Markdown Parser using marked.js + DOMPurify
    function parseMarkdown(text) {
        try {
            // Check if libraries are loaded
            if (typeof marked === 'undefined' || typeof DOMPurify === 'undefined') {
                console.warn('Marked or DOMPurify not loaded. Falling back to simple text.');
                return text.replace(/\n/g, '<br>');
            }

            // Parse Markdown to HTML
            const rawHtml = marked.parse(text);

            // Sanitize HTML to prevent XSS (scripts, onclicks, etc.)
            const cleanHtml = DOMPurify.sanitize(rawHtml);

            return cleanHtml;
        } catch (e) {
            console.error('Markdown parsing error:', e);
            return text;
        }
    }

    // Listen for Enter key
    userInput.addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            sendChat();
        }
    });
</script>
