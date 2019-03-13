<!DOCTYPE html>
<html lang="en">
  <head>
    <title>{{ config('app.name') }}</title>
    <meta charset="utf-8">
    <!--base(href="/")-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minmum-scale=1, maximum-scale=1">
    <!-- StyleSheet-->
    <link href="{{ URL::asset('admin_assets/css/bootstrap.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ URL::asset('admin_assets/css/fonts.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ URL::asset('admin_assets/css/font-custom-icons.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ URL::asset('admin_assets/css/button.css') }}" type="text/css" rel="stylesheet">
    <!--owl-carousel-->
    <link href="{{ URL::asset('admin_assets/css/owl.carousel.cs') }}s" type="text/css" rel="stylesheet">
    <link href="{{ URL::asset('admin_assets/css/owl.theme.default.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ URL::asset('admin_assets/css/style.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ URL::asset('admin_assets/css/responsive.css') }}" type="text/css" rel="stylesheet">
    <!-- Scripting-->
    <script src="{{ URL::asset('admin_assets/js/modernizr.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}" type="text/javascript"></script>



</head>
<body>
          @yield('content')
    </div>
    <script src="{{ URL::asset('admin_assets/js/owl.carousel.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('admin_assets/js/app.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('admin_assets/js/routing.js') }}" type="text/javascript"></script>
</body>
</html>
