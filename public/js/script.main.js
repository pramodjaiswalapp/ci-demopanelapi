$( function () {
    //rightside menu toggle
    $( '.leftsidebar-nav li' ).click( function ( e ) {
        var _ele = $( this ).find( 'ul.dropmenu' );
        if ( _ele.length > 0 ) {
            if ( $( this ).hasClass( 'active' ) ) {
                $( '.leftsidebar-nav li' ).removeClass( 'active' );
                $( 'ul.dropmenu' ).slideUp();
            }
            else {
                $( '.leftsidebar-nav li' ).removeClass( 'active' );
                $( 'ul.dropmenu' ).slideUp();
                $( this ).addClass( 'active' );
                $( this ).find( 'ul' ).slideDown();
            }
        }
        else {

            if ( $( this ).parent().hasClass( 'dropmenu' ) ) {
                e.stopPropagation();
                $( 'li ul.dropmenu li' ).removeClass( 'active' );
                $( this ).addClass( 'active' );
            }
            else {
                $( '.leftsidebar-nav li' ).removeClass( 'active' );
                $( this ).addClass( 'active' );
                $( 'ul.dropmenu' ).slideUp();
            }
        }

    } );
    //toggle menu
    $( '.trigger-right-nav' ).click( function () {
        $( 'body' ).toggleClass( 'body-xs' );
    } );
    //trigger account menu
    $( '.trigger-account-menu' ).click( function ( e ) {
        e.stopPropagation();
        $( this ).prev().addClass( 'active' );
    } );

    var ele = $( 'html' ).find( 'input[type="password"]' );
    $( ele ).parent().append( '<a  href="javascript:void(0);" class="typeToggle ficon ficon-right"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>' )
    $( 'body' ).on( 'click', '.typeToggle', function () {
        if ( $( this ).hasClass( 'open' ) ) {
            $( this ).removeClass( 'open' );
            $( this ).find( '.fa' ).addClass( 'fa-eye-slash' ).removeClass( 'fa-eye' );
            $( ele ).prop( 'type', 'password' );
        }
        else {
            $( this ).addClass( 'open' );
            $( this ).find( '.fa' ).addClass( 'fa-eye' ).removeClass( 'fa-eye-slash' );
            $( ele ).prop( 'type', 'text' );
        }
    } );

    //tab container js
    $( 'body' ).on( 'click', '.tabaction-wrap a', function () {
        $( '.tabaction-wrap a' ).removeClass( 'active' );
        $( '.tabpane' ).removeClass( 'open' );
        $( this ).addClass( 'active' );
        $( $( this ).attr( 'data-id' ) ).addClass( 'open' );
    } );

    $( document ).click( function () {
        $( 'header .drop-menu' ).removeClass( 'active' );
    } )
    //Media Image gallery
    $( document ).ready( function () {
        $( '#lightgallery' ).lightGallery();
    } );

    $( function () {
        $( '#postmodal' ).slimScroll( {
            height: '300px'
        } );
    } );

    //Moview icon gallery
    $( document ).ready( function () {
        $( '.dynamic' ).on( 'click', function ( e ) {
            var data_id = JSON.parse( window.atob( $( this ).attr( "data-id" ) ) );
            /**
             *
             */
            var data = {
                post_id: data_id.id,
                csrf_token: $( "#csrf_token" ).val()
            };

            var Response_data = [ ];
            $.ajax( {
                url: window.location.origin + "/AdminPanel/admin/Posts/getPost",
                type: "POST",
                data: data,
                cache: false,
                success: function ( res ) {

                    var obj = JSON.parse( res );
                    $( "#csrf_token" ).val( obj.csrfToken );
                    $( obj.responce ).each( function ( key, value ) {
                        temp = {
                            thumb: "https://appinventiv-development.s3.amazonaws.com/android/1516861760349/443&271.jpg",
                            src: "sachinchoolur.github.io/lightGallery/static/videos/video2.mp4"
                        }
                        Response_data.push( temp );
                    } );

                    $( this ).lightGallery( {
                        dynamic: true,
                        html: true,
                        mobileSrc: true,
                        videojs: true,
                        dynamic: true,
                        mode: 'lg-fade',
                        dynamicEl: Response_data
                    } );
                }
            } );
            /**
             *
             */
        } );
    } );

} );