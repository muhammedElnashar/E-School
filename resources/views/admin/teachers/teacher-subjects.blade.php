@extends('layouts.master')

@section('title')
    {{ __('Teacher Subjects') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="page-title">{{ __('Manage Teacher Subjects') }}</h3>
            <a href="{{ route('teacher.subject.create',$teacher->id) }}" class="btn btn-primary">{{ __('Add Subject') }}</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title">{{ __('Teacher Subjects List') }}</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Teacher') }}</th>
                            <th>{{ __('Subject') }}</th>
                            <th>{{ __('Stage') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($subjects as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->teacher->name }}</td>
                                <td>{{ $item->subject->name }}</td>
                                <td>{{ $item->educationStage->name??"" }}</td>
                                <td>
                                    <form action="{{ route('teacher.subject.destroy', $item->id) }}" method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger deleted-btn">{{ __('Delete') }}</button>
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

                    {{ $subjects->links() }}
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
