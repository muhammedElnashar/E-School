@extends('layouts.master')

@section('title')
    {{ __('Purchases') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="page-title">{{ __('Teacher Transactions') }}</h3>


        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title">{{ __('Teacher Transactions List') }}</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Teacher Name') }}</th>
                            <th>{{ __('Admin Name') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Paid At') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($transactions as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->teacher->name }}</td>
                                <td>{{ $item->admin->name }}</td>
                                <td>{{ $item->amount }}</td>
                                <td>{{ $item->paid_at->format('Y-m-d H:m') }}</td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">{{ __('No data found.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

