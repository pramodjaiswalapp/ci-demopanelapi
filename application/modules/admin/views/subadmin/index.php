<?php
 $filterArr = $this->input->get();
 $filterArr = ( object ) $filterArr;
?>
<link href="<?php echo base_url() ?>public/css/datepicker.min.css" rel='stylesheet'>
<input type="hidden" id="filterVal" value='<?php echo json_encode( $filterArr ); ?>'>
<input type="hidden" id="pageUrl" value='<?php echo base_url().$module.'/'.strtolower( $controller ).'/'.$method; ?>'>
<div class="inner-right-panel">
    <!--breadcrumb wrap-->
    <div class="breadcrumb-wrap">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Sub-admin</li>
        </ol>
    </div>
    <!--Filter Section -->
    <div class="fltr-srch-wrap white-wrapper clearfix">
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
                    <input type="text" maxlength="15" value="<?php echo (isset( $searchlike ) && !empty( $searchlike )) ? $searchlike : '' ?>" class="search-box searchlike" placeholder="Search by name or email" id="searchuser" name="search">
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
                            <a href="admin/subadmin/add" title="Add admin" class="icon_filter"><img src="<?php echo base_url() ?>public/images/add.svg"></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--Filter Section Close-->
    <div class="row">
        <div class="col-lg-6">Total Sub-admin: <?php echo $totalrows ?></div>
    </div>
    <!--Table-->
    <label id="error">
        <?php $alertMsg  = $this->session->flashdata( 'alertMsg' ); ?>
        <div class="alert alert-success" <?php echo (!(isset( $alertMsg ) && !empty( $alertMsg ))) ? "style='display:none'" : "" ?> role="alert">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
            <strong>
                <span class="alertType"><?php echo (isset( $alertMsg ) && !empty( $alertMsg )) ? $alertMsg['type'] : "" ?></span>
            </strong>
            <span class="alertText"><?php echo (isset( $alertMsg ) && !empty( $alertMsg )) ? $alertMsg['text'] : "" ?></span>
        </div>
    </label>
    <!-- Content -->
    <input type="hidden" id="filterparams" value='<?php echo json_encode( $filterArr ); ?>'>
    <section class="content-wrapper clearfix">

        <div class="col-lg-6 col-sm-6 hide-mobile"></div>
        <div class="clear"></div>
        <!-- Content section -->
        <div class="white-wrapper p-md">
            <div class="table-responsive custom-tbl">
                <table class="table outlet-table table-striped m-n">
                    <thead>
                        <tr>
                            <th width='20px'>S.No</th>
                            <th width="180px">
                                <a href="<?php base_url() ?>admin/subadmin?data=<?php echo queryStringBuilder( "field=name&order=".$order_by.$get_query ); ?>" class="sort <?php echo $order_by_name; ?>">Name</a>
                            </th>
                            <th><a href="<?php base_url() ?>admin/subadmin?data=<?php echo queryStringBuilder( "field=email&order=".$order_by.$get_query ); ?>" class="sort <?php echo $order_by_email; ?>">Email</a> </th>
                            <th width='100px'>Status </th>
                            <th width='180px'><a href="<?php base_url() ?>admin/subadmin?data=<?php echo queryStringBuilder( "field=added&order=".$order_by.$get_query ); ?>" class="sort <?php echo $order_by_date; ?>">Added On</a> </th>
                            <th width='80px' class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                         $pagecount = 10;
                         $i         = (($page * $pagecount) - $pagecount) + 1;
                        ?>
                        <?php
                         if ( !empty( $data ) ) {
                             foreach ( $data as $key => $data ) {
                                 ?>
                                 <tr>
                                     <td><?php echo $i; ?></td>
                                     <td align="left">
                                         <a href="admin/subadmin/view?data=<?php echo queryStringBuilder( "id=".$data['admin_id'] ); ?>">
                                             <?php echo $data['admin_name'] ?>
                                         </a>
                                     </td>
                                     <td align="left">
                                         <?php echo $data['admin_email'] ?>
                                     </td>
                                     <td>
                                         <?php echo (BLOCKED == $data['status'] ) ? 'Blocked' : 'Active' ?>
                                     </td>
                                     <td><?php echo mdate( DATE_FORMAT, strtotime( $data['create_date'] ) ); ?></td>
                                     <td width='80px'  class="text-center" >
                                         <a class="table_icon" title="Edit" href="admin/subadmin/edit?data=<?php echo queryStringBuilder( "id=".$data['admin_id'] ); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                         <?php if ( BLOCKED != $data['status'] ) { ?>
                                             <a href="javascript:void(0);" id="block_<?php echo $data['admin_id']; ?>" class="table_icon" title="Block"><i class="fa fa-ban" aria-hidden="true" onclick="blockUser( 'subadmin', 2, '<?php echo encryptDecrypt( $data['admin_id'] ); ?>', 'req/change-user-status', 'Do you really want to block this Sub-admin?', 'Block' );"></i></a>
                                             <a href="javascript:void(0);" id="unblock_<?php echo $data['admin_id']; ?>" style="display:none" class="table_icon" title="Unblock"><i class="fa fa-unlock" aria-hidden="true" onclick="blockUser( 'subadmin', 1, '<?php echo encryptDecrypt( $data['admin_id'] ); ?>', 'req/change-user-status', 'Do you really want to unblock this Sub-admin?', 'Unblock' );"></i></a>
                                             <?php
                                         }
                                         else {
                                             ?>
                                             <a href="javascript:void(0);"  style="display:none" id="block_<?php echo $data['admin_id']; ?>" class="table_icon" title="Block"><i class="fa fa-ban" aria-hidden="true" onclick="blockUser( 'subadmin', 2, '<?php echo encryptDecrypt( $data['admin_id'] ); ?>', 'req/change-user-status', 'Do you really want to block this Sub-admin?', 'Block' );"></i></a>
                                             <a href="javascript:void(0);" id="unblock_<?php echo $data['admin_id']; ?>"  class="table_icon" title="Unblock"><i class="fa fa-unlock" aria-hidden="true" onclick="blockUser( 'subadmin', 1, '<?php echo encryptDecrypt( $data['admin_id'] ); ?>', 'req/change-user-status', 'Do you really want to unblock this Sub-admin?', 'Unblock' );"></i></a>
                                         <?php } ?>


                                         <a href="javascript:void(0);" class="table_icon" title="Delete"><i class="fa fa-trash" aria-hidden="true" onclick="deleteUser( 'subadmin', 3, '<?php echo encryptDecrypt( $data['admin_id'] ); ?>', 'req/change-user-status', 'Do you really want to delete this Sub-admin?' );"></i></a>
                                     </td>
                                 </tr>
                                 <?php
                                 $i++;
                             }
                         }
                         else {
                             ?>
                             <tr>
                                 <td colspan="9" class="text-center">No Sub-admin exist</td>
                             </tr>
                         <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="clear"></div>
            <nav class="pagination-wrapper m-t-md">
                <ul class="pagination">
                    <?php
                     if ( isset( $links ) ) {
                         echo $links;
                     }
                    ?>
                </ul>
            </nav>
        </div>
        <!-- Content section End -->
    </section>
</div>

<div class="modal fade modelbody bd-example-modal-sm" id="Deletemodal" role="dialog">
    <div class="modal-dialog width" role="document">
        <div class="modal-content">
            <div class="modal-body">
                Are you sure you want to delete selected Subadmin ?
            </div>
            <div class="modal-footer">
                <div class="col-lg-6 col-sm-6">

                    <button type="button" class="custom-btn cancel" data-dismiss="modal" onclick="window.location.href = '/Subadmin/'">Cancel</button>
                </div>
                <div class="col-lg-6 col-sm-6">

                    <input type="hidden" id="hiddenuser" value="" />
                    <button type="button" class="custom-btn save delete-subadmin">Yes</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modelbody bd-example-modal-sm" id="Blockmodal" role="dialog">
    <div class="modal-dialog width" role="document">
        <div class="modal-content">
            <div class="modal-body">
                Are you sure you want to block selected Subadmin ?
            </div>
            <div class="modal-footer">
                <div class="col-lg-6 col-sm-6">
                    <button type="button" class="custom-btn cancel" data-dismiss="modal">Cancel</button>
                </div>
                <div class="col-lg-6 col-sm-6">
                    <input type="hidden" id="blockuserid" value="" />
                    <input type="hidden" id="blockuserstatus" value="" />
                    <button type="button" class="custom-btn save block-admin">Yes</button>
                </div>
            </div>
        </div>
    </div>
</div>



