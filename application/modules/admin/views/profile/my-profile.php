
<div class="inner-right-panel">

    <!--breadcrumb wrap-->
    <div class="breadcrumb-wrap">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url() ?>admin/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Admin Profile</li>
        </ol>
    </div>
    <!--breadcrumb wrap close-->
    <!--Filter Section -->
    <?php
     if ( $this->session->flashdata( 'message' ) != '' ) {
         echo $this->session->flashdata( 'message' );
     }
    ?>
    <div class="form-item-wrap">
        <div class="form-item-title clearfix">
            <h3 class="title"> Profile</h3>
        </div>
        <!-- title and form upper action end-->
        <div class="white-wrapper clearfix">
            <div class="col-lg-3 col-sm-5">
                <div class="form-profile-pic-wrapper image-view-wrapper img-view150p img-viewbdr-radius4p">
                    <div class="profile-pic image-view img-view150" style="background-image:url('<?php echo (!empty( $editdata['admin_profile_pic'] )) ? IMAGE_PATH.$editdata['admin_profile_pic'] : DEFAULT_IMAGE ?>');">
                    </div>
                </div>
                <span class="loder-wrraper-single"></span>
            </div>
            <div class="col-lg-9 col-sm-7">
                <div class="row">
                    <div class="col-lg-12 col-sm-12">

                        <div class="form-group">
                            <label class="admin-label">Name</label>
                            <div class="input-holder">
                                <span type="text" name="Merchant_Name" id="Merchant_Name" value="" placeholder="john samth"><?php echo (isset( $editdata['admin_name'] ) && !empty( $editdata['admin_name'] ))
                                ? $editdata['admin_name'] : '' ?></span>
                                <span class="text-detail"><?php echo form_error( 'Merchant_Name' ) ?></span>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-12 col-sm-12">
                        <div class="form-group">
                            <label class="admin-label">Email ID</label>
                            <div class="input-holder">
                                <span type="text" name="email" value="" id="email" placeholder="RFID120@gmail.com"><?php echo (isset( $editdata['admin_email'] ) && !empty( $editdata['admin_email'] )) ? $editdata['admin_email']
                : '' ?></span>
                            </div>
                            <div class="error"><?php echo form_error( 'email' ) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-sm-12  text-center">
                <div class="button-wrap">
                    <ul>
                        <li> <a href="admin/change-password"><button type="button" name="changepassword" class="commn-btn save">Change Password</button></a>  </li>
                        <li> <a href="admin/edit-profile"><button type="button" name="editprofile" class="commn-btn save">Edit Profile</button></a> </li>
                    </ul>
                </div>
            </div>
        </div>
        <!--form ele wrapper end-->
    </div>
    <!--form element wrapper end-->
</div>
<!--close form view   -->
<!--Filter Section Close-->
</div>
<!--Table listing-->
