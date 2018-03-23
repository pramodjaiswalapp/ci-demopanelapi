/*GOOGLE SIGNUP HANDLERS BEGIN*/

(function () {
        	var po = document.createElement('script');
        	po.type = 'text/javascript';
        	po.async = true;
        	po.src = 'https://apis.google.com/js/client.js?onload=onLoadCallback';
        	var s = document.getElementsByTagName('script')[0];
        	s.parentNode.insertBefore(po, s);
})();

function handleResponse(resp){
    var userdetail = resp.result;
    
      
    var profile_data = {};
    
    profile_data["first_name"] = userdetail.name.givenName;
    profile_data["last_name"] = userdetail.name.familyName;
    profile_data["image"] = userdetail.image.url;
    profile_data["email"] = userdetail.emails[0].value;
    profile_data["google_id"] = userdetail.id;
    
    var csrf_token = $('#csrfToken').val();
        
    $.ajax({
        url: domain + "/req/ajax_post_google",
        type: 'post',
        dataType: 'json',
//            data:JSON.stringify(data),
        data:{ response:profile_data,csrf_token:csrf_token },

        success: function (data) {
            window.location = domain + '/web/Dashboard';
            console.log('success - redirect');
        },error : function (data) {
            console.log('error linkedin');
        }       
    });    
}

function onLoadCallback() {
    gapi.client.setApiKey('AIzaSyA-iVGNoEQkn5ra1w50qFeu6GcmgbKvdOA'); //set your API KEY
    gapi.client.load('plus', 'v1', function () {
    }); //Load Google + API
}

function googleLoginCallback(result) {
    if (result['status']['signed_in']) {
        var request = gapi.client.plus.people.get({
            'userId': 'me'
        });

        request.execute(function (resp) {
            handleResponse(resp);
        });
     

    }

}
/**/

$(".getGoogle").on("click", function () {
    var myParams = {
        'clientid': '93283559969-10bh6q6gnd6ngae1an7814tgtiuej581.apps.googleusercontent.com', //You need to set client id
        'cookiepolicy': 'single_host_origin',
        'callback': 'googleLoginCallback', //callback function
        'approvalprompt': 'force',
        'scope': 'https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.profile.emails.read'
    };
    var test = gapi.auth.signIn(myParams);
});