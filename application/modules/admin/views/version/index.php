<?php
 $filterArr = $this->input->get();
 $filterArr = ( object ) $filterArr;
?>
<link href="<?php echo base_url() ?>public/css/datepicker.min.css" rel='stylesheet'>
<input type="hidden" id="filterVal" value='<?php echo json_encode( $filterArr ); ?>'>
<input type="hidden" id="pageUrl" value='<?php echo base_url().$module.'/'.strtolower( $controller ).'/'.$method; ?>'>
<input type="hidden" value="<?php echo $csrfToken; ?>" name="csrf" id="csrf">
<div class="inner-right-panel">
    <!--breadcrumb wrap-->
    <div class="breadcrumb-wrap">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Manage Version</li>
        </ol>
    </div>
    <!--breadcrumb wrap close-->
    <!--Filter Section -->
    <div class="fltr-srch-wrap clearfix white-wrapper">
        <div class="row">

            <div class="col-lg-4 col-sm-4">
                <form action="">
                    <div class="srch-wrap col-sm-space">
                        <button class="srch search-icon" style="cursor:default"></button>
                        <a href="javascript:void(0)"> <span class="srch-close-icon searchCloseBtn"></span></a>
                        <input type="text" value="<?php echo (isset( $searchlike ) && !empty( $searchlike )) ? $searchlike : '' ?>" class="search-box searchlike" placeholder="Search by version name or title" id="searchuser" name="search">
                    </div>
                </form>
            </div>
            <div class="col-lg-2 col-sm-2">
                <?php if ( isset( $searchlike ) && "" != $searchlike ) { ?>
                     <div class="go_back">Go Back</div>
                 <?php } ?>

            </div>
            <div class="col-lg-6 col-sm-6">
                <div class="top-opt-wrap text-right">
                    <ul>
                        <li>
                            <a <?php echo $permission['version_add']; ?> href="admin/version/add" title="Add Content" class="icon_filter"><img src="<?php echo base_url() ?>public/images/add.svg"> </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!--Filter Section Close-->
    <div class="row">
        <div class="col-lg-6">Total Content: <?php echo $totalrows ?></div>
    </div>
    <!--Table-->
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
    <div class="clearfix white-wrapper">
        <div class="table-responsive custom-tbl">
            <!--table div-->
            <table id="example" class="list-table table table-striped sortable" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th width="50px">S.No</th>
                        <th width="15%">
                            <a href="<?php base_url() ?>admin/version?data=<?php echo queryStringBuilder( "field=name&order=".$order_by.$get_query ); ?>" class="sort <?php echo $order_by_name; ?>">Version Name</a>
                        </th>
                        <th><a href="<?php base_url() ?>admin/version?data=<?php echo queryStringBuilder( "field=title&order=".$order_by.$get_query ); ?>" class="sort <?php echo $order_by_title; ?>">title</a></th>
                        <th width="25%">Description</th>
                        <th width="80px">Platform</th>
                        <th width="80px">Update Type</th>
                        <th width="140px">Is Current Version</th>
                        <th width="200px">
                            <a href="<?php base_url() ?>admin/version?data=<?php echo queryStringBuilder( "field=added&order=".$order_by.$get_query ); ?>" class="sort <?php echo $order_by_date; ?>">Created Date</a>
                        </th>
                        <?php if ( $permission['action'] ) { ?>
                             <th width="70px" class="text-center">Action</th>
                         <?php } ?>
                    </tr>

                </thead>
                <tbody id="table_tr">
                    <?php
                     if ( isset( $versions['result'] ) && count( $versions['result'] ) > 0 ):
                         if ( $page > 1 ) {
                             $i = (($page * $limit) - $limit) + 1;
                         }
                         else {
                             $i = 1;
                         }
                         foreach ( $versions['result'] as $key => $value ):
                             ?>

                         <td><?php echo $i++; ?></td>
                         <td><?php echo $value['version_name']; ?></td>
                         <td><?php echo $value['versiob_title']; ?></td>
                         <td><?php
                             if ( !empty( $value['version_desc'] ) ) {
                                 echo substr( $value['version_desc'], 0, 155 );
                                 if ( strlen( $value['version_desc'] ) > 154 ) {
                                     echo '...';
                                 }
                             }
                             ?></td>
                         <td><?php echo ($value['platform'] == ANDROID) ? "Andorid" : "Iphone"; ?></td>
                         <td><?php echo ($value['update_type'] == NORMAL) ? "Normal" : (($value['update_type'] == SKIPPABLE) ? "Skippable" : "Forcefully"); ?></td>
                         <td><?php echo ($value['is_cur_version'] == YES) ? "Yes" : "No"; ?></td>
                         <td><?php echo mdate( DATE_FORMAT, strtotime( $value['create_date'] ) ); ?></td>
         <?php if ( $permission['action'] ) { ?>
                             <td  class="text-center">
                                 <a <?php echo $permission['version_edit']; ?> class="table_icon" title="Edit" href="<?php echo base_url() ?>admin/version/edit?data=<?php echo queryStringBuilder( "id=".$value['vid'] ); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                 <a <?php echo $permission['version_delete']; ?> href="javascript:void(0);" class="table_icon" title="Delete"><i class="fa fa-trash" aria-hidden="true" onclick="deleteUser( 'version',<?php echo DELETED; ?>, '<?php echo encryptDecrypt( $value['vid'] ); ?>', 'req/change-user-status', 'Do you really want to delete this version?' );"></i></a>
                             </td>
                         <?php } ?>
                         </tr>
                         <?php
                     endforeach;
                 else:
                     echo '<tr><td colspan="9" class="text-center">No result found.</td></tr>';
                 endif;
                ?>
                </tbody>
            </table>
        </div>
        <div class="pagination_wrap clearfix">
<?php echo $link; ?>
        </div>
    </div>
    <!-- table 1 close-->
</div>
<!--Table listing-->
