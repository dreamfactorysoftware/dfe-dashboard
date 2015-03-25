<div class="panel panel-default panel-dsp">
	<div class="panel-heading">
		<h4 class="panel-title">
			<a class="accordion-toggle"
			   data-toggle="collapse"
			   data-parent="#{{ $groupId }}"
			   href="#{{ $targetId }}">
				{{ $triggerContent }}
			</a>
		</h4>
	</div>
	<div id="{{ $targetId }}" class="panel-collapse collapse {{ $opened ? 'panel-body-open in' : null }}">
		<div class="panel-body">
			{{ $targetContent; }}
		</div>
	</div>
</div>
