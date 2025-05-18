@extends('layouts.master')

@section('title')
    {{ __('Packages') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="page-title">{{ __('Manage Packages') }}</h3>
            <a href="{{ route('package.create') }}" class="btn btn-primary">{{ __('Create Package') }}</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title">{{ __('Package List') }}</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Package Scope') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Lecture Credits') }}</th>
                            <th>{{ __('Education Stage - Subject') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($packages as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->package_scope}}</td>
                                <td>{{ $item->price }}</td>
                                <td>{{ $item->lecture_credits }}</td>
                                <td>
                                    {{ $item->educationStageSubject->educationStage->name ?? '' }} -
                                    {{ $item->educationStageSubject->subject->name ?? '' }}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal-{{ $item->id }}">
                                        {{ __('Edit') }}
                                    </button>
                                    <form action="{{ route('package.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger deleted-btn">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel-{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-md" role="document">
                                    <form action="{{ route('package.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="w-100">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">{{ __('Edit Package') }} - {{ $item->name }}</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>{{ __('Name') }}</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ __('Description') }}</label>
                                                    <textarea name="description" class="form-control" rows="2">{{ $item->description }}</textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ __('Price') }}</label>
                                                    <input type="number" name="price" class="form-control" value="{{ $item->price }}" required >
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ __('Lecture Credits') }}</label>
                                                    <input type="number" name="lecture_credits" class="form-control" value="{{ $item->lecture_credits }}" required>
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ __('Package Scope') }}</label>
                                                    <select name="package_scope" class="form-control" required>
                                                        @foreach (\App\Enums\Scopes::cases() as $scope)
                                                            <option value="{{ $scope->value }}" {{ $item->package_scope === $scope ? 'selected' : '' }}>
                                                                {{ __($scope->value) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>


                                                <div class="form-group">
                                                    <label>{{ __('Education Stage Subject') }}</label>
                                                    <select name="education_stage_subject_id" class="form-control" required>
                                                        @foreach ($educationStageSubjects as $ess)
                                                            <option value="{{ $ess->id }}" {{ $item->education_stage_subject_id == $ess->id ? 'selected' : '' }}>
                                                                {{ $ess->educationStage->name ?? '' }} - {{ $ess->subject->name ?? '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                                <button type="submit" class="btn btn-success">{{ __('Save Changes') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="7">{{ __('No data found.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    {{ $packages->links() }}
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
