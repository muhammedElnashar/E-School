@extends('layouts.master')

@section('title')
    {{ __('Conversations Message') }}
@endsection
@section("css")

@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="page-title">{{ __('Manage Conversations Message') }}</h3>
            <a href="{{ route('admin.chat.conversations') }}" class="btn btn-facebook">{{ __('Back To Conversations') }}</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title">{{ __('Conversation Message List') }}</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Sender') }}</th>
                            <th>{{ __('Message') }}</th>
                            <th>{{ __('send_at') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($messages as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->sender->name ?? ""}}</td>
                                <td>{{ $item->message }}</td>
                                <td>{{ $item->created_at->format('Y-m-d - h:m') }}</td>

                                <td>
                                    <form action="{{ route('admin.message.delete', $item->id) }}" method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger delete-btn">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>


                        @empty
                            <tr>
                                <td colspan="7">{{ __('No data found.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                    {{ $messages->links() }}

                </div>
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
