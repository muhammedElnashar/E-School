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
                                <label>{{ __('Education Stage Subject') }} <span class="text-danger">*</span></label>
                                <select name="education_stage_subject_id" class="form-control" >
                                    <option value="">{{ __('Select') }}</option>
                                    @foreach ($educationStageSubjects as $ess)
                                        <option value="{{ $ess->id }}" {{ old('education_stage_subject_id') == $ess->id ? 'selected' : '' }}>
                                            {{ $ess->educationStage->name ?? '' }} - {{ $ess->subject->name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"  value="{{ old('name') }}">
                            </div>

                            <div class="form-group">
                                <label>{{ __('Description') }}</label>
                                <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Price') }} <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="price" class="form-control"  value="{{ old('price') }}">
                            </div>

                            <div class="form-group">
                                <label>{{ __('Lecture Credits') }}</label>
                                <input type="number" name="lecture_credits" class="form-control" value="{{ old('lecture_credits', 0) }}">
                            </div>

                            <div class="form-group">
                                <label>{{ __('Package Scope') }}</label>
                                <select name="package_scope" class="form-control" >
                                    <option value="">{{ __('Select') }}</option>
                                    @foreach (\App\Enums\Scopes::cases() as $scope)
                                        <option value="{{ $scope->value }}"
                                        @if(old('package_scope'))
                                            {{ old('package_scope') == $scope->value ? 'selected' : '' }}
                                            @else
                                            {{ isset($item) && $item->package_scope === $scope ? 'selected' : '' }}
                                            @endif
                                        >
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
