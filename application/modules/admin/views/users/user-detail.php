<div class="inner-right-panel">

    <!--breadcrumb wrap-->
    <div class="breadcrumb-wrap">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url() ?>admin/users">Users</a></li>
            <li class="breadcrumb-item active">User Detail</li>
        </ol>
    </div>

    <!--Filter Section -->
    <div class="form-item-wrap">
        <div class="form-item-title clearfix">
            <h3 class="title">User Detail</h3>
        </div>
        <!-- title and form upper action end-->
        <div class="white-wrapper clearfix">
            <div class="row">
                <div class="col-lg-3 col-sm-5 col-xs-12">
                    <div class="form-profile-pic-wrapper image-view-wrapper img-view150p img-viewbdr-radius4p">
                        <div class="profile-pic image-view img-view150" style="background-image:url('public/images/default.png');">
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-sm-7 col-xs-12">
                    <div class="row">
                        <!--form ele wrapper-->
                        <div class="user-detail-panell">

                            <div class="col-xs-12">
                                <div class="form-group">
                                    
                                </div>
                            </div>

                            <div class="col-lg-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="admin-label">Name</label>
                                    <div class="input-holder">
                                        <span class="text-detail"><?php echo ucfirst($profile['first_name']) . ' ' . ucfirst($profile['middle_name']) . ' ' . $profile['last_name']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="admin-label">Email ID</label>
                                    <div class="input-holder">
                                        <span class="text-detail"><?php echo (isset($profile['email']) && !empty($profile['email']) ) ? $profile['email'] : "Not available"; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="admin-label">Status</label>
                                    <div class="input-holder">
                                        <span class="text-detail"><?php echo $status      = (isset($profile['status']) && !empty($profile['status']) ) ? $status_array[$profile['status']] : 'Not available'; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="admin-label">Gender</label>
                                    <div class="input-holder">
                                        <span class="text-detail"><?php echo ($profile['gender'] == 1) ? 'Male' : ($profile['gender'] == 2) ? 'Female' : 'Other'; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="col-lg-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="admin-label">Address</label>
                                    <div class="input-holder">
                                        <span class="text-detail"><?php echo (isset($profile['address']) && !empty($profile['address']) ) ? $profile['address'] : "Not available"; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="admin-label">Phone Number</label>
                                    <div class="input-holder">
                                        <span class="text-detail"><?php echo (isset($profile['phone']) && !empty($profile['phone']) ) ? $profile['phone'] : "Not available"; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="col-lg-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="admin-label">Age</label>
                                    <div class="input-holder">
                                        <span class="text-detail"><?php echo ($profile['age'] > 0) ? $profile['age'] : 'Not Available'; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label class="admin-label">Register Date</label>
                                    <div class="input-holder">
                                        <span class="text-detail"><?php echo date("d M Y H:i a", strtotime($profile['registered_date'])); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                        </div>
                        <!--form ele wrapper end-->

                    </div>
                </div>

            </div>
        </div>
        <!--form element wrapper end-->
    </div>

    <!--Filter Section -->
    <div class="form-item-wrap">
        <div class="form-item-title clearfix">
            <h3 class="title">Subscriptions</h3>
        </div>

        <div class="white-wrapper">
            <div class="clearfix">
                <!-- title and form upper action end-->
                <div class="user-setting-wrap list_view_toggle">
                    <ul>
                        <li class="list_view">
                            <a href="javascript:void(0);"><i class="fa fa-th-list" aria-hidden="true" title="List"></i></a>
                        </li>
                        <li class="mono_view">
                            <a href="javascript:void(0);"><i class="fa fa-th" aria-hidden="true" title="View"></i></a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="clearfix list_view_subscription">
                <?php
                $single_view = [];
                ?>
                <section class="content-wrapper clearfix" >

                    <div class="col-lg-6 col-sm-6 hide-mobile"></div>
                    <div class="clear"></div>
                    <!-- Content section -->
                    <div class="white-wrapper p-md">
                        <div class="table-responsive custom-tbl">
                            <table class="table outlet-table table-striped m-n">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <!--<th>Type</th>-->
                                        <th>Valid From</th>
                                        <th>Valid Till</th>
                                        <th>Renew Date</th>
                                        <th style="cursor:pointer">Added On<a  href="javascript:void(0);" class="th-icon"><i class=""></i></a> </th>
                                        <th>Status </th>
                                        <th width='80px' class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $pagecount   = 10;
                                    $i           = (($page * $pagecount) - $pagecount) + 1;
                                    ?>
                                    <?php
                                    if (empty($data)) {
                                        echo '<tr><td colspan = "9" class = "text-center">No subscriptions exist.</td></tr>';
                                    }

                                    foreach ($data as $key => $subscription_data) {

                                        if (empty($single_view) && $subscription_data['status'] == ACTIVE) {

                                            $single_view['id']                = $subscription_data['id'];
                                            $single_view['subscription_name'] = $subscription_data['subscription_name'];
                                            $single_view['price']             = $subscription_data['price'];
                                            $single_view['renew_date']        = $subscription_data['renew_date'];
                                            $single_view['create_date']       = $subscription_data['create_date'];
                                            $single_view['status']            = $subscription_data['status'];
                                            $single_view['description']       = $subscription_data['description'];
                                        }
                                        ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td class="text-nowrap" align="left">
                                                <!--<a href="admin/Subscriptions/view?data=<?php echo queryStringBuilder("id=" . $subscription_data['id']); ?>">-->
                                                <?php echo $subscription_data['subscription_name'] ?>
                                                <!--</a>-->
                                            </td>

                                            <?php echo '<td class="text-nowrap" align="left">$' . $subscription_data['price'] . '</td>' ?>

                                                                                                                                                                                                                                                                                                                            <!--<td align="left">-->
                                            <?php //echo ($subscription_data['subs_type'] == RECURRING) ? "Recurring Subscription " : "One-Time Subscription"  ?>
                                            <!--</td>-->

                                            <?php echo '<td align="left">' . date('d-M-Y', strtotime($subscription_data['start_date'])) . '</td>'; ?>

                                            <?php echo '<td align="left">' . date('d-M-Y', strtotime($subscription_data['end_date'])) . '</td>'; ?>

                                            <?php echo '<td align="left">' . date('d-M-Y', strtotime($subscription_data['renew_date'])) . '</td>'; ?>

                                            <?php echo '<td align="left">' . date('d-M-Y', strtotime($subscription_data['create_date'])) . '</td>'; ?>

                                            <?php echo ($subscription_data['status'] == 1) ? '<td>Active</td>' : '<td>Revoked</td>' ?>


                                            <td width='80px'  class="text-center" >
                                                <?php
                                                switch ($subscription_data['status']) {
                                                    case ACTIVE:
                                                        ?>
                                                        <a href="javascript:void(0);" id="block_<?php echo $subscription_data['id']; ?>" class="table_icon">
                                                            <i class="fa fa-ban" aria-hidden="true" onclick="blockUser('user_subscriptions', 2, '<?php echo encryptDecrypt($subscription_data['id']); ?>', 'req/change-user-status', 'Do you really want to revoke this subscription?', 'Revoke');">
                                                            </i>
                                                        </a>
                                                        <?php
                                                        break;

                                                    default:
                                                        break;
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="clear"></div>
                        <nav class="pagination-wrapper m-t-md">
                            <ul class="pagination">
                                <?php
                                if (isset($links)) {
                                    echo $links;
                                }
                                ?>
                            </ul>
                        </nav>
                    </div>
                    <!-- Content section End -->
                </section>
            </div>
        </div>

        <div class=" clearfix card_view_subscription">
            <section class="content-wrapper clearfix" >
                <?php
                if (empty($single_view)) {
                    echo '<div class="white-wrapper"><div class="form-ele-wrapper clearfix"><div class="row text-center">No active subscriptions exists.</div></div></div></div>';
                }
                if (!empty($single_view)) {
                    ?>

                    <div class="white-wrapper">
                        <?php
                        if ($single_view['status'] == ACTIVE) {
                            ?>
                            <a href="javascript:void(0);" id="block_<?php echo $single_view['id']; ?>" class="table_icon">
                                <i class="fa fa-ban" aria-hidden="true" onclick="blockUser('user_subscriptions', 2, '<?php echo encryptDecrypt($single_view['id']); ?>', 'req/change-user-status', 'Do you really want to revoke this subscription?', 'Revoke');">
                                    REVOKE</i>
                            </a>
                            <?php
                        }
                        ?>
                        <!-- title and form upper action end-->
                        <div class="form-ele-wrapper clearfix">
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <div class="stats-overview stat-block">
                                        <div class="details">
                                            <div class="title">
                                                Subscription Title
                                            </div>
                                            <div class="numbers">
                                                <?php echo $single_view['subscription_name']; ?>
                                            </div>
                                            <div class="month text-center">
                                                <span>$<?php echo $single_view['price']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-4">
                                    <div class="stats-overview stat-block">
                                        <div class="details">
                                            <div class="title">
                                                Subscription Status
                                            </div>
                                            <div class="numbers">
                                                <?php echo ($single_view['status'] == 1) ? '<span>Active</span>' : '<span>Revoked</span>' ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-4">
                                    <div class="stats-overview stat-block">
                                        <div class="details">
                                            <div class="title">
                                                Subscription Created On
                                            </div>
                                            <div class="numbers">
                                                <?php echo date('M d ,Y', strtotime($single_view['create_date'])); ?>
                                            </div>
                                        </div>
                                        <div class="details">
                                            <div class="title">
                                                Subscription Will Renew On
                                            </div>
                                            <div class="numbers">
                                                <?php echo date('M d ,Y', strtotime($single_view['renew_date'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>

                                <div class="col-sm-12">
                                    <div class="stats-overview stat-block">
                                        <div class="details">
                                            <div class="title">
                                                Description
                                            </div>
                                            <div class="details-description">
                                                <?php echo $single_view['description']; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--form ele wrapper end-->
                        </div>
                        <!--form element wrapper end-->
                    </div>
                    <!--close form view   -->

                </section>
            </div>
            <?php
        }
        ?>

    </div>
    <!--form element wrapper end-->
</div>