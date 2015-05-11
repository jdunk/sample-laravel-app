<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div style="margin: 0 0 20px 0; height: 90px; width: 100%;">
			<img alt="Acme Logo" style="width:100%; height:auto;" src="https://s3-us-west-1.amazonaws.com/acme/assets/elements/acme-logo-dark.png">
		</div>

		@yield('content')

		<p>
			<b>{{ Config::get('acme.title') }}</b>
			<i>Make Style.</i><br>
			<a href="https://acme.com/">https://acme.com/</a>.
		</p>
	</body>
</html>
