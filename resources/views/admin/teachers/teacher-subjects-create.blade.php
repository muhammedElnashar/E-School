@extends('layouts.master')

@section('title', __('Add Subject for Teacher'))

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">{{ __('Add Subject for') }} {{ $teacher->name }}</h3>
            <a href="{{ route('teacher.subject.index',$teacher->id) }}" class="btn btn-outline-primary btn-sm">{{ __('Back to List') }}</a>

        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('teacher.subject.store') }}">
                            @csrf
                            <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">

                            <div class="form-group">
                                <label>{{ __('Subject') }}</label>
                                <select name="subject_id" id="subject_id" class="form-control" required>
                                    <option value="">{{ __('Select Subject') }}</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Education Stage') }}</label>
                                <select name="education_stage_id" class="form-control" id="stageSelect">
                                    <option value="">{{ __('Select Education Stage') }}</option>
                                    {{-- لا تملأ خيارات هنا تلقائيًا --}}
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">{{ __('Save') }}</button>
                            <a href="{{ route('teacher.subject.index', $teacher->id) }}" class="btn btn-secondary">
                                {{ __('Back') }}
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section("script")
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const subjectSelect = document.getElementById('subject_id');
        const stageSelect = document.getElementById('stageSelect');

        subjectSelect.addEventListener('change', function () {
            const subjectId = this.value;

            stageSelect.innerHTML = '<option value="">{{ __("Select Education Stage") }}</option>';

            if (!subjectId) {
                return;
            }

            fetch(`/admin/subjects/${subjectId}/related-stages`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(stage => {
                        const option = document.createElement('option');
                        option.value = stage.id;
                        option.textContent = stage.name;
                        stageSelect.appendChild(option);
                    });

                    // تعيين القيمة القديمة إن وجدت
                    @if(old('education_stage_id'))
                        stageSelect.value = '{{ old('education_stage_id') }}';
                    @endif
                })
                .catch(error => console.error('Error fetching stages:', error));
        });

        if (subjectSelect.value) {
            subjectSelect.dispatchEvent(new Event('change'));
        }
    });

</script>
@endsection
