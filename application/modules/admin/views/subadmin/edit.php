<?php
 $userPermission    = isset( $permission[1] ) ? $permission[1] : array ();
 $versionPermission = isset( $permission[2] ) ? $permission[2] : array ();
 $notiPermission    = isset( $permission[3] ) ? $permission[3] : array ();

 $temp       = json_decode( $permission, TRUE );
 $permission = $temp['permission'];
?>

<link href="public/css/form-roles.css" rel='stylesheet'>
<body>
    <!-- Content -->
    <section class="content-wrapper clearfix">
        <div class="upper-head-panel m-b-lg clearfix">
            <ul class="breadcrumb reward-breadcrumb">
                <li><a href="admin/subadmin">Sub Admins</a></li>
                <li class="active">Update Sub-admin</li>
            </ul>
        </div>
        <div class="clear"></div>
        <div class="col-sm-12">
            <div class="adminRoles-wrapper">
                <div class="form-item-title clearfix">
                    <h3 class="title">Update the Sub-admin detail</h3>
                </div>
                <!-- title and form upper action end-->
                <?php echo form_open_multipart( '', array ('id' => 'subadmin_add') ); ?>
                <input type='hidden' value='<?php echo encryptDecrypt( $admin_id ); ?>' name='token' >
                <div class="form-ele-wrapper clearfix">
                    <div class="row">
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group">
                                <label class="admin-label">Admin Name</label>
                                <div class="input-holder">
                                    <input type="text" class="form-control material-control" maxlength="100" value="<?php echo $admindetail['admin_name'] ?>" name="name" placeholder="* Sub-admin Name" value="<?php echo set_value( 'name' ); ?>">
                                    <?php echo form_error( 'name', '<label class="alert-danger">', '</label>' ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group">
                                <label class="admin-label">Email</label>
                                <div class="input-holder">
                                    <input type="text" class="form-control material-control" maxlength="100" value="<?php echo $admindetail['admin_email'] ?>" name="email" placeholder="* Sub-admin Email" value="<?php echo set_value( 'email' ); ?>">
                                    <?php echo form_error( 'email', '<label class=" alert-danger">', '</label>' ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group">
                                <label class="admin-label">Password</label>
                                <div class="input-holder">
                                    <input type="text" class="form-control material-control" maxlength="16" name="newpassword" placeholder="* New Password">
                                    <?php echo form_error( 'password', '<label class=" alert-danger">', '</label>' ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group">
                                <label class="admin-label">Status</label>
                                <div class="commn-select-wrap">
                                    <select class="selectpicker" name="status">
                                        <option value="">Select</option>
                                        <option <?php echo ($admindetail['status'] == 1) ? 'Selected' : '' ?> value="1">Active</option>
                                        <option <?php echo ($admindetail['status'] == 2) ? 'Selected' : '' ?> value="2">Inactive</option>
                                    </select>
                                    <?php echo form_error( 'status', '<label class="alert-danger">', '</label>' ); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 clearfix">
                        <div class="adminRoles-wrapper p-md m-b-lg">
                            <div class="form-ele-wrapper clearfix">
                                <div class="row">
                                    <div class="col-lg-12"><h2 class="title-box m-t-n p-t-20">Is Admin :</h2></div>
                                    <div class="col-lg-12">
                                        <div class = "custom-check main-check">
                                            <input <?php echo in_array( "admin", $permission ) ? "checked" : ""; ?>  id = "is_admin" name = "is_admin" class="is_admin" value = "admin" type="checkbox">
                                            <label for="is_admin"><span></span>Admin</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- ADMIN NEW-->


                    <input type="hidden" name="permission" id="permission" value="">
                    <div class="col-sm-12 clearfix" id="subAdminDiv">
                        <div class="adminRoles-wrapper p-md m-b-lg">
                            <div class="form-ele-wrapper clearfix">
                                <div class="row">
                                    <div class="col-lg-12"><h2 class="title-box m-t-n p-t-20">Sub-admin Roles :</h2></div>
                                    <div class="col-lg-12">
                                        <?php
                                         foreach ( $acl_config as $key => $per_array ) {
                                             ?>
                                             <div class = "custom-check main-check">
                                                 <input data-child="child_<?php echo $key; ?>" id = "main-check<?php echo $key; ?>" name = "<?php echo $key; ?>" class="parent_check parent_<?php echo $key; ?>" value = "1" type="checkbox">
                                                 <label for="main-check<?php echo $key; ?>"><span></span><?php echo $this->lang->line( $key ); ?> </label>
                                                 <ul class="check-column">
                                                     <?php
                                                     foreach ( $per_array as $per_key => $per_value ) {
                                                         ?>
                                                         <li>
                                                             <div class="custom-check">
                                                                 <input <?php echo in_array( $per_key, $permission ) ? "checked" : ""; ?> data-parent="parent_<?php echo $key; ?>"  id="subcheck_<?php echo $per_key ?>" name="<?php echo $per_key ?>" value="<?php echo $per_key ?>" class="child child_<?php echo $key; ?>" type="checkbox" >
                                                                 <label for="subcheck_<?php echo $per_key ?>"><span></span><?php echo $per_value['text']; ?></label>
                                                             </div>
                                                         </li>
                                                         <?php
                                                     }
                                                     ?>
                                                 </ul>
                                             </div>
                                         <?php }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ADMIN NEW END-->

                    <?php if ( 0 ) { ?>
                         <div class="col-sm-12 clearfix">
                             <div class="adminRoles-wrapper p-md m-b-lg">
                                 <div class="form-ele-wrapper clearfix">
                                     <div class="row">
                                         <div class="col-lg-12"><h2 class="title-box m-t-n p-t-20">Sub-admin Roles :</h2></div>
                                         <div class="col-lg-12">
                                             <div class="custom-check main-check">
                                                 <input id="main-check1" name="user" onchange="permission( 'user' )"  value="1" <?php echo (!empty( $userPermission )) ? 'checked' : '' ?> type="checkbox">
                                                 <label for="main-check1"><span></span>Manage User </label>
                                                 <ul class="check-column">
                                                     <li>
                                                         <div class="custom-check">
                                                             <input id="subcheck1-1" name="permission[user][view]" <?php echo (!empty( $userPermission['viewp'] )) ? 'checked' : '' ?> <?php
                                                             echo (!empty( $userPermission )) ? '' : 'disabled="true"'
                                                             ?> value="1" class="user" type="checkbox" >
                                                             <label for="subcheck1-1"><span></span>View </label>
                                                         </div>
                                                     </li>
                                                     <li>
                                                         <div class="custom-check">
                                                             <input id="subcheck1-2" name="permission[user][block]" <?php echo (!empty( $userPermission['blockp'] )) ? 'checked' : '' ?> value="1"  <?php
                                                             echo (!empty( $userPermission )) ? '' : 'disabled="true"'
                                                             ?> class="user" type="checkbox" >
                                                             <label for="subcheck1-2"><span></span>Block  </label>
                                                         </div>
                                                     </li>
                                                     <li>
                                                         <div class="custom-check">
                                                             <input id="subcheck1-3" name="permission[user][delete]" <?php echo (!empty( $userPermission['deletep'] )) ? 'checked' : '' ?> value="1" class="user" <?php
                                                             echo (!empty( $userPermission )) ? '' : 'disabled="true"'
                                                             ?> type="checkbox" >
                                                             <label for="subcheck1-3"><span></span>Delete</label>
                                                         </div>
                                                     </li>
                                                 </ul>
                                             </div>
                                             <div class="clear"></div>
                                             <div class="custom-check main-check">
                                                 <input id="main-check2" name="version" <?php echo (!empty( $versionPermission )) ? 'checked' : '' ?> onchange="permission( 'Version' )" value="2" type="checkbox">
                                                 <label for="main-check2"><span></span>Manage Version</label>
                                                 <ul class="check-column">
                                                     <li>
                                                         <div class="custom-check">
                                                             <input id="subcheck2-4" name="permission[version][add]" <?php echo (!empty( $versionPermission['addp'] )) ? 'checked' : '' ?> value="1"  <?php
                                                             echo (!empty( $versionPermission )) ? '' : 'disabled="true"'
                                                             ?> class="Version" type="checkbox">
                                                             <label for="subcheck2-4"><span></span>Add</label>
                                                         </div>
                                                     </li>
                                                     <li>
                                                         <div class="custom-check">
                                                             <input id="subcheck2-2" name="permission[version][edit]" <?php echo (!empty( $versionPermission['editp'] )) ? 'checked' : '' ?> value="1" <?php
                                                             echo (!empty( $versionPermission )) ? '' : 'disabled="true"'
                                                             ?> class="Version" type="checkbox">
                                                             <label for="subcheck2-2"><span></span>Edit </label>
                                                         </div>
                                                     </li>
                                                     <li>
                                                         <div class="custom-check">
                                                             <input id="subcheck2-3" name="permission[version][delete]" <?php echo (!empty( $versionPermission['deletep'] )) ? 'checked' : '' ?> value="1" <?php
                                                             echo (!empty( $versionPermission )) ? '' : 'disabled="true"'
                                                             ?> class="Version" type="checkbox">
                                                             <label for="subcheck2-3"><span></span>Delete </label>
                                                         </div>
                                                     </li>

                                                 </ul>
                                             </div>
                                             <div class="clear"></div>
                                             <div class="custom-check main-check">
                                                 <input id="main-check3" name="notification" onchange="permission( 'Notification' )" <?php echo (!empty( $notiPermission )) ? 'checked' : '' ?> value="3"  type="checkbox">
                                                 <label for="main-check3"><span></span>Manage Notifications </label>
                                                 <ul class="check-column">
                                                     <li>
                                                         <div class="custom-check">
                                                             <input id="subcheck3-1"  name="permission[notification][add]" <?php echo (!empty( $notiPermission['addp'] )) ? 'checked' : '' ?> value="1" <?php
                                                             echo (!empty( $notiPermission )) ? '' : 'disabled="true"'
                                                             ?> class="Notification"  type="checkbox">
                                                             <label for="subcheck3-1"><span></span>Add </label>
                                                         </div>
                                                     </li>
                                                     <li>
                                                         <div class="custom-check">
                                                             <input id="subcheck3-3"  name="permission[notification][edit]" <?php echo (!empty( $notiPermission['editp'] )) ? 'checked' : '' ?> value="1" <?php
                                                             echo (!empty( $notiPermission )) ? '' : 'disabled="true"'
                                                             ?> class="Notification"  type="checkbox">
                                                             <label for="subcheck3-3"><span></span>Edit or Resend</label>
                                                         </div>
                                                     </li>
                                                     <li>
                                                         <div class="custom-check">
                                                             <input id="subcheck3-4"  name="permission[notification][delete]" <?php echo (!empty( $notiPermission['deletep'] )) ? 'checked' : '' ?> value="1" <?php
                                                             echo (!empty( $notiPermission )) ? '' : 'disabled="true"'
                                                             ?> class="Notification"  type="checkbox">
                                                             <label for="subcheck3-4"><span></span>Delete</label>
                                                         </div>
                                                     </li>
                                                 </ul>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>

                     <?php } ?>
                </div>
            </div>
            <div class="col-sm-12 col-xs-12">
                <div class="form-ele-action-bottom-wrap btns-center clearfix">
                    <div class="button-wrap text-center">
                        <button type="button"  onclick="window.location.href = '<?php echo base_url() ?>admin/subadmin'"class="commn-btn cancel">Cancel</button>
                        <button type="button" id="save_button" class="commn-btn save">Save</button>
                    </div>
                </div>
            </div>
            <!--form ele wrapper end-->
            <!--close form view   -->
            <?php echo form_close(); ?>
    </section>
</body>
<script>
    $( '.parent_check' ).click( function () {
        var child = "." + $( this ).attr( "data-child" );
        if ( $( this ).is( ":checked" ) ) {
            $( child ).attr( "checked", "checked" );
        }
        else {
            $( child ).removeAttr( "checked" );
        }
    } );


    $( ".child" ).click( function () {
        var parent_check = false;

        var parent = "." + $( this ).attr( "data-parent" );
        var child = $( parent ).attr( "data-child" );

        $( "." + child ).each( function ( k, v ) {
            if ( $( v ).is( ":checked" ) ) {
                parent_check = true;
            }
        } );

        if ( parent_check ) {
            $( parent ).attr( "checked", "checked" );
        }
        else {
            $( parent ).removeAttr( "checked" );
        }
    } );


    $( "#save_button" ).click( function () {
        var obj = [ ];

        if ( !$( "#is_admin" ).is( ":checked" ) ) {
            $( ".child" ).each( function ( key, value ) {
                if ( $( value ).is( ":checked" ) ) {
                    obj.push( $( value ).val() );
                }
            } );
        }
        else {
            obj.push( "admin" );
        }

        $( "#permission" ).val( JSON.stringify( obj ) );
        $( "#subadmin_add" ).submit();
    } );


    var parentCheck = function () {
        if ( !$( "#is_admin" ).is( ":checked" ) ) {
            $( ".child" ).each( function ( key, value ) {
                var parent = "." + $( this ).attr( "data-parent" );
                if ( $( value ).is( ":checked" ) ) {
                    $( parent ).attr( "checked", "checked" );
                }
            } );
        }
        else {
            $( "#subAdminDiv" ).css( "display", "none" );
        }
    };

    $( window ).load( function () {
        parentCheck();
    } );


    $( "#is_admin" ).click( function () {
        if ( $( this ).is( ":checked" ) ) {
            $( "#subAdminDiv" ).hide( "slow", "swing" );
            $( 'input[type="checkbox"]' ).each( function ( key, value ) {
                $( value ).attr( "disabled", true );
            } );
        }
        else {
            $( "#subAdminDiv" ).show( "slow", "swing" );
            $( 'input[type="checkbox"]' ).each( function ( key, value ) {
                $( value ).removeAttr( "disabled" );
            } );
        }
        $( this ).removeAttr( "disabled" );
    } );
</script>