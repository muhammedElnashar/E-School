@extends('layouts.master')

@section('title')
    {{ __('Create Announcement') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"><i class="mdi mdi-cart-plus"></i> {{ __('Create Announcement') }}</h3>
            <a href="{{ route('announcements.index') }}" class="btn btn-outline-primary btn-sm">{{ __('Back to List') }}</a>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3" method="POST" action="{{ route('announcements.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>{{ __('Title') }} <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}" >
                            </div>

                            <div class="form-group">
                                <label>{{ __('Content') }}</label>
                                <textarea  name="content"  class="form-control">{{ old('content') }}
                                </textarea>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Image') }}</label>
                                <input type="file" name="image"  class="form-control">
                            </div>

                            <button class="btn btn-theme btn-block" type="submit">{{ __('Submit') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


