var id = 'profileImage1';
var minCropBoxWidth = "150";
var minCropBoxHeight = "100";
var profileImage = "profileImage1";
var fillId = "imagepicker2";
var disabledId = "next";
var imageMe = "imageMe";
var uploadpath = "uploadpath";
var running = 0;
var inp = 0;
//update in main.js for resizable
var checkResizable = false;
var _w = null;
var _h = null;
var _resiz = null;
var AsRatio = null;
var formelement = null;
var multiImageCheck = null;

var xwid = 800;
var yhig = 1640;

//update in main.js end
function showalertmessage( msg ) {

    $( "#alertMsgLayout" ).html( msg );
    $( "#finalalertLayout" ).show();
    $( "#alertdivLayout" ).fadeIn();
}

// add 1 more parameter name = 'resizable'
function callme( id1, minCropBoxWidth1, minCropBoxHeight1, fillId1, disabledId1, imageMe1, resizable, path, ratio, target = '', multiImage = false) {

    // alert(multiImage);

    //show laoder
    // $('.myloader').show();

    $( '#my-cropper' ).removeClass( 'circular' );
    $( '#avatarInput' ).val( '' );
    if ( running == 1 ) {
        showalertmessage( "Please wait..." );
        return false;
    }
    multiImageCheck = multiImage;
    formelement = target;
    id = id1;
    minCropBoxWidth = minCropBoxWidth1;
    minCropBoxHeight = minCropBoxHeight1;
    profileImage = id1;
    fillId = fillId1;
    disabledId = disabledId1;
    imageMe = imageMe1;
    uploadpath = path //useful for ajax path to upload image for
    //update in main.js for resizable
    checkResizable = resizable;
    switch ( ratio ) {
        case 'circular':
            console.log( ' in circular' );
            AsRatio = 1 / 1;
            _w = NaN;
            _h = NaN;
            _resiz = 1;
            $( '#my-cropper' ).addClass( 'circular' );
            break;
        case 'nofixed':
            console.log( ' in notfixed' );
            AsRatio = minCropBoxWidth / minCropBoxHeight;//
            _w = NaN;
            _h = NaN;
            //_w = minCropBoxWidth/2;
            //_h = minCropBoxHeight/2;
            _resiz = 1
            break;
        case 'timeline':
            console.log( ' in timeline' );
            AsRatio = 128 / 45;
            _w = NaN;
            _h = NaN;
            _resiz = 1
            break;
        default:
            console.log( ' in default' );
            AsRatio = 16 / 9;
            _w = minCropBoxWidth;
            _h = minCropBoxHeight;
            _resiz = 0
            break;

    }
    //update in main.js end


    if ( id == 'profileImage1' ) {
        // $("#crop-avatar").children().bind('click', function(){ return false; });
        return new CropAvatar( $( '#crop-avatar1' ) );

    }
    else {
        //$("#crop-avatar1").children().bind('click', function(){ return false; });
        return new CropAvatar( $( '#crop-avatar' ) );
}
}


function CropAvatar( $element ) {
    this.$container = $element;

    this.$avatarView = this.$container.find( '.media' );

    this.$avatar = this.$avatarView.find( 'img' );
    this.$avatarModal = this.$container.find( '#avatar-modal' );
    this.$avatarModal = $( '#avatar-modal' );
    this.$loading = $( '.loading' );

    this.$avatarForm = this.$avatarModal.find( '.avatar-form' );
    this.$avatarUpload = this.$avatarForm.find( '.avatar-upload' );
    this.$avatarSrc = this.$avatarForm.find( '.avatar-src' );
    this.$avatarData = this.$avatarForm.find( '.avatar-data' );
    this.$avatarInput = this.$avatarForm.find( '.avatar-input' );
    this.$avatarSave = this.$avatarForm.find( '.avatar-save' );
    this.$avatarBtns = this.$avatarForm.find( '.avatar-btns' );
    this.$avatarZoom = this.$avatarForm.find( '.avatar-zooms' );
    this.$avatarWrapper = this.$avatarModal.find( '.avatar-wrapper' );
    this.$avatarPreview = this.$avatarModal.find( '.avatar-preview' );

    this.init();

    //$('#crop-avatar').click();

}


