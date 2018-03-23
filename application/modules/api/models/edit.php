<!-- Inside Main content -->
<form action="" method="post" enctype="multipart/form-data">
    <div class="inside-main-content clearfix">
        <div class="product-detail-wrap clearfix">
            <div class="row">
                <div class="col-sm-4 col-xs-12">
                    <div class="product-img-wrap">
                        <img src="<?php echo $user_detail['user_image']; ?>" id="imagepicker1">
                        <a class="camIco" href="javascript:void(0);" onClick="callme('user_image', '640', '640', 'imagepicker1', 'next', 'loader','1')">
                            <img src="images/cam_ico.png">
                        </a>
                        <div id="loader" class="imgsppiner" style="display: none;"></div>
                    </div>
                    <div class="user-thumb-detail">
                        <div class="uthumb-name">
                            <input type="text" class="edit-uname" name="name" id="name" value="<?php echo $user_detail['name']; ?>">
                            <div class="edit-profile_pic">
                                <input type="hidden" id="user_image" name="user_image" value="<?php echo $user_detail['user_image']; ?>">
                            </div>
                        </div>

                        <div class="uthumb-breif">
                            <textarea class="edit-uinfo form-control" id="about_me" placeholder="about" name="about_me"><?php if(isset($user_detail['about_me']) && $user_detail['about_me']!=''){echo $user_detail['about_me'];}?></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8 col-xs-12">
                    <div class="edit-profile-wrap">
                        <div class="edit-title-wrap">
                            <h3>Edit Profile Info:</h3>
                            <p>All fields are mandatory. Make sure all fields are filled with correct data.</p>
                        </div>
                        <div class="edit-form-wrap clearfix">
                            <div class="form-group locked">
                                <div class="row">
                                    <label class="col-sm-3 col-xs-12">Username:</label>
                                    <div class="col-sm-9 col-xs-12">
                                        <div class="input-wrap">
                                            <label class="input-label"><?php if($user_detail['username']!=''){echo $user_detail['username'];}?><i class="fa fa-lock" aria-hidden="true"></i></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <label class="col-sm-3 col-xs-12">Mobile Number:</label>
                                    <div class="col-sm-9 col-xs-12">
                                        <div class="input-wrap clearfix">
