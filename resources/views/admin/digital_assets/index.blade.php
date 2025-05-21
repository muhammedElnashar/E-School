@extends('layouts.master')

@section('title')
    {{ __('Digital Assets') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="page-title">{{ __('Manage Digital Assets') }}</h3>
            <a href="{{ route('digital-assets.create') }}" class="btn btn-primary">{{ __('Create Digital Asset') }}</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title">{{ __('Digital Asset List') }}</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __(' Subject') }}</th>
                            <th>{{ __('Education Stage') }}</th>
                            <th>{{ __('File') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($digitalAssets as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->price }}</td>
                                <td>{{ $item->subject?->name  }}</td>
                                <td>{{ $item->educationStage?->name  }}</td>
                                <td>
                                    @if($item->file_path)
                                        <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank">{{ __('View File') }}</a>
                                    @else
                                        {{ __('No File') }}
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal-{{ $item->id }}">
                                        {{ __('Edit') }}
                                    </button>
                                    <form action="{{ route('digital-assets.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger deleted-btn">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal-{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel-{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-md" role="document">
                                    <form action="{{ route('digital-assets.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="w-100">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title" id="editModalLabel-{{ $item->id }}">{{ __('Edit Digital Asset') }} - {{ $item->name }}</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="{{ __('Close') }}">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="name-{{ $item->id }}">{{ __('Name') }} <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" id="name-{{ $item->id }}" class="form-control" value="{{ old('name', $item->name) }}" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="price-{{ $item->id }}">{{ __('Price') }}</label>
                                                    <input type="number" name="price" id="price-{{ $item->id }}" class="form-control" step="0.01" value="{{ old('price', $item->price) }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('Subjects') }} <span
                                                            class="text-danger">*</span></label>
                                                    <select name="subject_id" class="form-control edit-subject-select"
                                                            data-item-id="{{ $item->id }}" required>

                                                        <option value="">{{ __('Select Subject') }}</option>
                                                        @foreach ($subjects as $subject)
                                                            <option
                                                                value="{{ $subject->id }}" {{ (old('subject_id', $item->subject_id) == $subject->id) ? 'selected' : '' }}>
                                                                {{ $subject->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">

                                                    <label>{{ __('Education Stages') }} <span
                                                            class="text-danger">*</span></label>

                                                    <select name="education_stage_id"
                                                            class="form-control edit-stage-select"
                                                            data-item-id="{{ $item->id }}">
                                                        <option value="">{{ __('Select Education Stage') }}</option>
                                                        @foreach ($educationStages as $stage)
                                                            <option
                                                                value="{{ $stage->id }}" {{ (old('education_stage_id', $item->education_stage_id) == $stage->id) ? 'selected' : '' }}>
                                                                {{ $stage->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>


                                                <div class="form-group">
                                                    <label for="file-{{ $item->id }}">{{ __('Upload File') }}</label>
                                                    <input type="file" name="file" id="file-{{ $item->id }}" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.csv">
                                                    @if ($item->file_path)
                                                        <p class="mt-2">
                                                            {{ __('Current file:') }}
                                                            <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank">{{ basename($item->file_path) }}</a>
                                                        </p>
                                                    @endif
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

                    {{ $digitalAssets->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            // تابع تغيير المادة داخل المودال
            document.querySelectorAll('.edit-subject-select').forEach(function (select) {
                select.addEventListener('change', function () {
                    const subjectId = this.value;
                    const itemId = this.getAttribute('data-item-id');
                    const stageSelect = document.querySelector('.edit-stage-select[data-item-id="' + itemId + '"]');

                    if (!subjectId) {
                        // امسح الخيارات إذا لم يتم اختيار مادة
                        stageSelect.innerHTML = '<option value="">{{ __("Select Education Stage") }}</option>';
                        return;
                    }

                    fetch(`/admin/subjects/${subjectId}/related-stages`)
                        .then(response => response.json())
                        .then(data => {
                            // امسح الخيارات القديمة
                            stageSelect.innerHTML = '<option value="">{{ __("Select Education Stage") }}</option>';
                            data.forEach(function (stage) {
                                const option = document.createElement('option');
                                option.value = stage.id;
                                option.textContent = stage.name;
                                stageSelect.appendChild(option);
                            });
                        })
                        .catch(err => {
                            console.error('Failed to load stages:', err);
                        });
                });
            });

        });

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
