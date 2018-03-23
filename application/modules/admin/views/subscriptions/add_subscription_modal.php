<!-- Modal -->
<div id="subcribe-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <?php echo form_open('/admin/Subscriptions/add', array("id" => "add_subscription", "autocomplete" => "off")); ?>
            <input type="hidden" id="filterVal" name="csrfToken" value='<?php echo $csrfToken; ?>'>
            <div class="modal-header modal-alt-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title modal-heading">SUBSCRIBE</h4>
            </div>
            <div class="modal-body">
                <div class="subscription-form">
                    <div class="row">
                        <div class="form-group clearfix">
                            <div class="col-lg-12">
                                <label class="admin-label">Subscription Name*</label>
                                <div class="input-holder">
                                    <input type="text" name="title" id="title" value="" onkeypress="return restrict_special_chars(event);" maxlength="25" placeholder="Enter Title" class="valid">
                                    <span> <?php echo form_error("title"); ?></span>
                                    <span class="error_name text-danger"></span>
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
                                    <input type="text" name="sub_price" onkeypress="return isNumber(event);" value="" id="sub_price" maxlength="5" placeholder="Enter Price">
                                    <span> <?php echo form_error("sub_price"); ?></span>
                                    <span class="error_price text-danger"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--                    <div class="row">
                                            <div class="form-group clearfix">
                                                <div class="col-lg-12">
                                                    <label class="admin-label">Subscription Type</label>
                                                    <div class="form-blk selector">-->
                    <!-- Select Picker -->
<!--                                    <select placeholder="Select" name="subscription_type" class="selectpicker form-control" id="purpose">
                        <option>Select</option>
                        <option value="1">Recurring Subscription</option>
                        <option value="2">One-Time Subscription</option>
                    </select>
                    <span class="error_type text-danger"></span>-->
                    <!-- //Select Picker -->
                    <!--                                </div>
                                                </div>
                                            </div>
                                        </div>-->
                    <!--                    <div class="row validity_box">
                                            <div class="form-group clearfix">
                                                <div class="col-lg-12">
                                                    <label class="admin-label">Subscription Type</label>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="form-blk selector">
                                                                 calendar
                                                                <input type="text"  data-provide="datepicker" placeholder="From" id="startDate" name="from_date" value="" class="form-control form-field">
                                                                <label class="ficon ficon-right" for="startDate"><i class="fa fa-calendar"></i></label>
                                                                 //calendar
                                                            </div>
                                                            <span class="error_dates text-danger"></span>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-blk selector">
                                                                 calendar
                                                                <input type="text" data-provide="datepicker" placeholder="To" id="endDate" name="to_date" value="" class="form-control form-field">
                                                                <label class="ficon ficon-right" for="endDate"><i class="fa fa-calendar"></i></label>
                                                                 //calendar
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>-->
                    <div class="row" id="showtime">
                        <div class="form-group clearfix">
                            <div class="col-lg-12">
                                <label class="admin-label">Subscription Type</label>
                                <div class="form-group clearfix">
                                    <div class="custom_check">
                                        <input class="filled-in" type="checkbox" name ="one_time_option" id="checkboxone1" name="check1" value = "1">
                                        <label class="add_subs_timespan" for="checkboxone1"></label>
                                        <span>Daily</span>
                                    </div>
                                    <div class="custom_check">
                                        <input class="filled-in" type="checkbox" name ="one_time_option" id="checkboxone2" name="check2" value = "2">
                                        <label class="add_subs_timespan" for="checkboxone2"></label>
                                        <span>Weekly</span>
                                    </div>
                                    <div class="custom_check">
                                        <input class="filled-in" type="checkbox" name ="one_time_option" id="checkboxone3" name="check3" value = "3">
                                        <label class="add_subs_timespan" for="checkboxone3"></label>
                                        <span>Monthly</span>
                                    </div>
                                    <div class="custom_check">
                                        <input class="filled-in" checked type="checkbox" name ="one_time_option" id="checkboxone4" name="check4" value = "4">
                                        <label class="add_subs_timespan" for="checkboxone4"></label>
                                        <span>Yearly</span>
                                    </div>
                                    <div class="custom_check">
                                        <input class="filled-in" type="checkbox" name ="one_time_option" id="checkboxone5" name="check5" value = "5">
                                        <label class="add_subs_timespan" for="checkboxone5"></label>
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
                                    <textarea name="description" id="" value="" placeholder="" maxlength="100" class="valid"></textarea>
                                    <span> <?php echo form_error("description"); ?></span>
                                    <span class="error_description text-danger"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group clearfix">
                            <div class="col-lg-12">
                                <div class="button-wrap text-center">
                                    <button type="button" data-dismiss="modal" onclick="" class="commn-btn cancel">Cancel</button>
                                    <button type="button"  onclick="check_subscription_form();" class="commn-btn save">Save</button>
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
