<!-- Include Required Prerequisites -->
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
            <?php echo form_open_multipart(); ?>
            <div class="form-ele-wrapper clearfix">

                <div class="row">
                    <div class="col-lg-4 col-sm-4">
                        <div class="form-profile-pic-wrapper">
                            <div class="profile-pic" id="profilePic" style="background-image:url(<?php echo (isset($editdata['admin_profile_pic']) && !empty($editdata['admin_profile_pic'])) ? base_url() . 'public/admin/' . $editdata['admin_profile_pic'] : '' ?>);">
                                <span href="javascript:void(0);" class="upimage-btn">

                                </span>
                                <input type="file" id="upload" style="display:none;" accept="image/*" name="notificationImage">
                                <label class="camera" for="upload"><i class="fa fa-camera" aria-hidden="true"></i></label>

                                <label id="image-error" class="alert-danger"></label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" value="<?php echo $detail['id'] ?>" name="notiId">
                    <span class="loder-wrraper-single"></span>

                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label class="admin-label">Title</label>
                            <div class="input-holder">
                                <input type="text" name="title" name="title" value="<?php echo $detail['title'] ?>" id="title" placeholder="Notification title">
                                <span class="titleErr error"></span>
                            </div>

                        </div>
                    </div>

                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label class="admin-label">External Link</label>
                            <div class="input-holder">
                                <input type="text" name="link" value="<?php echo $detail['link'] ?>" id="link" placeholder="Enter link">
                            </div>

                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="admin-label">Message</label>
                            <div class="input-holder">
                                <textarea class="custom-textarea" style="resize:none;" value="<?php echo $detail['message'] ?>" maxlength="255" name="message" id="message-text"></textarea>
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
                                    <option <?php echo ($detail['platform'] == '1') ? 'Selected' : '' ?> value="1">All</option>
                                    <option <?php echo ($detail['platform'] == '2') ? 'Selected' : '' ?> value="2">Android</option>
                                    <option <?php echo ($detail['platform'] == '3') ? 'Selected' : '' ?> value="3">iOS</option>
                                </select>
                                <span class="platformErr error"></span>
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
                                    <option <?php echo ($detail['gender'] == '1') ? 'Selected' : '' ?> value="1">Male</option>
                                    <option <?php echo ($detail['gender'] == '2') ? 'Selected' : '' ?> value="2">Female</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Filter Section Close-->
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <input type="submit" onclick="return checkNotiValidation()" class="commn-btn" value="Send Now">
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
    </div>
</div>

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