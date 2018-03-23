<?php
$userpermission = isset($permission[1])?$permission[1]:array();
$optionpermission = isset($permission[2])?$permission[2]:array();
$portfoliopermission = isset($permission[3])?$permission[3]:array();
$videopermission = isset($permission[4])?$permission[4]:array();
$notificationpermission = isset($permission[5])?$permission[5]:array();
$feedbackpermission = isset($permission[6])?$permission[6]:array();
$apppermission = isset($permission[7])?$permission[7]:array();
$brokerpermission = isset($permission[8])?$permission[8]:array();
// echo "<pre>"; 
//print_r($userpermission );die;
?>

    <!-- Content -->
    <section class="content-wrapper clearfix">
        <div class="upper-head-panel m-b-lg clearfix">
            <ul class="breadcrumb reward-breadcrumb">
                <li><a href="<?php echo base_url(); ?>Subadmin/index">"Sub Admins</a></li>
                <li class="active">Add Admin</li>
            </ul>
        </div>	
        <div class="clear"></div>
        <div class="white-wrapper p-a-sm clearfix">
            <form method="post" id="subadmin-list">
            <div class="row">
                <div class="col-sm-12 col-lg-8 p-n">                
                    <div class="col-xs-4 col-sm-5 col-lg-5"><label class="input-label m-b-md" >Admin Name :</label></div>                    
                    <div class="col-xs-8 col-sm-7 col-lg-7">
                        <div class="input-in m-b-md">
                           
                             <input type="text" id="fullname" value="<?php echo (!empty($admindetail['fullName']))?$admindetail['fullName']:"";  ?>" name="fullName" placeholder="Enter Admin Name" />   
                              <span id="fullnameval" style='color:Red' ></span>
                               <span style="color:blue"><?php echo form_error('fullName'); ?></span>
                        </div>					   
                    </div>
                    <div class="clear"></div>
                    <div class="col-xs-4 col-sm-5 col-lg-5"><label class="input-label m-b-md">Email :</label></div>
                    <div class="col-xs-8 col-sm-7 col-lg-7">
                        <div class="input-in m-b-md">
                            <input type="text" value="<?php echo (!empty($admindetail['email']))?$admindetail['email']:"";  ?>" id="email" name="email" placeholder="Enter Email" readonly/>
                            <span id="emailval" style='color:Red' ></span> 
                            <span style="color:blue"><?php echo form_error('email'); ?></span>
                        </div>					   
                    </div> 
                    <div class="clear"></div>
                    <div class="col-xs-4 col-sm-5 col-lg-5"><label class="input-label m-b-md">Mobile Number :</label></div>
                    <div class="col-xs-8 col-sm-7 col-lg-7">
                        <div class="input-in m-b-md">
                            <input type="text" id="phoneNumber" value="<?php echo (!empty($admindetail['phoneNumber']))?$admindetail['phoneNumber']:"";  ?>" name="phoneNumber" placeholder="Enter Mobile Number" onkeypress="check_num1(event)" maxlength="10"/>                    
                          <span id="phoneNumberval" style='color:Red' ></span> 
                          <span style="color:blue"><?php echo form_error('phoneNumber'); ?></span>
                         
                        </div>					   
                    </div>
                   
                   <div class="clear"></div>
                    <div class="col-xs-5 col-sm-5 col-lg-5"><label class="input-label m-b-sm">Status :</label></div>
                    <div class="col-xs-7 col-sm-7 col-lg-7">
                        <ul class="session-tabs clearfix">
                            <li><label>Active</label> <input type="radio"  value="1" <?php if(!empty($admindetail['status']==1)){echo "checked";} ?> name="status" class="status" ></li>
                            <li><label>Deactive</label> <input type="radio" value="0" <?php  if(!empty($admindetail['status'] == 0)){ echo "checked";}  ?> name="status"  class="status" ></li>
                            <span id="statusval" style='color:Red' ></span> 
                            <span style="color:blue"><?php echo form_error('status'); ?></span>
                        </ul>				   
                    </div>                                            
                </div>
                
            </div> <br><br>
                <div class="col-lg-2 col-sm-2"></div>
                 
                <!-- admin permission edit-->
                
                <div class="white-wrapper p-md m-b-lg">
          <div class="row">
                    <div class="col-lg-12"><h2 class="title-box m-t-n p-t-20">Admin Roles :</h2></div>
                    <div class="col-lg-12">
                        <div class="custom-check main-check">
                            <input id="main-check1" name="user" onchange="permission('user')"  value="1" <?php echo (count($userpermission) > 0)?"checked":"" ?>  type="checkbox">
                            <label for="main-check1"><span></span>Users </label>
                            <ul class="check-column">
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck1-1" name="permission[user][view]" value="1" <?php  if(isset($userpermission['viewp']) && $userpermission['viewp']) { echo "checked";}?>  <?php echo (count($userpermission) == 0)?"disabled":"" ?> class="user" type="checkbox">
                                        <label for="subcheck1-1"><span></span>View </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck1-2" name="permission[user][edit]" value="1" <?php  if(isset($userpermission['editp']) && $userpermission['editp']) { echo "checked";} ?> <?php echo (count($userpermission) == 0)?"disabled":""?> class="user" type="checkbox">
                                        <label for="subcheck1-2"><span></span>Edit  </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck1-3" name="permission[user][delete]" value="1"<?php  if(isset($userpermission['deletep'])&& $userpermission['deletep']) { echo "checked";} ?> <?php echo (count($userpermission) == 0)?"disabled":""?> class="user" type="checkbox">
                                        <label for="subcheck1-3"><span></span>Delete</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck1-4" name="permission[user][block]" value="1"<?php if(isset($userpermission['blockp'])&& $userpermission['blockp']){ echo "checked";}  ?><?php echo (count($userpermission) == 0)?"disabled":""?>  class="user" type="checkbox">
                                        <label for="subcheck1-4"><span></span>Block </label>
                                    </div>
                                </li>
                            </ul>
                        </div>			
                        <div class="clear"></div>
                        <div class="custom-check main-check">
                            <input id="main-check2" name="option" onchange="permission('Option')" value="2"<?php echo (count($optionpermission)>0)?"checked":""  ?>  type="checkbox">
                            <label for="main-check2"><span></span>Option Position Page </label>
                            <ul class="check-column">
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck2-1" name="permission[option][view]" value="1"<?php if(isset($optionpermission['viewp']) && $optionpermission['viewp']) { echo "checked";}?><?php echo (count($optionpermission) == 0)?"disabled":""?>  class="Option" type="checkbox">
                                        <label for="subcheck2-1"><span></span>View </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck2-2" name="permission[option][edit]" value="1"<?php if(isset($optionpermission['editp'])&& $optionpermission['editp']) { echo "checked";}?> <?php echo (count($optionpermission) == 0)?"disabled":""?> class="Option" type="checkbox">
                                        <label for="subcheck2-2"><span></span>Edit </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck2-3" name="permission[option][delete]" value="1"<?php if(isset($optionpermission['deletep'])&& $optionpermission['deletep']) { echo "checked";}?> <?php echo (count($optionpermission) == 0)?"disabled":""?> class="Option" type="checkbox">
                                        <label for="subcheck2-3"><span></span>Delete </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck2-4" name="permission[option][add]" value="1"<?php if(isset($optionpermission['addp'])&& $optionpermission['addp']) { echo "checked";}  ?><?php echo (count($optionpermission) == 0)?"disabled":""?>  class="Option" type="checkbox">
                                        <label for="subcheck2-4"><span></span>Add New</label>
                                    </div>
                                </li>
                            </ul>
                        </div>			
                        <div class="clear"></div>
                        <div class="custom-check main-check">
                            <input id="main-check3" name="portfolio" onchange="permission('Portfolio')" value="3"<?php echo (count($portfoliopermission)>0)?"checked":""  ?>  type="checkbox">
                            <label for="main-check3"><span></span>Portfolio reports </label>
                            <ul class="check-column">
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck3-1" name="permission[portfolio][view]" value="1"<?php if(isset($portfoliopermission['viewp']) && $portfoliopermission['viewp']) { echo "checked";} ?> <?php echo (count($portfoliopermission) == 0)?"disabled":""?>class="Portfolio"  type="checkbox">
                                        <label for="subcheck3-1"><span></span>View </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck3-2" name="permission[portfolio][edit]" value="1"<?php  if(isset($portfoliopermission['editp'])&& $portfoliopermission['editp']) { echo "checked";} ?><?php echo (count($portfoliopermission) == 0)?"disabled":""?> class="Portfolio"  type="checkbox">
                                        <label for="subcheck3-2"><span></span>Edit  </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck3-3" name="permission[portfolio][delete]" value="1"<?php if(isset($portfoliopermission['deletep'])&& $portfoliopermission['deletep']) { echo "checked";}?> <?php echo (count($portfoliopermission) == 0)?"disabled":""?>class="Portfolio"  type="checkbox">
                                        <label for="subcheck3-3"><span></span>Delete</label>
                                    </div>
                                </li>				
                            </ul>
                        </div>			
                        <div class="clear"></div>
                        <div class="custom-check main-check">
                            <input id="main-check4" name="video" onchange="permission('Video')" value="4"<?php  echo(count($videopermission)>0)?"checked":""?>  type="checkbox">
                            <label for="main-check4"><span></span>Videos</label>
                            <ul class="check-column">
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck4-1" name="permission[video][view]" value="1"<?php if(isset($videopermission['viewp']) && $videopermission['viewp']) { echo "checked";}?><?php echo (count($videopermission) == 0)?"disabled":""?> class="Video" type="checkbox">
                                        <label for="subcheck4-1"><span></span>View </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck4-2" name="permission[video][edit]" value="1"<?php if(isset($videopermission['editp'])&& $videopermission['editp']) { echo "checked";}  ?> <?php echo (count($videopermission) == 0)?"disabled":""?>class="Video"  type="checkbox">
                                        <label for="subcheck4-2"><span></span>Edit  </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck4-3" name="permission[video][delete]" value="1"<?php if(isset($videopermission['deletep'])&& $videopermission['deletep']) { echo "checked";}  ?> <?php echo (count($videopermission) == 0)?"disabled":""?>class="Video" type="checkbox">
                                        <label for="subcheck4-3"><span></span>Delete</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck4-4" name="permission[video][add]" value="1"<?php if(isset($videopermission['addp'])&& $videopermission['addp']) { echo "checked";}  ?> <?php echo (count($videopermission) == 0)?"disabled":""?>class="Video"  type="checkbox">
                                        <label for="subcheck4-4"><span></span>Add</label>
                                    </div>
                                </li>				
                            </ul>
                        </div>			
                        <div class="clear"></div>
                        <div class="custom-check main-check">
                            <input id="main-check5" name="notification"  onchange="permission('Notification')" value="5"<?php echo(count($notificationpermission)>0)?"checked":"" ?> type="checkbox">
                            <label for="main-check5"><span></span>Push Notification</label>
                            <ul class="check-column">
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck5-1" name="permission[notification][add]" class="Notification" value="1"<?php if(isset($notificationpermission['addp'])&& $notificationpermission['addp']) { echo "checked";} ?><?php echo (count($notificationpermission) == 0)?"disabled":""?>  type="checkbox">
                                        <label for="subcheck5-1"><span></span>Send </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck5-2" name="permission[notification][edit]" class="Notification" value="1"<?php if(isset($notificationpermission['editp']) && ($notificationpermission['editp'])) { echo "checked";} ?><?php echo (count($notificationpermission) == 0)?"disabled":""?> type="checkbox">
                                        <label for="subcheck5-2"><span></span>Edit  </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck5-3" name="permission[notification][delete]" class="Notification" value="1"<?php if(isset($notificationpermission['deletep'])&& ($notificationpermission['deletep'])) { echo "checked";} ?> <?php echo (count($notificationpermission) == 0)?"disabled":""?> type="checkbox">
                                        <label for="subcheck5-3"><span></span>Delete</label>
                                    </div>
                                </li>							
                            </ul>
                        </div>			
                        <div class="clear"></div>
                        <div class="custom-check main-check">
                            <input id="main-check6" name="feedback" onchange="permission('Feedback')" value="6"<?php echo(count($feedbackpermission)>0)?"checked":"" ?> type="checkbox">
                            <label for="main-check6"><span></span>Feedback's</label>
                            <ul class="check-column">
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck6-1" name="permission[feedback][add]" class="Feedback" value="1"<?php if(isset($feedbackpermission['addp'])&& ($feedbackpermission['addp'])) { echo "checked";}?> <?php echo (count($feedbackpermission) == 0)?"disabled":""?> type="checkbox">
                                        <label for="subcheck6-1"><span></span>Add </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck6-2" name="permission[feedback][edit]" class="Feedback" value="1"<?php if(isset($feedbackpermission['editp'])&& ($feedbackpermission['editp'])) { echo "checked";} ?><?php echo (count($feedbackpermission) == 0)?"disabled":""?>  type="checkbox">
                                        <label for="subcheck6-2"><span></span>Reply  </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck6-3" name="permission[feedback][delete]" class="Feedback" value="1"<?php if(isset($feedbackpermission['deletep'])&& ($feedbackpermission['deletep'])) { echo "checked";} ?> <?php echo (count($feedbackpermission) == 0)?"disabled":""?> type="checkbox">
                                        <label for="subcheck6-3"><span></span>Delete</label>
                                    </div>
                                </li>							
                            </ul>
                        </div>			
                        <div class="clear"></div>
                        <div class="custom-check main-check">
                            <input id="main-check7" name="app" onchange="permission('App')" value="7"<?php echo (count($apppermission)>0)?"checked":"" ?> type="checkbox">
                            <label for="main-check7"><span></span>App Content</label>
                            <ul class="check-column">
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck7-1" name="permission[app][view]" class="App" value="1"<?php if(isset($apppermission['viewp'])&& ($apppermission['viewp'])) { echo "checked";}  ?><?php echo (count($apppermission) == 0)?"disabled":""?>  type="checkbox">
                                        <label for="subcheck7-1"><span></span>View </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck7-2" name="permission[app][edit]" class="App" value="1"<?php if(isset($apppermission['editp'])&& ($apppermission['editp'])) { echo "checked";}  ?><?php echo (count($apppermission) == 0)?"disabled":""?>  type="checkbox">
                                        <label for="subcheck7-2"><span></span>Edit  </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck7-3" name="permission[app][delete]" class="App" value="1"<?php if(isset($apppermission['deletep'])&& ($apppermission['deletep'])) { echo "checked";}?><?php echo (count($apppermission) == 0)?"disabled":""?>  type="checkbox">
                                        <label for="subcheck7-3"><span></span>Delete</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck7-4" name="permission[app][add]" class="App" value="1"<?php if(isset($apppermission['addp'])&& ($apppermission['addp'])) { echo "checked";} ?><?php echo (count($apppermission) == 0)?"disabled":""?>  type="checkbox">
                                        <label for="subcheck7-4"><span></span>Add</label>
                                    </div>
                                </li>				
                            </ul>
                        </div>			
                        <div class="clear"></div>
                        <div class="custom-check main-check">
                            <input id="main-check8" name="broker" onchange="permission('Broker')" value="8"<?php if(count($brokerpermission)){ echo "checked";}  ?> type="checkbox">
                            <label for="main-check8"><span></span>Brokers</label>
                            <ul class="check-column">
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck8-1" name="permission[broker][view]" class="Broker" value="1"<?php if(isset($brokerpermission['viewp'])&& ($brokerpermission['viewp'])) { echo "checked";}?><?php echo (count($brokerpermission) == 0)?"disabled":"" ?>  type="checkbox">
                                        <label for="subcheck8-1"><span></span>View </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck8-2" name="permission[broker][edit]" class="Broker" value="1"<?php if(isset($brokerpermission['editp'])&& ($brokerpermission['editp'])) { echo "checked";} ?><?php echo (count($brokerpermission) == 0)?"disabled":""?>  type="checkbox">
                                        <label for="subcheck8-2"><span></span>Edit  </label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck8-3" name="permission[broker][delete]" class="Broker" value="1"<?php if(isset($brokerpermission['deletep'])&& ($brokerpermission['deletep'])) { echo "checked";} ?><?php echo (count($brokerpermission) == 0)?"disabled":""?>  type="checkbox">
                                        <label for="subcheck8-3"><span></span>Delete</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-check">
                                        <input id="subcheck8-4" name="permission[broker][add]" class="Broker" value="1"<?php if(isset($brokerpermission['addp'])&& ($brokerpermission['addp'])) { echo "checked";} ?><?php echo (count($brokerpermission) == 0)?"disabled":""?>  type="checkbox">
                                        <label for="subcheck8-4"><span></span>Add</label>
                                    </div>
                                </li>				
                            </ul>
                        </div>			
                    </div>
                </div>
            
            </div>
            <div class="clear"></div> 
                <div class="devider m-b-md m-t-md"></div>
                <div class="col-lg-2 col-sm-2"></div>
                <div class="col-lg-8 col-sm-8">
                    <div class="col-lg-6 col-sm-6">
                        <button type="submit" class="custom-btn save" onclick="return sub_Validation();">Update Admin </button>
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <button type="button" class="custom-btn cancel"onclick="window.location.href = '/Subadmin';">Cancel</button>
                    </div>
                </div>
            <div class="col-lg-2 col-sm-2"></div>
        </div>
         
</form>
            </div>

       

    </section>


