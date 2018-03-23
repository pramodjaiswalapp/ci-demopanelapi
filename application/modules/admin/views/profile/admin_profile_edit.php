<div class="inner-right-panel">
    <!--breadcrumb wrap-->
    <div class="breadcrumb-wrap">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url() ?>admin/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo base_url() ?>admin/profile">Admin Profile</a></li>
            <li class="breadcrumb-item active">Edit Profile</li>
        </ol>
    </div>
    <!--breadcrumb wrap close-->
    <?php
     if ( $this->session->flashdata( 'message' ) != '' ) {
         echo $this->session->flashdata( 'message' );
     }
    ?>
    <!--Filter Section -->
    <?php echo form_open_multipart( '', array ('id' => 'editadminprofile1') ); ?>
    <div class="form-item-wrap">
        <div class="form-item-title clearfix">
            <h3 class="title">Edit Profile</h3>
        </div>
        <!-- title and form upper action end-->
        <div class="white-wrapper clearfix">
            <div class="row">
                <div class="col-lg-3 col-sm-5">
                    <div class="err_wrap">
                        <div class="image-view-wrapper img-view150p img-viewbdr-radius4p">
                            <div class="image-view img-view150" id="profilePic" style="background-image:url('<?php
                             echo (!empty( $editdata['admin_profile_pic'] )) ? IMAGE_PATH.$editdata['admin_profile_pic'] : DEFAULT_IMAGE
                            ?>');">
                                <a href="javascript:void(0);" class="upimage-btn">
                                    <input type="file" id="upload" style="display:none;" accept="image/*" name="admin_image" onchange="loadFile_signup( event, 'profilePic', this )">
                                </a>
                                <label class="camera" for="upload"><i class="fa fa-camera" aria-hidden="true"></i></label>
                                <label id="image-error" class="alert-danger"></label>
                            </div>
                        </div>
                        <span class="loder-wrraper-single"></span>
                        <p>Max Size : 1024X768</p>
                    </div>
                </div>
                <div class="col-lg-9 col-sm-7">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="admin-label">Name</label>
                                <div class="input-holder">
                                    <input type="text" maxlength="100" name="Admin_Name" id="Admin_Name" value="<?php
                                     echo (isset( $editdata['admin_name'] ) && !empty( $editdata['admin_name'] )) ? $editdata['admin_name'] : set_value( 'Admin_Name' );
                                    ?>" placeholder="Enter Name">
                                           <?php echo form_error( 'Admin_Name', '<label class="alert-danger">', '</label>' ); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="admin-label">Email ID</label>
                                <div class="input-holder">
                                    <input type="text" readonly maxlength="100" name="email" value="<?php
                                     echo (isset( $editdata['admin_email'] ) && !empty( $editdata['admin_email'] )) ? $editdata['admin_email'] : set_value( 'email' );
                                    ?>" id="email" placeholder="Enter Email">
                                           <?php echo form_error( 'email', '<label class="alert-danger">', '</label>' ); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-ele-action-bottom-wrap btns-center clearfix">
                <div class="button-wrap text-center">
                    <button type="button"  onclick="window.location.href = '<?php echo base_url() ?>admin/profile'"class="commn-btn cancel">Cancel</button>
                    <button type="submit" class="commn-btn save">Save</button>
                </div>
            </div>
            <!--form ele wrapper end-->
        </div>
        <!--form element wrapper end-->
    </div>
    <!--close form view   -->
    <?php echo form_close(); ?>
    <!--Filter Section Close-->
</div>
<!--Table listing-->
<script>
    $( document ).ready( function () {
        setTimeout( function () {
            $( '.alert-danger' ).fadeOut( 5000 );
        }, 3000 );
    } );
</script>