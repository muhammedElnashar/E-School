<!-- partial:../../partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/') }}">
                <i class="fa fa-tachometer menu-icon" style="margin: 0 1px 0 1px"></i>
                <span class="menu-title">{{ __('dashboard') }}</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#academics-menu" aria-expanded="false"
               aria-controls="academics-menu">
                <i class="fa fa-dedent menu-icon"></i><span class="menu-title">{{ __('Subjects') }}</span>
                <i class="fa fa-angle-left fa-2xl menu-arrow"></i>
            </a>
            <div class="collapse" id="academics-menu">
                <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route("subjects.index")}}"> {{ __('Subjects') }} </a>
                        </li>
                    <li class="nav-item">
                            <a class="nav-link" href="{{route("subjects.stages.management")}}"> {{ __('Subjects Edu_Stages') }} </a>
                        </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#education-menu" aria-expanded="false"
               aria-controls="education-menu">
                <i class="fa fa-edge menu-icon"></i><span class="menu-title">{{ __('Education Stages') }}</span>
                <i class="fa fa-angle-left fa-2xl menu-arrow"></i>
            </a>
            <div class="collapse" id="education-menu">
                <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route("stages.index")}}"> {{ __('Education Stages') }} </a>
                        </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#lesson-menu" aria-expanded="false"
               aria-controls="lesson-menu">
                <i class="fa fa-dedent menu-icon"></i><span class="menu-title">{{ __('Lessons') }}</span>
                <i class="fa fa-angle-left fa-2xl menu-arrow"></i>
            </a>
            <div class="collapse" id="lesson-menu">
                <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route("lessons.index")}}"> {{ __('Lessons List') }} </a>
                        </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#market-menu" aria-expanded="false"
               aria-controls="market-menu">
                <i class="fa fa-university menu-icon"></i><span class="menu-title">{{ __('MarketpLace Items') }}</span>
                <i class="fa fa-angle-left fa-2xl menu-arrow"></i>
            </a>
            <div class="collapse" id="market-menu">
                <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route("package.index")}}"> {{ __(' Packages') }} </a>
                        </li>
                    <li class="nav-item">
                            <a class="nav-link" href="{{route("digital-assets.index")}}"> {{ __(' Assets') }} </a>
                        </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#teacher-menu" aria-expanded="false"
               aria-controls="teacher-menu">
                <i class="fa fa-user menu-icon"></i><span class="menu-title">{{ __('Users') }}</span>
                <i class="fa fa-angle-left fa-2xl menu-arrow"></i>
            </a>
            <div class="collapse" id="teacher-menu">
                <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route("teacher.index")}}"> {{ __(' Teacher List') }} </a>
                        </li>
                    <li class="nav-item">
                            <a class="nav-link" href="{{route("admin.index")}}"> {{ __(' Admin List') }} </a>
                        </li>
                    <li class="nav-item">
                            <a class="nav-link" href="{{route("users.index")}}"> {{ __(' Student List') }} </a>
                        </li>

                </ul>
            </div>
        </li>
        @if(auth()->user()->isSuperAdmin())
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#payment-menu" aria-expanded="false"
               aria-controls="payment-menu">
                <i class="fa fa-paypal menu-icon"></i><span class="menu-title">{{ __('Purchases') }}</span>
                <i class="fa fa-angle-left fa-2xl menu-arrow"></i>
            </a>
                <div class="collapse" id="payment-menu">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route("purchases.index")}}"> {{ __('Manual Purchases List') }} </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route("purchases.payments")}}"> {{ __('Online Payment List') }} </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route("admin.transaction.teacher-transactions")}}"> {{ __('Teacher Transactions List') }} </a>
                        </li>
                    </ul>
                </div>

        </li>
        @endif
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#announcement-menu" aria-expanded="false"
               aria-controls="announcement-menu">
                <i class="fa fa-book menu-icon"></i><span class="menu-title">{{ __('Announcements') }}</span>
                <i class="fa fa-angle-left fa-2xl menu-arrow"></i>
            </a>
            <div class="collapse" id="announcement-menu">
                <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route("announcements.index")}}"> {{ __(' Announcements List') }} </a>
                        </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#assignment-menu" aria-expanded="false"
               aria-controls="assignment-menu">
                <i class="fa fa-star-o menu-icon"></i><span class="menu-title">{{ __('Assignments') }}</span>
                <i class="fa fa-angle-left fa-2xl menu-arrow"></i>
            </a>
            <div class="collapse" id="assignment-menu">
                <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route("assignment.all")}}"> {{ __(' Assignments List') }} </a>
                        </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#chat-menu" aria-expanded="false"
               aria-controls="chat-menu">
                <i class="fa fa-send menu-icon"></i><span class="menu-title">{{ __('Chats') }}</span>
                <i class="fa fa-angle-left fa-2xl menu-arrow"></i>
            </a>
            <div class="collapse" id="chat-menu">
                <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route("admin.chat.index")}}"> {{ __('All Chats') }} </a>
                        </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#conversations-menu" aria-expanded="false"
               aria-controls="conversations-menu">
                <i class="fa fa-send menu-icon"></i><span class="menu-title">{{ __('Conversations') }}</span>
                <i class="fa fa-angle-left fa-2xl menu-arrow"></i>
            </a>
            <div class="collapse" id="conversations-menu">
                <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route("admin.chat.conversations")}}"> {{ __('All Conversations') }} </a>
                        </li>
                </ul>
            </div>
        </li>
        @if(auth()->user()->isSuperAdmin())
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#settings-menu" aria-expanded="false"
               aria-controls="settings-menu">
                <i class="fa fa-gear menu-icon"></i><span class="menu-title">{{ __('Settings') }}</span>
                <i class="fa fa-angle-left fa-2xl menu-arrow"></i>
            </a>
            <div class="collapse" id="settings-menu">
                <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route("settings.index")}}"> {{ __('All Settings') }} </a>
                        </li>
                </ul>
            </div>
        </li>
        @endif

    </ul>
</nav>
