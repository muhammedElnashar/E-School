@extends('layouts.master')

@section('title')
    {{ __('Lessons') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="page-title">{{ __('Manage Lesson') }}</h3>
            <a href="{{ route('lessons.create') }}" class="btn btn-primary">{{ __('Create Lesson') }}</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title">{{ __('Lesson List') }}</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Teacher') }}</th>
                            <th>{{ __('Subject') }}</th>
                            <th>{{ __("Stage") }}</th>
                            <th>{{ __('Start Date') }}</th>
                            <th>{{ __(' End Date') }}</th>
                            <th>{{ __('Lesson Type') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($lessons as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->teacher->name }}</td>
                                <td>{{ $item->subject->name }}</td>
                                <td>{{ $item->educationStage->name??"" }}</td>
                                <td>{{ $item->start_datetime }}</td>
                                <td>{{ $item->end_datetime }}</td>
                                <td>{{ $item->lesson_type }}</td>

                                <td>
                                  {{--  <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                            data-target="#editModal-{{ $item->id }}">
                                        {{ __('Edit') }}
                                    </button>--}}
                                    <a href="{{ route('lessons.occurrences', $item->id) }}" class="btn btn-sm btn-primary">View Recurrences</a>
                                        <a href="{{ route('lessons.edit', $item->id) }}"
                                           class="btn btn-sm btn-info">{{ __('edit') }}</a>

                                    <form action="{{ route('lessons.destroy', $item->id) }}" method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger deleted-btn">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal-{{ $item->id }}" tabindex="-1" role="dialog"
                                 aria-labelledby="editModalLabel-{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-md" role="document">
                                    <form action="{{ route('lessons.update', $item->id) }}" method="POST"
                                          enctype="multipart/form-data" class="w-100">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">{{ __('Edit Package') }}
                                                    - {{ $item->name }}</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">{{ __('Close') }}</button>
                                                    <button type="submit"
                                                            class="btn btn-success">{{ __('Save Changes') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="8">{{ __('No data found.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    {{ $lessons->links() }}
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
                        text: '{{ __("Are you sure you want to delete this package?") }}',
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
