{{--@php
    $lang = Session::get('language');
@endphp--}}
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.bundle.base.css') }}" async>

<link rel="stylesheet" href="{{ asset('/assets/fonts/font-awesome.min.css') }}" async/>
<link rel="stylesheet" href="{{ asset('/assets/select2/select2.min.css') }}" async>
<link rel="stylesheet" href="{{ asset('/assets/jquery-toast-plugin/jquery.toast.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/color-picker/color.min.css') }}" async>
        <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/css/datepicker.min.css') }}" async>
<link rel="stylesheet" href="{{ asset('/assets/css/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/css/ekko-lightbox.css') }}">

<link rel="stylesheet" href="{{ asset('/assets/bootstrap-table/bootstrap-table.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/bootstrap-table/fixed-columns.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/bootstrap-table/reorder-rows.css') }}">


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
