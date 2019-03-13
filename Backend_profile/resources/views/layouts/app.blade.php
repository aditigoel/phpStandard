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
   
    <link href="{{ URL::asset('admin_assets/css/style.css') }}" type="text/css" rel="stylesheet">
  
   
</head>
<body>
          @yield('content')
    
</body>
</html>
