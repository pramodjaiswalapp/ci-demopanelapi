<style>
    .myloader{
        width: 16%;
        position: absolute;
        margin-top: -29px;
        /*display: none;*/
    }
    .avatar-wrapper {
        height: 330px;
        min-height: 400px;
        width: 100%;
        margin-top: 20px;
        box-shadow: inset 0 0 5px rgba(0,0,0,.25);
        background-color: #fcfcfc;
        overflow: hidden;
    }

    .avatar-form .modal-header {
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
    }

    .avatar-form .modal-header {
        background: #2c2c2c;
    }

    .avatar-form .modal-header .img_up_hd h1 {
        font-size: 15px;
        color: #fff;
        float: left;
    }

    .avatar-chooseimg-wrapper{
        position: relative;
        padding: 10px 14px;
        font-size: 13px;
        color: #000;
        display: inline-block;
        float: left;
        overflow: hidden;
        -webkit-box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14), 0 1px 5px 0 rgba(0,0,0,0.12), 0 3px 1px -2px rgba(0,0,0,0.2) !important;
        box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14), 0 1px 5px 0 rgba(0,0,0,0.12), 0 3px 1px -2px rgba(0,0,0,0.2) !important;
    }

    .avatar-chooseimg-wrapper input{
        display: block;
        position: absolute;
        top: 0;
        right: 0;
        cursor: pointer;
        opacity: 0;
        height: 100%;
    }

    .avatar-btns,
    .avatar-zooms {
        float: left;
        margin: 10px 3px 0 0;
    }

</style>

<link href="<?php echo base_url(); ?>public/cropper/cropper.min.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>public/cropper/cropper.js"></script>
<script src="<?php echo base_url(); ?>public/cropper/cropper.min.js"></script>
<script src="<?php echo base_url(); ?>public/cropper/main.js"></script>
<script>
    function addCoverImage() {
        callme( 'avatar_src', '1024', '360', 'imagepicker2', 'addshopbtn', 'imageMe1', 'true', '', 'circular' );
    }

    var baseUrl = '<?php echo base_url() ?>';
    if ( location.hostname == "localhost" ) {
        var domain = location.protocol + '//' + location.hostname + (location.port ? ':' + location.port : '') + '/AdminPanel/admin';
        var domain2 = location.protocol + '//' + location.hostname + (location.port ? ':' + location.port : '') + '/AdminPanel';
    }
    else {
        var domain = location.protocol + '//' + location.hostname + (location.port ? ':' + location.port : '') + '/admin';
        var domain2 = location.protocol + '//' + location.hostname + (location.port ? ':' + location.port : '');
    }

    var current_directory = "C:/xampp/htdocs/AdminPanel";
</script>
<!-- Large modal -->
<div class="inner-right-panel">
    <!--breadcrumb wrap-->
    <div class="breadcrumb-wrap">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Image Cropper </li>
        </ol>
    </div>
    <!-- Breadcrumb End-->

    <!-- Image Display and Selection -->
    <div class="form-item-wrap">
        <div class="form-item-title clearfix">
            <h3 class="title">Admin Edit Profile</h3>
        </div>
        <div class="white-wrapper clearfix">
            <div class="row">
                <div class="col-lg-3 col-sm-5">
                    <div class="image-view-wrapper img-view150p img-viewbdr-radius4p">
                        <div class="image-view img-view150 profile-pic" id="profile-pic" style="background-image:url('<?php echo DEFAULT_IMAGE; ?>');">
                            <div class="image_upload_trigger" onclick="addCoverImage()">
                                <input type="hidden" name="imgurl" class="inputhidden">
                                <input type="hidden" id="imgChange" name="imgChange" value="">
                                <label class="camera" for="upload"><i class="fa fa-camera" aria-hidden="true"></i></label>
                            </div>
                        </div>
                    </div>
                    <span class="loder-wrraper-single"></span>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="form-group">
                        <label class="admin-label">Cropped Image Path</label>
                        <div class="input-holder">
                            <input type="text" id="image_path" name="image_path" readonly="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Image Display and Selection End-->
</div>


<!--*******************cropper modal************************-->
<div class="modal fade" id="avatar-modal" aria-hidden="true" aria-labelledby="avatar-modal-label" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="avatar-form" id="my-cropper" action="<?php echo base_url(); ?>public/cropper/crop.php" enctype="multipart/form-data" method="post">

                <div class="modal-header" style="border-bottom:none !important; min-height:0px;">
                    <div class="img_up_hd"><h1>Upload Image</h1></div>
                    <div class="close_wrapper">
                        <button class="close" data-dismiss="modal" type="button">&times;</button>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="up_img_wrap">
                        <div class="reatiler_box insidegrey_bg">

                            <!-- Upload image and data -->
                            <div class="avatar-upload text-center clearfix">
                                <input class="avatar-src" name="avatar_src" type="hidden">
                                <input class="avatar-data" name="avatar_data" type="hidden">
                                <div class="avatar-chooseimg-wrapper">
                                    <label for="avatarInput">Choose Image</label>
                                    <input  class="avatar-input commn-btn save" id="avatarInput" name="avatar_file" type="file" accept="image/x-png, image/png, image/gif, image/jpeg, image/jpg">
                                </div>
                            </div>

                            <!-- Crop and preview -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="avatar-wrapper"></div>
                                </div>
                            </div>

                            <div class="row" style="padding-left: 15px;">
                                <div class="clearfix">
                                    <div class="avatar-btns text-center">
                                        <div class="btn-group">
                                            <button class="btn btn-default btn-cropper fa fa-rotate-left" data-method="rotate" data-option="-90" type="button" title="Rotate -90 degrees"></button>
                                        </div>
                                        <div class="btn-group">
                                            <button class="btn btn-default btn-cropper fa fa-rotate-right" data-method="rotate" data-option="90" type="button" title="Rotate 90 degrees"></button>
                                        </div>
                                    </div>
                                    <div class="avatar-zooms text-center">
                                        <div class="btn-group">
                                            <button class="btn btn-default btn-cropper fa fa-plus" data-method="zoom" data-option="0.1" type="button" title="Zoom Out"></button>
                                        </div>
                                        <div class="btn-group">
                                            <button class="btn btn-default btn-cropper fa fa-minus " data-method="zoom" data-option="-0.1" type="button" title="Zoom In"></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-sm-12 text-center change clearfix" style="margin-top:20px">
                                    <button class="commn-btn cancel" data-dismiss="modal" type="reset">Cancel</button>
                                    <button class="commn-btn save sv-btn" type="submit">Save</button>
                                    <img class="myloader" src="images/loader.svg" style="display: none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--*******************cropper modal end********************-->