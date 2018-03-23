<!DOCTYPE html>
<html lang="en">
    <head>
        <base href="<?php echo base_url(); ?>">

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0,minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>RCC</title>
        <link rel="icon" type="image/png" sizes="32x32" href="public/images/logo.png">
        <!-- Bootstrap Core CSS -->

        <link href="public/css/bootstrap.min.css" rel='stylesheet'>
        <link href="public/css/style.css" rel='stylesheet'>
        <link href="public/css/plugin/bootstrap-select.min.css" rel='stylesheet'>
        <link href="public/css/plugin/jquery.mCustomScrollbar.min.css" rel='stylesheet'>
        <link href="public/css/plugin/lightgallery.min.css" rel='stylesheet'>
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel='stylesheet'>
        <!-- Gallery Video js -->
        <link href="http://vjs.zencdn.net/6.6.3/video-js.css" rel="stylesheet">
        <script src="public/js/jquery.min.js"></script>

        <script>
            var projectglobal = {};
            projectglobal.baseurl = "";
            var csrf_token = <?php echo "'" . $this->security->get_csrf_hash() . "'"; ?>;
            var baseUrl = '';
        </script>

    </head>
    <?php
    $this->load->helper('cookie');
    $controller   = strtolower($this->router->fetch_class());
    $method       = strtolower($this->router->fetch_method());
    $sidebarState = isset($adminInfo["sidebar_state"]) ? $adminInfo["sidebar_state"] : "";
    $sideBar      = get_cookie('sideBar');
    $sideBar      = isset($sideBar) ? $sideBar : "";
//        echo $controller;die('test');
    ?>
    <body class="<?php echo ($sideBar == 'minimized') ? 'body-sm' : '' ?>">
        <div class="in-data-wrap">
            <!--left panel-->
            <aside class="left-panel-wrapper">
                <div class="closeSidebar768">x</div>
                <div class="left-panel">
                    <div class="inner-left-pannel">
                        <div class="user-short-detail">
                            <div class="image-view-wrapper img-view80p img-viewbdr-radius">
                                <a href = "admin/profile"> <div id="lefft-logo" class="image-view img-view80" style="background:url('<?php
                                    echo (!empty($admininfo['admin_profile_pic'])) ? IMAGE_PATH . $admininfo['admin_profile_pic'] : DEFAULT_IMAGE
                                    ?>')"></div></a>
                            </div>
                            <span class="user-name"><?php echo (!empty($admininfo['admin_name'])) ? ucwords($admininfo['admin_name']) : "Reusable Admin"; ?></span>
                        </div>
                        <div class="left-menu">
                            <ul>
                                <li>
                                    <a href="admin/dashboard" class="<?php echo ($controller == 'dashboard') ? 'active' : ''; ?>"  >
                                        <span class="left-side-icon"><i class="fa fa-tachometer"></i></span><label class="nav-txt">Dashboard</label>
                                    </a>
                                </li>

                                <li>
                                    <a href="admin/users" class="<?php echo ($controller == 'user') ? 'active' : ''; ?>">
                                        <span class="left-side-icon"><i class="fa fa-users"></i></span><label class="nav-txt">Users</label>
                                    </a>
                                </li>
                                <li>
                                    <a href="admin/cms" class="<?php echo ($controller == 'cms') ? 'active' : ''; ?>" >
                                        <span class="left-side-icon"><i class="fa fa-file"></i></span><label class="nav-txt">Content</label>
                                    </a>
                                </li>
                                <li>
                                    <a href="admin/version" class="<?php echo ($controller == 'version') ? 'active' : ''; ?>" >
                                        <span class="left-side-icon"><i class="fa fa-code"></i></span><label class="nav-txt">Manage Version</label>
                                    </a>
                                </li>
                                <li>
                                    <a href="admin/notification" class="<?php echo ($controller == 'notification') ? 'active' : ''; ?>">
                                        <span class="left-side-icon"><i class="fa fa-bell"></i></span><label class="nav-txt">Notification</label>
                                    </a>
                                </li>
                                <li>
                                    <a href="admin/cropper" class="<?php echo ($controller == 'cropper') ? 'active' : ''; ?>">
                                        <span class="left-side-icon"><i class="fa fa-crop"></i></span><label class="nav-txt">RCC Cropper</label>
                                    </a>
                                </li>
                                <?php if ($admininfo['role_id'] == 1) { ?>
                                    <li>
                                        <a href="admin/subadmin" class="<?php echo ($controller == 'subadmin') ? 'active' : ''; ?>">
                                            <span class="left-side-icon"><i class="fa fa-user-secret"></i></span><label class="nav-txt">Sub Admin</label>
                                        </a>
                                    </li>
                                <?php } ?>

                                <!--                                <li>
                                                                    <a href="<?php echo base_url(); ?>admin/posts" class="<?php echo ($controller == 'post_management') ? 'active' : ''; ?>" >
                                                                        <span class="left-side-icon"><i class="fa fa-address-card"></i></span><label class="nav-txt">Post Management</label>
                                                                    </a>
                                                                </li>-->
                                <li>
                                    <a href="<?php echo base_url(); ?>admin/Subscriptions" class="<?php echo ($controller == 'subscriptions') ? 'active' : ''; ?>" >
                                        <span class="left-side-icon"><i class="fa fa-rss"></i></span><label class="nav-txt">Subscriptions</label>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </aside>
            <!--left panel-->
            <div class="right-panel">
                <!--Header-->
                <header>
                    <!--toggle button-->
                    <div class="toggle-btn-wrap" data-state="<?php echo empty($sidebarState) ? "expanded" : "minimized"; ?>">
                        <span class="line-bar"></span>
                        <span class="line-bar shot-line-br"></span>
                        <span class="line-bar"></span>
                    </div>
                    <!--toggle button close-->
                    <!--nav brand wrap-->
                    <div class="nav-brand">
                        <a href="javascripit:void(0)" class="brand">
                            <img src="public/images/logo.png" title="Admin Logo">
                        </a>
                    </div>
                    <!--nav brand wrap close-->
                    <!--User Setting-->
                    <div class="user-setting-wrap">
                        <div class="user-pic-wrap">
                            <ul>
                                <li>
                                    <a href="admin/profile"><img src="public/images/setting.svg" title="Setting"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="logoutUser();"><img src="public/images/logout.svg" title="Logout"></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!--User Setting wrap close-->
                </header>