(function ( factory ) {
    if ( typeof define === 'function' && define.amd ) {
        define( [ 'jquery' ], factory );
    }
    else if ( typeof exports === 'object' ) {
        // Node / CommonJS
        factory( require( 'jquery' ) );
    }
    else {
        factory( jQuery );
    }
})( function ( $ ) {

    'use strict';

    var console = window.console || {
        log: function () {}
    };
    CropAvatar.prototype = {
        constructor: CropAvatar,
        support: {
            fileList: !!$( '<input type="file">' ).prop( 'files' ),
            blobURLs: !!window.URL && URL.createObjectURL,
            formData: !!window.FormData
        },
        init: function () {
            this.support.datauri = this.support.fileList && this.support.blobURLs;

            if ( !this.support.formData ) {
                this.initIframe();
            }

            this.initTooltip();
            this.initModal();
            this.addListener();


        },
        addListener: function () {

            this.$avatarView.on( 'click', $.proxy( this.click, this ) );
            this.$avatarInput.on( 'change', $.proxy( this.change, this ) );
            this.$avatarForm.on( 'submit', $.proxy( this.submit, this ) );
            this.$avatarBtns.on( 'click', $.proxy( this.rotate, this ) );
            this.$avatarZoom.on( 'click', $.proxy( this.zoom, this ) );
            this.$avatarModal.modal( 'show' );
        },
        initTooltip: function () {
            this.$avatarView.tooltip( {
                placement: 'bottom'
            } );
        },
        initModal: function () {
            console.log( "1" );

            this.$avatarModal.modal( {
                show: false
            } );
        },
        initPreview: function () {
            var url = this.$avatar.attr( 'src' );

            this.$avatarPreview.html( '<img src="' + url + '">' );
        },
        initIframe: function () {
            var target = 'upload-iframe-' + (new Date()).getTime(),
                    $iframe = $( '<iframe>' ).attr( {
                name: target,
                src: ''
            } ),
                    _this = this;

            // Ready ifrmae
            $iframe.one( 'load', function () {

                // respond response
                $iframe.on( 'load', function () {
                    var data;

                    try {
                        data = $( this ).contents().find( 'body' ).text();
                    }
                    catch ( e ) {
                        console.log( e.message );
                    }

                    if ( data ) {
                        try {
                            data = $.parseJSON( data );
                        }
                        catch ( e ) {
                            console.log( e.message );
                        }

                        _this.submitDone( data );
                    }
                    else {
                        _this.submitFail( 'Image upload failed!' );
                    }

                    _this.submitEnd();

                } );
            } );

            this.$iframe = $iframe;
            this.$avatarForm.attr( 'target', target ).after( $iframe.hide() );
        },
        click: function () {

            if ( running == 1 ) {
                showalertmessage( "Please wait..." );
                return false;
            }
            if ( IsNumeric( edit_env ) ) {
                if ( edit_env == "0" ) {
                    return false;
                }
                else {

                    this.$avatarModal.modal( 'show' );
                }
            }
            else {
                this.$avatarModal.modal( 'show' );
            }
            //this.initPreview();
        },
        change: function () {
            var files,
                    file;

            if ( this.support.datauri ) {
                files = this.$avatarInput.prop( 'files' );

                if ( files.length > 0 ) {
                    file = files[0];

                    if ( this.isImageFile( file ) ) {
                        if ( this.url ) {
                            URL.revokeObjectURL( this.url ); // Revoke the old one
                        }

                        this.url = URL.createObjectURL( file );
                        this.startCropper();
                    }
                }
            }
            else {
                file = this.$avatarInput.val();

                if ( this.isImageFile( file ) ) {
                    this.syncUpload();
                }
            }
        },
        submit: function () {
            if ( !this.$avatarSrc.val() && !this.$avatarInput.val() ) {
                return false;
            }
            var my_file_size = $( "#avatarInput" )[0].files[0].size;
            var type = $( "#avatarInput" )[0].files[0].type;
            var validFileExtensions = [ 'image/x-png', 'image/png', 'image/gif', 'image/jpeg', 'image/jpg' ];
            console.log( type );

            if ( jQuery.inArray( type, validFileExtensions ) !== -1 ) {
                if ( my_file_size < 10000000 ) {
                    if ( id == 'profileImage1' ) {
                        $( "#crop-avatar" ).children().bind( 'click', function () {
                            return false;
                        } );
                    }
                    else {
                        $( "#crop-avatar1" ).children().bind( 'click', function () {
                            return false;
                        } );
                    }

                    if ( running == 1 ) {

                        //alert("Please Wait..");
                        return false;
                    }
                    else {

                        if ( this.support.formData ) {
                            inp = 1;
                            this.ajaxUpload();
                            return false;
                        }
                    }
                }
                else {
                    $( '#errMsg' ).remove();
                    var msg = "File size should be less then 10 MB";
                    var $alert = [
                        '<div id="errMsg" class="alert alert-danger avatar-alert alert-dismissable">',
                        '<button type="button" class="close" data-dismiss="alert">&times;</button>',
                        msg,
                        '</div>'
                    ].join( '' );

                    this.$avatarUpload.after( $alert );
                    setTimeout( function () {
                        $( '#errMsg' ).hide();
                    }, 3000 );
                    return false;
                }
            }
            else {
                var msg = "Invalid file type! supported file format .png, .jpg, .gif, .bmp";
                var $alert = [
                    '<div id="errMsg" class="alert alert-danger avatar-alert alert-dismissable">',
                    '<button type="button" class="close" data-dismiss="alert">&times;</button>',
                    msg,
                    '</div>'
                ].join( '' );

                this.$avatarUpload.after( $alert );
                setTimeout( function () {
                    $( '#errMsg' ).hide();
                }, 3000 );
                return false;
            }
        },
        rotate: function ( e ) {
            var data;

            if ( this.active ) {
                data = $( e.target ).data();

                if ( data.method ) {
                    this.$img.cropper( data.method, data.option );
                }
            }
        },
        zoom: function ( e ) {
            var data;
            console.log( this.active );
            if ( this.active ) {
                data = $( e.target ).data();

                if ( data.method ) {

                    this.$img.cropper( data.method, data.option );
                }
            }
        },
        isImageFile: function ( file ) {
            if ( file.type ) {
                return /^image\/\w+$/.test( file.type );
            }
            else {
                return /\.(jpg|jpeg|png|gif)$/.test( file );
            }
        },
        startCropper: function () {

            var _this = this;
            //update in main.js for resizable

            if ( this.active ) {
                this.$img.cropper( 'replace', this.url );
            }
            else {

                this.$img = $( '<img src="' + this.url + '">' );
                this.$avatarWrapper.empty().html( this.$img );

                this.$img.cropper( {
                    viewMode: 3,
                    //aspectRatio: AsRatio,
                    aspectRatio: NaN,
                    dragMode: '0',
                    preview: 0,
                    strict: false,
                    minCropBoxWidth: _w,
                    minCropBoxHeight: _h,
                    width: _w,
                    height: _h,
                    //autoCropArea: 0.6,
                    //cropBoxResizable: _resiz,
                    cropBoxResizable: true,
                    //update in main.js for resizable end


                    touchDragZoom: 0,
                    //mouseWheelZoom:0,
                    crop: function ( data ) {
                        xwid = data.width;
                        yhig = data.height;

                        console.log( xwid + ' == == == == ' + yhig );
                        var json = [
                            '{"x":' + data.x,
                            '"y":' + data.y,
                            '"height":' + data.height,
                            '"width":' + data.width,
                            '"rotate":' + data.rotate + '}'
                        ].join();

                        _this.$avatarData.val( json );
                    }
                } );

                this.active = true;
            }


            this.$avatarModal.on( 'hidden.bs.modal', function () {


                _this.$avatarPreview.empty();
                _this.stopCropper();
                //$("#crop-avatar").children().unbind('click');
                //$("#crop-avatar1").children().unbind('click');
            } );
        },
        stopCropper: function () {

            if ( this.active ) {

                this.$img.cropper( 'destroy' );
                this.$img.remove();
                this.active = false;
                //running=0;

            }
        },
        ajaxUpload: function () {

            /*var url = this.$avatarForm.attr('action') + "?minCropBoxWidth=" + minCropBoxWidth + "&&minCropBoxHeight=" + minCropBoxHeight,
             data = new FormData(this.$avatarForm[0]),
             _this = this;*/

            var url = this.$avatarForm.attr( 'action' ) + "?minCropBoxWidth=" + xwid + "&&minCropBoxHeight=" + yhig,
                    data = new FormData( this.$avatarForm[0] ),
                    _this = this;
            running = 1;
            console.log( running + " 6" );
            //console.log(xwid+' == == == == '+yhig);
            $.ajax( url, {
                type: 'post',
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                beforeSend: function () {
                    _this.submitStart();
                },
                success: function ( data ) {
                    var str = data.result;
                    var im = str.split( '/' ).pop();

                    // works only when you give path to controller
                    if ( uploadpath != '' ) {
                        $.ajax( {
                            type: "post",
                            url: uploadpath, // change path on server
                            data: 'val=' + data.result,
                            success: function ( data2 ) {
                                if ( data2 ) {
                                    location.reload();
                                }
                            }
                        } );
                    }
                    var newimgurl = domain2 + '/public/cropper/images/' + im;
                    var getcwdurl = current_directory + '/public/cropper/images/' + im;
                    // alert(multiImageCheck);
                    if ( multiImageCheck == 'true' )
                    {
                        // html for multiple upload as preview
                        var uploadBox = '<figure class="gallery_img m-b-xs" style="position:relative; " id="fig6"> <img id="imgdiv6" src="' + newimgurl + '"><span class="ficon-r"><i onclick="removeGal(this);" class="fa fa-times m_t_2 cx pull-right"></i></span><input id="imghidden6" class="inputhidden" type="hidden" name="imgurl' + formelement + '[]" value="' + im + '"><div id="imageMe6" style="display: none;"><div class="sk-spinner sk-spinner-rotating-plane" style="position:absolute; top: 40%; left: 40%;"></div></div></figure>';
                        $( '#UploadedImageContainer' + formelement ).append( uploadBox );

                        // check uploaded image count and hide uploader box
                        if ( $( "div#UploadedImageContainer figure" ).size() > 9 ) {
                            $( '.image_upload_wrapper' ).hide();
                        }
                    }
                    else
                    {
                        // console.log('t: '+formelement);
                        if ( formelement == '' ) { // for single image upload
                            $( '.inputhidden' ).val( getcwdurl );
                            $( '#image_path' ).val( getcwdurl );
                            //$( '.profile-pic' ).attr( 'src', newimgurl );
                            $( '#profile-pic' ).css( 'background-image', "url('" + newimgurl + "')" );
                            // $('.profile-pic').parent().find('.imgage_upload_circle').style.background=url('');
                            $( '.profile-pic' ).parent().css( 'background', 'url("")' );
                        }
                        else {   // dynamic image set after image upload
                            $( '.inputhidden' + formelement ).val( im );
                            $( '.profile-pic' + formelement ).attr( 'src', newimgurl );
                            $( '.profile-pic' + formelement ).parent().css( 'background', 'url("")' );
                            // $('.profile-pic'+formelement).parent().css('background','url(" ")');
                        }
                    }

                    _this.submitDone( data );
                    $( '.sv-btn' ).prop( 'disabled', false );

                },
                error: function ( XMLHttpRequest, textStatus, errorThrown ) {
                    _this.submitFail( textStatus || errorThrown );
                },
                complete: function () {
                    _this.submitEnd();
                }
            } );
        },
        syncUpload: function () {
            this.$avatarSave.click();
        },
        submitStart: function () {
            $( '.myloader' ).show();
            $( '.sv-btn' ).prop( "disabled", true );
            // this.$loading.fadeIn();
            running = 1;
            console.log( running + " 7" );
            $( "#" + fillId ).attr( 'src', 'images/transparent.png' );
            $( "#" + imageMe ).show();
            // $('.change').html('uploading');
            $( '.change' ).prop( 'disabled', true );
            // this.$avatarModal.modal('hide');

            $( "#" + disabledId ).prop( "disabled", true );
        },
        submitDone: function ( data ) {
            if ( $.isPlainObject( data ) && data.state === 200 ) {
                if ( data.result ) {
                    this.url = data.result;

                    if ( this.support.datauri || this.uploaded ) {
                        this.uploaded = false;
                        this.cropDone();
                    }
                    else {
                        this.uploaded = true;
                        this.$avatarSrc.val( this.url );
                        this.startCropper();
                    }
                    $( "#" + disabledId ).prop( "disabled", false );
                    this.$avatarInput.val( '' );
                }
                else if ( data.message ) {
                    this.alert( data.message );
                }
            }
            else {
                this.alert( 'Failed to response' );
            }

            //hide popup and remove disabled button
            $( '.myloader' ).hide();
            $( '.sv-btn' ).prop( "disabled", false );


        },
        submitFail: function ( msg ) {
            this.alert( msg );
            this.$avatarInput.val( '' );
            $( "#" + disabledId ).prop( "disabled", false );
            running = 0;
            console.log( running + " 9" );

            //hide popup and remove disabled button
            $( '.myloader' ).hide();
            $( '.sv-btn' ).prop( "disabled", false );
        },
        submitEnd: function () {
            this.$loading.fadeOut();
            this.$avatarInput.val( '' );
            $( "#" + disabledId ).prop( "disabled", false );
            running = 0;

        },
        cropDone: function () {
            this.$avatarForm.get( 0 ).reset();
            // this.$avatar.attr('src', this.url);
            $( '#' + profileImage ).attr( 'value', this.url );
            /* url = this.url;
             request = new XMLHttpRequest();
             request.onprogress = onProgress;
             request.onload = onComplete;
             request.onerror = onError;

             var $progress = document.querySelector( '#' + fillId );
             request.open( 'GET', url, true );
             request.overrideMimeType( 'text/plain; charset=x-user-defined' );
             request.send( null );
             */
            this.stopCropper();
            this.$avatarModal.modal( 'hide' );

        },
        alert: function ( msg ) {
            var $alert = [
                '<div class="alert alert-danger avatar-alert alert-dismissable">',
                '<button type="button" class="close" data-dismiss="alert">&times;</button>',
                msg,
                '</div>'
            ].join( '' );

            this.$avatarUpload.after( $alert );
        }
    };


    var request, url;

    function onProgress( event ) {
        if ( !event.lengthComputable ) {
            return;
        }
        var loaded = event.loaded;
        var total = event.total;
        var progress = (loaded / total).toFixed( 2 );
        $( "#" + fillId ).attr( 'src', 'images/transparent.png' );
        $( "#" + imageMe ).show();
        running = 1;
        console.log( running + " 10" );
    }

    function onComplete( event ) {

        $( "#" + fillId ).attr( 'src', url );
        //console.log( url );
        $( "#" + imageMe ).hide();
        running = 0;
        //console.log(running+" 11");
        $( "#crop-avatar" ).children().unbind( 'click' );
        $( "#crop-avatar1" ).children().unbind( 'click' );
        if ( id == 'Gallery_0' || id == 'Gallery_1' || id == 'Gallery_2' || id == 'Gallery_3' || id == 'Gallery_4' ) {
            // savegalleryimage(id);
        }
    }

    function onError( event ) {
        console.log( "Don't Know ERROR" );

        running = 0;
        //console.log(running+" 12");
    }
} );
