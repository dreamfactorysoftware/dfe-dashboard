<div class="panel panel-success panel-dsp">
	<div class="panel-heading" role="tab" id="heading-{{ $groupId }}">
		<h4 class="panel-title">
			<a data-toggle="collapse"
			   data-parent="#{{ $groupId }}"
			   href="#{{ $targetId }}"
			   aria-expanded="true"
			   aria-controls="collapseOne">{!! $triggerContent !!}</a>
		</h4>
	</div>
	<div id="{{ $targetId }}"
		 class="panel-collapse collapse {{ $opened ? 'in panel-body-open' : null }}"
		 role="tabpanel"
		 aria-labelledby="heading-{{ $groupId }}">
		<div class="panel-body">{!! $targetContent !!}</div>
	</div>
</div>
