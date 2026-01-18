<!-- Sidenav Menu Start -->
<div class="sidenav-menu">

    <!-- Brand Logo -->
    <a href="{{ route('featured-projects.index') }}" class="logo">
        <span class="logo-light">
            <span class="logo-lg"><img src="{{ asset('assets/images/logo.png') }}" alt="logo"></span>
            <span class="logo-sm"><img src="{{ asset('assets/images/logo-sm.png') }}" alt="small logo"></span>
        </span>

        <span class="logo-dark">
            <span class="logo-lg"><img src="{{ asset('assets/images/logo-dark.png') }}" alt="dark logo"></span>
            <span class="logo-sm"><img src="{{ asset('assets/images/logo-sm.png') }}" alt="small logo"></span>
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <button class="button-sm-hover">
        <i class="ri-circle-line align-middle"></i>
    </button>

    <!-- Full Sidebar Menu Close Button -->
    <button class="button-close-fullsidebar">
        <i class="ri-close-line align-middle"></i>
    </button>

    <div data-simplebar>
        <!--- Sidenav Menu -->
        <ul class="side-nav">
            <li class="side-nav-title">Navigation</li>
            <li class="side-nav-item">
                <a href="{{ route('featured-projects.index') }}" class="side-nav-link">
                    <span class="menu-icon"><i class="ri-briefcase-4-line"></i></span>
                    <span class="menu-text"> Featured Projects </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('clients.index') }}" class="side-nav-link">
                    <span class="menu-icon"><i class="ri-image-line"></i></span>
                    <span class="menu-text"> Clients </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('users.index') }}" class="side-nav-link">
                    <span class="menu-icon"><i class="ri-user-3-line"></i></span>
                    <span class="menu-text"> Users </span>
                </a>
            </li>
        </ul>
    </div>
</div>
<!-- Sidenav Menu End -->
