<nav class="sidenav navbar navbar-vertical fixed-left navbar-expand-xs navbar-light bg-white" id="sidenav-main">
    <div class="scrollbar-inner scroll-scrollx_visible">
        <div class="sidenav-header d-flex align-items-center">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('pictures') }}/A4lution_logo.png" class="navbar-brand-img" alt="...">
            </a>
            <div class="ml-auto">
                <!-- Sidenav toggler -->
                <div class="sidenav-toggler d-none d-xl-block" data-action="sidenav-unpin" data-target="#sidenav-main">
                    <div class="sidenav-toggler-inner">
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="navbar-inner">
            <!-- Collapse -->
            <div class="collapse navbar-collapse" id="sidenav-collapse-main">
                <!-- Nav items -->
                <ul class="navbar-nav">
                    @foreach ($sidebarMenu as $mainView)
                        <li class="nav-item {{ $mainView->module == optional($nowView)->module ? 'active' : '' }}">
                            <a class="nav-link collapsed" href="#{{ $mainView->menu_slug }}" data-toggle="collapse"
                                role="button"
                                aria-expanded="{{ $parentSection == 'dashboards' ? 'true' : '' }}"
                                aria-controls="{{ $mainView->menu_slug }}">
                                <i class="ni ni-shop text-primary"></i>
                                <span class="nav-link-text">{{ $mainView->menu_title }}</span>
                            </a>
                            <div class="collapse {{ $mainView->module == optional($nowView)->module ? 'show' : '' }}"
                                id="{{ $mainView->menu_slug }}">
                                <ul class="nav nav-sm flex-column">
                                    @foreach ($mainView->subViews as $subView)
                                        <li class="nav-item {{ $subView->menu_slug == optional($nowView)->menu_slug ? 'active' : '' }}">
                                            <a href="{{ url($subView->path) }}"
                                                class="nav-link">{{ $subView->menu_title }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</nav>
