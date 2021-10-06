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
                    @foreach ($parentMenu as $item)
                        <li class="nav-item {{ $parentSection == 'dashboards' ? 'active' : '' }}">
                            <a class="nav-link collapsed" href="#{{ $item->menu_slug }}" data-toggle="collapse"
                               role="button"
                               aria-expanded="{{ $parentSection == 'dashboards' ? 'true' : '' }}"
                               aria-controls="{{ $item->menu_slug }}">
                                <i class="ni ni-shop text-primary"></i>
                                <span class="nav-link-text">{{ $item->menu_title }}</span>
                            </a>
                            <div class="collapse {{ $parentSection == 'dashboards' ? 'show' : '' }}"
                                 id="{{ $item->menu_slug }}">
                                <ul class="nav nav-sm flex-column">
                                    @foreach ($childMenu as $subItem)
                                        @if ($subItem->module === $item->module)
                                            <li class="nav-item {{ $elementName == 'dashboard' ? 'active' : '' }}">
                                                <a href="{{ url($subItem->path) }}"
                                                   class="nav-link">{{ $subItem->menu_title }}</a>
                                            </li>
                                        @endif
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
