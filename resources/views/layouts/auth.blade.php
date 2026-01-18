<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.title-meta')
    @include('partials.head-css')
</head>

<body class="authentication-bg">
    <div class="account-pages py-5">
        <div class="container">
            @yield('content')
        </div>
    </div>

    @include('partials.footer-scripts')
</body>

</html>
