<!DOCTYPE html>
<html lang="en">
    <head>
        <title>@yield('title')</title>
        <?php $version = env('JS_VERSION', 1); ?>
        @include('web.layouts.header')

    </head>
    <body class="app-bg">
        <div id="my-wrapper" class="my-wrapper">
            @yield('content')
        </div><!-- /my-wrapper -->
    </body>
</html>