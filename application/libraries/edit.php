<!-- Include Required Prerequisites -->
<script type="text/javascript" src="<?php echo base_url() ?>public/js/jquery.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />



<div class="inner-right-panel">



    <!--breadcrumb wrap-->
    <div class="breadcrumb-wrap">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url() ?>admin/notification">Notifications</a></li>
            <li class="breadcrumb-item active">Add Notification</li>
        </ol>
    </div>
    <!--breadcrumb wrap close-->



    <!--Filter Section -->
    <div class="white-wrapper">
        <div class="form-item-title clearfix">
            <h3 class="title">Add Notification</h3>
        </div>
        <!-- title and form upper action end-->
        <form method="post">
            <?php echo form_open('next_controller'); ?>
            <div class="form-ele-wrapper clearfix">

                <div class="row">
                    <div class="col-lg-4 col-sm-4">
                        <div class="form-profile-pic-wrapper">
                            <div class="profile-pic" id="profilePic" style="background-image:url(<?php echo (isset($editdata['admin_profile_pic']) && !empty($editdata['admin_profile_pic'])) ? base_url() . 'public/admin/' . $editdata['admin_profile_pic'] : '' ?>);">
                                <span href="javascript:void(0);" class="upimage-btn">
                                    <img src="public/images/camera.svg">
                                </span>
                                <input type="file" id="upload" accept="image/*" name="notificationImage">
                                <label id="image-error" class="alert-danger"></label>
                            </div>
                        </div>
                    </div>
                    <span class="loder-wrraper-single"></span>

                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label class="admin-label">Title</label>
                            <div class="input-holder">
                                <input type="text" name="title" name="title" id="title" placeholder="Notification title">
                                <span class="titleErr error"></span>
                            </div>

                        </div>
                    </div>

                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label class="admin-label">External Link</label>
                            <div class="input-holder">
                                <input type="text" name="link" id="link" placeholder="Enter link">
                            </div>

                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="admin-label">Message</label>
                            <div class="input-holder">
                                <textarea class="custom-textarea" maxlength="255" name="message" id="message-text"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="wrap-insection">
                <div class="section-title">
                    <h3>Select Users</h3>
                </div>

                <!--Filter Section -->

                <div class="fltr-srch-wrap clearfix">
                    <div class="row">
                        <div class="col-lg-4 col-sm-4">
                            <div class="display">
                                <select name="platform" class="form-control platform">
                                    <option value="">Select Platform</option>
                                    <option value="1">All</option>
                                    <option value="2">Android</option>
                                    <option value="3">iOS</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-4">
                            <div class="display">
                                <input type="text" id="regDate" class="regDate" name="regDate" placeholder="Select Date Range">
                            </div>

                        </div>
                        <div class="col-lg-4 col-sm-4">
                            <div class="display">
                                <select name="gender" class="form-control gender">
                                    <option value="">Select Gender</option>
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Filter Section Close-->
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <input type="submit" onclick="return checkNotiValidation()" class="btn btn-success pull-right sendNoti" value="Send Now">
                    </div>
                </div>
        </form>
        <!--Filter Wrapper-->
        <div class="filter-wrap ">
            <div class="filter_hd clearfix">
                <div class="pull-left">
                    <h2 class="fltr-heading">Filter </h2>
                </div>
                <div class="pull-right">
                    <span class="close flt_cl" data-dismiss="modal">X</span>
                </div>
            </div>
            <div class="inner-filter-wrap">
                <div class="fltr-field-wrap">
                    <label class="admin-label">Status</label>
                    <div class="commn-select-wrap">
                        <select class="selectpicker">
                            <option>as</option>
                            <option>asads</option>
                            <option>dsadasdaas</option>
                        </select>
                    </div>

                </div>
                <div class="fltr-field-wrap">
                    <label class="admin-label">Registration Date</label>
                    <div class="inputfield-wrap">
                        <input type="text" name="" value="" class="form-date_wrap" id="datepicker_1" placeholder="From">
                        <input type="text" name="" value="" class="form-date_wrap" id="datepicker_2" placeholder="To">
                    </div>
                </div>
                <div class="fltr-field-wrap">
                    <label class="admin-label">Country</label>
                    <div class="commn-select-wrap">
                        <select class="selectpicker">
                            <option>Select Country</option>
                            <option>asads</option>
                            <option>dsadasdaas</option>
                        </select>
                    </div>
                </div>
                <div class="fltr-field-wrap">
                    <label class="admin-label">State</label>
                    <div class="commn-select-wrap">
                        <select class="selectpicker">
                            <option>Select State</option>
                            <option>asads</option>
                            <option>dsadasdaas</option>
                        </select>
                    </div>
                </div>
                <div class="fltr-field-wrap">
                    <label class="admin-label">City</label>
                    <div class="commn-select-wrap">
                        <select class="selectpicker">
                            <option>Select City</option>
                            <option>asads</option>
                            <option>dsadasdaas</option>
                        </select>
                    </div>
                </div>
                <div class="fltr-field-wrap">
                    <label class="admin-label">Platform</label>
                    <div class="commn-select-wrap">
                        <select class="selectpicker">
                            <option>Select Platform</option>
                            <option>asads</option>
                            <option>dsadasdaas</option>
                        </select>
                    </div>
                </div>

                <div class="button-wrap text-center">
                    <button type="Submit" class="commn-btn cancel">Reset</button>
                    <button type="reset" class="commn-btn save">Filter</button>
                </div>
            </div>
        </div>
        <!--Filter Wrapper Close-->
    </div>
</div>
</div>
<script src="<?php echo base_url() ?>public/js/validation.js"></script>
<script>
                            $(document).ready(function () {
                                $('#regDate').daterangepicker(
                                        {
                                            locale: {
                                                format: 'DD/MM/YYYY'
                                            },
                                            autoUpdateInput: false
                                        }
                                );
                            })

</script>