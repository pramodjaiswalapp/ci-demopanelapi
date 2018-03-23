<?php

?>
<div class="inner-right-panel">

    <!--breadcrumb wrap-->
<div class="breadcrumb-wrap">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url()?>admin/version">Manage Version</a></li>
        <li class="breadcrumb-item active">Add Version</li>
    </ol>
</div>
<!--breadcrumb wrap close-->
    <!--Filter Section -->
    <?php echo form_open_multipart('',array('id'=>'version_add_form'));?>
        <div class="white-wrapper">
            <div class="form-item-title clearfix">
                <h3 class="title">Add Version</h3>
            </div>
            <!-- title and form upper action end-->
            <div class="form-ele-wrapper clearfix">
                <div class="row">
                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label class="admin-label">Version Name</label>
                            <div class="input-holder">
                                <input type="text" class="form-control material-control" maxlength="30" name="name" value="<?php echo set_value('name');?>">
                                <?php echo form_error('name','<label class="alert-danger">','</label>');?>
                                <!-- <span class="error_wrap"></span> -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label class="admin-label">Title</label>
                            <div class="input-holder">
                                <input type="text" class="form-control material-control" maxlength="30" name="title" value="<?php echo set_value('title');?>">
                                <?php echo form_error('title','<label class=" alert-danger">','</label>');?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label class="admin-label">Description</label>
                            <div class="input-holder">
                                <textarea class="custom-textarea" maxlength="500" name="desc" style="overflow:auto;resize:none" id="page_desc"><?php echo set_value('desc');?></textarea>
                                <?php echo form_error('desc','<label class="alert-danger">','</label>');?>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="col-sm-4 col-xs-4">
                        <div class="form-group">
                            <label class="admin-label">Platform</label>
                            <div class="commn-select-wrap">
                              
                                <select class="selectpicker" name="platform">
                                      <option value="">Select</option>
                                      <option <?php if(set_value('platform')==ANDROID){ echo "selected='selected'";}?> value="<?php echo ANDROID;?>">Android</option>
                                      <option <?php if(set_value('platform')==IPHONE){ echo "selected='selected'";}?> value="<?php echo IPHONE;?>">Iphone</option>
                                </select>
                                <?php echo form_error('platform','<label class="alert-danger">','</label>');?>
                            </div>
                            <!-- <span class="error_wrap"></span> -->
                        </div>
                    </div>
                    
                    <div class="col-sm-4 col-xs-4">
                        <div class="form-group">
                            <label class="admin-label">Update Type</label>
                            <div class="commn-select-wrap">
                              
                                <select class="selectpicker" name="update_type">
                                      <option value="">Select</option>
                                      <option <?php if(set_value('update_type')==NORMAL){ echo "selected='selected'";}?> value="<?php echo NORMAL;?>">Normal</option>
<!--                                      <option <?php if(set_value('update_type')==SKIPPABLE){ echo "selected='selected'";}?> value="<?php echo SKIPPABLE;?>">Skippable</option>-->
                                      <option <?php if(set_value('update_type')==FORCEFULLY){ echo "selected='selected'";}?> value="<?php echo FORCEFULLY;?>">Forcefully</option>
                                   </select>
                                <?php echo form_error('update_type','<label class="alert-danger">','</label>');?>
                            </div>
                            <!-- <span class="error_wrap"></span> -->
                        </div>
                    </div>
                    
                    <div class="col-sm-4 col-xs-4">
                        <div class="form-group">
                            <label class="admin-label">Is current version ?</label>
                            <div class="commn-select-wrap">
                              
                                <select class="selectpicker" name="current_version">
                                      <option value="">Select</option>
                                      <option <?php if(set_value('current_version')==YES){ echo "selected='selected'";}?> value="<?php echo YES;?>">Yes</option>
                                      <option <?php if(set_value('current_version')==NO && set_value('current_version')!=''){ echo "selected='selected'";}?> value="<?php echo NO;?>">No</option>
                                   </select>
                                <?php echo form_error('current_version','<label class="alert-danger">','</label>');?>
                            </div>
                            <!-- <span class="error_wrap"></span> -->
                        </div>
                    </div>
                    
                    
                </div>
                <div class="form-ele-action-bottom-wrap btns-center clearfix">
                    <div class="button-wrap text-center">
                        <button type="button"  onclick="window.location.href='<?php echo base_url()?>admin/version'"class="commn-btn cancel">Cancel</button>
                        <button type="submit" class="commn-btn save">Save</button>
                    </div>
                </div>
                <!--form ele wrapper end-->
            </div>
            <!--form element wrapper end-->

        </div>
        <!--close form view   -->

    <?php echo form_close();?>
    <!--Filter Section Close-->
</div>
<!--Table listing-->
