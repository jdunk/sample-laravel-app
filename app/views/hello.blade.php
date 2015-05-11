@extends('layouts.splash')

@section('meta')
	<meta property="og:title" content="Acme" />
	<meta property="og:description"
      content="Acme is a community-based platform connecting up-and-coming fashion designers with their customers, fans, and friends." />
	<meta property="og:image" content="http://s3-us-west-1.amazonaws.com/acme-dev/media/elements/bradsritter-bloom-social.jpg" />
    <meta property="og:image:secure_url" content="https://s3-us-west-1.amazonaws.com/acme-dev/media/elements/bradsritter-bloom-social.jpg" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="800" />
@stop

@section('styles')
  {{ HTML::style('/css/splash.min.css') }}
@stop

@section('footer-scripts')
	<!-- {{ HTML::script('/js/ng/ui-bootstrap.min.js') }} -->
	{{ HTML::script('/js/ng/coming_soon/build.js') }}
@stop

@section('title')
   	Coming Soon.
@stop

@section('content')

<div class="ng-app-container">
	<div class="ui-view-transition" id="ui-view" ui-view></div>
</div>

<!--
	<div class="row mtl mbm animated fadeIn showdelay1s">
		<div class="col-sm-offset-3 col-sm-6">
			<div class="splash-logo">
				<img src="https://s3-us-west-1.amazonaws.com/acme-dev/media/elements/acme-logo.svg" alt="Acme Logo">
			</div>
		</div>
	</div>

	<div class="row animated bounceInDown showdelay2s">
		<div class="col-sm-offset-3 col-sm-6">
			<div class="alert alert-dismissable alert-info mbn">
				<button data-dismiss="alert" class="close" type="button">×</button>
				<strong>Sign up</strong> to get a notification when we launch:
			</div>


			<form class="subscription-form" id="subscription-form" novalidate>
				<div class="form-group">
				  <div class="input-group">
					<input type="text" class="form-control input-lg" placeholder="Enter E-Mail Address" autofocus formnovalidate="">
					<span class="input-group-btn">
					  <button type="button" class="btn btn-default input-lg">GO</button>
					</span>
				  </div>
				</div>
			</form>

		</div>
	</div>
-->

@stop
