<body >
    <!--Login page  Wrap-->
    <div class="data-wrap">
        <!--COl Wrapper-->
        <div class="in-col-wrap clearfix">
            <!-- alert -->
            <?php if ( null !== $this->session->flashdata( "greetings" ) ) { ?>
                 <div class="alert alert-danger" role="alert">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                     <h4 class="alert-heading"><?php echo $this->session->flashdata( "greetings" ) ?></h4>
                     <p><?php echo $this->session->flashdata( "message" ) ?></p>
                 </div>
                 <?php
             }

             if ( $this->session->flashdata( 'password_updated' ) != '' ) {
                 echo $this->session->flashdata( 'password_updated' );
             }
            ?>
            <!-- //alert -->
            <!--form inner col-->
            <div class="index-form-wrap">
                <div class="form_hd">
                    <figure class="index-logo">
                        <img src="public/images/logo.png">
                    </figure>
                </div>
                <div class="form_inner_wrap">
                    <div class="login-error">
                        <span class="error"></span>
                    </div>
                    <?php echo form_open( '', array ('id' => 'login_admin_form', 'class' => 'login_form') ) ?>

                    <h1 class="index-comn-heading">Login  </h1>
                    <!-- <p class="index-note">Enter Your Details below to access your account </p> -->
                    <div class="form-field-wrap clearfix">
                        <!-- <span class="ad-user"></span> -->
                        <input type="text" class="login_filed removespace" maxlength="40" placeholder="Email Address" onfocus="this.removeAttribute( 'readonly' );" readonly name="email" onkeyup="a = this.value;this.value = a.trim()" value="<?php
                         echo isset( $email ) ? $email : set_value( 'email' );
                        ?>"  autocomplete="off" />
                               <?php
                                echo isset( $error ) ? '<label class="error_label">'.$error.'</label>' : form_error( 'email', '<label class="error_label">', '</label>' )
                               ?>


                    </div>
                    <div class="form-field-wrap clearfix" id="passworderr">
                        <!-- <span class="ad-password"></span> -->
                        <input type="password" class="login_filed removespace" maxlength="20" placeholder="Password" onfocus="this.removeAttribute( 'readonly' );" readonly name="password" value="<?php
                         echo isset( $password ) ? $password : set_value( 'password' );
                        ?>"  autocomplete="off" required />
                               <?php echo form_error( 'password', '<label class="error_label">', '</label>' ) ?>

                    </div>
                    <div class="form-field-wrap">
                        <span class="rember-col">
                            <div class="th-checkbox">
                                <input type="checkbox" name="remember_me" id="remember_me" value="remember_me" >
                                <label for="remember_me" class="lbl-check">Remember me</label>
                            </div>
                        </span>
                        <span class="forgot-pass">
                            <a class="" href="<?php echo base_url(); ?>admin/forgot">Forgot Password?</a>
                        </span>
                    </div>
                    <div class="clear"></div>
                    <div class="btn-wrapper form-btn">
                        <button class="commn-btn save" type="submit" id="login">Login</button>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <!--form inner col close-->
        </div>
    </div>

