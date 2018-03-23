<?php
 $filterArr  = $this->input->get();
 $filterArr  = ( object ) $filterArr;
 $controller = $this->router->fetch_class();
 $method     = $this->router->fetch_method();
 $module     = $this->router->fetch_module();
?>
<link href="<?php echo base_url() ?>public/css/datepicker.min.css" rel='stylesheet'>
<input type="hidden" id="filterVal" value='<?php echo json_encode( $filterArr ); ?>'>
<input type="hidden" id="pageUrl" value='<?php echo base_url().$module.'/'.strtolower( $controller ).'/'.$method; ?>'>


<div class="inner-right-panel">

    <!--breadcrumb wrap-->
    <div class="breadcrumb-wrap">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Notification</li>
        </ol>
    </div>
    <!--breadcrumb wrap close-->

    <!--Filter Section -->
    <div class="white-wrapper">
        <div class="fltr-srch-wrap clearfix">
            <div class="row">
                <div class="col-lg-2 col-sm-3">
                    <div class="display col-sm-space">
                        <select class="selectpicker dispLimit">
                            <option <?php echo ($limit == 10) ? 'Selected' : '' ?> value="10">Display 10</option>
                            <option <?php echo ($limit == 20) ? 'Selected' : '' ?> value="20">Display 20</option>
                            <option <?php echo ($limit == 50) ? 'Selected' : '' ?> value="50">Display 50</option>
                            <option <?php echo ($limit == 100) ? 'Selected' : '' ?> value="100">Display 100</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-4">
                    <div class="srch-wrap col-sm-space">
                        <button class="srch search-icon" style="cursor:default"></button>
                        <a href="javascript:void(0)"> <span class="srch-close-icon searchCloseBtn"></span></a>
                        <input autocomplete="off" type="text" value="<?php echo (isset( $searchlike ) && !empty( $searchlike )) ? $searchlike : '' ?>" class="search-box searchlike" placeholder="Search by push title" id="searchuser" name="search">
                    </div>
                </div>
                <div class="col-lg-2 col-sm-2">
                    <?php if ( isset( $searchlike ) && "" != $searchlike ) { ?>
                         <div class="go_back">Go Back</div>
                     <?php } ?>

                </div>
                <div class="col-lg-4 col-sm-4">
                    <div class="top-opt-wrap text-right">
                        <ul>
                            <li>
                                <a href="javascript:void(0)" title="File Export" class="icon_filter" id="filter-side-wrapper"><img src="public/images/filter.svg"></a>
                            </li>
                            <li>
                                <a <?php echo $permission['notification_add']; ?> href="<?php echo base_url().'admin/notification/add'; ?>" title="Filter" id="filter-side-wrapper" class="icon_filter">
                                    <img src="public/images/add.svg">
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Filter Section Close-->
    <?php
     if ( $this->session->flashdata( 'message' ) != '' ) {
         echo $this->session->flashdata( 'message' );
     }
    ?>

    <!--Filter Wrapper-->
    <div class="filter-wrap">
        <div class="filter_hd clearfix">
            <div class="pull-left">
                <h2 class="fltr-heading">Filter </h2>
            </div>
            <div class="pull-right">
                <span class="close flt_cl" data-dismiss="modal">X</span>
            </div>
        </div>
        <div class="inner-filter-wrap">
            <div class="fltr-field-wrap">
                <label class="admin-label">Platform</label>
                <div class="commn-select-wrap">
                    <select class="selectpicker platform">
                        <option value="">All</option>
                        <option <?php echo ($platform == 1) ? 'Selected' : '' ?> value="1">Android</option>
                        <option <?php echo ($platform == 2) ? 'Selected' : '' ?> value="2">iOS</option>
                    </select>
                </div>
            </div>
            <div class="fltr-field-wrap">
                <label class="admin-label">Push Date</label>
                <div class="inputfield-wrap">
                    <input readonly type="text" value="<?php echo!empty( $startDate ) ? date( 'm/d/Y', strtotime( $startDate ) ) : "" ?>" class="form-date_wrap startDate" data-provide="datepicker" id="from_date" placeholder="From">
                    <input readonly type="text" value="<?php echo!empty( $endDate ) ? date( 'm/d/Y', strtotime( $endDate ) ) : "" ?>" class="form-date_wrap endDate" data-provide="datepicker" id="to_date" placeholder="To">
                </div>

            </div>
            <div class="button-wrap text-center">
                <button type="Submit" class="commn-btn cancel resetfilter">Reset</button>
                <button type="reset" class="commn-btn save applyfilter">Filter</button>
            </div>
        </div>
    </div>
    <!--Filter Wrapper Close-->
    <div class="row">
        <div class="col-lg-6">Total Notifications: <?php echo $totalrows ?></div>
    </div>
    <label id="error">
        <?php $alertMsg = $this->session->flashdata( 'alertMsg' ); ?>
        <div class="alert alert-success" <?php echo (!(isset( $alertMsg ) && !empty( $alertMsg ))) ? "style='display:none'" : "" ?> role="alert">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
            <strong>
                <span class="alertType"><?php echo (isset( $alertMsg ) && !empty( $alertMsg )) ? $alertMsg['type'] : "" ?></span>
            </strong>
            <span class="alertText"><?php echo (isset( $alertMsg ) && !empty( $alertMsg )) ? $alertMsg['text'] : "" ?></span>
        </div>
    </label>
    <!--Table-->
    <div class="white-wrapper">
        <div class="table-responsive custom-tbl">
            <!--table div-->
            <table id="example" class="list-table table table-striped sortable" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th width="50px">S.No</th>
                        <th>Push Title</th>
                        <th>Platform</th>
                        <th>Gender</th>
                        <th>User's Sent Count</th>
                        <th  width="200px" >Added On</th>
                        <?php if ( $permission['action'] ) { ?>
                             <th width="70px" class="text-center" >Action</th>
                         <?php } ?>
                    </tr>
                </thead>
                <tbody>

                    <?php
                     if ( !empty( $notiList ) ) {
                         if ( $page > 1 ) {
                             $i = (($page * $limit) - $limit) + 1;
                         }
                         else {
                             $i = 1;
                         }
                         ?>
                         <?php foreach ( $notiList as $list ) { ?>
                             <tr>
                                 <td><?php echo $i; ?></td>
                                 <td>
                                     <?php echo $list['title'] ?>
                                 </td>
                                 <td><?php echo ($list['platform'] == 1) ? 'All' : (($list['platform'] == 2) ? 'Android' : 'iOS'); ?></td>
                                 <td><?php echo ($list['gender'] == 3) ? 'All' : (($list['gender'] == 1) ? 'Male' : 'Female'); ?></td>
                                 <td><?php echo $list['total_sents'] ?></td>
                                 <td><?php echo mdate( DATE_FORMAT, strtotime( $list['created_at'] ) ); ?></td>
                                 <?php if ( $permission['action'] ) { ?>
                                     <td  class="text-center" >
                                         <a <?php echo $permission['notification_resend']; ?> href="javascript:void(0);" class="table_icon" title="Edit Modal" data-toggle="modal" data-target="#editModal" onclick="$( '#notiToken' ).val( '<?php echo queryStringBuilder( "id=".$list['id'] ); ?>' )"><i class="fa fa-paper-plane" aria-hidden="true"></i></a>
                                         <a <?php echo $permission['notification_delete']; ?> href="javascript:void(0);" class="table_icon" title="Delete" onclick="deleteUser( 'notification',<?php echo DELETED; ?>, '<?php echo encryptDecrypt( $list['id'] ); ?>', 'req/change-user-status', 'Do you really want to delete this notification ?' );">
                                             <i class="fa fa-trash" aria-hidden="true"></i>
                                         </a>
                                     </td>
                                 <?php } ?>
                             </tr>
                             <?php
                             $i++;
                         }
                     }
                     else {
                         ?>
                     <td colspan="9"  class="text-center">No notifications found</td>
                 <?php } ?>
                </tbody>
            </table>
        </div>
        <!-- Pagenation and Display data wrap-->
        <div class="pagination_wrap clearfix">
            <?php echo $links; ?>
        </div>
        <!-- Pagination and Display data wrap-->
    </div>
    <!-- table 1 close-->
</div>
</div>
<!--Edit  Modal Close-->
<div id="editModal" class="modal fade" role="dialog">
    <input type="hidden" id="uid" name="uid" value="">
    <input type="hidden" id="ustatus" name="ustatus" value="">
    <div class="modal-dialog modal-custom">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-alt-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title modal-heading">Resend</h4>
            </div>
            <div class="modal-body">
                <p class="modal-para">Please select the option</p>
            </div>
            <input type="hidden" id="notiToken">
            <div class="modal-footer">
                <div class="button-wrap">
                    <button type="button" class="commn-btn cancel resendPush" data-dismiss="modal">Resend Now</button>
                    <button type="button" class="commn-btn save editPush" >Edit & Resend</button>
                </div>
            </div>

        </div>
    </div>
    <!--Edit Modal Close-->
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="<?php echo base_url() ?>public/js/datepicker.min.js"></script>
    <script src="<?php echo base_url() ?>public/js/notification.js"></script>
