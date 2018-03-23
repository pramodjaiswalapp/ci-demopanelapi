
<div class="inner-right-panel">


    <!--breadcrumb wrap-->
    <div class="breadcrumb-wrap">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url() ?>admin/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Change Password</li>
        </ol>
    </div>
    <!--breadcrumb wrap close-->


    <!--Filter Section -->
    <?php echo form_open( '', array ('id' => 'password_change_form') ); ?>
    <div class="form-item-wrap">
        <div class="form-item-title clearfix">
            <h3 class="title">Change Password</h3>
        </div>

        <!-- title and form upper action end-->
        <div class="white-wrapper clearfix">



            <div class="row">
                <div class="col-sm-5 col-xs-12">
                    <div class="form-group">
                        <label class="admin-label">* Old Password</label>
                        <div class="input-holder">
                            <input type="password" maxlength="16" class="form-control material-control" maxlength="55" name="oldpassword" value="<?php echo set_value( 'oldpassword' ); ?>">
                            <?php
                             echo isset( $error_message ) ? '<label class="alert-danger">'.$error_message.'</label>' : form_error( 'oldpassword', '<label class="alert-danger">', '</label>' );
                            ?>
                            <!-- <span class="error_wrap"></span> -->
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-5 col-xs-12">
                    <div class="form-group">
                        <label class="admin-label">* New Password</label>
                        <div class="input-holder">
                            <input type="password" maxlength="16" class="form-control material-control" maxlength="55" name="password" value="<?php echo set_value( 'password' ); ?>" id="password">
                            <?php echo form_error( 'password', '<label class="alert-danger">', '</label>' ); ?>
                            <!-- <span class="error_wrap"></span> -->
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-5 col-xs-12">
                    <div class="form-group">
                        <label class="admin-label">* Confirm Password</label>
                        <div class="input-holder">
                            <input type="password" maxlength="16" class="form-control material-control" maxlength="55" name="confirm_password" value="<?php echo set_value( 'confirm_password' ); ?>">
                            <?php echo form_error( 'confirm_password', '<label class="alert-danger">', '</label>' ); ?>
                            <!-- <span class="error_wrap"></span> -->
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
