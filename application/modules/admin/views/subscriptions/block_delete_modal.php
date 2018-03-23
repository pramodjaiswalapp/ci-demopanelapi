<div class="modal fade modelbody bd-example-modal-sm" id="Deletemodal" role="dialog">
    <div class="modal-dialog width" role="document">
        <div class="modal-content">
            <div class="modal-body">
                Are you sure you want to delete selected subscription ?
            </div>
            <div class="modal-footer">
                <div class="col-lg-6 col-sm-6">

                    <button type="button" class="custom-btn cancel" data-dismiss="modal" onclick="window.location.href = '/subscriptions/'">Cancel</button>
                </div>
                <div class="col-lg-6 col-sm-6">

                    <input type="hidden" id="hiddenuser" value="" />
                    <button type="button" class="custom-btn save delete-subscriptions">Yes</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modelbody bd-example-modal-sm" id="Blockmodal" role="dialog">
    <div class="modal-dialog width" role="document">
        <div class="modal-content">
            <div class="modal-body">
                Are you sure you want to block selected subscription ?
            </div>
            <div class="modal-footer">
                <div class="col-lg-6 col-sm-6">
                    <button type="button" class="custom-btn cancel" data-dismiss="modal">Cancel</button>
                </div>
                <div class="col-lg-6 col-sm-6">
                    <input type="hidden" id="blockuserid" value="" />
                    <input type="hidden" id="blockuserstatus" value="" />
                    <button type="button" class="custom-btn save block-admin">Yes</button>
                </div>
            </div>
        </div>
    </div>
</div>