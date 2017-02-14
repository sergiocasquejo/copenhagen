<!DOCTYPE html>
<html lang="en" ng-app="copenhagenApp">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Copenhagen</title>
    <base href="/">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="/lib/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="/lib/bootstrap-theme.min.css" type="text/css">
    <link rel="stylesheet" href="/lib/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="/lib/fullcalendar/dist/fullcalendar.min.css" type="text/css">
    <link rel="stylesheet" href="/lib/angular-bootstrap-toggle/dist/angular-bootstrap-toggle.min.css" type="text/css">
    <link rel="stylesheet" href="/css/admin.min.css" type="text/css">
    <script src="/lib/jquery/dist/jquery.min.js" type="text/javascript"></script>
    <script src="/lib/angular.min.js" type="text/javascript"></script>
    <script src="/lib/angular-ui-router.min.js" type="text/javascript"></script>
    <script src="/lib/angular-file-upload/dist/angular-file-upload.min.js" type="text/javascript"></script>
    <script src="/lib/angular-bootstrap-toggle/dist/angular-bootstrap-toggle.min.js" type="text/javascript"></script>
    <script src="/lib/ui-bootstrap-tpls-2.5.0.min.js" type="text/javascript"></script>
    <script src="/lib/moment/moment.js" type="text/javascript"></script>
    <script src="/lib/fullcalendar/dist/fullcalendar.min.js" type="text/javascript"></script>
    <script src="/lib/calendar.js" type="text/javascript"></script>
    <script src="/js/app.js" type="text/javascript"></script>
    <script src="/js/routes/index.js" type="text/javascript"></script>
    <script src="/js/services/api.js" type="text/javascript"></script>
    <script src="/js/controllers/auth.js" type="text/javascript"></script>
    <script src="/js/controllers/home.js" type="text/javascript"></script>
    <script src="/js/controllers/admin.js" type="text/javascript"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <ui-view></ui-view>
</body>

</html>