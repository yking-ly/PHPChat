<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/security.php';

// Require authentication
requireAuth();

$currentUserId = getCurrentUserId();
$currentUsername = getCurrentUsername();
$avatarColor = $_SESSION['avatar_color'] ?? '#6366f1';
$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PHPChat - Real-time messaging with friends">
    <title>Chat - PHPChat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="chat-page">
    <div class="chat-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo-small">
                    <div class="logo-icon-small">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21 11.5C21.0034 12.8199 20.6951 14.1219 20.1 15.3C19.3944 16.7118 18.3098 17.8992 16.9674 18.7293C15.6251 19.5594 14.0782 19.9994 12.5 20C11.1801 20.0035 9.87812 19.6951 8.7 19.1L3 21L4.9 15.3C4.30493 14.1219 3.99656 12.8199 4 11.5C4.00061 9.92179 4.44061 8.37488 5.27072 7.03258C6.10083 5.69028 7.28825 4.6056 8.7 3.90003C9.87812 3.30496 11.1801 2.99659 12.5 3.00003H13C15.0843 3.11502 17.053 3.99479 18.5291 5.47089C20.0052 6.94699 20.885 8.91568 21 11V11.5Z"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <span>PHPChat</span>
                </div>
                <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <line x1="3" y1="12" x2="21" y2="12" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" />
                        <line x1="3" y1="6" x2="21" y2="6" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" />
                        <line x1="3" y1="18" x2="21" y2="18" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" />
                    </svg>
                </button>
            </div>

            <div class="sidebar-search">
                <div class="search-wrapper">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" />
                        <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <input type="text" id="searchUsers" placeholder="Search conversations...">
                </div>
            </div>

            <div class="users-list" id="usersList">
                <div class="loading-users">
                    <div class="skeleton-user">
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-info">
                            <div class="skeleton-name"></div>
                            <div class="skeleton-message"></div>
                        </div>
                    </div>
                    <div class="skeleton-user">
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-info">
                            <div class="skeleton-name"></div>
                            <div class="skeleton-message"></div>
                        </div>
                    </div>
                    <div class="skeleton-user">
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-info">
                            <div class="skeleton-name"></div>
                            <div class="skeleton-message"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sidebar-footer">
                <div class="current-user">
                    <div class="avatar" style="background-color: <?php echo htmlspecialchars($avatarColor); ?>">
                        <?php echo strtoupper(substr($currentUsername, 0, 1)); ?>
                    </div>
                    <div class="user-info">
                        <span class="username">
                            <?php echo htmlspecialchars($currentUsername); ?>
                        </span>
                        <span class="status-text">Online</span>
                    </div>
                </div>
                <button class="logout-btn" id="logoutBtn" title="Logout">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </aside>

        <!-- Mobile Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Main Chat Area -->
        <main class="chat-main" id="chatMain">
            <!-- Empty State -->
            <div class="empty-state" id="emptyState">
                <div class="empty-state-content">
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21 11.5C21.0034 12.8199 20.6951 14.1219 20.1 15.3C19.3944 16.7118 18.3098 17.8992 16.9674 18.7293C15.6251 19.5594 14.0782 19.9994 12.5 20C11.1801 20.0035 9.87812 19.6951 8.7 19.1L3 21L4.9 15.3C4.30493 14.1219 3.99656 12.8199 4 11.5C4.00061 9.92179 4.44061 8.37488 5.27072 7.03258C6.10083 5.69028 7.28825 4.6056 8.7 3.90003C9.87812 3.30496 11.1801 2.99659 12.5 3.00003H13C15.0843 3.11502 17.053 3.99479 18.5291 5.47089C20.0052 6.94699 20.885 8.91568 21 11V11.5Z"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h2>Welcome to PHPChat</h2>
                    <p>Select a conversation from the sidebar to start chatting</p>
                </div>
            </div>

            <!-- Chat View (hidden by default) -->
            <div class="chat-view hidden" id="chatView">
                <!-- Chat Header -->
                <div class="chat-header">
                    <button class="back-btn" id="backBtn">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <div class="chat-user-info">
                        <div class="avatar" id="chatAvatar">A</div>
                        <div class="user-details">
                            <h3 class="chat-username" id="chatUsername">Username</h3>
                            <span class="chat-status" id="chatStatus">Online</span>
                        </div>
                    </div>
                </div>

                <!-- Messages Container -->
                <div class="messages-container" id="messagesContainer">
                    <div class="load-more-wrapper hidden" id="loadMoreWrapper">
                        <button class="load-more-btn" id="loadMoreBtn">
                            <span class="load-more-text">Load older messages</span>
                            <span class="load-more-spinner hidden">
                                <svg class="spinner" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none"
                                        stroke-dasharray="31.416" stroke-dashoffset="10" />
                                </svg>
                            </span>
                        </button>
                    </div>
                    <div class="messages" id="messages"></div>
                    <div class="scroll-to-bottom hidden" id="scrollToBottom">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5V19M12 19L19 12M12 19L5 12" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>

                <!-- Message Input -->
                <div class="message-input-container">
                    <form class="message-form" id="messageForm">
                        <div class="message-input-wrapper">
                            <textarea id="messageInput" placeholder="Type a message..." rows="1"
                                maxlength="5000"></textarea>
                        </div>
                        <button type="submit" class="send-btn" id="sendBtn" disabled>
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 2L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Global state
        const state = {
            currentUserId: <?php echo $currentUserId; ?>,
            selectedUserId: null,
            selectedUsername: null,
            users: [],
            lastMessageId: 0,
            isLoadingMessages: false,
            hasMoreMessages: false,
            pollingInterval: null,
            heartbeatInterval: null
        };

        // DOM Elements
        const elements = {
            sidebar: document.getElementById('sidebar'),
            sidebarOverlay: document.getElementById('sidebarOverlay'),
            menuToggle: document.getElementById('menuToggle'),
            usersList: document.getElementById('usersList'),
            searchUsers: document.getElementById('searchUsers'),
            emptyState: document.getElementById('emptyState'),
            chatView: document.getElementById('chatView'),
            chatAvatar: document.getElementById('chatAvatar'),
            chatUsername: document.getElementById('chatUsername'),
            chatStatus: document.getElementById('chatStatus'),
            messagesContainer: document.getElementById('messagesContainer'),
            messages: document.getElementById('messages'),
            loadMoreWrapper: document.getElementById('loadMoreWrapper'),
            loadMoreBtn: document.getElementById('loadMoreBtn'),
            scrollToBottom: document.getElementById('scrollToBottom'),
            messageForm: document.getElementById('messageForm'),
            messageInput: document.getElementById('messageInput'),
            sendBtn: document.getElementById('sendBtn'),
            backBtn: document.getElementById('backBtn'),
            logoutBtn: document.getElementById('logoutBtn')
        };

        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            loadUsers();
            startHeartbeat();
            setupEventListeners();
        });

        // Event Listeners
        function setupEventListeners() {
            // Mobile menu toggle
            elements.menuToggle.addEventListener('click', toggleSidebar);
            elements.sidebarOverlay.addEventListener('click', closeSidebar);
            elements.backBtn.addEventListener('click', closeChatOnMobile);

            // Search users
            elements.searchUsers.addEventListener('input', filterUsers);

            // Message form
            elements.messageForm.addEventListener('submit', sendMessage);
            elements.messageInput.addEventListener('input', handleInputChange);
            elements.messageInput.addEventListener('keydown', handleKeyDown);

            // Load more messages
            elements.loadMoreBtn.addEventListener('click', loadMoreMessages);

            // Scroll to bottom button
            elements.scrollToBottom.addEventListener('click', scrollToBottom);
            elements.messagesContainer.addEventListener('scroll', handleScroll);

            // Logout
            elements.logoutBtn.addEventListener('click', logout);

            // Handle page visibility changes
            document.addEventListener('visibilitychange', handleVisibilityChange);
        }

        // Toggle sidebar on mobile
        function toggleSidebar() {
            elements.sidebar.classList.toggle('open');
            elements.sidebarOverlay.classList.toggle('active');
        }

        function closeSidebar() {
            elements.sidebar.classList.remove('open');
            elements.sidebarOverlay.classList.remove('active');
        }

        function closeChatOnMobile() {
            if (window.innerWidth <= 768) {
                elements.sidebar.classList.add('open');
                elements.sidebarOverlay.classList.add('active');
            }
        }

        // Load users list
        async function loadUsers() {
            try {
                const response = await fetch('api/chat/get_users.php');
                const data = await response.json();

                if (data.success) {
                    state.users = data.data.users;
                    renderUsers(state.users);
                }
            } catch (error) {
                console.error('Error loading users:', error);
            }
        }

        // Render users list
        function renderUsers(users) {
            if (users.length === 0) {
                elements.usersList.innerHTML = `
                    <div class="no-users">
                        <p>No users found</p>
                    </div>
                `;
                return;
            }

            elements.usersList.innerHTML = users.map(user => `
                <div class="user-item ${user.id === state.selectedUserId ? 'active' : ''}" 
                     data-user-id="${user.id}"
                     data-username="${user.username}"
                     data-avatar-color="${user.avatar_color}"
                     data-is-online="${user.is_online}"
                     data-last-seen="${user.last_seen_formatted}">
                    <div class="user-avatar" style="background-color: ${user.avatar_color}">
                        ${user.username.charAt(0).toUpperCase()}
                        <span class="online-indicator ${user.is_online ? 'online' : 'offline'}"></span>
                    </div>
                    <div class="user-details">
                        <div class="user-name-row">
                            <span class="user-name">${escapeHtml(user.username)}</span>
                            ${user.unread_count > 0 ? `<span class="unread-badge">${user.unread_count}</span>` : ''}
                        </div>
                        <p class="last-message">${user.last_message || 'No messages yet'}</p>
                    </div>
                </div>
            `).join('');

            // Add click handlers
            document.querySelectorAll('.user-item').forEach(item => {
                item.addEventListener('click', () => selectUser(item));
            });
        }

        // Filter users by search
        function filterUsers() {
            const search = elements.searchUsers.value.toLowerCase();
            const filtered = state.users.filter(user =>
                user.username.toLowerCase().includes(search)
            );
            renderUsers(filtered);
        }

        // Select a user to chat with
        function selectUser(userItem) {
            const userId = parseInt(userItem.dataset.userId);
            const username = userItem.dataset.username;
            const avatarColor = userItem.dataset.avatarColor;
            const isOnline = userItem.dataset.isOnline === 'true';
            const lastSeen = userItem.dataset.lastSeen;

            // Update state
            state.selectedUserId = userId;
            state.selectedUsername = username;
            state.lastMessageId = 0;
            state.hasMoreMessages = false;

            // Update UI
            document.querySelectorAll('.user-item').forEach(item => {
                item.classList.remove('active');
            });
            userItem.classList.add('active');

            // Update chat header
            elements.chatAvatar.textContent = username.charAt(0).toUpperCase();
            elements.chatAvatar.style.backgroundColor = avatarColor;
            elements.chatUsername.textContent = username;
            elements.chatStatus.textContent = isOnline ? 'Online' : lastSeen;
            elements.chatStatus.className = `chat-status ${isOnline ? 'online' : 'offline'}`;

            // Show chat view
            elements.emptyState.classList.add('hidden');
            elements.chatView.classList.remove('hidden');

            // Clear messages and load new
            elements.messages.innerHTML = '';
            elements.loadMoreWrapper.classList.add('hidden');

            // Close sidebar on mobile
            closeSidebar();

            // Load messages
            loadMessages();

            // Start polling for new messages
            startPolling();
        }

        // Load messages
        async function loadMessages(beforeId = null) {
            if (state.isLoadingMessages || !state.selectedUserId) return;

            state.isLoadingMessages = true;

            if (beforeId) {
                elements.loadMoreBtn.querySelector('.load-more-text').classList.add('hidden');
                elements.loadMoreBtn.querySelector('.load-more-spinner').classList.remove('hidden');
            }

            try {
                let url = `api/chat/get_messages.php?user_id=${state.selectedUserId}`;
                if (beforeId) {
                    url += `&before_id=${beforeId}`;
                }

                const response = await fetch(url);
                const data = await response.json();

                if (data.success) {
                    const messages = data.data.messages;
                    state.hasMoreMessages = data.data.has_more;

                    if (beforeId) {
                        // Prepend older messages
                        prependMessages(messages);
                    } else {
                        // Initial load
                        renderMessages(messages);
                        scrollToBottom();
                    }

                    // Update last message ID for polling
                    if (messages.length > 0) {
                        const lastMsg = messages[messages.length - 1];
                        if (lastMsg.id > state.lastMessageId) {
                            state.lastMessageId = lastMsg.id;
                        }
                    }

                    // Show/hide load more button
                    if (state.hasMoreMessages) {
                        elements.loadMoreWrapper.classList.remove('hidden');
                    } else {
                        elements.loadMoreWrapper.classList.add('hidden');
                    }
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            } finally {
                state.isLoadingMessages = false;
                elements.loadMoreBtn.querySelector('.load-more-text').classList.remove('hidden');
                elements.loadMoreBtn.querySelector('.load-more-spinner').classList.add('hidden');
            }
        }

        // Render messages
        function renderMessages(messages) {
            let lastDate = null;
            let html = '';

            messages.forEach(msg => {
                const messageDate = new Date(msg.timestamp).toLocaleDateString();

                // Add date separator if date changed
                if (messageDate !== lastDate) {
                    html += `<div class="date-separator"><span>${formatDate(msg.timestamp)}</span></div>`;
                    lastDate = messageDate;
                }

                html += createMessageHTML(msg);
            });

            elements.messages.innerHTML = html;
        }

        // Prepend older messages
        function prependMessages(messages) {
            if (messages.length === 0) return;

            const scrollHeightBefore = elements.messagesContainer.scrollHeight;
            let lastDate = null;
            let html = '';

            // Get the first existing date separator if any
            const firstDateSep = elements.messages.querySelector('.date-separator');
            const existingFirstDate = firstDateSep ? firstDateSep.textContent : null;

            messages.forEach(msg => {
                const messageDate = new Date(msg.timestamp).toLocaleDateString();
                const formattedDate = formatDate(msg.timestamp);

                // Add date separator if date changed
                if (messageDate !== lastDate) {
                    // Don't add if it matches the existing first date
                    if (formattedDate !== existingFirstDate) {
                        html += `<div class="date-separator"><span>${formattedDate}</span></div>`;
                    }
                    lastDate = messageDate;
                }

                html += createMessageHTML(msg);
            });

            // Remove duplicate date separator if present
            if (firstDateSep && lastDate === new Date(messages[messages.length - 1].timestamp).toLocaleDateString()) {
                firstDateSep.remove();
            }

            elements.messages.insertAdjacentHTML('afterbegin', html);

            // Maintain scroll position
            const scrollHeightAfter = elements.messagesContainer.scrollHeight;
            elements.messagesContainer.scrollTop = scrollHeightAfter - scrollHeightBefore;
        }

        // Append new messages
        function appendMessages(messages) {
            if (messages.length === 0) return;

            const shouldScroll = isNearBottom();
            let lastDate = null;

            // Get the last date from existing messages
            const dateSeps = elements.messages.querySelectorAll('.date-separator');
            if (dateSeps.length > 0) {
                lastDate = dateSeps[dateSeps.length - 1].textContent;
            }

            let html = '';
            messages.forEach(msg => {
                const messageDate = formatDate(msg.timestamp);

                // Add date separator if date changed
                if (messageDate !== lastDate) {
                    html += `<div class="date-separator"><span>${messageDate}</span></div>`;
                    lastDate = messageDate;
                }

                html += createMessageHTML(msg);

                // Update last message ID
                if (msg.id > state.lastMessageId) {
                    state.lastMessageId = msg.id;
                }
            });

            elements.messages.insertAdjacentHTML('beforeend', html);

            if (shouldScroll) {
                scrollToBottom();
            } else {
                elements.scrollToBottom.classList.remove('hidden');
            }
        }

        // Create message HTML
        function createMessageHTML(msg) {
            const isSent = msg.sender_id === state.currentUserId;
            const time = formatTime(msg.timestamp);

            return `
                <div class="message ${isSent ? 'sent' : 'received'}" data-message-id="${msg.id}">
                    <div class="message-bubble">
                        <p class="message-text">${escapeHtml(msg.message)}</p>
                        <span class="message-time">${time}</span>
                    </div>
                </div>
            `;
        }

        // Load more messages
        function loadMoreMessages() {
            const firstMessage = elements.messages.querySelector('.message');
            if (firstMessage) {
                const beforeId = parseInt(firstMessage.dataset.messageId);
                loadMessages(beforeId);
            }
        }

        // Send message
        async function sendMessage(e) {
            e.preventDefault();

            const message = elements.messageInput.value.trim();
            if (!message || !state.selectedUserId) return;

            // Optimistically add message to UI
            const tempId = 'temp-' + Date.now();
            const tempMessage = {
                id: tempId,
                sender_id: state.currentUserId,
                message: message,
                timestamp: new Date().toISOString()
            };

            appendMessages([tempMessage]);
            elements.messageInput.value = '';
            elements.sendBtn.disabled = true;
            autoResize(elements.messageInput);

            try {
                const formData = new FormData();
                formData.append('receiver_id', state.selectedUserId);
                formData.append('message', message);

                const response = await fetch('api/chat/send_message.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Replace temp message with real one
                    const tempEl = elements.messages.querySelector(`[data-message-id="${tempId}"]`);
                    if (tempEl) {
                        tempEl.dataset.messageId = data.data.message.id;
                    }
                    state.lastMessageId = data.data.message.id;
                } else {
                    // Mark message as failed
                    const tempEl = elements.messages.querySelector(`[data-message-id="${tempId}"]`);
                    if (tempEl) {
                        tempEl.classList.add('failed');
                    }
                }
            } catch (error) {
                console.error('Error sending message:', error);
            }

            // Refresh users list to update last message
            loadUsers();
        }

        // Poll for new messages
        function startPolling() {
            // Clear existing interval
            if (state.pollingInterval) {
                clearInterval(state.pollingInterval);
            }

            // Poll every 2 seconds
            state.pollingInterval = setInterval(pollMessages, 2000);
        }

        async function pollMessages() {
            if (!state.selectedUserId || state.isLoadingMessages) return;

            try {
                const response = await fetch(
                    `api/chat/get_messages.php?user_id=${state.selectedUserId}&after_id=${state.lastMessageId}`
                );
                const data = await response.json();

                if (data.success && data.data.messages.length > 0) {
                    appendMessages(data.data.messages);

                    // Refresh users list to update unread counts
                    loadUsers();
                }
            } catch (error) {
                console.error('Error polling messages:', error);
            }
        }

        // Heartbeat
        function startHeartbeat() {
            // Send heartbeat immediately
            sendHeartbeat();

            // Then every 30 seconds
            state.heartbeatInterval = setInterval(sendHeartbeat, 30000);
        }

        async function sendHeartbeat() {
            try {
                await fetch('api/status/heartbeat.php');
            } catch (error) {
                console.error('Heartbeat error:', error);
            }
        }

        // Handle visibility change
        function handleVisibilityChange() {
            if (document.hidden) {
                // Page is hidden, stop polling (but keep heartbeat)
                if (state.pollingInterval) {
                    clearInterval(state.pollingInterval);
                    state.pollingInterval = null;
                }
            } else {
                // Page is visible, resume polling
                if (state.selectedUserId) {
                    pollMessages();
                    startPolling();
                }
                // Send heartbeat
                sendHeartbeat();
            }
        }

        // Handle input changes
        function handleInputChange(e) {
            autoResize(e.target);
            elements.sendBtn.disabled = e.target.value.trim() === '';
        }

        function handleKeyDown(e) {
            // Send on Enter (but not Shift+Enter)
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (!elements.sendBtn.disabled) {
                    elements.messageForm.dispatchEvent(new Event('submit'));
                }
            }
        }

        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
        }

        // Scroll handling
        function handleScroll() {
            if (!isNearBottom()) {
                elements.scrollToBottom.classList.remove('hidden');
            } else {
                elements.scrollToBottom.classList.add('hidden');
            }
        }

        function isNearBottom() {
            const container = elements.messagesContainer;
            return container.scrollHeight - container.scrollTop - container.clientHeight < 100;
        }

        function scrollToBottom() {
            elements.messagesContainer.scrollTop = elements.messagesContainer.scrollHeight;
            elements.scrollToBottom.classList.add('hidden');
        }

        // Logout
        async function logout() {
            try {
                await fetch('api/auth/logout.php', { method: 'GET' });
                window.location.href = 'login.php';
            } catch (error) {
                window.location.href = 'api/auth/logout.php';
            }
        }

        // Utility functions
        function formatDate(timestamp) {
            const date = new Date(timestamp);
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);

            if (date.toDateString() === today.toDateString()) {
                return 'Today';
            } else if (date.toDateString() === yesterday.toDateString()) {
                return 'Yesterday';
            } else {
                return date.toLocaleDateString('en-US', {
                    weekday: 'short',
                    month: 'short',
                    day: 'numeric',
                    year: date.getFullYear() !== today.getFullYear() ? 'numeric' : undefined
                });
            }
        }

        function formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>

</html>