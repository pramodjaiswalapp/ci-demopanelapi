
var domain = window.location.origin;
// Check FB ID in DB and set session if it exists
function replace_login(response) {
    FB.api('/me', {
        locale: 'en_US',
        fields: 'id,first_name,last_name,email,link,gender,locale,picture'
    },
    function (response) {
        var csrf = $('#csrfToken').val();

        $.post(domain + "/req/ajax_post_login", {csrf_token: csrf, response: response}, function (data) {
            window.location = domain + '/web/Dashboard';

        }).error(function () {
            window.location.reload();
        })
    });
}

//Facebook Login Function
function fb_login() {
    FB.login(function (response) {
        console.log(response);
        replace_login(response);
    }, {
        scope: 'public_profile',
        return_scopes: true
    });
}

//facebook Logout Function
function fb_logOut() {
    FB.logout(function (response) {
        // Person is now logged out
        console.log(response);
    });
}

//facebook login Status call back
function statusChangeCallback(response) {
    var fb_status = response.status;
    console.log(fb_status);
    switch (fb_status) {
        case "connected" :
            replace_login();
            break;
        case "not_authorized" :
            fb_login();
            break;
        case "unknown" :
            // fb_logOut();
            fb_login();
            break;
    }
}

//Check facebook user Login Status
function checkFacebookStatus() {
    FB.getLoginStatus(function (response) {
        statusChangeCallback(response);
    });
}


(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id))
        return;
    js = d.createElement(s);
    js.id = id;
    js.src = 'https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.12&appId=2033632550258081&autoLogAppEvents=1';
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));


/* FB Login*/
function init() {
    //Facebook INIT 
    FB.init({
        appId: '2033632550258081',
        cookie: true,
        xfbml: true,
        version: 'v2.1'
    });
}

/*  On Fb Login button click, initiate FB Login*/
$(document).on("click", ".log_in_fb", function () {

    init();
    checkFacebookStatus();

});


(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement(s);
    js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
