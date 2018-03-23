
$(".getLinkedIn").click(function () {
    //Check User Is Already Logged IN
    if (IN.User.isAuthorized()) {
        getProfileData();
    } else {
        IN.User.authorize();
        IN.Event.on(IN, "auth", getProfileData);
        //onLinkedInLoad();
    }
});
 

function getProfileData() {
    if (IN.User.isAuthorized()) {
        IN.API.Raw("/people/~:(id,email-address,first-name,last-name,formatted-name,public-profile-url,picture-url)").result(onSuccess).error(onError);
    }
}

var domain = window.location.origin;
// Handle the successful return from the API call
function onSuccess(data) {
    
    var csrf_token = $('#csrfToken').val();
    
     $.ajax({
            url: domain + "/req/ajax_post_linkedin",
            type: 'post',
            dataType: 'json',
            data:{ response:JSON.stringify(data),csrf_token:csrf_token },
            
            success: function (data) {
                window.location = domain + '/web/Dashboard';
            },error : function (data) {
                console.log('error linkedin');
            }       
        });
    
}

// Handle an error response from the API call
function onError(error) {
    console.log(error);
}   