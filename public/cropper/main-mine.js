var id = 'profileImage1';
var minCropBoxWidth = "150";
var minCropBoxHeight = "100";
var profileImage = "profileImage1";
var fillId = "imagepicker2";
var disabledId = "next";
var imageMe = "imageMe";
var uploadpath = "uploadpath";
var hideshow_remove_icon = '';
var running = 0;
var inp = 0;
var default_image = '/transparent.png';
//update in main.js for resizable
var checkResizable = false;
var _w = null;
var _h = null;
var _resiz = null;
var AsRatio = null;
var s3move = true;
var imageRatio = '';
//update in main.js end
function showalertmessage(msg) {

    $("#alertMsgLayout").html(msg);
    $("#finalalertLayout").show();
    $("#alertdivLayout").fadeIn();
}

function callme(id1, minCropBoxWidth1, minCropBoxHeight1, fillId1, disabledId1, imageMe1, resizable, path, ratio, hideshow_remove_icon_show, default_image_show, s3move) {
    hideshow_remove_icon = hideshow_remove_icon_show;
    $('#addbox').hide();
    $('.imagePostbtn').prop('disabled', true);
    if (default_image_show != '') {
        default_image = default_image_show;
    }
    $('#avatar-modal').removeClass('circular');
    $('#avatar-modal .row').hide();
    $('#avatarInput').val('');
    if (running == 1) {
        showalertmessage("Please wait...");
        return false;
    }
    id = id1;
    //if (minCropBoxWidth1 == "640")
    //     minCropBoxWidth1 = "1080";
    imageRatio = ratio;
    minCropBoxWidth = minCropBoxWidth1;
    minCropBoxHeight = minCropBoxHeight1;
    profileImage = id1;
    fillId = fillId1;
    disabledId = disabledId1;
    imageMe = imageMe1;
    //is_move_s3 = s3move;

    checkResizable = resizable;
    console.log('width: ' + minCropBoxWidth1);
    console.log('height: ' + minCropBoxHeight1);
    switch (ratio) {
        case 'circular':
            AsRatio = 1 / 1;
            _w = minCropBoxWidth;
            _h = minCropBoxHeight;
            _resiz = checkResizable;
            $('#avatar-modal').addClass('circular');
            break;
        case 'nofixed':
            AsRatio = minCropBoxWidth / minCropBoxHeight;
            _w = minCropBoxWidth;
            _h = minCropBoxHeight;
            _resiz = 1;
            break;
        case 'square':
            AsRatio = 1 / 1;
            _w = minCropBoxWidth;
            _h = minCropBoxHeight;
            _resiz = 1;
            break;
        case 'timeline':
            AsRatio = 16 / 9;
            _w = minCropBoxWidth;
            _h = minCropBoxHeight;
            _resiz = 1;
            break;
        default:
            AsRatio = 16 / 9;
            _w = minCropBoxWidth;
            _h = minCropBoxHeight;
            _resiz = 0
            break;

    }
    //update in main.js end
    if (id == 'profileImage1') {
        // $("#crop-avatar").children().bind('click', function(){ return false; });
        return new CropAvatar($('#crop-avatar1'));
    } else {
        //$("#crop-avatar1").children().bind('click', function(){ return false; });
        console.log('dasdasdsad');
        return new CropAvatar($('#crop-avatar'));

    }
}

function CropAvatar($element) {
    this.$container = $element;

    this.$avatarView = this.$container.find('.media');

    this.$avatar = this.$avatarView.find('img');
    this.$avatarModal = this.$container.find('#avatar-modal');
    this.$avatarModal = $('#avatar-modal');
    this.$loading = $('.loading');

    this.$avatarForm = this.$avatarModal.find('.avatar-form');
    this.$avatarUpload = this.$avatarForm.find('.avatar-upload');
    this.$avatarSrc = this.$avatarForm.find('.avatar-src');
    this.$avatarData = this.$avatarForm.find('.avatar-data');
    this.$avatarInput = this.$avatarForm.find('.avatar-input');
    this.$avatarSave = this.$avatarForm.find('.avatar-save');
    this.$avatarBtns = this.$avatarForm.find('.avatar-btns');
    this.$avatarZoom = this.$avatarForm.find('.avatar-zooms');
    this.$avatarWrapper = this.$avatarModal.find('.avatar-wrapper');
    this.$avatarPreview = this.$avatarModal.find('.avatar-preview');

    this.init();

    //$('#crop-avatar').click();

}



