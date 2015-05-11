<html ng-app="APP">
	<head>
		<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>
			@if ($__env->yieldContent('title'))
				{{ Config::get('acme.title') }}: @yield('title')
			@elseif ($__env->yieldContent('heading1'))
				{{ Config::get('acme.title') }}: @yield('heading1')
			@else
				{{ Config::get('acme.title') }}
			@endif
		</title>
        
		<link rel="stylesheet" href="/css/bootstrap/bootstrap.min.css">
        <link rel="stylesheet" href="/css/base.min.css">  

    	@yield('styles')

		@section('header-scripts')

		<base href="/">

		@show
	</head>
    <body ng-cloak>

        @if (Auth::check())
            @include('compositions.main_navigation')
        @endif

        @yield('logo')

        @if ($__env->yieldContent('heading1'))
        	<div class="section-header">
              <div class="container">
                <div class="row">

                  <div class="col-lg-12">
                    <h1 class="page-header">
                    	@yield('heading1')
                    </h1>
                  </div>

                </div>
              </div> <!-- /.container -->
            </div> <!-- /.section-colored -->
        @endif

        @section('sidebar')
            
        @show



        <div class="container">
            @include('layouts.flash')

            @yield('content')
        </div>

        
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <!--
        <script src="/js/jquery/bootstrap-dropdown.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
        -->

        <script src="/js/ng/angular.min.js"></script>
        <!-- <script src="/js/base.min.js"></script> -->
        @yield('footer-scripts')

        <script type="text/javascript">
            // dummy app to appease ng for now
            if (!APP) {
                var APP = angular.module('APP', []);
            }
        </script>
    </body>
</html>
