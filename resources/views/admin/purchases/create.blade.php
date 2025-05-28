@extends('layouts.master')

@section('title')
    {{ __('Manual Purchase') }}
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
            <h3 class="page-title"><i class="mdi mdi-cart-plus"></i> {{ __('Add Manual Purchase') }}</h3>
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-primary btn-sm">{{ __('Back to List') }}</a>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('purchases.store') }}">
                            @csrf

                            {{-- الطالب --}}
                            <div class="form-group">
                                <label>{{ __('Student') }} <span class="text-danger">*</span></label>
                                <select name="user_id" class="form-control select2" >
                                    <option value="">{{ __('Select Student') }}</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}" {{ old('user_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->user_code }} -{{ $student->name }} - {{ $student->email }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- عنصر المتجر --}}
                            <div class="form-group">
                                <label>{{ __('Marketplace Item') }} <span class="text-danger">*</span></label>
                                <select name="marketplace_item_id" class="form-control select2 " required>
                                    <option value="">{{ __('Select Item') }}</option>
                                    @foreach ($marketplaceItems as $item)
                                        <option
                                            value="{{ $item->id }}"
                                            data-price="{{ $item->price }}"
                                            data-credits="{{ $item->lecture_credits }}"
                                            {{ old('marketplace_item_id') == $item->id ? 'selected' : '' }}
                                        >
                                            {{ $item->name }} - {{ ucfirst($item->package_scope->value) }} - {{ number_format($item->price, 2) }}$
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" name="price" id="selected_price">
                            <input type="hidden" name="remaining_credits" id="selected_credits">

                            <button type="submit" class="btn btn-theme btn-block">{{ __('Submit Purchase') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Select2 core CSS -->
    @section('script')
        <script>
            $(document).ready(function () {
                $('.select2').select2({
                    placeholder: 'Select an option',
                    width: '100%'
                });

                // عند تغيير عنصر المتجر
                $('select[name="marketplace_item_id"]').on('change', function () {
                    var selected = $(this).find('option:selected');
                    var price = selected.data('price') || 0;
                    var credits = selected.data('credits') || 0;

                    $('#selected_price').val(price);
                    $('#selected_credits').val(credits);
                });

                // تفعيل التغيير لو فيه old selected
                $('select[name="marketplace_item_id"]').trigger('change');
            });
        </script>
    @endsection

@endsection
