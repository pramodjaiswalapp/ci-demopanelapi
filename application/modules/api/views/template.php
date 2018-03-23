<?php //print_r($name);die; ?>
<html lang="en">
<head>
  <title>Automated BonApp</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="http://bonappstage.appnationz.com/public/adminpanel/images/logoo.jpg">
  
  <table cellpadding="0" cellspacing="0" border="0" style="margin:0 auto; min-width:320px; max-width:600px; border:2px solid #f16725;" width="100%">
  <tr style="background-color:rgba(245, 245, 245, 0.61);; padding:5px;">
  <td align="center"><a href="http://localhost/Bonapp"><img src="http://localhost/Bonapp" alt="Automated BonApp"></a></td>
  </tr>
  
  <tr><td>&nbsp; </td> </tr>
  <tr><td>&nbsp; </td> </tr>
  <tr><td>&nbsp; </td> </tr>
  
  <tr>
  <td style="   color: #383636;
    font-family: arial;
    font-size: 20px;
    padding: 0 35px;">Hi <?php echo $name; ?>, </td>
  </tr>
  
    <tr>
  <td style="color: #404652;
    font-family: arial;
    font-size: 16px;
    line-height: 26px;
    padding: 35px"> A request was made recently to reset your password. 
<a href="<?php echo $link ?> " style="text-decoration:none; color:#f16725;"><strong> Click here</strong></a> to change your password.
  <!--If you did not ask for your password to be reset, <a href="#" style="text-decoration:none; color:#f16725;"> <strong>Please let us know</strong>.</a> --></td>
  </tr>
  
  
    <tr>
  <td style="color: #f16725;
    font-family: arial;
    font-size: 16px;
    line-height: 26px;
    padding: 35px"><strong>Warm Regards,<br> 
 BonApp TEAM</strong>
  </td>
  </tr>  
  
  
  </table>