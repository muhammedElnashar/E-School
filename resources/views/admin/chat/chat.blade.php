@extends('layouts.master')

@section('title', 'Chat With ' . $targetUser->name)

@section('content')
    <div class="content-wrapper">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    @if($targetUser->image)
                        <img src="{{ asset('images/' . $targetUser->image) }}" alt="User Avatar" class="rounded-circle" style="width: 40px; height: 40px;">
                    @else
                    <img src="{{asset('admin.jpg')}}" alt="User Avatar" class="rounded-circle" style="width: 40px; height: 40px;"> {{ $targetUser->name }}</h5>
                @endif
                <a href="{{ route('admin.chat.index') }}" class="btn btn-sm btn-secondary" > <i class="fa fa-arrow-right"></i></a>
            </div>

            <div class="card-body" style="height: 400px; overflow-y: auto;" id="chat-box">
                @forelse($messages as $message)
                    @php
                        $isMine = $message->sender_id === auth()->id();
                    @endphp
                    <div class="mb-3 d-flex {{ $isMine ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="px-3 py-2 rounded shadow-sm {{ $isMine ? 'bg-primary text-white text-end' : 'bg-light text-start' }}" style="max-width: 70%;">
                            <div><strong>{{ $message->sender->name }}</strong></div>
                            <div>{{ $message->message }}</div>
                            <div class="small  mt-1">{{ $message->created_at->diffForHumans()  }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">لا توجد رسائل في هذه المحادثة بعد.</p>
                @endforelse
            </div>

            <div class="card-footer">
                <form method="POST" action="{{ route('admin.chat.send') }}" id="sendMessageForm">
                    @csrf
                    <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                    <div class="input-group">
                        <textarea name="message" class="form-control" rows="1" placeholder="Type Message" required></textarea>
                        <button class="btn btn-primary" type="submit">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        document.getElementById('sendMessageForm').addEventListener('submit', function(e) {
            e.preventDefault(); // منع التحديث التلقائي

            const form = e.target;
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // تفريغ حقل الإدخال
                        form.message.value = '';

                        // سيتم عرض الرسالة تلقائيًا من خلال Pusher
                    } else {
                        alert('فشل إرسال الرسالة');
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert('حدث خطأ أثناء الإرسال');
                });
        });
    </script>

    <script>
        window.onload = function () {
            var chatBox = document.getElementById('chat-box');
            chatBox.scrollTop = chatBox.scrollHeight;
        };
    </script>
    <!-- Pusher JS -->
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
        Pusher.logToConsole = true;

        const pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
            cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
            authEndpoint: "/broadcasting/auth",
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }
        });

        const channel = pusher.subscribe("private-conversation.{{ $conversation->id }}");
        channel.bind("new.message", function(data) {
            console.log('رسالة جديدة:', data);

            const isMine = parseInt(data.sender_id) === {{ auth()->id() }};
            const chatBox = document.getElementById('chat-box');

            const messageHtml = `
            <div class="mb-3 d-flex ${isMine ? 'justify-content-end' : 'justify-content-start'}">
                <div class="px-3 py-2 rounded shadow-sm ${isMine ? 'bg-primary text-white text-end' : 'bg-light text-start'}" style="max-width: 70%;">
                    <div><strong>${data.sender_name}</strong></div>
                    <div>${data.message}</div>
                    <div class="small mt-1">${data.created_at}</div>
                </div>
            </div>
        `;

            chatBox.innerHTML += messageHtml;
            chatBox.scrollTop = chatBox.scrollHeight;
        });
    </script>



@endsection
