@extends('layouts.master')

@section('title')
    {{ __('Purchases') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="page-title">{{ __('Purchases') }}</h3>
            <a href="{{ route('purchases.create') }}" class="btn btn-primary">{{ __('Manual Purchases') }}</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                    <h4 class="card-title">{{ __('Manual Purchase List') }}</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Username') }}</th>
                            <th>{{ __('Marketplace Item') }}</th>
                            <th>{{ __(' price') }}</th>
                            <th>{{ __('Remaining Credits') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($purchases as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->user->name }}</td>
                                <td>{{ $item->marketplaceItem->name}} - {{ $item->marketplaceItem-> package_scope}} </td>
                                <td>{{ $item->price }}</td>
                                <td>{{ $item->remaining_credits }}</td>
                                <td>
                                    <form action="{{ route('purchases.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger deleted-btn">{{ __('Delete') }}</button>
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

                    {{ $purchases->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>

        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.deleted-btn').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: '{{ __("Delete Confirmation") }}',
                        text: '{{ __("Are you sure you want to delete this digital asset?") }}',
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
