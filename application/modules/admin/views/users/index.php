<?php
 $showAction = $permission['action'];
?>
<link href="<?php echo base_url() ?>public/css/datepicker.min.css" rel='stylesheet'>
<input type="hidden" id="filterVal" value='<?php echo json_encode( $filterVal ); ?>'>
<input type="hidden" id="pageUrl" value='<?php echo base_url().$module.'/'.strtolower( $controller ).'/'.$method; ?>'>
<div class="inner-right-panel">
    <!--breadcrumb wrap-->
    <div class="breadcrumb-wrap">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Users</li>
        </ol>
    </div>
    <!--Filter Section -->
    <div class="fltr-srch-wrap white-wrapper clearfix">
        <div class="row">
            <div class="col-lg-2 col-sm-3">
                <div class="display  col-sm-space">
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
                    <a href="javascript:void(0);"> <span class="srch-close-icon searchCloseBtn"></span></a>
                    <input type="text" maxlength="15" value="<?php echo (isset( $searchlike ) && !empty( $searchlike )) ? $searchlike : '' ?>" class="search-box searchlike" placeholder="Search by name, email or phone no." id="searchuser" name="search">
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
                            <a href="javascript:void(0)" title="Filter" id="filter-side-wrapper" class="icon_filter"><img src="<?php echo base_url() ?>public/images/filter.svg"></a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" title="File Export" class="icon_filter exportCsv"><img src="<?php echo base_url() ?>public/images/export-file.svg"> </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--Filter Section Close-->
    <!--Filter Wrapper-->
    <div class="filter-wrap ">
        <div class="filter_hd clearfix">
            <div class="pull-left">
                <h2 class="fltr-heading">Filter</h2>
            </div>
            <div class="pull-right">
                <span class="close flt_cl" data-dismiss="modal">X</span>
            </div>
        </div>
        <div class="inner-filter-wrap">

            <div class="fltr-field-wrap">
                <label class="admin-label">Status</label>
                <div class="commn-select-wrap">
                    <select class="selectpicker filter status" name="status">
                        <option value="">All</option>
                        <option <?php echo ($status == ACTIVE) ? 'selected' : '' ?> value="1">Active</option>
                        <option <?php echo ($status == BLOCKED) ? 'selected' : '' ?> value="2">Blocked</option>
                    </select>

                </div>
            </div>
            <?php
             $this->load->helper( 'state_helper' );
             $countries = get_country_list();
            ?>
            <div class="fltr-field-wrap">
                <label class="admin-label">Country</label>
                <div class="commn-select-wrap">
                    <select class="selectpicker filter country" name="country" data-live-search="true">
                        <option value="">Select Country</option>
                        <?php
                         if ( !empty( $countries ) ) {
                             foreach ( $countries as $key => $val ) {
                                 ?>
                                 <option <?php
                                 if ( isset( $country ) && $country == $val['id'] ) {
                                     echo "selected='selected'";
                                 }
                                 ?> value="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></option>

                                 <?php
                             }
                         }
                        ?>
                    </select>
                </div>
            </div>
            <div class="fltr-field-wrap">
                <label class="admin-label">Registration Date</label>
                <div class="inputfield-wrap">
                    <input readonly type="text" name="startDate" data-provide="datepicker" value="<?php echo isset( $startDate ) ? $startDate : "" ?>" class="form-control startDate" id="startDate" placeholder="From">
                </div>

            </div>
            <div class="fltr-field-wrap">
                <div class="inputfield-wrap">
                    <input readonly type="text" name="endDate" data-provide="datepicker" value="<?php echo isset( $endDate ) ? $endDate : "" ?>" class="form-control endDate" id="endDate" placeholder="To">
                </div>
            </div>

            <div class="button-wrap text-center">
                <button type="reset" class="commn-btn cancel resetfilter" id="resetbutton">Reset</button>
                <input type="submit" class="commn-btn save applyFilterUser" id="filterbutton"name="filter" value="Apply">
            </div>

        </div>
    </div>
    <!--Filter Wrapper Close-->

    <p class="tt-count">Total Users: <?php echo $totalrows ?></p>

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
    <div class="white-wrapper">
        <div class="table-responsive custom-tbl">
            <!--table div-->
            <table id="example" class="list-table table table-striped sortable" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th align='left' width="50px">S.No</th>
                        <th align='left'  width="30%">
                            <a href="<?php base_url() ?>admin/users?data=<?php echo queryStringBuilder( "field=name&order=".$order_by.$get_query ); ?>" class="sort <?php echo $order_by_name; ?>">Name</a>
                        </th>
                        <th>Email Id</th>
                        <th  width="150px">Mobile Number</th>
                        <th  width="180px">
                            <a href="<?php base_url() ?>admin/users?data=<?php echo queryStringBuilder( "field=registered&order=".$order_by.$get_query ); ?>" class="sort <?php echo $order_by_date; ?>">Registered On</a>
                        </th>
                        <th width="80px">status</th>
                        <?php if ( $showAction ) { ?>
                             <th width="80px" class="text-center">Action</th>
                         <?php } ?>
                    </tr>
                </thead>
                <tbody id="table_tr">
                    <?php
                     if ( isset( $userlist ) && count( $userlist ) ) {
                         if ( $page > 1 ) {
                             $i = (($page * $limit) - $limit) + 1;
                         }
                         else {
                             $i = 1;
                         }
                         foreach ( $userlist as $value ) {
                             ?>

                             <tr id ="remove_<?php echo $value['user_id']; ?>" >
                                 <td align='left'><span class="serialno"><?php echo $i; ?></span></td>
                                 <td align='left'>
                                     <?php if ( $permission['permissions']['user_detail'] ) { ?>
                                         <a href="<?php echo base_url() ?>admin/users/detail?data=<?php echo queryStringBuilder( "id=".$value['user_id'] ); ?>"><?php echo ucfirst( $value['first_name'] ).' '.ucfirst( $value['last_name'] ); ?></a>
                                         <?php
                                     }
                                     else {
                                         ?>
                                         <?php echo ucfirst( $value['first_name'] ).' '.ucfirst( $value['last_name'] ); ?>
                                     <?php } ?>
                                 </td>
                                 <td><?php echo $value['email']; ?></td>
                                 <td><?php echo!empty( $value['phone'] ) ? $value['phone'] : "Not Available"; ?></td>
                                 <td><?php echo mdate( DATE_FORMAT, strtotime( $value['registered_date'] ) ); ?></td>
                                 <td id ="status_<?php echo $value['user_id']; ?>"><?php echo ($value['status'] == ACTIVE) ? "Active" : "Blocked"; ?></td>
                                 <?php if ( $showAction ) { ?>
                                     <td  class="text-center">
                                         <?php
                                         if ( $permission['permissions']['user_detail'] ) {
                                             if ( 0 ) {
                                                 ?>
                                                 <a href="<?php echo base_url(); ?>admin/posts?data=<?php echo queryStringBuilder( "id=".$value['user_id'] ); ?>" class="table_icon" title="Film"><i class="fa fa-film" ></i></a>
                                                 <?php
                                             }
                                             else {
                                                 ?>
                                                 <a href="javascript:alert('User have no post!!')" class="table_icon" title="Film"><i class="fa fa-film" ></i></a>
                                                 <?php
                                             }
                                         }
                                         echo '<span '.$permission['user_block'].'>';
                                         if ( BLOCKED == $value['status'] ) {
                                             ?>
                                             <a href="javascript:void(0);" id ="unblock_<?php echo $value['user_id']; ?>" class="table_icon" title="Unblock"><i class="fa fa-unlock" aria-hidden="true" onclick="blockUser( 'user',<?php echo ACTIVE; ?>, '<?php echo encryptDecrypt( $value['user_id'] ); ?>', 'req/change-user-status', 'Do you really want to active this user?', 'Unblock' );"></i></a>
                                             <a href="javascript:void(0);"  id ="block_<?php echo $value['user_id']; ?>" style="display:none;" class="table_icon" title="Block"><i class="fa fa-ban" aria-hidden="true" onclick="blockUser( 'user',<?php echo BLOCKED; ?>, '<?php echo encryptDecrypt( $value['user_id'] ); ?>', 'req/change-user-status', 'Do you really want to block this user?', 'Block' );"></i></a>
                                             <?php
                                         }
                                         else {
                                             ?>
                                             <a href="javascript:void(0);" id ="block_<?php echo $value['user_id']; ?>" class="table_icon" title="Block"><i class="fa fa-ban" aria-hidden="true" onclick="blockUser( 'user',<?php echo BLOCKED; ?>, '<?php echo encryptDecrypt( $value['user_id'] ); ?>', 'req/change-user-status', 'Do you really want to block this user?', 'Block' );"></i></a>
                                             <a href="javascript:void(0);" id ="unblock_<?php echo $value['user_id']; ?>" style="display:none;" class="table_icon" title="Unblock"><i class="fa fa-unlock" aria-hidden="true" onclick="blockUser( 'user',<?php echo ACTIVE; ?>, '<?php echo encryptDecrypt( $value['user_id'] ); ?>', 'req/change-user-status', 'Do you really want to active this user?', 'Unblock' );"></i></a>
                                             <?php
                                         }
                                         echo "</span>";
                                         echo '<span '.$permission['user_delete'].'>';
                                         ?>
                                         <a href="javascript:void(0);" class="table_icon" title="Delete"><i class="fa fa-trash" aria-hidden="true" onclick="deleteUser( 'user',<?php echo DELETED; ?>, '<?php echo encryptDecrypt( $value['user_id'] ); ?>', 'req/change-user-status', 'Do you really want to delete this user?' );"></i></a>
                                         <?php echo "</span>"; ?>
                                     </td>
                                 <?php } ?>
                             </tr>
                             <?php
                             $i++;
                         }
                     }
                     else {
                         ?>
                         <tr><td colspan="9" class="text-center">No result found.</td></tr
                     <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="pagination_wrap clearfix">
            <?php echo $link; ?>
        </div>

    </div>
</div>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="<?php echo base_url() ?>public/js/datepicker.min.js"></script>
<script>

                                $( document ).ready( function () {

                                    var nowTemp = new Date();
                                    var now = new Date( nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0 );

                                    var checkin = $( '#startDate' ).datepicker( {
                                        onRender: function ( date ) {
                                            return date.valueOf() > now.valueOf() ? 'disabled' : '';
                                        }
                                    } ).on( 'changeDate', function ( ev ) {
                                        $( '#endDate' ).val( '' );
                                        if ( ev.date.valueOf() < checkout.date.valueOf() ) {
                                            var newDate = new Date( ev.date )
                                            newDate.setDate( newDate.getDate() );
                                            checkout.setValue( newDate );
                                        }
                                        checkin.hide();
                                        $( '#endDate' )[0].focus();
                                    } ).data( 'datepicker' );
                                    var checkout = $( '#endDate' ).datepicker( {
                                        onRender: function ( date ) {
                                            return date.valueOf() < checkin.date.valueOf() || date.valueOf() > now.valueOf() ? 'disabled' : '';
                                        }
                                    } ).on( 'changeDate', function ( ev ) {
                                        checkout.hide();
                                    } ).data( 'datepicker' );


                                    //on datepicker 2 focus
                                    $( '#datepicker_2' ).focus( function () {
                                        if ( $( '#datepicker_1' ).val() == '' ) {
                                            checkout.hide();
                                        }
                                    } );
                                    //prevent typing datepicker's input
                                    $( '#datepicker_2, #datepicker_1' ).keydown( function ( e ) {
                                        e.preventDefault();
                                        return false;
                                    } );

                                } );
</script>