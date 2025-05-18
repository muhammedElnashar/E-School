@extends('layouts.master')

@section('title', __('Manage Subject Stages'))

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">{{ __('Manage Subject Stages') }}</h3>
        </div>

        <div class="row">
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">

                        {{-- اختيار المادة --}}
                        <div class="form-group">
                            <label>{{ __('Select Subject') }}</label>
                            <select id="subject-select" class="form-control">
                                <option value="">{{ __('Choose Subject') }}</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- اختيار المراحل (select2 multiple) --}}
                        <div class="form-group mt-3">
                            <label>{{ __('Select Stages') }}</label>
                            <select id="stages-select" class="form-control select2" multiple="multiple" disabled></select>
                        </div>

                        <button id="save-button" class="btn btn-primary mt-3" disabled>{{ __('Save') }}</button>
                    </div>
                </div>
            </div>

            {{-- جدول المواد والمراحل --}}
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h5>{{ __('Subjects and their stages') }}</h5>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>{{ __('Subject') }}</th>
                                <th>{{ __('Stages') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($subjects as $subject)
                                <tr>
                                    <td>{{ $subject->name }}</td>
                                    <td>
                                        @foreach ($subject->stages as $stage)
                                            <span class="badge badge-info">{{ $stage->name }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')

    <!-- روابط Toastr CSS و JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {

            $('#subject-select').on('change', function() {
                let subjectId = $(this).val();

                if (!subjectId) {
                    $('#stages-select').val(null).trigger('change');
                    $('#stages-select').prop('disabled', true);
                    $('#save-button').prop('disabled', true);
                    return;
                }

                $('#stages-select').prop('disabled', false);
                $('#save-button').prop('disabled', false);

                $.get(`/admin/subjects/${subjectId}/stages`, function(data) {
                    $('#stages-select').empty();

                    data.allStages.forEach(stage => {
                        let isSelected = data.selectedStages.includes(stage.id);
                        let option = new Option(stage.name, stage.id, isSelected, isSelected);
                        $('#stages-select').append(option);
                    });

                    $('#stages-select').trigger('change');
                });
            });

            $('#save-button').on('click', function() {
                let subjectId = $('#subject-select').val();
                let selectedStages = $('#stages-select').val();

                if (!subjectId) {
                    toastr.error('Please select a subject.');
                    return;
                }

                $.ajax({
                    url: `/admin/subjects/${subjectId}/stages/sync`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        stages: selectedStages
                    },
                    success: function() {
                        Swal.fire({
                            title: "{{ __('success') }}",
                            text: "{{ __('stages_updated_successfully') }}",
                            icon: "success",
                            confirmButtonText: "{{ __('ok') }}"
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function() {
                        toastr.error('An error occurred while saving.');
                    }
                });
            });
        });
    </script>
@endsection
