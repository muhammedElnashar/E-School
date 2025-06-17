@extends('layouts.master')

@section('title')
    {{ __('Create Lessons') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"><i class="mdi mdi-cart-plus"></i> {{ __('Create Lesson') }}</h3>
            <a href="{{ route('lessons.index') }}" class="btn btn-outline-primary btn-sm">{{ __('Back to List') }}</a>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3" method="POST" action="{{ route('lessons.store') }}">
                            @csrf

                            <div class="form-group">
                                <label>{{ __('Teacher') }} <span class="text-danger">*</span></label>
                                <select name="teacher_id" id="teacher_id" class="form-control"  >
                                    <option value="">{{ __('Select Teacher') }}</option>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Subject') }} <span class="text-danger">*</span></label>
                                <select id="subject_id" name="subject_id" class="form-control">
                                    <option value="">Select Subject</option>
                                    {{-- سيتم تعبئتها عبر JavaScript --}}
                                </select>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Education Stage') }}</label>
                                <select name="education_stage_id" class="form-control" id="stageSelect">
                                    <option value="">{{ __('Select Education Stage') }}</option>
                                    {{-- لا تملأ خيارات هنا تلقائيًا --}}
                                </select>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Start Date') }} <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_datetime" class="form-control" value="{{ old('start_datetime') }}" >
                            </div>
                            <div class="form-group">
                                <label>{{ __('End Date') }} <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_datetime" class="form-control" value="{{ old('end_datetime') }}" >
                            </div>

                            <div class="form-group">
                                <label>{{ __('Lesson Type') }}</label>
                                <select name="lesson_type" class="form-control" required >
                                    <option value="">{{ __('Select Lesson Type') }}</option>
                                    @foreach (\App\Enums\LessonType::cases() as $lessonType)
                                        <option value="{{ $lessonType->value }}"
                                            {{ old('lesson_type') == $lessonType->value ? 'selected' : '' }}>
                                            {{ __($lessonType->value) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group">
                                <label for="weeks_count">Recurrence Week<span class="text-danger">*</span></label>
                                <input
                                    type="number"
                                    name="recurrence[weeks_count]"
                                    id="weeks_count"
                                    class="form-control"
                                    min="1"
                                    value="{{ old('recurrence.weeks_count', 1) }}"

                                >
                            </div>

                            <div class="form-group">
                                <label>Exception Weeks</label>

                                <div id="exceptionWeeksContainer">
                                    {{-- لا نعرض أي input مبدئياً --}}
                                </div>

                                <button type="button" id="addExceptionWeekBtn" class="btn btn-sm btn-primary mt-2">Add Exception Week</button>
                            </div>


                            <button class="btn btn-theme btn-block" type="submit">{{ __('Submit') }}</button>
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
    <script>
        document.getElementById('teacher_id').addEventListener('change', function () {
            let teacherId = this.value;

            fetch(`/admin/teachers/${teacherId}/subjects`)
                .then(res => res.json())

                .then(data => {
                    let subjectSelect = document.getElementById('subject_id');
                    subjectSelect.innerHTML = '<option value="">Select Subject</option>';
                    data.forEach(subject => {
                        console.log(data)
                        subjectSelect.innerHTML += `<option value="${subject.id}">${subject.name}</option>`;
                    });
                });
        });

    </script>

@endsection

