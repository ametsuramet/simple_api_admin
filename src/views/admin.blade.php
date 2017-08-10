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
    <link href="/vendor/simple_admin_api/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="/vendor/simple_admin_api/plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="/vendor/simple_admin_api/plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Morris Chart Css-->
    <link href="/vendor/simple_admin_api/plugins/morrisjs/morris.css" rel="stylesheet" />
    
    <!-- Bootstrap Select Css -->
    <link href="/vendor/simple_admin_api/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
    
    <!-- Custom Css -->
    <link href="/vendor/simple_admin_api/css/style.css" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="/vendor/simple_admin_api/css/themes/all-themes.css" rel="stylesheet" />
    @yield('style')
    @include('simple_admin_api.style')
    
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
    @include('simple_admin_api.header')
    <section>
        @include('simple_admin_api.sidebar')
        
    </section>

    @yield('content')

    <!-- Jquery Core Js -->
    <script src="/vendor/simple_admin_api/plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="/vendor/simple_admin_api/plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Select Plugin Js -->
    <script src="/vendor/simple_admin_api/plugins/bootstrap-select/js/bootstrap-select.js"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="/vendor/simple_admin_api/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="/vendor/simple_admin_api/plugins/node-waves/waves.js"></script>

    <!-- Jquery CountTo Plugin Js -->
    <script src="/vendor/simple_admin_api/plugins/jquery-countto/jquery.countTo.js"></script>

    <!-- Morris Plugin Js -->
    <script src="/vendor/simple_admin_api/plugins/raphael/raphael.min.js"></script>
    <script src="/vendor/simple_admin_api/plugins/morrisjs/morris.js"></script>

    <!-- ChartJs -->
    <script src="/vendor/simple_admin_api/plugins/chartjs/Chart.bundle.js"></script>

    

    <!-- Sparkline Chart Plugin Js -->
    <script src="/vendor/simple_admin_api/plugins/jquery-sparkline/jquery.sparkline.js"></script>
    
    <!-- Ckeditor -->
    <script src="/vendor/simple_admin_api/plugins/ckeditor/ckeditor.js"></script>

    <!-- TinyMCE -->
    <script src="/vendor/simple_admin_api/plugins/tinymce/tinymce.js"></script>

    <!-- Custom Js -->
    <script src="/vendor/simple_admin_api/js/admin.js"></script>
    <script type="text/javascript">
        $(function () {

    
            var ckeditor = document.getElementById('ckeditor');
            if (ckeditor) {
                //CKEditor
                CKEDITOR.replace('ckeditor');
                CKEDITOR.config.height = 300;
            }

            //TinyMCE
            tinymce.init({
                selector: "textarea#tinymce",
                theme: "modern",
                height: 300,
                plugins: [
                    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                    'insertdatetime media nonbreaking save table contextmenu directionality',
                    'emoticons template paste textcolor colorpicker textpattern imagetools'
                ],
                toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                toolbar2: 'print preview media | forecolor backcolor emoticons',
                image_advtab: true
            });
            tinymce.suffix = ".min";
            tinyMCE.baseURL = '/vendor/simple_admin_api/plugins/tinymce';
        });
    </script>

    @yield('script')
    @include('simple_admin_api.script')
    
    <!-- Demo Js -->
    <script src="/vendor/simple_admin_api/js/demo.js"></script>

</body>

</html>