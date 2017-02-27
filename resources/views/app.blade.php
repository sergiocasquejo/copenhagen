<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" ng-app="copenhagenApp">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="fragment" content="!">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <base href="/">

    <!-- Styles -->
    <link rel="stylesheet" href="/lib/bootstrap/dist/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="/lib/bootstrap/dist/css/bootstrap-theme.min.css" type="text/css">
    <link rel="stylesheet" href="/lib/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="/lib/angular-bootstrap-toggle/dist/angular-bootstrap-toggle.min.css" type="text/css">
    <link rel="stylesheet" href="/lib/angular-moment-picker/dist/angular-moment-picker.min.css" type="text/css">
    <link rel="stylesheet" href="/lib/angular-bootstrap-calendar/dist/css/angular-bootstrap-calendar.min.css" type="text/css">
    <link rel="stylesheet" href="/lib/angularjs-slider/dist/rzslider.min.css" type="text/css">
    <link rel="stylesheet" href="/css/app.min.css" type="text/css">

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <ui-view>@yield('content')</ui-view>

    <script src="/lib/angular/angular.min.js" type="text/javascript"></script>
    <script src="/lib/angular-ui-router/release/angular-ui-router.min.js" type="text/javascript"></script>
    <script src="/lib/angular-file-upload/dist/angular-file-upload.min.js" type="text/javascript"></script>
    <script src="/lib/angular-bootstrap-toggle/dist/angular-bootstrap-toggle.min.js" type="text/javascript"></script>
    <script src="/lib/angular-bootstrap/ui-bootstrap-tpls.min.js" type="text/javascript"></script>
    <script src="/lib/moment/moment.js" type="text/javascript"></script>
    <script src="/lib/angular-moment-picker/dist/angular-moment-picker.min.js" type="text/javascript"></script>
    <script src="/lib/angular-bootstrap-calendar/dist/js/angular-bootstrap-calendar-tpls.min.js" type="text/javascript"></script>
    <script src="/lib/ng-country-select/dist/ng-country-select.min.js" type="text/javascript"></script>
    <script src="/lib/angular-animate/angular-animate.min.js"></script>
    <script src="/lib/angularjs-slider/dist/rzslider.min.js"></script>
    <script src="/js/app.js" type="text/javascript"></script>
    <script src="/js/routes/index.js" type="text/javascript"></script>
    <script src="/js/services/api.js" type="text/javascript"></script>
    <script src="/js/controllers/auth.js" type="text/javascript"></script>
    <script src="/js/controllers/home.js" type="text/javascript"></script>
    <script src="/js/controllers/admin.js" type="text/javascript"></script>
    <script src="/js/controllers/rate.js" type="text/javascript"></script>
    <script src="/js/controllers/room.js" type="text/javascript"></script>
    <script src="/js/controllers/calendar.js" type="text/javascript"></script>
    <script src="/js/controllers/bookings.js" type="text/javascript"></script>
</body>

</html>