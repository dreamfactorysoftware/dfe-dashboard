<div class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{ $modalTitle or '' }}</h4>
            </div>
            <div class="modal-body">
                    {!! $modalBody or '' !!}
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-close" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success">{{ $modalButtonText or 'Save' }}</button>
            </div>
        </div>
    </div>
</div>