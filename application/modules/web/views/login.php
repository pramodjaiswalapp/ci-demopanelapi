
<body >

    <input type = "hidden" id = "csrfToken" value = "<?php echo $csrfToken; ?>">
    <!--Login page  Wrap-->
    <div class="data-wrap">
        <!--COl Wrapper-->
        <div class="in-col-wrap clearfix">
            <!--Left Col-->
            <div class="in-left-col">
                <?php
                if ($this->session->flashdata('message') != '') {
                    echo $this->session->flashdata('message');
                }
                ?>
                <!--form inner col-->
                <div class="index-form-wrap">
                    <div class="form_inner_wrap">
                        <div class="login-error">
                            <span class="error"></span>
                        </div>
                        <?php echo form_open('', array('id' => 'login_admin_form')) ?>

                        <h1 class="index-comn-heading-web">Web-Login</h1>
                        <p class="index-note">Enter the details below to access your account </p>
                        <div class="form-field-wrap">
                            <span class="ad-user"></span>
                            <input type="text" id="email_id" class="login_filed removespace" maxlength="40" placeholder="* User Id" onfocus="this.removeAttribute('readonly');" readonly name="email" value="<?php echo isset($email) ? $email : set_value('email'); ?>"  autocomplete="off" />
                            <label class = "alert-danger email_error"></label>

                        </div>
                        <div class = "form-field-wrap" id = "passworderr">
                            <span class = "ad-password"></span>
                            <input type = "password" id = "web_login_pass" class = "login_filed removespace" maxlength = "20" placeholder = "* Password" onfocus = "this.removeAttribute('readonly');" readonly name = "password" value = "<?php echo isset($password) ? $password : set_value('password'); ?>" autocomplete = "off" required />
                            <?php echo form_error('password', '<label class="alert-danger">', '</label>') ?>
                            <?php echo isset($error) ? '<label class="alert-danger">' . $error . '</label>' : ''; ?>
                        </div>
                        <div class="form-field-wrap clearfix">
                            <span class="rember-col">
                                <div class="th-checkbox">
                                    <input type="checkbox" name="filter" id="flowers" value="remember_me">
                                    <label for="flowers" class="lbl-check">Remember me</label>
                                </div>
                            </span>
                            <span class="forgot-pass">
                                <!--<a class="" href="<?php echo base_url(); ?>admin/forgot">Forgot Password?</a>-->
                            </span>
                        </div>
                        <div class="form-field-wrap">
                            <div class="btn-wrapper">
                                <button class="index-comn-btn" type="submit" id="login" title="Login" >Login</button>
                            </div>
                        </div>

                        <div id="outer">
                            <span class="social_login_msg">Or login with: </span>
                            <button type="button" class="log_in_fb"><i class="fa fa-facebook-f"></i></button>
                            <button type="button" class="getLinkedIn"><i class="fa fa-linkedin"></i></button>
                            <button type="button" class="getGoogle"><i class="fa fa-google"></i></button>
                            <!--<button type="button" class="getInstagram"><i class="fa fa-instagram"></i></button>-->
                            <button type="button" id = "insta-wjs" class="getInstagram" onclick="window.open('web/instaRedirect', 'instagram_login_box', 'width=600,height=400');
                                    return false;"><i class="fa fa-instagram"></i>
                            </button>
                            <button type="button" id = "twitter-wjs" class="getTwitter" onclick="window.open('web/redirect', 'twitter_login_box', 'width=600,height=400');
                                    return false;"><i class="fa fa-twitter"></i>
                            </button>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <!--form inner col close-->
        </div>
    </div>
</div>

