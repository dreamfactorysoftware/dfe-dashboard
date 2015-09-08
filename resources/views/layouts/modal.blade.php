<div class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                @if($modalTitle)
                    <h4 class="modal-title">{{ $modalTitle }}</h4>
                @endif
            </div>
            <div class="modal-body">
                @if($modalBody)
                    {!! $modalBody !!}
                @endif
            </div>
            <div class="modal-footer">
                @if($modalClose)
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                @endif
                <button type="button" class="btn btn-primary">{{ $modalButtonText or 'Save' }}</button>
            </div>
        </div>
    </div>
</div>