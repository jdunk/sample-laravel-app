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

        <base href="/">
        
		<link rel="stylesheet" href="/css/bootstrap/bootstrap.min.css">
		<link rel="stylesheet" href="/css/animate.css">
        <link rel="stylesheet" href="/css/base.min.css">

        <link rel="stylesheet" href="//s3-us-west-1.amazonaws.com/acme/assets/fonts/alternate-gothic/stylesheet.css">
        <link rel="stylesheet" href="//s3-us-west-1.amazonaws.com/acme/assets/fonts/foro-light/stylesheet.css">

        <link rel="icon" href="https://s3-us-west-1.amazonaws.com/acme/assets/elements/favicon.png">

    	@yield('styles')

		@section('header-scripts')

		@show
	</head>
    <body ng-cloak>

        @if (Auth::check())
            @include('compositions.main_navigation')
        @endif

		<div class="container header">
			 <div class="row mtl mbm ">
				<div class="col-md-3">
					<div class="logo" style="background-color: rgba(255,255,255,0.3)">
						<img src="https://s3-us-west-1.amazonaws.com/acme/assets/elements/acme-logo-dark.png" alt="Acme Logo">
					</div>
				</div>
			</div>
		</div>

        @if ($__env->yieldContent('heading1'))
        	<div class="section-header">
              <div class="container">
                <div class="row">

                  <div class="col-md-6">
                    <h1 class="header">
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

			<div class="row">
            	<div class="col-md-6">
            		@include('layouts.flash')
            		@yield('content')
            	</div>
            </div>

        </div>

        
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>


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
