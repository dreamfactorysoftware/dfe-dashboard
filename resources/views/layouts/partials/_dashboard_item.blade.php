<div class="panel panel-{{ config('dashboard.panel-context','info') }} panel-dsp">
    <div class="panel-heading" role="tab" id="heading-{{ $groupId }}">
        <h4 class="panel-title">
            <span class="instance-heading-dsp-name">{{ $instanceName }}
                <span class="text-muted">{{ $defaultDomain }}</span></span>
            <span class="instance-heading-status pull-right"><i class="fa fa-fw {{ $statusIcon }} fa-2x"></i></span>
        </h4>
    </div>
    <div id="{{ $targetId }}"
        class="panel-collapse collapse in panel-body-open"
        role="tabpanel"
        aria-labelledby="heading-{{ $groupId }}">
        <div class="panel-body">{!! $targetContent !!}</div>
    </div>
</div>
