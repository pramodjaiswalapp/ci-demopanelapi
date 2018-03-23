
<body >
    <style>

        /* Access denied css */
        .access-denied-wrap {
            position: absolute;
            transform: translate(-50% , -50%);
            -webkit-transform: translate(-50% , -50%);
            left: 50%;
            top:50%;
            width: 450px;
            padding: 30px 50px;
            box-shadow: 0px 0 10px rgba(0, 0, 0, 0.33);
        }
        .denied-icon {
            width: 30px;
            height: 30px;
            position: absolute;
            left: 20px;
            margin: 0px;
        }
        .denied-icon img {
            width:100%;
        }
        .access-denied {
            float: left;
            margin: 0 0 0 15px;
        }
        .access-denied h2 {
            margin: 0 0 20px 0;
            font-size: 20px;
            font-weight: 400;
        }
        .access-denied span {
            font-size: 14px;
            color: #000;
            margin: 0 0 40px 0;
            display: block;
        }
        .access-denied button {
            background: #ccc;
            border: none;
            padding: 7px 15px;
        }

    </style>
    <!--Login page  Wrap-->
    <div class="data-wrap">
        <!--COl Wrapper-->
        <!--Error page starts -->
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="error-page-wrap">
                        <figure>
                            <img src="<?php echo base_url(); ?>public/images/logo.png">
                        </figure>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="access-denied-wrap">
                <figure class="denied-icon"><img src="img/access-denied.png" alt=""></figure>
                <div class="access-denied">
                    <h2>Error: Access Denied</h2>
                    <span>You do not have permission to access........</span>
                    <a href="<?php echo site_url().$redurl; ?>" class="index-comn-btn">Go Back</a>
                </div>

            </div>
        </div>
        <!--Error page ends -->
    </div>

