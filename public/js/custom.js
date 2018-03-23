// sidebar in out
$('.toggle-btn-wrap').click(function(e){
    e.stopPropagation();
    if($(this).hasClass('active')) {
        $(this).removeClass('active');
        $('body').removeClass('body-sm');
        $('.left-panel-wrapper').removeClass('left-panel-show');
    }
    else {
        $(this).addClass('active');
        $('body').addClass('body-sm');
        $('.left-panel-wrapper').addClass('left-panel-show');
    }
})

// sidebar
$('.closeSidebar768').click(function(){
    $('.toggle-btn-wrap').removeClass('active');
    $('body').removeClass('body-sm');
})

//Action Tool tip js Start
$( ".user-td" ).click( function ( e ) {
    e.stopPropagation();
    $( ".user-call-wrap" ).hide();
    $( this ).find( ".user-call-wrap" ).show();
} );
$( "body" ).click( function () {
    $( ".user-call-wrap" ).hide();
} );

//Action Tool tip js Close


//Filter Show or hide JS
$( "#filter-side-wrapper" ).click( function ( e ) {
    e.stopPropagation();
    $( ".filter-wrap" ).addClass( "active" );
} );
$( "body" ).click( function ( e ) {
    if ( !$( e.target ).is( '.filter-wrap, .filter-wrap *' ) ) {
        $( ".filter-wrap" ).removeClass( "active" );
    }
} );
$( ".flt_cl" ).click( function ( e ) {
    $( ".filter-wrap" ).removeClass( "active" );
} );
//Filter Show or hide JS Close


//Select Picker Js Start
$( '.selectpicker' ).selectpicker( {
} );

//Select Picker Js Close


$( ".srch-box" ).keyup( function () {
    var char_length = $( this ).val().length;
    if ( char_length > 0 ) {
        $( ".srch-close-icon" ).addClass( "show-srch" );
    }
    else {
        $( ".srch-close-icon" ).removeClass( "show-srch" );
    }
} );

$( ".srch-close-icon" ).click( function () {
    $( this ).hide();
    $( ".search-box" ).val( '' );
    $( ".search-icon" ).show();
} );

$( ".go_back" ).click( function () {
    var pageUrl = $( '#pageUrl' ).val();
    window.location.href = pageUrl;
} );


function edit_subscription(name,price,id,desc,subs_recurring){
    
    var default_subs_type = 4;
    $('#edit_title').val(name);
    $('#edit_sub_price').val(price);
    $('#edit_description').val(desc);
    $('#id_form').val(id);
    
    $('input[name=one_time_option][value="'+subs_recurring+'"]').prop("checked", true);      
    if( subs_recurring === '' || subs_recurring === null ){
            $('input[name=one_time_option][value="'+default_subs_type+'"]').prop("checked", true);      
    }   
    $('#edit-subcribe-modal').modal('show');
    
}

//restrict special chars and spaces
function restrict_special_chars(event) {
    var k = event ? event.which : window.event.keyCode;
    if (k == 32) {
        return true;
    }
    
    var iChars = "!@#$%^&*()+=-[]\\\';,./{}|\":<>?_~";

    if (iChars.indexOf(event.key) !== -1) {
        event.preventDefault();
        return false;
    }
}


//check phone number validation

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    
    if( charCode == 46 ){
        return true;
    }else if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

//On Subscription recurrance selection in Add Subscription case
$('.add_subs_timespan').click(function () {
   
    $('#showtime').find('input[type=checkbox]:checked').removeAttr('checked');
    $(this).prop("checked", true);
});

//On Subscription recurrance selection in Edit Subscription case
$('.edit_subs_timespan').click(function () {
    
    $('#edit_showtime').find('input[type=checkbox]:checked').removeAttr('checked');
    $(this).prop("checked", true);
});

 $('#edit_title').bind("paste",function(e) {
     e.preventDefault();
 });
 $('#edit_sub_price').bind("paste",function(e) {
     e.preventDefault();
 });
 $('#title').bind("paste",function(e) {
     e.preventDefault();
 });
 $('#sub_price').bind("paste",function(e) {
     e.preventDefault();
 });