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
            <li class="breadcrumb-item active">Edit Notification</li>
        </ol>
    </div>
    <!--breadcrumb wrap close-->
    <!--Filter Section -->
    <div class="">
        <div class="form-item-title clearfix">
            <h3 class="title">Add Notification</h3>
        </div>
        <!-- title and form upper action end-->
        <?php echo form_open_multipart(); ?>
        <div class="white-wrapper clearfix">

            <div class="row">
                <div class="col-lg-3 col-sm-5 col-xs-12">
                    <div class="image-view-wrapper img-view150p img-viewbdr-radius4p">
                        <div class="image-view img-view150" id="profilePic" style="background-image:url(<?php echo $detail['image']; ?>)">
                            <span href="javascript:void(0);" class="upimage-btn"></span>
                            <input type="file" id="upload" style="display:none;" accept="image/*" name="notificationImage">
                            <label class="camera" for="upload"><i class="fa fa-camera" aria-hidden="true"></i></label>
                            <label id="image-error" class="alert-danger"></label>
                        </div>
                    </div>
                </div>
                <input type="hidden" value="<?php echo $detail['id'] ?>" name="notiId">
                <input type="hidden" value="<?php echo $detail['image'] ?>" name="old_img">
                <span class="loder-wrraper-single"></span>

                <div class="col-lg-9 col-sm-7 col-xs-12">
                    <div class="row">
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
                                    <textarea class="custom-textarea" style="resize:none;"  maxlength="255" name="message" id="message-text"><?php echo $detail['message'] ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-4">
                            <div class="form-group display">
                                <label class="admin-label">Platform</label>
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
                            <div class="form-group display showcalendar-up">
                                <label class="admin-label">Date Range</label>
                                <input type="text" id="regDate" class="regDate" name="regDate" placeholder="Select Date Range" value="<?php echo $detail['date_range'] ?>">
                            </div>

                        </div>
                        <div class="col-lg-4 col-sm-4">
                            <div class="form-group display">
                                <label class="admin-label">Gender</label>
                                <select name="gender" class="form-control gender">
                                    <option value="">Select Gender</option>
                                    <option <?php echo ($detail['gender'] == '3') ? 'Selected' : '' ?> value="3">All</option>
                                    <option <?php echo ($detail['gender'] == '1') ? 'Selected' : '' ?> value="1">Male</option>
                                    <option <?php echo ($detail['gender'] == '2') ? 'Selected' : '' ?> value="2">Female</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="clearfix"></div>

                <div class="col-lg-12">
                    <div class="form-ele-action-bottom-wrap btns-center clearfix">
                        <div class="button-wrap text-center">
                            <input type="button" onclick="history.go( -1 )" class="commn-btn cancel" value="Cancel">
                            <input type="submit" onclick="return checkNotiValidation()" class="commn-btn save" value="Send Now">
                        </div>
                    </div>
                </div>

                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>

<script>
    $( document ).ready( function () {
        $( '#regDate' ).daterangepicker(
                {
                    locale: {
                        format: 'DD/MM/YYYY'
                    },
                    autoApply: true
                }
        );
<?php if ( empty( $detail['date_range'] ) ) { ?>
             $( '#regDate' ).val( '' );
 <?php } ?>
    } )


    $( document ).ready( function () {

        $( '#upload' ).change( function () {
            var file = this.files[0];
            var reader = new FileReader();
            reader.onloadend = function () {
                $( '#profilePic' ).css( 'background-image', 'url("' + reader.result + '")' );
            }
            if ( file ) {
                reader.readAsDataURL( file );
            }
            else {
                console.log( 'not done' );
            }
        } );

        $( '#regDate' ).daterangepicker(
                {
                    locale: {
                        format: 'DD/MM/YYYY'
                    },
                    autoApply: true,
                    drops: 'up'
                }
        );
        $( '#regDate' ).val( '' );
    } )

</script>