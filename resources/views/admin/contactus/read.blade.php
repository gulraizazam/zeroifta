@extends('layouts.new_main')
@section('content')

<!-- New Form Design Start -->
<div class="contactform-read">
  <div class="sec1-style">
    <!-- Chat Form Header -->
    <div class="form-readInn">
      <div class="user-form">
        <div>
          <img class="uh-img" src="https://zeroifta.test/images/1717919272.png" alt="">
        </div>
        <div class="user-info">
          <div>
            <h3 class="uh-head head-20">{{ $form->subject }}</h3>
          </div>
          <div class="uh-spans">
            <span class="status-dot online"></span>
            <span class="status-text">Active Now</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Chat Area -->
    <div class="fchat-inn chat-container">
      <!-- User Chat -->
      <div class="userchat-tab">
        <div class="message-bubble user-bubble">
          <div class="message-content">{{ $form->description }}</div>
          <div class="message-time">{{ \Carbon\Carbon::parse($form->created_at)->format('g:i A') }} • {{ \Carbon\Carbon::parse($form->created_at)->format('M d, Y') }}</div>
        </div>
      </div>

      <!-- Admin Reply Chat -->
      <div class="admin-rply" id="messages">
        <!-- Messages will be loaded here -->
      </div>
    </div>

    <!-- Chat Input -->
    <div class="formChat-input mt-4">
      <div class="input-group">
        <textarea id="message" class="form-control custom-textarea" placeholder="Type your message here..." rows="1"></textarea>
        <button class="send-button" onclick="sendMessage()">
          <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
            <path d="M3.58594 20V4L22.5859 12L3.58594 20ZM5.58594 17L17.4359 12L5.58594 7V10.5L11.5859 12L5.58594 13.5V17Z" fill="#FFFFFF"/>
          </svg>
        </button>
      </div>
    </div>

  </div>
</div>
<!-- New Form Design End -->

<style>
.chat-container {
  height: 60vh;
  overflow-y: auto;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 10px;
}

.user-info {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.status-dot {
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  margin-right: 5px;
}

.status-dot.online {
  background: #28a745;
}

.status-text {
  color: #6c757d;
  font-size: 0.9rem;
}

.message-bubble {
  max-width: 80%;
  min-width: 150px;
  margin: 10px 0;
  padding: 12px 16px;
  border-radius: 20px;
  position: relative;
  word-wrap: break-word;
  width: fit-content;
}

.user-bubble {
  background: #e9ecef;
  margin-right: auto;
  border-bottom-left-radius: 5px;
}

.admin-bubble {
  background: #007bff;
  color: white;
  margin-left: auto;
  border-bottom-right-radius: 5px;
}

.message-content {
  font-size: 15px;
  line-height: 1.4;
}

.message-time {
  font-size: 11px;
  opacity: 0.7;
  margin-top: 5px;
  text-align: right;
  white-space: normal;
  line-height: 1.2;
  color: rgba(0, 0, 0, 0.6);
}

.admin-bubble .message-time {
  color: rgba(255, 255, 255, 0.8);
}

.message-sender {
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 4px;
}

.custom-textarea {
  border-radius: 20px;
  resize: none;
  border: 1px solid #dee2e6;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.send-button {
  background: #007bff;
  border: none;
  border-radius: 50%;
  width: 45px;
  height: 45px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-left: 10px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.send-button:hover {
  background: #0056b3;
  transform: scale(1.05);
}

@media (max-width: 768px) {
  .message-bubble {
    max-width: 90%;
  }

  .custom-textarea {
    font-size: 14px;
  }

  .send-button {
    width: 40px;
    height: 40px;
  }
}

@media (max-width: 480px) {
  .message-time {
    font-size: 10px;
  }

  .message-bubble {
    max-width: 95%;
    padding: 10px 14px;
    margin: 8px 0;
  }

  .message-content {
    font-size: 14px;
  }

  .chat-container {
    padding: 15px;
  }
}
</style>

<script>
window.onload = function() {
    fetchMessages();
    setInterval(fetchMessages, 5000); // Refresh messages every 5 seconds
}

async function sendMessage() {
    const messageInput = document.getElementById('message');
    const message = messageInput.value.trim();

    if (!message) return;

    try {
        const response = await fetch('{{ route('messages.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                contact_id: '{{ $form->id }}',
                message: message,
                sender: '{{ auth()->user()->name }}'
            })
        });

        const data = await response.json();

        if (response.ok) {
            messageInput.value = '';
            await fetchMessages();
            // Scroll to bottom after sending message
            const chatContainer = document.querySelector('.chat-container');
            chatContainer.scrollTop = chatContainer.scrollHeight;
        } else {
            console.error('Error sending message:', data);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function fetchMessages() {
    try {
        const response = await fetch(`/messages/{{ $form->id }}`);
        const data = await response.json();

        let messagesHtml = '';
        data.forEach(function(message) {
            const isAdmin = message.sender === '{{ auth()->user()->name }}';
            const bubbleClass = isAdmin ? 'admin-bubble' : 'user-bubble';
            const alignClass = isAdmin ? 'text-end' : 'text-start';
            const date = new Date(message.created_at);
            const timeStr = date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            const dateStr = date.toLocaleDateString([], {month: 'short', day: 'numeric', year: 'numeric'});

            messagesHtml += `
                <div class="${alignClass}">
                    <div class="message-bubble ${bubbleClass}">
                        <div class="message-sender">${message.sender}</div>
                        <div class="message-content">${message.message}</div>
                        <div class="message-time">${timeStr} • ${dateStr}</div>
                    </div>
                </div>`;
        });

        document.getElementById('messages').innerHTML = messagesHtml;
    } catch (error) {
        console.error('Error fetching messages:', error);
    }
}

// Add event listener for Enter key
document.getElementById('message').addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});
</script>

@endsection
