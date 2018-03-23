<link href="public/css/form-roles.css" rel='stylesheet'>

<body>
    <!-- Content -->
    <section class="content-wrapper clearfix">
        <div class="upper-head-panel m-b-lg clearfix">
            <ul class="breadcrumb reward-breadcrumb">
                <li><a href="admin/subadmin">Sub Admins</a></li>
                <li class="active">Add Sub-admin</li>
            </ul>
        </div>
        <div class="clear"></div>
        <div class="col-sm-12">
            <div class="adminRoles-wrapper">
                <div class="form-item-title clearfix">
                    <h3 class="title">Fill the below form</h3>
                </div>
                <!-- title and form upper action end-->
                <?php echo form_open_multipart( '', array ('id' => 'subadmin_add') ); ?>
                <input type="hidden" name="permission" id="permission" value="">
                <div class="white-wrapper clearfix">
                    <div class="row">
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="admin-label">Admin Name</label>
                                <div class="input-holder">
                                    <input type="text" class="form-control material-control" maxlength="30" name="name" placeholder="* Sub-admin Name" value="<?php echo set_value( 'name' ); ?>">
                                    <?php echo form_error( 'name', '<label class="alert-danger">', '</label>' ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="admin-label">Email</label>
                                <div class="input-holder">
                                    <input type="text" class="form-control material-control" maxlength="30" name="email" placeholder="* Sub-admin Email" value="<?php echo set_value( 'email' ); ?>">
                                    <?php echo form_error( 'email', '<label class=" alert-danger">', '</label>' ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="admin-label">Password</label>
                                <div class="input-holder">
                                    <input type="text" class="form-control material-control" maxlength="16" name="password" placeholder="* Sub-admin Password" value="<?php echo set_value( 'password' ); ?>">
                                    <?php echo form_error( 'password', '<label class=" alert-danger">', '</label>' ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <label class="admin-label">Status</label>
                            <div class="commn-select-wrap">
                                <select class="selectpicker" name="status">
                                    <option value="">Select</option>
                                    <option value="1">Active</option>
                                    <option value="2">Inactive</option>
                                </select>
                                <?php echo form_error( 'status', '<label class="alert-danger">', '</label>' ); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <!-- ADMIN NEW-->
                        <div class="col-sm-12 clearfix">
                            <div class="adminRoles-wrapper p-md m-b-lg" style="margin:0 0 20px 0">
                                <div class="roles-category clearfix">
                                    <div class="row">
                                        <div class="col-lg-12"><h2 class="title-box m-t-n p-t-20">Is Admin :</h2></div>
                                        <div class="col-lg-12">
                                            <div class = "custom-check main-check">
                                                <input id = "is_admin" name = "is_admin" class="is_admin" value = "admin" type="checkbox">
                                                <label for="is_admin"><span></span>Admin</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 clearfix" id="subAdminDiv">
                            <div class="adminRoles-wrapper p-md m-b-lg">
                                <div class="roles-category clearfix">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <h2 class="title-box m-t-n p-t-20">Sub-admin Roles :</h2>
                                        </div>
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
                                                                     <input data-parent="parent_<?php echo $key; ?>"  id="subcheck_<?php echo $per_key ?>" name="<?php echo $per_key ?>" value="<?php echo $per_key ?>" class="child child_<?php echo $key; ?>" type="checkbox" >
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
                             <div class="col-sm-12">
                                 <div class="adminRoles-wrapper p-md m-b-lg">
                                     <div class="white-wrapper clearfix">
                                         <div class="row">
                                             <div class="col-lg-12"><h2 class="title-box m-t-n p-t-20">Sub-admin Roles :</h2></div>
                                             <div class="col-lg-12">
                                                 <div class="custom-check main-check">
                                                     <input id="main-check1" name="user" onchange="permission( 'user' )"  value="1"   type="checkbox">
                                                     <label for="main-check1"><span></span>Manage User </label>
                                                     <ul class="check-column">
                                                         <li>
                                                             <div class="custom-check">
                                                                 <input id="subcheck1-1" disabled="true" name="permission[user][view]" value="1" class="user" type="checkbox">
                                                                 <label for="subcheck1-1"><span></span>View </label>
                                                             </div>
                                                         </li>
                                                         <li>
                                                             <div class="custom-check">
                                                                 <input id="subcheck1-2" disabled="true" name="permission[user][block]" value="1"  class="user" type="checkbox">
                                                                 <label for="subcheck1-2"><span></span>Block  </label>
                                                             </div>
                                                         </li>
                                                         <li>
                                                             <div class="custom-check">
                                                                 <input id="subcheck1-3" disabled="true" name="permission[user][delete]" value="1" class="user" type="checkbox">
                                                                 <label for="subcheck1-3"><span></span>Delete</label>
                                                             </div>
                                                         </li>
                                                     </ul>
                                                 </div>
                                                 <div class="clear"></div>
                                                 <div class="custom-check main-check">
                                                     <input id="main-check2" name="option" onchange="permission( 'Version' )" value="2" type="checkbox">
                                                     <label for="main-check2"><span></span>Manage Version</label>
                                                     <ul class="check-column">
                                                         <li>
                                                             <div class="custom-check">
                                                                 <input id="subcheck2-4" disabled="true" name="permission[version][add]" value="1"  class="Version" type="checkbox">
                                                                 <label for="subcheck2-4"><span></span>Add</label>
                                                             </div>
                                                         </li>
                                                         <li>
                                                             <div class="custom-check">
                                                                 <input id="subcheck2-2" disabled="true" name="permission[version][edit]" value="1" class="Version" type="checkbox">
                                                                 <label for="subcheck2-2"><span></span>Edit </label>
                                                             </div>
                                                         </li>
                                                         <li>
                                                             <div class="custom-check">
                                                                 <input id="subcheck2-3" disabled="true" name="permission[version][delete]" value="1" class="Version" type="checkbox">
                                                                 <label for="subcheck2-3"><span></span>Delete </label>
                                                             </div>
                                                         </li>

                                                     </ul>
                                                 </div>
                                                 <div class="clear"></div>
                                                 <div class="custom-check main-check">
                                                     <input id="main-check3" name="portfolio" onchange="permission( 'Notification' )" value="3"  type="checkbox">
                                                     <label for="main-check3"><span></span>Manage Notifications </label>
                                                     <ul class="check-column">
                                                         <li>
                                                             <div class="custom-check">
                                                                 <input id="subcheck3-1" disabled="true" name="permission[notification][add]" value="1"class="Notification"  type="checkbox">
                                                                 <label for="subcheck3-1"><span></span>Add </label>
                                                             </div>
                                                         </li>
                                                         <li>
                                                             <div class="custom-check">
                                                                 <input id="subcheck3-3" disabled="true" name="permission[notification][edit]" value="1" class="Notification"  type="checkbox">
                                                                 <label for="subcheck3-3"><span></span>Edit or Resend</label>
                                                             </div>
                                                         </li>
                                                         <li>
                                                             <div class="custom-check">
                                                                 <input id="subcheck3-4" disabled="true" name="permission[notification][delete]" value="1" class="Notification"  type="checkbox">
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

                        <div class="col-sm-12 col-xs-12">
                            <div class="form-ele-action-bottom-wrap btns-center clearfix">
                                <div class="button-wrap text-center">
                                    <button type="button"  onclick="window.location.href = '<?php echo base_url() ?>admin/version'"class="commn-btn cancel">Cancel</button>
                                    <button type="button" class="commn-btn save" id="save_button">Save</button>
                                </div>
                            </div>
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

                        var parent = $( this ).attr( "data-parent" );
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