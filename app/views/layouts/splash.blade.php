<html ng-app="APP">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		@yield('meta')

		<title>
			@if ($__env->yieldContent('title'))
				{{ Config::get('acme.title') }}: @yield('title')
			@elseif ($__env->yieldContent('heading1'))
				{{ Config::get('acme.title') }}: @yield('heading1')
			@else
				{{ Config::get('acme.title') }}
			@endif
		</title>

		<base href="/">

		<link rel="stylesheet" href="/css/bootstrap/bootstrap.min.css">
		<link rel="stylesheet" href="/css/base.min.css">
		<link rel="stylesheet" href="/css/animate.css">

		<link rel="stylesheet" href="https://s3-us-west-1.amazonaws.com/acme/assets/fonts/acme-social-icons/css/acme-social-icons.css">
		<link rel="stylesheet" href="//s3-us-west-1.amazonaws.com/acme/assets/fonts/alternate-gothic/stylesheet.css">
		<link rel="stylesheet" href="//s3-us-west-1.amazonaws.com/acme/assets/fonts/foro-light/stylesheet.css">

		<link rel="icon" href="https://s3-us-west-1.amazonaws.com/acme/assets/elements/favicon.png">

		<base href="/">

    	@yield('styles')

		@section('header-scripts')

		@show
	</head>
    <body class="splash" ng-cloak>

    	<div class="fs-bg fs-bg-splash animated fadeIn showdelay2s"></div>

        <div class="container-fluid">
            @include('layouts.flash')

            @yield('content')
        </div>

        <footer class="footer animated slideInUp showdelay2s">
		  <div style="text-align: center;">
			<ul class="social-links">
				<li><a href="http://instagram.com/acme" alt="Acme on Instagram"><span class="icon-instagram"></span> Instagram</a></li>
                <li><a href="http://acme.tumblr.com" alt="Acme on Tumblr"><span class="icon-tumblr"></span> Tumblr</a></li>
                <li><a href="http://twitter.com/Acmestyle" alt="Acme on Twitter"><span class="icon-twitter"></span> Twitter</a></li>
               	<li><a href="http://facebook.com/acme" alt="Acme on Facebook"><span class="icon-facebook"></span> Facebook</a></li>
				<li><a href="http://pinterest.com/acme" alt="Acme on Pinterest"><span class="icon-pinterest"></span> Pinterest</a></li>

			</ul>
		  </div>
		</footer>

        <!--
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="/js/jquery/bootstrap-dropdown.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
        -->

        <script src="/js/ng/angular.min.js"></script>

        @yield('footer-scripts')

        <script type="text/javascript">
            // dummy app to appease ng for now
            if (!APP) {
                var APP = angular.module('APP', []);
            }
        </script>

		@if (App::environment() === 'production')
			<script type="text/javascript">
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			  ga('create', 'UA-56797258-1', 'auto');
			  ga('send', 'pageview');
			</script>
		@else
			<!-- google analytics disabled outside of production environment -->
		@endif

    </body>
</html>
