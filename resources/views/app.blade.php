<!DOCTYPE html>
<html lang="<?php echo config('app.locale') ?>" ng-app="copenhagenApp">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="@{{ keywords }}"/>
    <meta name="description" content="@{{ description  }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="fragment" content="!">
    <title ng-bind="title">{{ config('app.name', 'Copenhagen') }}</title>
    <base href="/">
    <link rel="canonical" href="@{{ canonical }}"/>
    <link rel="icon" type="image/png" href="favicon.ico" />
    <link rel="stylesheet" href="/dist/styles/app.min.css" type="text/css">
    <link type="text/css" rel="stylesheet" charset="UTF-8" href="https://translate.googleapis.com/translate_static/css/translateelement.css">
    <!-- Scripts -->
    <script>
        window.CopenhagenAppConfig = {!! json_encode([
            'csrfToken' => csrf_token(),
            'paymentMethod' => ['pesopay' => config('pesopay.enable')],
            'bedding' => config('copenhagen.bedding'),
            'disabledDates' => json_encode(\App\DisableDate::getFutureDisabledDates()),
            'extraPerson' => 500
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
    <script src="/dist/scripts/app.min.js"></script>
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({pageLanguage: 'en', includedLanguages: 'de,es,fr,id,it,ja,ko,pt,ru,zh-CN',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

</body>

</html>