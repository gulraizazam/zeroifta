@extends('layouts.main')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            @if(Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
            @endif
            @if(Session::has('error'))
                <div class="alert alert-danger">{{ Session::get('error') }}</div>
            @endif
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Subject: {{ $form->subject }}</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $form->description }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Chat Section -->
                    <div id="messages" class="p-3 chat-box" style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; margin-top: 20px;">
                        <!-- Messages will be loaded here via AJAX -->
                    </div>

                    <div class="mt-3">
                        <textarea id="message" class="form-control" placeholder="Type your message here" rows="3"></textarea>
                        <button class="btn btn-primary mt-2" onclick="sendMessage()">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetchMessages();

        function fetchMessages() {
            fetch(`/messages/{{ $form->id }}`)
                .then(response => response.json())
                .then(data => {
                    let messagesHtml = '';
                    data.forEach(function(message) {
                        const isReadClass = message.is_read ? 'text-muted' : 'text-black';
                        const senderClass = message.sender === '{{ auth()->user()->name }}' ? 'text-end' : 'text-start';
                        const bubbleClass = message.sender === '{{ auth()->user()->name }}' ? 'bg-primary text-white' : 'bg-light';
                        messagesHtml += `
                            <div class="d-flex flex-column mb-3 ${senderClass}">
                                <div class="p-2 rounded ${bubbleClass}">
                                    <strong>${message.sender}:</strong> ${message.message}
                                    <small class="d-block text-end ${isReadClass}">${new Date(message.created_at).toLocaleString()}</small>
                                </div>
                            </div>`;
                    });
                    document.getElementById('messages').innerHTML = messagesHtml;
                });
        }

        window.sendMessage = function() {
            const message = document.getElementById('message').value;
            fetch('{{ route('messages.store') }}', {
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
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('message').value = '';
                    fetchMessages();
                }
            });
        }
    });
</script>

<style>
  .d-block .text-end .text-primary{
    color:white !important;
  }
    .chat-box {
        background-color: #f9f9f9;
        border-radius: 8px;
        padding: 10px;
    }
    .chat-box .text-start {
        align-self: flex-start;
    }
    .chat-box .text-end {
        align-self: flex-end;
    }
    .chat-box .bg-primary {
        color: white;
    }
    .chat-box .bg-light {
        background-color: #f1f1f1;
    }
</style>
@endsection
