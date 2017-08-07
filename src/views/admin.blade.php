<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>@yield('title')</title>
    <!-- Favicon-->
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="/vendor/simple_orm/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="/vendor/simple_orm/plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="/vendor/simple_orm/plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Morris Chart Css-->
    <link href="/vendor/simple_orm/plugins/morrisjs/morris.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="/vendor/simple_orm/css/style.css" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="/vendor/simple_orm/css/themes/all-themes.css" rel="stylesheet" />
</head>

<body class="theme-red">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
    @include('simple_orm.header')
    <section>
        @include('simple_orm.sidebar')
        
    </section>

    @yield('content')

    <!-- Jquery Core Js -->
    <script src="/vendor/simple_orm/plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="/vendor/simple_orm/plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Select Plugin Js -->
    <script src="/vendor/simple_orm/plugins/bootstrap-select/js/bootstrap-select.js"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="/vendor/simple_orm/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="/vendor/simple_orm/plugins/node-waves/waves.js"></script>

    <!-- Jquery CountTo Plugin Js -->
    <script src="/vendor/simple_orm/plugins/jquery-countto/jquery.countTo.js"></script>

    <!-- Morris Plugin Js -->
    <script src="/vendor/simple_orm/plugins/raphael/raphael.min.js"></script>
    <script src="/vendor/simple_orm/plugins/morrisjs/morris.js"></script>

    <!-- ChartJs -->
    <script src="/vendor/simple_orm/plugins/chartjs/Chart.bundle.js"></script>

    <!-- Flot Charts Plugin Js -->
    <script src="/vendor/simple_orm/plugins/flot-charts/jquery.flot.js"></script>
    <script src="/vendor/simple_orm/plugins/flot-charts/jquery.flot.resize.js"></script>
    <script src="/vendor/simple_orm/plugins/flot-charts/jquery.flot.pie.js"></script>
    <script src="/vendor/simple_orm/plugins/flot-charts/jquery.flot.categories.js"></script>
    <script src="/vendor/simple_orm/plugins/flot-charts/jquery.flot.time.js"></script>

    <!-- Sparkline Chart Plugin Js -->
    <script src="/vendor/simple_orm/plugins/jquery-sparkline/jquery.sparkline.js"></script>

    <!-- Custom Js -->
    <script src="/vendor/simple_orm/js/admin.js"></script>
    <script src="/vendor/simple_orm/js/pages/index.js"></script>

    <!-- Demo Js -->
    <script src="/vendor/simple_orm/js/demo.js"></script>
</body>

</html>