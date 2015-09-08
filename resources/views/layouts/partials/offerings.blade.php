<div class="form-group">
    <label for="{{ $tag }}"
           class="col-md-2 control-label"
           style="white-space: nowrap; text-end-overflow: hidden;">{{ $displayName }}</label>

    <div class="col-md-6">
        <select id="{{ $tag }}" name="{{ $tag }}" class="form-control">{!! $options !!}</select>
    </div>
    {!! $helpBlock !!}
</div>
