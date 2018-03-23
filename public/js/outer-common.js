
//-----------------------------------------------------------------------
/**
 *@Description Here is the methods starts for the form validations in admin using jquery validator.
 *
 */
$( document ).ready( function () {

    $( '.alert-success' ).fadeOut( 5000 );
    /**
     * @name validate add app version form
     * @description This method is used to validate add app version form.
     *
     */
    $( "#login_admin_form" ).validate( {
        errorClass: "error-label",
        rules: {
            email: {
                required: true,
                email: true,
            },
            password: {
                required: true,

            },
        },
        messages: {
            email: {
                required: string.loginemail,

            },
            password: {
                required: string.loginpassword,

            },
        },
        submitHandler: function ( form ) {
            form.submit();
        }
    } );

    /**
     * @name validate add app version form
     * @description This method is used to validate add app version form.
     *
     */
    $( "#forget_pwd_admin_form" ).validate( {
        errorClass: "error-label",
        rules: {
            email: {
                required: true,
                email: true
            },
        },
        messages: {
            email: {
                required: string.loginemail,

            },
        },
        submitHandler: function ( form ) {
            form.submit();
        }
    } );

    $( 'input' ).keypress( function ( e ) {
        var inp = $.trim( $( this ).val() ).length;
        if ( inp == 0 && e.which === 32 ) {
            return false;
        }
    } );

    // Restrict copy - paste on web login.
    $( '#web_login_pass' ).bind( "cut copy paste", function ( e ) {
        e.preventDefault();

    } );


} );

function validatepassword() {
    var password = $.trim( $( '#password' ).val() );
    var cnfpassword = $.trim( $( '#cnfpassword' ).val() );

    var flag = 0;
    if ( password.length == 0 || password.length < 8 ) {
        $( '.passwordErr' ).css( { opacity: 1 } );
        $( '.passwordErr' ).text( string.passwordErr );
    }
    else {
        $( '.passwordErr' ).css( { opacity: 0 } );
        $( '.passwordErr' ).text( '' );
        flag++;
    }
    if ( cnfpassword.length == 0 || (password != cnfpassword) ) {
        $( '.cnfpasswordErr' ).text( string.cnfPassowrdErr );
        $( '.cnfpasswordErr' ).css( { opacity: 1 } );
    }
    else {
        $( '.cnfpasswordErr' ).css( { opacity: 0 } );
        $( '.cnfpasswordErr' ).text( '' );
        flag++;
    }
    if ( flag == 2 ) {
        return true;
    }
    else {
        return false;
    }

}

function CheckforNum( e ) {
    //console.log(String.fromCharCode(e.keyCode));
    // Allow: backspace, delete, tab, escape, enter and  +
    if ( $.inArray( e.keyCode, [ 46, 8, 9, 27, 13 ] ) !== -1 || (e.which === 187) || (e.keyCode == 65 && e.ctrlKey === true) || (e.keyCode == 86 && e.ctrlKey === true) || (e.keyCode == 67 && e.ctrlKey === true) || (e.keyCode == 88 && e.ctrlKey === true) || (e.keyCode >= 35 && e.keyCode <= 39) ) {
        // let it happen, don't do anything
        return;
    }
    // Ensure that it is a number and stop the keypress
    if ( (e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105) ) {
        e.preventDefault();
    }
}

