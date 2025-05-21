@extends('layouts.master')

@section('title')
    {{ __('Create Package') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"><i class="mdi mdi-cart-plus"></i> {{ __('Create Package') }}</h3>
            <a href="{{ route('package.index') }}" class="btn btn-outline-primary btn-sm">{{ __('Back to List') }}</a>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3" method="POST" action="{{ route('package.store') }}">
                            @csrf

                            <div class="form-group">
                                <label>{{ __('Subject') }} <span class="text-danger">*</span></label>
                                <select name="subject_id" class="form-control" id="subjectSelect" required>
                                    <option value="">{{ __('Select Subject') }}</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
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

                            <div class="form-group">
                                <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Description') }}</label>
                                <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Price') }} <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Lecture Credits') }}</label>
                                <input type="number" name="lecture_credits" class="form-control" value="{{ old('lecture_credits', 0) }}">
                            </div>

                            <div class="form-group">
                                <label>{{ __('Package Scope') }}</label>
                                <select name="package_scope" class="form-control" required>
                                    <option value="">{{ __('Select Scope') }}</option>
                                    @foreach (\App\Enums\Scopes::cases() as $scope)
                                        <option value="{{ $scope->value }}"
                                            {{ old('package_scope') == $scope->value ? 'selected' : '' }}>
                                            {{ __($scope->value) }}
                                        </option>
                                    @endforeach
                                </select>
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
        document.addEventListener('DOMContentLoaded', function () {
            const subjectSelect = document.getElementById('subjectSelect');
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

