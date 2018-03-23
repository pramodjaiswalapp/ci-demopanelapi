
<div class="inner-right-web-panel"

     <div class="white-wrapper">
        <div class="row">
            <div class="col-lg-6">
                <a href = "web/logout"> <span class="btn btn-default btn-sm">Logout</span></a>
            </div>
        </div>
    </div>

    <div class="white-wrapper">
        <span class="top-sticker">User Profile</span>
        <div class="wrap">
            <div class="row">
                <div class="col-sm-12">
                    <figure class="usr-dtl-pic">
                        <img id = "preview-image" src="<?php echo (!empty($userinfo['image'])) ? $userinfo['image'] : 'public/images/no-image.jpg'; ?>">
                    </figure>
                </div>
                <div class="col-sm-12 m-t-sm">
                    <div class="row admin-filed-wrap">
                        <div class="col-xs-4">
                            <label class="admin-label">Name</label>
                        </div>
                        <div class="col-xs-8">
                            <div class="field-wrap">
                                <span class="show-label">
                                    <?php echo (!empty($userinfo['first_name'])) ? $userinfo['first_name'] : 'NA'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 m-t-sm">
                    <div class="row admin-filed-wrap">
                        <div class="col-xs-4">
                            <label class="admin-label">Email ID</label>
                        </div>
                        <div class="col-xs-8">
                            <div class="field-wrap">
                                <span class="show-label"><?php echo (!empty($userinfo['email'])) ? $userinfo['email'] : 'NA'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 m-t-sm">
                    <div class="row admin-filed-wrap">
                        <div class="col-xs-4">
                            <label class="admin-label">Username</label>
                        </div>
                        <div class="col-xs-8">
                            <div class="field-wrap">
                                <span class="show-label"><?php echo (!empty($userinfo['username']) ) ? $userinfo['username'] : 'NA'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
