@extends('layouts.master')

@section('title')
    {{ __('Create Settings') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"><i class="mdi mdi-cart-plus"></i> {{ __('Create Teacher') }}</h3>
            <a href="{{ route('settings.index') }}" class="btn btn-outline-primary btn-sm">{{ __('Back to List') }}</a>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3" method="POST" action="{{ route('settings.store') }}">
                            @csrf
                            <div class="form-group">
                                <label>{{ __('Key') }} <span class="text-danger">*</span></label>
                                <input type="text" name="key" class="form-control" value="{{ old('key') }}" required>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Value') }}</label>
                                <textarea
                                    name="value" class="form-control">{{ old('value') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="add_to_env">Active</label>
                                <select name="add_to_env" id="add_to_env" class="form-control">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
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


