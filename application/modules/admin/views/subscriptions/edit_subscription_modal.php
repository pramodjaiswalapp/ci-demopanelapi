<!-- Modal -->
<div id="edit-subcribe-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <?php echo form_open('/admin/Subscriptions/edit', array("id" => "edit_subscription", "autocomplete" => "off")); ?>
            <input type="hidden" id="filterVal" value='<?php echo $csrfToken; ?>'>
            <input type="hidden" id="id_form" name = "id_form" value="">
            <div class="modal-header modal-alt-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title modal-heading">EDIT SUBSCRIPTION</h4>
            </div>
            <div class="modal-body">
                <div class="subscription-form">
                    <div class="row">
                        <div class="form-group clearfix">
                            <div class="col-lg-12">
                                <label class="admin-label">Subscription Name*</label>
                                <div class="input-holder">
                                    <input type="text" maxlength="25" name="title" onkeypress="return restrict_special_chars(event);" id="edit_title" value="" placeholder="Enter Title" class="valid">
                                    <span> <?php echo form_error("title"); ?></span>
                                    <span class="error_edit_name text-danger"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group clearfix">
                            <div class="col-lg-12">
                                <label class="admin-label">Price*</label>
                                <div class="input-holder price_sec">
                                    <span class="dollr">$</span>
                                    <input type="text" name="sub_price" onkeypress="return isNumber(event);" maxlength="5" value="" id="edit_sub_price" placeholder="Enter Price">
                                    <span> <?php echo form_error("sub_price"); ?></span>
                                    <span class="error_edit_price text-danger"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="edit_showtime">
                        <div class="form-group clearfix">
                            <div class="col-lg-12">
                                <label class="admin-label">Subscription Type</label>
                                <div class="form-group clearfix">
                                    <div class="custom_check">
                                        <input class="filled-in" type="checkbox" name ="one_time_option" id="edit_checkboxone1" name="check1" value = "1">
                                        <label class = "edit_subs_timespan" for="edit_checkboxone1"></label>
                                        <span>Daily</span>
                                    </div>
                                    <div class="custom_check">
                                        <input class="filled-in" type="checkbox" name ="one_time_option" id="edit_checkboxone2" name="check2" value = "2">
                                        <label class = "edit_subs_timespan" for="edit_checkboxone2"></label>
                                        <span>Weekly</span>
                                    </div>
                                    <div class="custom_check">
                                        <input class="filled-in" type="checkbox" name ="one_time_option" id="edit_checkboxone3" name="check3" value = "3">
                                        <label class = "edit_subs_timespan" for="edit_checkboxone3"></label>
                                        <span>Monthly</span>
                                    </div>
                                    <div class="custom_check">
                                        <input class="filled-in"  type="checkbox" name ="one_time_option" id="edit_checkboxone4" name="check4" value = "4">
                                        <label class = "edit_subs_timespan" for="edit_checkboxone4"></label>
                                        <span>Yearly</span>
                                    </div>
                                    <div class="custom_check">
                                        <input class="filled-in" type="checkbox" name ="one_time_option" id="edit_checkboxone5" name="check5" value = "5">
                                        <label class="add_subs_timespan" for="edit_checkboxone5"></label>
                                        <span>One-Time</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group clearfix">
                            <div class="col-lg-12">
                                <label class="admin-label">Description</label>
                                <div class="input-holder">
                                    <textarea name="description" id="edit_description" value="" placeholder="" maxlength="100" class="valid"></textarea>
                                    <span> <?php echo form_error("description"); ?></span>
                                    <span class="error_edit_description text-danger"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group clearfix">
                            <div class="col-lg-12">
                                <div class="button-wrap text-center">
                                    <button type="button" onclick="" data-dismiss="modal" class="commn-btn cancel">Cancel</button>
                                    <button type="button"  onclick="check_edit_subscription_form();" class="commn-btn edit_subscription save">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>

    </div>
</div>
