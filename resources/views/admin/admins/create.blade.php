@extends('layouts.master')

@section('title')
    {{ __('Create Admin') }}
@endsection
@section("css")
    <style>
        /* الحاوية العامة لـ Select2 */
        .select2-container {
            width: 100% !important;
        }

        /* الحقل المختار */
        .select2-container .select2-selection--single {
            height: 45px;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        /* عند التركيز */
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default .select2-selection--single:active,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
            outline: none;
        }

        /* النص المعروض */
        .select2-container .select2-selection__rendered {
            color: #212529;
            font-size: 14px;
        }

        /* السهم */
        .select2-container .select2-selection__arrow {
            height: 100%;
            right: 10px;
        }

        /* القائمة المنسدلة */
        .select2-dropdown {
            border-radius: 10px;
            border: 1px solid #ccc;
        }

        .select2-results__option {
            padding: 10px;
            font-size: 14px;
        }

        .select2-results__option--highlighted {
            background-color: #007bff;
            color: #fff;
        }
    </style>

@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"><i class="mdi mdi-cart-plus"></i> {{ __('Create Admin') }}</h3>
            <a href="{{ route('admin.index') }}" class="btn btn-outline-primary btn-sm">{{ __('Back to List') }}</a>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3" method="POST" action="{{ route('admin.store') }}">
                            @csrf
                            <div class="form-group">
                                <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" >
                            </div>

                            <div class="form-group">
                                <label>{{ __('Email') }}</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>{{ __('Role') }}</label>
                                <select name="role" class="form-control select2" >
                                    <option value="">{{ __('Select Role') }}</option>
                                    @foreach (\App\Enums\RoleEnum::cases() as $role)
                                        @if (in_array($role->value, [\App\Enums\RoleEnum::Admin->value, \App\Enums\RoleEnum::SuperAdmin->value]))
                                            <option value="{{ $role->value }}"
                                                {{ old('role') == $role->value ? 'selected' : '' }}>
                                                {{ __($role->value) }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Phone') }}</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>{{ __('Password') }} <span class="text-danger">*</span></label>
                                <input type="password"  name="password" class="form-control"  >
                            </div>

                            <button class="btn btn-theme btn-block" type="submit">{{ __('Submit') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section("script")
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                placeholder: 'Select an option',
                width: '100%'
            });
        });
    </script>

@endsection