<!--                                                    <input type="text" class="input-ccode form-control" value="+1">-->
                                            <input type="text" class="w360 form-control phone" id="phone" name="phone" value="<?php if($user_detail['phone']!=''){echo $user_detail['phone'];}?>" onkeydown="CheckforNum(event)" maxlength="14">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group locked">
                                <div class="row">
                                    <label class="col-sm-3 col-xs-12">Email:</label>
                                    <div class="col-sm-9 col-xs-12">
                                        <div class="input-wrap">
                                            <label class="input-label"><?php echo $user_detail['email']; ?><i class="fa fa-lock" aria-hidden="true"></i></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <label class="col-sm-3 col-xs-12">Date of Birth:</label>
                                     <div class="col-sm-9 col-xs-12">
                                        <div id="setDate-wrap" class="input-wrap clearfix">
                                            <!--<input type="text" class="input-ccode form-control" name="month" id="month" value="<?php if($user_detail['dob']!=''&&$user_detail['dob']!='0000-00-00'){echo date('m',strtotime($user_detail['dob']));} ?>" placeholder="MM" max="31" maxlength="2">
                                            <input type="text" class="input-ccode form-control" name="day" id="day" value="<?php if($user_detail['dob']!=''&&$user_detail['dob']!='0000-00-00'){echo date('d',strtotime($user_detail['dob']));} ?>" placeholder="DD" maxlength="2" max="12">
                                            <input type="text" class="input-ccode form-control" name="year" id="year" value="<?php if($user_detail['dob']!=''&&$user_detail['dob']!='0000-00-00'){echo date('Y',strtotime($user_detail['dob']));} ?>" placeholder="YYYY" maxlength="4" max="<?php date('Y') ?>">-->
                                            <input class="w360 form-control" value="<?php echo ($user_detail['dob'] != '0000-00-00')?date('m/d/Y',strtotime($user_detail['dob'])):"" ?>" name="dob" type="text" id="setDate">
                                        </div>
                                     </div>

                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <label class="col-sm-3 col-xs-12">Address</label>
                                    <div class="col-sm-9 col-xs-12">
                                        <div class="input-wrap clearfix">
                                            <input type="text" autocomplete="off" class="w360 form-control address" id="address" name="address"  onkeydown="getaddress(this.value)" value="<?php if($user_detail['address']!=''){echo $user_detail['address'];}?>">
                                            <ul class="location-nav"></ul>
                                            <input type='hidden' name="user_lat" id='user_lat' value="<?php if($user_detail['user_lat']!=''){echo $user_detail['user_lat'];}?>">
                                            <input type='hidden' name="user_long" id='user_long' value="<?php if($user_detail['user_long']!=''){echo $user_detail['user_long'];}?>">
                                            <input type='hidden' name="country" id='country' value="<?php if($user_detail['country']!=''){echo $user_detail['country'];}?>">
                                            <input type='hidden' name="city" id='city' value="<?php if($user_detail['city']!=''){echo $user_detail['city'];}?>">
                                            <span id="address_span" style="color:red;display:none" class="error_msg address_span" >Enter Address</span>
                                            <span class="ln"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="row">
                                    <label class="col-sm-3 col-xs-12">Are you Employeed?</label>
                                    <div class="col-sm-9 col-xs-12">
                                        <div class="input-wrap clearfix">
                                            <div class="radio-col inline">
                                                <input type="radio" name="radio_emp" id="radio_emp1" value="1" <?php if($user_detail['is_employee']=='1'){echo 'checked';}?>>
                                                <label for="radio_emp1"><span></span>Yes</label>
                                            </div>
                                            <div class="radio-col inline">
                                                <input type="radio" name="radio_emp" id="radio_emp2" value="2" <?php if($user_detail['is_employee']=='2'){echo 'checked';}?>>
                                                <label for="radio_emp2"><span></span>No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="employeed"  <?php if($user_detail['is_employee']=='2'){echo 'style="display: none"';}?>  >
                            <div class="form-group">
                                <div class="row">
                                    <label class="col-sm-3 col-xs-12">Select Employer type:</label>
                                    <div class="col-sm-9 col-xs-12">
                                        <div class="input-wrap">
                                            <select class="w360 form-control allusertype" onchange="getalluserlist(this.value,<?php echo $user_detail['employer_id'] ?>)" >
                                                <option value="">Select</option>
                                                <option <?php echo ($usertype == 1)?'Selected':"" ?> value="1">Dispenary</option>
                                                <option <?php echo ($usertype == 2)?'Selected':"" ?> value="2">Delivery Service</option>
                                                <option <?php echo ($usertype == 3)?'Selected':"" ?> value="3">Vendor</option>
                                                <option <?php echo ($usertype == 4)?'Selected':"" ?> value="4">Distributor</option>                                   
                                        </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="col-sm-3 col-xs-12">Employeed At:</label>
                                    <div class="col-sm-9 col-xs-12">
                                        <div class="input-wrap">
                                            <select class="w360 form-control" id="employeedat" name="employeedat">
                                                <option value="">Select Employer Type</option>
                                        </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <label class="col-sm-3 col-xs-12">Designation:</label>
                                    <div class="col-sm-9 col-xs-12">
                                        <input type="text" class="w360 form-control checkforchar" id="designation " name="designation" value="<?php if($user_detail['designation']!=''){echo $user_detail['designation'];}?>" >
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="edit-btn-wrap clearfix">
                        <input type="button" class="btn btn-cancel-grey" value="Cancel" onclick="window.location.href='/web/profile'">
                        <input type="submit" class="btn btn-success-theme" value="Save">
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>
<!-- Inside Main content End -->
<script>
$(window).load(function(){
   $('.allusertype').trigger('change'); 
});
$(document).ready(function () {
    $('#setDate').datepicker({
          container: '#setDate-wrap',
          autoclose: true,
          endDate:'-14Y'
    });

    $('input:radio[name=radio_emp]').click(function () {
        if ($("input[name='radio_emp']:checked").val() == '1') {
           $('#employeed').show();
        }
        if ($("input[name='radio_emp']:checked").val() == '2') {
            $('#employeed').hide();
            $('#employeedat').val('');
            $('#designation').val('');
        }
    });
    
    
    $('.employeedat option[value="' + <?php echo $user_detail['employer_id'] ?> + '"]').prop('selected', true);
//    $(".employeedat").val('');
    
});

var previewImage = function(input, block) {
    var fileTypes = ['jpg', 'jpeg', 'png'];
    var extension = input.files[0].name.split('.').pop().toLowerCase(); /*se preia extensia*/
    var isSuccess = fileTypes.indexOf(extension) > -1; /*se verifica extensia*/

    if (isSuccess) {
      var reader = new FileReader();
      reader.onload = function(e) {
          block.attr('src', e.target.result);
      };
      reader.readAsDataURL(input.files[0]);
    } else {
      alert('Image not accepted');
    }
};

$('#user_image').on('change', function() {
    previewImage(this, $('.product-img-wrap img'));
});

</script>
