<!DOCTYPE html>
<html lang="en">

    <head>
        <title>Project Name</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="<?php echo base_url() ?>public/css/style.main.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>public/css/form-element.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>public/css/style.media.main.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>public/css/jquery-confirm.css">
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
                            window.location.replace("ProjectName://reusable.applaurels.com/userId/<?php echo $userId; ?>");
                            this.timer = setTimeout(this.openWebApp, 3000);
                        },
                        openWebApp: function () {
                            window.location.replace("https://play.google.com/store/apps/details?id=com.fssai.fssai&userId=<?php echo $userId ?>&hl=en");
                        }
                    };
                    app.launchApp();

                } else if (isIphone == true) {
                    var app = {
                        launchApp: function () {
                            window.location.replace("ProjectName://reusable.applaurels.com/userId/<?php echo $userId; ?>");
                            this.timer = setTimeout(this.openWebApp, 1000);
                        },
                        openWebApp: function () {
                            window.location.replace("https://iimmpact.com/");
                        }
                    };
                    app.launchApp();
                }
            })();
        </script>

    </head>
    <body class="center-box-body">
        <div class="center-box-wrap">
            <div class="center-box">
                <div class="center-box-inner">
                    <div class="center-box-logo">
                        <a href="javascript:void(0);">
                            <img src="/public/images/logo-large.png" alt="Project logo">
                        </a>
                    </div>
                    <div class="center-box-title">
                        <h3>Reset Password</h3>
                    </div>
                    <div class="form-element-wrap">
                        <input type="hidden" id="token" value="<?php echo $token; ?>">
                        <div class="ele-block">
                            <div class="form-group">
                                <label for="password">Password <mark>*</mark></label>
                                <div class="form-input-wrap">
                                    <input name="password" id="password" maxlength="16" type="password" class="password form-control">
                                    <span class="passwordErr error"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="cnfpassword">Confirm Password <mark>*</mark></label>
                                <div class="form-input-wrap">
                                    <input name="cnfpassword" id="cnfpassword" maxlength="16" type="password" class="password form-control">
                                    <span class="cnfpasswordErr error"></span>
                                </div>
                            </div>
                            <div class="form-btn-group">
                                <div class="btn-center">
                                    <button type="button" onclick="window.location.href = '/admin'" class="btn btn-cancel">Back</button>
                                    <button type="button" class="resetbtn btn btn-success">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <!-- page level script-->
    <!-- page level script end-->
    <script>
        var baseUrl = "<?php echo base_url(); ?>";
    </script> 
    <script src="<?php echo base_url() ?>public/js/script.main.js"></script>
    <script src="<?php echo base_url() ?>public/js/jquery-confirm.js"></script>
    <script src="<?php echo base_url() ?>public/js/validation.js"></script>
    <script src="<?php echo base_url() ?>public/js/common.js"></script>

    <style>
        .error{
            color:red;
        }    
    </style>

</html>