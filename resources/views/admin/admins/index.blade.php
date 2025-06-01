@extends('layouts.master')

@section('title')
    {{ __('Admins') }}
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
        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="page-title">{{ __('Manage Admin') }}</h3>
            <a href="{{ route('admin.create') }}" class="btn btn-primary">{{ __('Create Admin') }}</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title">{{ __('Admin List') }}</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Role') }}</th>
                            <th>{{ __('Image') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($admins as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->user_code }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->phone }}</td>
                                <td>{{ $item->role->value }}</td>
                                <td>
                                    <img src="{{asset("admin.jpg") }}">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" data-toggle="modal"
                                            data-target="#editModal-{{ $item->id }}">
                                        {{ __('Edit') }}
                                    </button>
                                    <form action="{{ route('admin.destroy', $item->id) }}" method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger deleted-btn">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal-{{ $item->id }}" tabindex="-1" role="dialog"
                                 aria-labelledby="editModalLabel-{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-md" role="document">
                                    <form action="{{ route('admin.update', $item->id) }}" method="POST"
                                          enctype="multipart/form-data" class="w-100">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">{{ __('Edit Admin') }}
                                                    - {{ $item->name }}</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control"
                                                           value="{{ old('name', $item->name) }}" required>
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ __('Email') }}</label>
                                                    <input type="text" name="email"
                                                           value="{{ old('email', $item->email) }}"
                                                           class="form-control">
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ __('Role') }}</label>
                                                    <select name="role" class="form-control select2">
                                                        <option value="">{{ __('Select Role') }}</option>
                                                        @foreach (\App\Enums\RoleEnum::cases() as $role)
                                                            @if (in_array($role->value, [\App\Enums\RoleEnum::Admin->value, \App\Enums\RoleEnum::SuperAdmin->value]))
                                                                <option value="{{ $role->value }}"
                                                                    {{ old('role', $item->role->value) == $role->value ? 'selected' : '' }}>
                                                                    {{ __($role->value) }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('Phone') }}</label>
                                                    <input type="text" name="phone"
                                                           value="{{ old('phone', $item->phone) }}"
                                                           class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('Password') }} <span
                                                            class="text-danger"></span></label>
                                                    <input type="password" name="password" class="form-control"
                                                           >
                                                </div>


                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">{{ __('Close') }}</button>
                                                    <button type="submit"
                                                            class="btn btn-success">{{ __('Save Changes') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="7">{{ __('No data found.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    {{ $admins->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>

        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.deleted-btn').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: '{{ __("Delete Confirmation") }}',
                        text: '{{ __("Are you sure you want to delete this Record?") }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '{{ __("Yes, delete it!") }}',
                        cancelButtonText: '{{ __("Cancel") }}',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                placeholder: 'Select an option',
                width: '100%'
            });
        });
    </script>

@endsection
