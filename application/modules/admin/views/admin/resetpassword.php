<!--Login page  Wrap-->
<div class="data-wrap">
    <?php
     if ( $this->session->flashdata( 'message' ) != '' ) {
         echo $this->session->flashdata( 'message' );
     }
    ?>
    <!--COl Wrapper-->
    <div class="in-col-wrap clearfix">
        <!--Left Col-->
        <div class="in-left-col">
            <!--form inner col-->
            <div class="index-form-wrap">
                <div class="form_hd">
                    <figure class="index-logo">
                        <img src="<?php echo base_url() ?>public/images/logo.png">
                    </figure>
                </div>
                <div class="form_inner_wrap">
                    <form method="post" id="resetform">
                        <input type="hidden" name="<?php echo $csrfName; ?>" id="<?php echo $csrfName; ?>" value="<?php echo $csrfToken; ?>">
                        <h1 class="index-comn-heading">Reset Password  </h1>
                        <p class="index-note"></p>

                        <div class="form-field-wrap clearfix" id="passerror">
                            <!--<span class="ad-password"></span>-->
                            <input type="password" class="login_filed removemessage" maxlength="40" placeholder="* Enter New Password" id="password" name="password"  autocomplete="off">
                            <span class="error-mssg passwordErr" id="password" ></span>
                            <span class="bar"></span>
                        </div>

                        <div class="form-field-wrap clearfix" id="conpassreq">
                            <!--<span class="ad-password"></span>-->
                            <input type="password" class="login_filed removemessage" maxlength="40" placeholder="* Enter Confirm Password" id="cnfpassword" name="cpassword"  autocomplete="off">
                            <span class="error-mssg cnfpasswordErr"  id="cnfpassword"></span>
                            <span class="bar"></span>
                        </div>

                        <div class="btn-wrapper form-btn">
                            <button class="commn-btn save" onclick="return validatepassword()" type="submit" id="resetbtn">Send</button>
                        </div>
                    </form>
                </div>
            </div>
            <!--form inner col close-->
        </div>
        <!--Left Col-->
    </div>
    <!--COl Wrapper-->
</div>

