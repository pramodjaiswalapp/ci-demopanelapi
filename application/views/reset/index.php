<!DOCTYPE html>
<html lang="en">
    <head>
        <base href="<?php echo base_url(); ?>">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, 
              minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title><?php echo PROJECT_NAME ?></title>
        <link rel="icon" type="image/png" sizes="32x32" href="public/images/logo.png">
        <link href='<?php echo base_url() . 'public/css/plugin/bootstrap.min.css' ?>' rel='stylesheet'>
        <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css' rel='stylesheet'>
        <link href='<?php echo base_url() . 'public/css/style.css' ?>' rel='stylesheet'>
        <link href='<?php echo base_url() . 'public/css/media.css' ?>' rel='stylesheet'>
        <script src="<?php echo base_url() ?>public/js/jquery.min.js"></script>
        <script src='<?php echo base_url() ?>public/js/mdetect.js'></script>
        <script type="text/javascript">

            (function () {
                var ua = navigator.userAgent.toLowerCase();

                var isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");
                var isIphone = ua.indexOf("iphone") > -1; //&& ua.indexOf("mobile");

                if (isAndroid == true) {
                    var app = {
                        launchApp: function () {
                            window.location.replace("ProjectName://<?php echo $_SERVER['HTTP_HOST'].'/userId/'.$userId; ?>");
                            this.timer = setTimeout(this.openWebApp, 3000);
                        },
                        openWebApp: function () {
                            window.location.replace("<?php echo base_url().'reset?token='.$token ?>&hl=en");
                        }
                    };
                    app.launchApp();

                } else if (isIphone == true) {
                    var app = {
                        launchApp: function () {
                            window.location.replace("ProjectName://<?php echo $_SERVER['HTTP_HOST'].'/userId/'.$userId; ?>");
                            this.timer = setTimeout(this.openWebApp, 1000);
                        },
                        openWebApp: function () {
                            window.location.replace("<?php echo base_url().'reset?token='.$token ?>");
                        }
                    };
                    app.launchApp();
                }
            })();
        </script>
    </head>
    <!--Login page  Wrap-->
    <div class="data-wrap">
        <!--COl Wrapper-->  
        <div class="in-col-wrap clearfix">
            <!--Left Col-->
            <div class="in-left-col">
                <label id="error">
                    <?php $alertMsg = $this->session->flashdata('alertMsg'); ?>
                    <div class="alert alert-success" <?php echo (!(isset($alertMsg) && !empty($alertMsg)))?"style='display:none'":"" ?> role="alert">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <strong>
                            <span class="alertType"><?php echo (isset($alertMsg) && !empty($alertMsg))?$alertMsg['type']:"" ?></span>
                        </strong>
                        <span class="alertText"><?php echo (isset($alertMsg) && !empty($alertMsg))?$alertMsg['text']:"" ?></span>
                    </div>
                </label>
                <!--form inner col-->
                <div class="index-form-wrap">
                    <div class="form_hd">
                        <figure class="index-logo">
                            <img src="<?php echo base_url() ?>public/images/logo.png">
                        </figure>
                    </div>
                    <div class="form_inner_wrap">
                        <form method="post" id="resetform" action="/reset/resetpassword">
                            <input type="hidden" name="<?php echo isset($csrfName)?$csrfName:""; ?>" id="<?php echo isset($csrfName)?$csrfName:""; ?>" value="<?php echo $csrfToken; ?>"> 
                            <input type="hidden" name="token" value="<?php echo isset($token)?$token:""; ?>"> 
                            <h1 class="index-comn-heading">Reset Password  </h1>
                            <p class="index-note"></p>

                            <div class="form-field-wrap" id="passerror">
                                <span class="ad-password"></span>
                                <input type="password" class="login_filed removemessage" maxlength="40" placeholder="* Enter New Password" id="password" name="password"  autocomplete="off"> 
                                <span class="error-mssg passwordErr" id="password" ></span>
                                <span class="bar"></span>
                            </div>

                            <div class="form-field-wrap" id="conpassreq">
                                <span class="ad-password"></span>
                                <input type="password" class="login_filed removemessage" maxlength="40" placeholder="* Enter Confirm Password" id="cnfpassword" name="cpassword"  autocomplete="off"> 
                                <span class="error-mssg cnfpasswordErr"  id="cnfpassword"></span>
                                <span class="bar"></span>
                            </div>
                            <div class="form-field-wrap">
                                <div class="btn-wrapper">
                                    <button class="index-comn-btn" onclick="return validatepassword()" type="submit" id="resetbtn">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!--form inner col close-->
            </div>
            <!--Left Col-->
        </div>
        <!--COl Wrapper-->
        <!--Footer-->
        <!--Login page  Wrap close-->
        <script src="<?php echo base_url() ?>public/js/outer-common.js"></script>
        <script src="<?php echo base_url() ?>public/js/global-msg.js"></script>
</html>


