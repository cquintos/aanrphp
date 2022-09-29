<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-T5X459N98J"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-T5X459N98J');
    </script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-T776QSC');
            </script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | KM4AANR</title>

    <script type="text/javascript"> (function() { var css = document.createElement('link'); css.href = 'https://use.fontawesome.com/releases/v5.1.0/css/all.css'; css.rel = 'stylesheet'; css.type = 'text/css'; document.getElementsByTagName('head')[0].appendChild(css); })(); </script>
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/dropzone.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- <link href="{{ asset('css/lightbox.css') }}" rel="stylesheet"> -->

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/storage/page_images/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="/storage/page_images/favicon-16x16.png" sizes="16x16" />

    <script src="{{ asset('js/lightbox.js') }}" defer></script>
    <script async charset="utf-8" src="//cdn.embedly.com/widgets/platform.js"></script>
    
    <script src="{{ asset('js/app.js') }}" ></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/dropzone.js') }}"></script>


    <!-- no-ui-slider -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.5.0/nouislider.min.css" integrity="sha512-qveKnGrvOChbSzAdtSs8p69eoLegyh+1hwOMbmpCViIwj7rn4oJjdmMvWOuyQlTOZgTlZA0N2PXA7iA8/2TUYA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.5.0/nouislider.min.js" integrity="sha512-ZKqmaRVpwWCw7S7mEjC89jDdWRD/oMS0mlfH96mO0u3wrPYoN+lXmqvyptH4P9mY6zkoPTSy5U2SwKVXRY5tYQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/wnumb/1.0.4/wNumb.min.js'></script>
    
    <!-- x-editable -->
    <link href="{{ asset('css/bootstrap-editable.css') }}" rel="stylesheet">
    <script src="{{ asset('js/bootstrap-editable.js') }}"></script>

    <script src="{{ asset('js/popper.min.js') }}"></script>

    <!-- chartjs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- bootstrap toggle -->
    <link href="{{ asset('css/bootstrap4-toggle.min.css') }}" rel="stylesheet">  
    <script src="{{ asset('js/bootstrap4-toggle.min.js') }}"></script>

    <!--Select2 -->
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
    
    <!-- bootstrap select CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">

    <!-- bootstrap select JavaScript -->
    <script src="{{ asset('js/bootstrap-select.min.js') }}"></script>

    <!-- Datatables -->
    <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" defer></script> 
    <script src="https://cdn.ckeditor.com/ckeditor5/28.0.0/classic/ckeditor.js"></script>

    @yield('top_scripts')
   
    <script>
    $(document).ready(function() {
        // show the alert
        setTimeout(function() {
            $(".alert").alert('close');
        }, 5000);
    });
    </script>

</head>
<body style="background-color:white">
    <div id="app">
        @unless(request()->is('dashboard/*') || request()->is('analytics/*'))
        <div class="icon-bar hide-when-mobile" style="z-index:500">
            <?php $sticky = App\SocialMediaSticky::all()?>
            <a target="_blank" href="{{$sticky->where('name', '=', 'PCAARRD')->first()->link}}" class="sarai"><img src="/storage/page_images/TRr6O4s.png" height="30" width="30"></a> 
            <a target="_blank" data-toggle="tooltip" title="Visit our Facebook"href="{{$sticky->where('name', '=', 'Facebook')->first()->link}}" class="facebook"><i class="fab fa-facebook"></i></a> 
            <a target="_blank" data-toggle="tooltip" title="Visit our Twitter" href="{{$sticky->where('name', '=', 'Twitter')->first()->link}}" class="twitter"><i class="fab fa-twitter"></i></a> 
            <a target="_blank" data-toggle="tooltip" title="Visit our Instagram" href="{{$sticky->where('name', '=', 'Instagram')->first()->link}}" class="instagram"><i class="fab fa-instagram"></i></a> 
            <a target="_blank" data-toggle="tooltip" title="Send us an email" href="mailto:{{$sticky->where('name', '=', 'Email')->first()->link}}" class="email"><i class="fas fa-envelope"></i></a>
            <a target="_blank" data-toggle="tooltip" title="Visit our YouTube" href="{{$sticky->where('name', '=', 'YouTube')->first()->link}}" class="youtube"><i class="fab fa-youtube"></i></a> 
            <a target="_blank" data-toggle="popover" data-trigger="focus" data-placement="right" data-content="Please help us make this website better" title="Click to see feedback form" href="{{$sticky->where('name', '=', 'Survey Form')->first()->link}}" class="feedback"><i class="far fa-comment-dots"></i></a>
        </div>
        @endunless
        <section class="sticky-top">
            @include('layouts.navbar')
        </section>
        @yield('content')
        @yield('side_icon_bar')
        @include('layouts.footer')
       
    </div>
    <style>
        @media only screen and (max-width: 600px) {
            .hide-when-mobile {
                display:none !important
            }
        }
    </style>
</body>
    @yield('scripts')
</html>
