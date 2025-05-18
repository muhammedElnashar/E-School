@extends('layouts.master')
@section('title')
    {{ __('dashboard') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-theme text-white mr-2">
                    <i class="fa fa-home"></i>
                </span> {{ __('dashboard') }}
            </h3>
        </div>
        @canany(['class-teacher'])
            <div class="row classes">
                @if ($class_sections)
                    <div class="col-md-12 grid-margin stretch-card search-container">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">{{ __('Class Teachers') }}</h4>
                                <div class="d-flex flex-wrap">
                                    @php
                                        $colors = [
                                            'bg-gradient-danger',
                                            'bg-gradient-success',
                                            'bg-gradient-primary',
                                            'bg-gradient-info',
                                            'bg-gradient-secondary',
                                            'bg-gradient-warning',
                                        ];
                                        $colorIndex = 0;
                                    @endphp

                                    @foreach ($class_sections as $class_section)
                                        @php
                                            $currentColor = $colors[$colorIndex];
                                            $colorIndex = ($colorIndex + 1) % count($colors);
                                        @endphp

                                        <div class="col-md-2 stretch-card grid-margin">
                                            <div class="card {{ $currentColor }} card-img-holder text-white">
                                                <div class="card-body">
                                                    <img src="{{ asset(config('global.CIRCLE_SVG')) }}"
                                                        class="card-img-absolute" alt="circle-image" />
                                                    <h6 class="mb-2">
                                                        <h4>{{ $class_section->class->name }}-{{ $class_section->section->name }}
                                                            {{ $class_section->class->medium->name }}
                                                            {{ $class_section->class->streams->name ?? '' }}</h4>
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endcanany

    </div>
@endsection
@section('script')

@endsection
