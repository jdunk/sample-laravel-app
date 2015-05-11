@extends('layouts.minimal')

@section('styles')

@stop

@section('title')
   	{{ ucfirst($mode) }}
@stop

@section('footer-scripts')
	{{ HTML::script('/js/ng/guest.min.js') }}
	{{ HTML::script('/js/ng/common/show-errors.js') }}
	{{ HTML::script('/js/ng/common/alerts.js') }}
	{{ HTML::script('/js/ng/common/focus.js') }}

	<script type="text/javascript">
		(function() {
			APP = angular.module('APP', [
				'nd.guest.authenticate'
			])
			.controller('AuthenticateController', ['$scope', function($scope) {

            }])
            .config(['$httpProvider', function($httpProvider) {
            	$httpProvider.defaults.headers.common = {
					'X-Requested-With': 'XMLHttpRequest',
					'X-testing-header': 'test'
				};
            }]);
		})();
	</script>
@stop

@section('heading1')

@stop

@section('content')

	<!--<div class="fs-bg fs-bg-auth animated fadeIn showdelay2s"></div>-->

	<div ng-controller="AuthenticateController">
		<div nd-authenticate="" nd-authenticate-mode="{{ $mode }}"></div>
	</div>

@stop
