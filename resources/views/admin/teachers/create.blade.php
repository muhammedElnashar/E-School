@extends('layouts.master')

@section('title')
    {{ __('Create Teacher') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"><i class="mdi mdi-cart-plus"></i> {{ __('Create Teacher') }}</h3>
            <a href="{{ route('teacher.index') }}" class="btn btn-outline-primary btn-sm">{{ __('Back to List') }}</a>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3" method="POST" action="{{ route('teacher.store') }}">
                            @csrf
                            <div class="form-group">
                                <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Email') }}</label>
                                <input type="text" name="email" value="{{ old('email') }}" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>{{ __('Password') }} <span class="text-danger">*</span></label>
                                <input type="password"  name="password" class="form-control"  required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Iban') }}</label>
                                <input type="text" name="iban" value="{{ old('iban') }}"
                                       class="form-control">
                            </div>
                            <button class="btn btn-theme btn-block" type="submit">{{ __('Submit') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


