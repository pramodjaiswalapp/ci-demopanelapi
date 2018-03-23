<?php
$className  = $this->router->fetch_class();
$methodName = $this->router->fetch_method();
$admindata  = $this->Common_model->fetch_data('admin', 'admin_name,admin_profile_pic', ['where' => ['admin_id' => $this->admininfo['id']]], true);
?>
<aside>
    <!--left panel-->
    <div class="left-panel">
        <div class="inner-left-pannel">
            <div class="user-short-detail">
                <div id="lefft-logo" class="image-view" style="background:url('<?php echo (isset($admindata['admin_profile_pic']) && !empty($admindata['admin_profile_pic'])) ? base_url() . 'public/admin/' . $admindata['admin_profile_pic'] : 'public/images/login.png' ?>')"></div>
                <span class="user-name"><?php echo (isset($admindata['admin_name']) && !empty($admindata['admin_name'])) ? $admindata['admin_name'] : '' ?></span>
            </div>
            <div class="left-menu">
                <ul>
                    <li>
                        <a href="admin/dashboard" <?php
                        if ($className == "dashboard" && $methodName == "index") {
                            echo "class='active'";
                        }
                        ?>>
                            <span class="dashboard_img comm-img"></span><label class="nav-txt">Dashboard</label>
                        </a>
                    </li>

                    <li>
                        <a href="admin/users" <?php echo ($className == "user") ? "class='active'" : "" ?> >
                            <span class="user_img comm-img"></span><label class="nav-txt">Users</label>
                        </a>
                    </li>
                    <li>
                        <a href="admin/cms" <?php
                        if ($className == "cms" && in_array($methodName, ['edit', 'add', 'index'])) {
                            echo "class='active'";
                        }
                        ?>>
                            <span class="activitylog comm-img"></span><label class="nav-txt">Content</label>
                        </a>
                    </li>
                    <li>
                        <a href="admin/version" <?php
                        if ($className == "version" && in_array($methodName, ['edit', 'add', 'index'])) {
                            echo "class='active'";
                        }
                        ?> >
                            <span class="copy_img comm-img"></span><label class="nav-txt">Manage Version</label>
                        </a>
                    </li>
                    <li>
                        <a href="admin/notification" <?php
                        if ($className == "notification" && in_array($methodName, ['edit', 'add', 'index'])) {
                            echo "class='active'";
                        }
                        ?>>
                            <span class="notification_img comm-img"></span><label class="nav-txt">Notification</label>
                        </a>
                    </li>
                    <li>
                        <a href="admin/post_management" class="<?php echo ($controller == 'post_management') ? 'active' : ''; ?>" >
                            <span class="copy_img comm-img"></span><label class="nav-txt">Post Management</label>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!--left panel-->
</aside>
