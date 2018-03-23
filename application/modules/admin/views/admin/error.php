
<body >
    <!--Login page  Wrap-->
    <div class="data-wrap">
        <!--COl Wrapper-->  
       <!--Error page starts -->
       <div class="container">
       <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="error-page-wrap">
                    <figure>
                        <img src="<?php echo base_url();?>public/images/logo.png">
                    </figure>
                </div>    
            </div>
        </div>
    </div>
    <div class="error-page">
       <div class="error-page-text">
           <h2>OOPS!</h2>
       </div>
       <h1>4<i class="em em-astonished"></i>4!</h1>
       <span><i class="fa fa-exclamation-triangle"></i> <?php echo $err_msg;?></span>
       <div class="error-back-btn">
           <a href="<?php echo site_url().$redurl;?>" class="index-comn-btn">Go Back</a>
        </div>
   </div>
   <!--Error page ends -->
    </div>

