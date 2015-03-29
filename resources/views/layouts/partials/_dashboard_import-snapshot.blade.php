<div id="dsp-import-snapshot" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="dsp-import-snapshot-label" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 id="dsp-import-snapshot-label">Import a Snapshot</h3>
    </div>
    <form id="_dsp-snapshot-control" method="POST">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="control" value="import">
        <input type="hidden" name="_token" id="csrf-token" value="{{ csrf_token() }}" />
        <div class="modal-body">
            <p>Importing a prior snapshot replaces the entire database and local storage area for a DSP.</p>

            <p>Please Note:
                <strong><em>This is a destructive process</em></strong>.<br /> You will permanently lose any and all data associated (not including snapshots) with this DSP when you import a snapshot.
            </p>

            <label for="dsp-snapshot-list">Choose a snapshot to import:</label>
            <select id="dsp-snapshot-list" name="dsp-snapshot-list"></select>
        </div>
        <div class="modal-footer">
            <button class="btn btn-success" data-dismiss="modal" aria-hidden="true">Cancel</button>
            <button type="submit" class="btn btn-primary btn-danger">Import</button>
        </div>
    </form>
</div>
