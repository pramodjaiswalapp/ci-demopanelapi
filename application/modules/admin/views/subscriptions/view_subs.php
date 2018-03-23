<div class="inner-right-panel">
    <?php
    $recur_type              = [1 => 'Daily', 2 => 'Weekly', 3 => 'Monthly', 4 => 'Annually', 5 => 'One-Time'];
    $subs_data               = $subscription_data[0];
    $subscription_name       = ( isset($subs_data["subscription_name"]) ) ? $subs_data["subscription_name"] : 'N/A';
    $subscription_price      = ( isset($subs_data["price"]) ) ? '$' . $subs_data["price"] : 'N/A';
    $subscription_desc       = ( isset($subs_data["description"]) ) ? $subs_data["description"] : 'N/A';
    $subscription_date       = ( isset($subs_data["create_date"]) ) ? $subs_data["create_date"] : 'N/A';
    $subscription_recur_type = ( isset($subs_data["subs_recurring"]) && isset($recur_type[$subs_data["subs_recurring"]]) ) ? $recur_type[$subs_data["subs_recurring"]] : 'N/A';
    ?>
    <!--breadcrumb wrap-->
    <div class="breadcrumb-wrap">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url() ?>admin/Subscriptions">Subscription</a></li>
            <li class="breadcrumb-item active">View Subscription</li>
        </ol>
    </div>
    <!--breadcrumb wrap close-->
    <!--Filter Section -->

    <div class="form-item-wrap">
        <div class="form-item-title clearfix">
            <h3 class="title">View Subscription</h3>
        </div>
        <!-- title and form upper action end-->
        <div class="white-wrapper clearfix">
            <div class="row">
                <div class="col-md-4 col-sm-4">
                    <div class="stats-overview stat-block">
                        <div class="details">
                            <div class="title">
                                Subscription Title
                            </div>
                            <div class="numbers">
                                <?php echo $subscription_name; ?>
                            </div>
                            <div class="month text-center">
                                <span><?php echo $subscription_price; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class = "col-md-4 col-sm-4">
                    <div class = "stats-overview stat-block">
                        <div class = "details">
                            <div class = "title">
                                Subscription Type
                            </div>
                            <div class = "numbers">
                                <?php echo $subscription_recur_type; ?>
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
                                <?php echo date('M d ,Y', strtotime($subscription_date)); ?>
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
                                <?php echo $subscription_desc; ?>
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

    <!--Filter Section Close-->
</div>
<!--Table listing-->

