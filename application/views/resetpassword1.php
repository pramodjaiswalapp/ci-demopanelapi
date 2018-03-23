  <!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, 
         minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
      <meta name="description" content="">
      <meta name="author" content="">
      <title>BonApp</title>
      <!-- Bootstrap Core CSS -->
      <link rel="stylesheet" href="http://bonappstage.appnationz.com/public/adminpanel/css/bootstrap.css">
      <link href="http://bonappstage.appnationz.com/public/adminpanel/css/bootstrap.css" rel="stylesheet">
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css" rel="stylesheet">
      <link href="http://bonappstage.appnationz.com/public/adminpanel/css/style.css" rel="stylesheet">
      <link href="http://bonappstage.appnationz.com/public/adminpanel/css/media.css" rel="stylesheet">
   </head>
<!--Login page  Wrap-->
<div class="data-wrap">
    <!--COl Wrapper-->  
    <div class="in-col-wrap clearfix">
        <!--Left Col-->
        <div class="in-left-col">
            <!--form inner col-->
            <div class="index-form-wrap">
                <form method="post" id="resetform">
                       <input type="hidden" name="<?php echo $csrfName;?>" id="<?php echo $csrfName;?>" value="<?php echo $csrfToken;?>"> 
                    <h1 class="index-comn-heading">Reset Password  </h1>
                    <p class="index-note"></p>

                    <div class="form-field-wrap" id="passerror">
                        <span class="ad-password"></span>
                        <input type="password" class="login_filed removemessage" maxlength="40" placeholder="Enter New Password" id="new_pass" name="password"  autocomplete="off"> 
                        <span class="error-mssg errorremove" id="new_pass1" ></span>
                        <span class="bar"></span>
                    </div>

                    <div class="form-field-wrap" id="conpassreq">
                        <span class="ad-password"></span>
                        <input type="password" class="login_filed removemessage" maxlength="40" placeholder="Enter Confirm Password" id="con_pass" name="cpassword"  autocomplete="off"> 
                        <span class="error-mssg errorremove"  id="con_pass1"></span>
                        <span class="bar"></span>
                    </div>


                    <input type="hidden" name="token" value="<?php echo $this->uri->segment(4); ?>" id="token">
                    <div class="form-field-wrap">
                        <button class="index-comn-btn" type="button" id="resetbtn">Send </button>
                    </div>
                </form>
            </div>
            <!--form inner col close-->
        </div>
        <!--Left Col-->
    </div>
    <!--COl Wrapper-->
    <!--Footer-->
