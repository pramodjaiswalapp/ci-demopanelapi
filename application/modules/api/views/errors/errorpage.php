<!DOCTYPE html>
<html lang="en">

    <head>
        <title>Caresocius</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/public/css/style.main.css">
        <link rel="stylesheet" href="/public/css/form-element.css">
        <link rel="stylesheet" href="/public/css/style.media.main.css">
        <script src="/public/js/jquery.min.js"></script>
        <script src="/public/js/script.main.js"></script>
    </head>

    <body class="center-box-body">
        <div class="center-box-wrap">

            <!--Error page starts -->
            <div class="error-page">
                <img src="/public/images/logo-large.png">
                <div class="error-page-text">
                    <h2>OOPS!</h2>
                </div>
                <h1><?php echo isset($code)?$code:404 ?></h1>
                <span><?php echo isset($msg)?$msg:'Sorry, the page  you requested could not be found.' ?></span>
            </div>
            <!--Error page ends -->

        </div>
    </body>
    <!-- page level script-->
    <!-- page level script end-->

</html>