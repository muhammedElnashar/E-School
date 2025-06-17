@extends('layouts.master')

@section('title')
    {{ __('Edit Lesson') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"><i class="mdi mdi-pencil"></i> {{ __('Edit Lesson') }}</h3>
            <a href="{{ route('lessons.index') }}" class="btn btn-outline-primary btn-sm">{{ __('Back to List') }}</a>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3" method="POST" action="{{ route('lessons.update', $lesson->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>{{ __('Teacher') }} <span class="text-danger">*</span></label>
                                <select name="teacher_id" id="teacher_id" class="form-control">
                                    <option value="">{{ __('Select Teacher') }}</option>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('teacher_id', $lesson->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Subject') }} <span class="text-danger">*</span></label>
                                <select id="subject_id" name="subject_id" class="form-control">
                                    <option value="">{{ __('Select Subject') }}</option>
                                    {{-- سيتم تعبئتها عبر JavaScript --}}
                                </select>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Education Stage') }}</label>
                                <select name="education_stage_id" class="form-control" id="stageSelect">
                                    <option value="">{{ __('Select Education Stage') }}</option>
                                    {{-- سيتم تعبئتها عبر JavaScript --}}
                                </select>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Start Date') }} <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_datetime" class="form-control"
                                       value="{{ old('start_datetime', \Carbon\Carbon::parse($lesson->start_datetime)->format('Y-m-d\TH:i')) }}">
                            </div>

                            <div class="form-group">
                                <label>{{ __('End Date') }} <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_datetime" class="form-control"
                                       value="{{ old('end_datetime', \Carbon\Carbon::parse($lesson->end_datetime)->format('Y-m-d\TH:i')) }}">
                            </div>

                            <div class="form-group">
                                <label>{{ __('Lesson Type') }}</label>
                                <select name="lesson_type" class="form-control" required>
                                    <option value="">{{ __('Select Lesson Type') }}</option>
                                    @foreach (\App\Enums\LessonType::cases() as $lessonType)
                                        <option value="{{ $lessonType->value }}" {{ old('lesson_type', $lesson->lesson_type) == $lessonType->value ? 'selected' : '' }}>
                                            {{ __($lessonType->value) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="weeks_count">Recurrence Weeks <span class="text-danger">*</span></label>
                                <input
                                    type="number"
                                    name="recurrence[weeks_count]"
                                    id="weeks_count"
                                    class="form-control"
                                    min="1"
                                    value="{{ old('recurrence.weeks_count', $lesson->recurrence->weeks_count ?? 1) }}"
                                >
                            </div>

                            <div class="form-group">
                                <label>Exception Weeks</label>

                                <div id="exceptionWeeksContainer">
                                    {{-- لا نعرض أي input مبدئياً --}}
                                </div>

                                <button type="button" id="addExceptionWeekBtn" class="btn btn-sm btn-primary mt-2">Add Exception Week</button>
                            </div>

                            <button class="btn btn-success btn-block" type="submit">{{ __('Update Lesson') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.getElementById('addExceptionWeekBtn').addEventListener('click', function() {
            const container = document.getElementById('exceptionWeeksContainer');

            // إنشاء عنصر input جديد من نوع number
            const input = document.createElement('input');
            input.type = 'number';
            input.name = 'recurrence[exception_weeks][]';
            input.min = 1;
            input.max = {{ $weeksCount ?? 52 }};
            input.classList.add('form-control', 'mb-2');
            input.placeholder = 'Enter week number';

            // إضافة الحقل الجديد
            container.appendChild(input);
        });
    </script>
    <script>


        document.addEventListener('DOMContentLoaded', function () {
            const subjectSelect = document.getElementById('subject_id');
            const stageSelect = document.getElementById('stageSelect');
            const teacherSelect = document.getElementById('teacher_id');

            teacherSelect.addEventListener('change', fetchSubjects);
            subjectSelect.addEventListener('change', fetchStages);

            function fetchSubjects() {
                let teacherId = teacherSelect.value;
                if (!teacherId) return;

                fetch(`/admin/teachers/${teacherId}/subjects`)
                    .then(res => res.json())
                    .then(data => {
                        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
                        data.forEach(subject => {
                            subjectSelect.innerHTML += `<option value="${subject.id}">${subject.name}</option>`;
                        });

                        // إعادة تحديد القيمة القديمة أو الحالية
                        subjectSelect.value = "{{ old('subject_id', $lesson->subject_id) }}";
                        subjectSelect.dispatchEvent(new Event('change'));
                    });
            }

            function fetchStages() {
                let subjectId = subjectSelect.value;
                if (!subjectId) return;

                fetch(`/admin/subjects/${subjectId}/related-stages`)
                    .then(res => res.json())
                    .then(data => {
                        stageSelect.innerHTML = '<option value="">Select Education Stage</option>';
                        data.forEach(stage => {
                            stageSelect.innerHTML += `<option value="${stage.id}">${stage.name}</option>`;
                        });

                        // إعادة تحديد القيمة القديمة أو الحالية
                        stageSelect.value = "{{ old('education_stage_id', $lesson->education_stage_id) }}";
                    });
            }

            // trigger load if values are prefilled
            if (teacherSelect.value) {
                fetchSubjects();
            }
        });
    </script>
@endsection