(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node / CommonJS
        factory(require('jquery'));
    } else {
        factory(jQuery);
    }
})(function ($) {

    'use strict';

    var console = window.console || {log: function () {}};
    CropAvatar.prototype = {
        constructor: CropAvatar,
        support: {
            fileList: !!$('<input type="file">').prop('files'),
            blobURLs: !!window.URL && URL.createObjectURL,
            formData: !!window.FormData
        },
        init: function () {
            this.support.datauri = this.support.fileList && this.support.blobURLs;

            if (!this.support.formData) {
                this.initIframe();
            }

            this.initTooltip();
            this.initModal();
            this.addListener();


        },
        addListener: function () {

            this.$avatarView.on('click', $.proxy(this.click, this));
            this.$avatarInput.on('change', $.proxy(this.change, this));
            this.$avatarForm.on('submit', $.proxy(this.submit, this));
            this.$avatarBtns.on('click', $.proxy(this.rotate, this));
            this.$avatarZoom.on('click', $.proxy(this.zoom, this));
            console.log("2");
            // this.$avatarModal.modal('show');
        },
        initTooltip: function () {
            this.$avatarView.tooltip({
                placement: 'bottom'
            });
        },
        initModal: function () {
            console.log("1");

            this.$avatarModal.modal({
                show: false
            });
        },
        initPreview: function () {
            var url = this.$avatar.attr('src');

            this.$avatarPreview.html('<img src="' + url + '">');
        },
        initIframe: function () {
            var target = 'upload-iframe-' + (new Date()).getTime(),
                    $iframe = $('<iframe>').attr({
                name: target,
                src: ''
            }),
                    _this = this;

            // Ready ifrmae
            $iframe.one('load', function () {

                // respond response
                $iframe.on('load', function () {
                    var data;

                    try {
                        data = $(this).contents().find('body').text();
                    } catch (e) {
                        console.log(e.message);
                    }

                    if (data) {
                        try {
                            data = $.parseJSON(data);
                        } catch (e) {
                            console.log(e.message);
                        }

                        _this.submitDone(data);
                    } else {
                        _this.submitFail('Image upload failed!');
                    }

                    _this.submitEnd();

                });
            });

            this.$iframe = $iframe;
            this.$avatarForm.attr('target', target).after($iframe.hide());
        },
        click: function () {

            if (running == 1) {
                showalertmessage("Please wait...");
                return false;
            }
            if (IsNumeric(edit_env)) {
                if (edit_env == "0") {
                    return false;
                } else {

                    //   this.$avatarModal.modal('show');
                }
            } else {
                // this.$avatarModal.modal('show');
            }
            //this.initPreview();
        },
        change: function () {
            var my_file_size = $("#avatarInput")[0].files[0].size;
            var type = $("#avatarInput")[0].files[0].type;
            var validFileExtensions = ['image/x-png', 'image/png', 'image/gif', 'image/jpeg', 'image/jpg'];
            //console.log("change " + type + my_file_size);
            if (jQuery.inArray(type, validFileExtensions) == -1) {
                var msg = "Invalid file type! supported file format .png, .jpg, .gif, .bmp";
                notifyalert(msg, 'error');
                return false;
            }
            $("#avatar-modal").modal('show');
            $('#avatar-modal .row').show();

            // $('#avatar-modal .row').show();

            var files,
                    file;

            if (this.support.datauri) {
                files = this.$avatarInput.prop('files');

                if (files.length > 0) {
                    file = files[0];

                    if (this.isImageFile(file)) {
                        if (this.url) {
                            URL.revokeObjectURL(this.url); // Revoke the old one
                        }

                        this.url = URL.createObjectURL(file);
                        this.startCropper();
                    }
                }
            } else {
                file = this.$avatarInput.val();

                if (this.isImageFile(file)) {
                    this.syncUpload();
                }
            }
        },
        submit: function () {
            if (!this.$avatarSrc.val() && !this.$avatarInput.val()) {
                return false;
            }
            var my_file_size = $("#avatarInput")[0].files[0].size;
            var type = $("#avatarInput")[0].files[0].type;
            var validFileExtensions = ['image/x-png', 'image/png', 'image/gif', 'image/jpeg', 'image/jpg'];
            console.log(type);

            if (jQuery.inArray(type, validFileExtensions) !== -1) {
                if (my_file_size < 10000000) {
                    if (id == 'profileImage1') {
                        $("#crop-avatar").children().bind('click', function () {
                            return false;
                        });
                    } else {
                        $("#crop-avatar1").children().bind('click', function () {
                            return false;
                        });
                    }

                    if (running == 1) {

                        //alert("Please Wait..");
                        return false;
                    } else {

                        if (this.support.formData) {
                            inp = 1;
                            this.ajaxUpload();
                            return false;
                        }
                    }
                } else {
                    $('#avatar-modal .row').hide();
                    var msg = "File size should be less then 10 MB";
                    var $alert = [
                        '<div id="errMsg" class="alert alert-danger avatar-alert alert-dismissable">',
                        '<button type="button" class="close" data-dismiss="alert">&times;</button>',
                        msg,
                        '</div>'
                    ].join('');
                    notifyalert(msg, 'error');
                    this.$avatarUpload.after($alert);
                    setTimeout(function () {
                        $('#errMsg').hide();
                    }, 3000);
                    return false;
                }
            } else {
                $('#avatar-modal .row').hide();
                var msg = "Invalid file type! supported file format .png, .jpg, .gif, .bmp";
                var $alert = [
                    '<div id="errMsg" class="alert alert-danger avatar-alert alert-dismissable">',
                    '<button type="button" class="close" data-dismiss="alert">&times;</button>',
                    msg,
                    '</div>'
                ].join('');
                notifyalert(msg, 'error');
                this.$avatarUpload.after($alert);
                setTimeout(function () {
                    $('#errMsg').hide();
                }, 3000);
                return false;
            }
        },
        rotate: function (e) {
            var data;

            if (this.active) {
                data = $(e.target).data();

                if (data.method) {
                    this.$img.cropper(data.method, data.option);
                }
            }
        },
        zoom: function (e) {
            var data;
            console.log(this.active);
            if (this.active) {
                data = $(e.target).data();

                if (data.method) {

                    this.$img.cropper(data.method, data.option);
                }
            }
        },
        isImageFile: function (file) {
            if (file.type) {
                return /^image\/\w+$/.test(file.type);
            } else {
                return /\.(jpg|jpeg|png|gif)$/.test(file);
            }
        },
        startCropper: function () {

            var _this = this;

            if (this.active) {
                this.$img.cropper('replace', this.url);
            } else {

                this.$img = $('<img src="' + this.url + '">');
                this.$avatarWrapper.empty().html(this.$img);

                this.$img.cropper({
                    aspectRatio: AsRatio,
                    dragMode: 'move',
                    viewMode: 2,
                    preview: '',
                    strict: true,
                    width: _w,
                    height: _h,
                    minContainerWidth:  568,//(imageRatio === 'circular' ? 570 : 568),
                    minContainerHeight: (imageRatio === 'circular' ? 570 : _h),
                    movable: false,
                    crop: function (data) {
                        var json = [
                            '{"x":' + data.x,
                            '"y":' + data.y,
                            '"height":' + data.height,
                            '"width":' + data.width,
                            '"rotate":' + data.rotate + '}'
                        ].join();

                        _this.$avatarData.val(json);
                    }
                });

                this.active = true;
            }



            this.$avatarModal.on('hidden.bs.modal', function () {


                _this.$avatarPreview.empty();
                _this.stopCropper();
                //$("#crop-avatar").children().unbind('click');
                //$("#crop-avatar1").children().unbind('click');
            });
        },
        stopCropper: function () {

            if (this.active) {

                this.$img.cropper('destroy');
                this.$img.remove();
                this.active = false;
                //running=0;

            }
        },
        ajaxUpload: function () {

            var url = this.$avatarForm.attr('action') + "?minCropBoxWidth=" + minCropBoxWidth + "&&minCropBoxHeight=" + minCropBoxHeight,
                    data = new FormData(this.$avatarForm[0]),
                    _this = this;

            running = 1;
            console.log(running + " 6");

            $.ajax(url, {
                type: 'post',
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                beforeSend: function () {
                    _this.submitStart();
                },
                success: function (data) {
                    var str=data.result;
                    var im=str.split('/').pop();

                    // works only when you give path to controller
                    if (uploadpath != '') {
                        $.ajax({
                            type: "post",
                            url: uploadpath, // change path on server
                            data: 'val=' + data.result,
                            success: function (data2) {
                                if (data2) {
                                    location.reload();
                                }
                            }
                        });
                    }
                    var newimgurl=domain2 +'/public/admincss/images/'+im;
                    $('.inputhidden').val(im);
                    $('.profile-pic').attr('src', newimgurl);

                    _this.submitDone(data);

                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    _this.submitFail(textStatus || errorThrown);
                },
                complete: function () {
                    _this.submitEnd();
                }
            });
        },
        syncUpload: function () {
            this.$avatarSave.click();
        },
        submitStart: function () {
            // this.$loading.fadeIn();
            running = 1;
            //console.log(running + " 7");
            $("#" + fillId).attr('src', default_image);
            $("#" + imageMe).show();
            this.$avatarModal.modal('hide');

            $("#" + disabledId).prop("disabled", true);
        },
        submitDone: function (data) {
            if ($.isPlainObject(data) && data.state === 200) {
                if (data.result) {
                    this.url = data.result;

                    if (this.support.datauri || this.uploaded) {
                        this.uploaded = false;
                        this.cropDone();
                    } else {
                        this.uploaded = true;
                        this.$avatarSrc.val(this.url);
                        this.startCropper();

                    }
                    $("#" + disabledId).prop("disabled", false);
                    this.$avatarInput.val('');
                } else if (data.message) {
                    this.alert(data.message);
                }
            } else {
                this.alert('Failed to response');
            }


        },
        submitFail: function (msg) {
            this.alert(msg);
            this.$avatarInput.val('');
            $("#" + disabledId).prop("disabled", false);
            running = 0;
            console.log(running + " 9");
        },
        submitEnd: function () {
            this.$loading.fadeOut();
            this.$avatarInput.val('');
            $("#" + disabledId).prop("disabled", false);
            running = 0;

        },
        cropDone: function () {
            this.$avatarForm.get(0).reset();
            // this.$avatar.attr('src', this.url);
            $('#' + profileImage).attr('value', this.url);
            url = this.url;
            request = new XMLHttpRequest();
            request.onprogress = onProgress;
            request.onload = onComplete;
            request.onerror = onError;

            var $progress = document.querySelector('#' + fillId);
            request.open('GET', url, true);
            request.overrideMimeType('text/plain; charset=x-user-defined');
            request.send(null);

            this.stopCropper();
            this.$avatarModal.modal('hide');

        },
        alert: function (msg) {
            var $alert = [
                '<div class="alert alert-danger avatar-alert alert-dismissable">',
                '<button type="button" class="close" data-dismiss="alert">&times;</button>',
                msg,
                '</div>'
            ].join('');

            this.$avatarUpload.after($alert);
        }
    };


    var request, url;

    function onProgress(event) {
        if (!event.lengthComputable) {
            return;
        }
        var loaded = event.loaded;
        var total = event.total;
        var progress = (loaded / total).toFixed(2);
        $("#" + fillId).attr('src', '/transparent.png');
        $("#" + imageMe).show();
        running = 1;
        console.log(running + " 10");

    }

    function onComplete(event) {
        console.log('fired');
        $("#" + fillId).attr('src', url);
        $("#" + imageMe).hide();
        // showPreview(); //show preview of first Img (function define in addimage.php)
        running = 0;
        if ($('.gUploaded').length < 3) {
            $('#addbox').show();
        }
        ShowPreview();
        $('.imagePostbtn').prop('disabled', false);
        //console.log(running+" 11");
        $("#crop-avatar").children().unbind('click');
        $("#crop-avatar1").children().unbind('click');
        if (id == 'Gallery_0' || id == 'Gallery_1' || id == 'Gallery_2' || id == 'Gallery_3' || id == 'Gallery_4') {
            // savegalleryimage(id);
        }
    }

    function onError(event) {
        console.log('error');

        running = 0;
    }

    function s3_upload_ajax(dataset, id) {
        var key = '';
        var msg = '';
        console.log("success_uplod_ajax" + id);
        if (id == 'hiddenDiv' || id == 'hiddenDiv1') {

            if (id == 'hiddenDiv') {
                key = 'cover_image';
            } else if (id == 'hiddenDiv1') {
                key = 'user_image';
            }
            console.log("ajax_data " + dataset.result);
            //$.ajax(url, {
            $.ajax({
                type: 'POST',
                url: base_url + 'user/profile/upload_cover',
                data: {"key": key, "name": dataset.result},

                success: function (response) {
                    var obj = jQuery.parseJSON(response);
                    var error = obj.response;
                    msg = obj.message;
                    console.log("ajax_data_success " + msg);
                    if (hideshow_remove_icon != '') {
                        $("#" + hideshow_remove_icon).parent().removeClass('display_none');
                        $("#" + hideshow_remove_icon).parent().addClass('display_block');
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    msg = 'false';
                },
                complete: function () {
                    msg = 'false';

                }
            });
            return msg;
        }
    }
});