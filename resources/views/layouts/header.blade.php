<!-- partial:../../partials/_navbar.html -->
<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center"><a
            class="navbar-brand brand-logo" href="{{ URL::to('/') }}"> <img src="{{url('assets/logo.svg') }}"
                                                                            alt="logo"> </a> <a
            class="navbar-brand brand-logo-mini" href="{{ URL::to('/') }}"> <img src="{{ url('assets/logo2.svg')}}"
                                                                                 alt="logo"> </a></div>
    <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize"><span
                class="fa fa-bars"></span></button>
        <ul class="navbar-nav navbar-nav-left">
            <li class="nav-item"><a class="nav-link" href="#" aria-expanded="false"> <span
                        class="badge badge-success">v</span> </a></li>
        </ul>
        <ul class="navbar-nav navbar-nav-right">
            <a class="nav-link" href="{{url('clear')}}"> <input
                    class="btn-inverse-info btn" type="submit" value="{{ __('cache_clear') }}">
            </a>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="languageDropdown" href="#" data-toggle="dropdown" aria-expanded="false" role="button" aria-haspopup="true">
                    <i class="fa fa-language"></i>
                    <span class="ml-1">{{ strtoupper(app()->getLocale()) }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="languageDropdown">
                    @foreach (get_language() as $language)
                        <a class="dropdown-item preview-item" href="{{ url('set-language/' . $language->code) }}">
                            <div class="preview-thumbnail">
                                <img src="{{ asset('assets/images/flags/' . $language->code . '.png') }}" alt="{{ $language->name }}" class="profile-pic" style="width: 30px; height: 20px;">
                            </div>
                            <div class="preview-item-content d-flex align-items-center">
                                <h6 class="preview-subject mb-0 font-weight-normal">{{ $language->name }}</h6>
                            </div>
                        </a>

                    @if (!$loop->last)
                            <div class="dropdown-divider"></div>
                        @endif
                    @endforeach
                </div>
            </li>


            <li class="nav-item nav-profile dropdown"><a class="nav-link dropdown-toggle" id="profileDropdown" href="#"
                                                         data-toggle="dropdown" aria-expanded="true">
                    @if(auth()->user()->role === 'admin')
                        <img src="{{ asset('assets/admin.jpg') }}" alt="image" class="nav-profile-img">
                    @else
                        <div class="nav-profile-img"><img src="{{ Auth::user()->image }}" alt="image"
                                                          onerror="onErrorImage(event)"></div>
                    @endif

                    <div class="nav-profile-text"><p class="mb-1 text-black">{{ Auth::user()->name }}</p></div>
                </a>
                <div class="dropdown-menu navbar-dropdown"
                     aria-labelledby="profileDropdown"> @can('update-admin-profile')
                        <a class="dropdown-item" href="{{ route('edit-profile') }}"> <i
                                class="fa fa-user mr-2"></i>{{ __('update_profile') }}</a>
                        <div class="dropdown-divider"></div>
                    @endcan
                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                          style="display: none;"> @csrf </form>
                    <a class="dropdown-item" href="#"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> <i
                            class="fa fa-sign-out mr-2 text-primary"></i> {{ __('signout') }} </a></div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                data-toggle="offcanvas"><span class="fa fa-bars"></span></button>
    </div>
</nav>
