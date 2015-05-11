@foreach (['success', 'info', 'warning', 'danger'] as $flashKey)
    @if ($flash = Session::get($flashKey))
    	<div class="alert alert-{{{ $flashKey }}}">{{{ $flash }}}</div>
    	<?php Session::flush($flashKey); ?>
    @endif
@endforeach
