@extends('layouts.master')

@section('title', 'Chats')

@section('content')
    <div class="content-wrapper">

        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <h3 class="page-title mb-0">{{ __('Manage Admin') }}</h3>
            <form method="GET" action="{{ route('admin.chat.search') }}" class="d-flex" style="gap: 10px;">
                <input type="text" name="query" class="form-control" placeholder="Search By user code or Name" required>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                @if($conversations->count() > 0)
                    @foreach($conversations as $conversation)
                        @php
                            $otherUser = $conversation->user_one_id == auth()->id()
                                ? $conversation->userTwo
                                : $conversation->userOne;
                        @endphp
                        <div class="border-bottom p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <a href="{{ route('admin.chat.withUser', $otherUser->id) }}" class="text-decoration-none d-flex align-items-center gap-3 text-dark">
                                        <div>
                                            @if($otherUser->image)
                                                <img src="{{ asset('images/' . $otherUser->image) }}" alt="User Avatar" class="rounded-circle" style="width: 50px; height: 50px;">
                                            @else
                                                <img src="{{ asset('admin.jpg') }}" alt="Default Avatar" class="rounded-circle" style="width: 50px; height: 50px;">
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $otherUser->name }}</h6>
                                            <p class="text-muted small mb-0">
                                                {{ $conversation->last_message ?? 'Message Empty' }}
                                            </p>
                                        </div>
                                    </a>
                                </div>

                                <div class="d-flex align-items-center gap-">
                                    <small class="text-muted m-3">{{ $conversation->updated_at->diffForHumans() }}</small>

                                    <form action="{{ route('admin.conversation.delete', $conversation->id) }}" method="POST" onsubmit="return confirm('Are you sure to delete this conversation?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm delete-btn btn-danger">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-4 text-center text-muted">
                        {{ __('No conversations found.') }}
                    </div>
                @endif
            </div>
            <div class="card-footer">
                {{ $conversations->links() }}
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>

        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.delete-btn').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: '{{ __("Delete Confirmation") }}',
                        text: '{{ __("Are you sure you want to delete this Record?") }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '{{ __("Yes, delete it!") }}',
                        cancelButtonText: '{{ __("Cancel") }}',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

@endsection
