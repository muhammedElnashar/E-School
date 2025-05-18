{{--@php
    $lang = Session::get('language');
@endphp--}}
<!-- الأساسيات -->
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.bundle.base.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/fonts/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">

<!-- إضافات -->
<link rel="stylesheet" href="{{ asset('/assets/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/jquery-toast-plugin/jquery.toast.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/color-picker/color.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/css/datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/css/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/css/ekko-lightbox.css') }}">

<!-- Bootstrap Table -->
<link rel="stylesheet" href="{{ asset('/assets/bootstrap-table/bootstrap-table.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/bootstrap-table/fixed-columns.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/bootstrap-table/reorder-rows.css') }}">
<style>
    /* تنسيق الحاوية العامة */
    .select2-container {
        width: 100% !important;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        font-size: 14px;
    }

    /* الشكل العام للـ Select */
    .select2-container--default .select2-selection--multiple {
        background-color: #f9f9f9;
        border: 1px solid #dcdcdc;
        border-radius: 8px;
        padding: 8px 10px;
        min-height: 44px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    /* شكل التحديد عند التركيز */
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #66afe9;
        box-shadow: 0 0 0 3px rgba(102, 175, 233, 0.3);
    }

    /* النصوص داخل التحديد */
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    /* شكل الـ tags */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #4e73df;
        color: #fff;
        border: none;
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 13px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* زر إزالة العنصر */
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        margin-right: 6px;
        color: #fff;
        font-weight: bold;
        cursor: pointer;
    }

    /* إخفاء السهم لأنه غير ضروري في multiple */
    .select2-container--default .select2-selection--multiple .select2-selection__arrow {
        display: none;
    }

    /* تحسين القائمة المنسدلة */
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #4e73df;
        color: white;
    }

    /* تحسين مظهر العناصر في القائمة */
    .select2-container--default .select2-results > .select2-results__options {
        max-height: 250px;
        overflow-y: auto;
    }
</style>




{{-- <link rel="shortcut icon" href="{{asset(config('global.LOGO_SM')) }}" /> --}}
<link rel="shortcut icon" href="{{ url(Storage::url(env('FAVICON'))) }}"/>

{{--@php--}}
{{--//    $theme_color = getSettings('theme_color');--}}
{{--//    $secondary_color = getSettings('secondary_color');--}}

{{--    // echo json_encode($theme_color);--}}
{{--//    $theme_color = $theme_color['theme_color'];--}}
{{--//    $secondary_color =   $secondary_color['secondary_color'];--}}
{{--@endphp--}}
{{--@php--}}
{{--/*    $login_image = getSettings('login_image');*/--}}
{{--    if($login_image!= null){--}}
{{--        $path = $login_image['login_image'];--}}
{{--        $login_image = url(Storage::url($path));--}}
{{--    }--}}
{{--    else {--}}
{{--        $login_image = url(Storage::url('eschool.jpg'));--}}
{{--    }--}}

{{--@endphp--}}
<style>

</style>
<script>
    var baseUrl = "{{ URL::to('/') }}";
    const onErrorImage = (e) => {
        e.target.src = "{{ asset('/storage/no_image_available.jpg') }}";
    };
</script>
