<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.title-meta')
    @include('partials.head-css')
    @stack('styles')
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">
        @include('partials.topbar')

        @include('partials.sidenav')

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->
        <div class="page-content">
            <div class="page-container">
                @yield('content')
            </div>

            @include('partials.footer')
        </div>
        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    @include('partials.customizer')

    @include('partials.footer-scripts')

    @stack('scripts')
</body>

</html>
