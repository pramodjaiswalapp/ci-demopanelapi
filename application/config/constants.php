<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 /*
   |--------------------------------------------------------------------------
   | Display Debug backtrace
   |--------------------------------------------------------------------------
   |
   | If set to TRUE, a backtrace will be displayed along with php errors. If
   | error_reporting is disabled, the backtrace will not display, regardless
   | of this setting
   |
  */
 defined( 'SHOW_DEBUG_BACKTRACE' ) OR define( 'SHOW_DEBUG_BACKTRACE', TRUE );

 /*
   |--------------------------------------------------------------------------
   | File and Directory Modes
   |--------------------------------------------------------------------------
   |
   | These prefs are used when checking and setting modes when working
   | with the file system.  The defaults are fine on servers with proper
   | security, but you may wish (or even need) to change the values in
   | certain environments (Apache running a separate process for each
   | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
   | always be used to set the mode correctly.
   |
  */
 defined( 'FILE_READ_MODE' ) OR define( 'FILE_READ_MODE', 0644 );
 defined( 'FILE_WRITE_MODE' ) OR define( 'FILE_WRITE_MODE', 0666 );
 defined( 'DIR_READ_MODE' ) OR define( 'DIR_READ_MODE', 0755 );
 defined( 'DIR_WRITE_MODE' ) OR define( 'DIR_WRITE_MODE', 0755 );

 /*
   |--------------------------------------------------------------------------
   | File Stream Modes
   |--------------------------------------------------------------------------
   |
   | These modes are used when working with fopen()/popen()
   |
  */
 defined( 'FOPEN_READ' ) OR define( 'FOPEN_READ', 'rb' );
 defined( 'FOPEN_READ_WRITE' ) OR define( 'FOPEN_READ_WRITE', 'r+b' );
 defined( 'FOPEN_WRITE_CREATE_DESTRUCTIVE' ) OR define( 'FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb' ); // truncates existing file data, use with care
 defined( 'FOPEN_READ_WRITE_CREATE_DESTRUCTIVE' ) OR define( 'FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b' ); // truncates existing file data, use with care
 defined( 'FOPEN_WRITE_CREATE' ) OR define( 'FOPEN_WRITE_CREATE', 'ab' );
 defined( 'FOPEN_READ_WRITE_CREATE' ) OR define( 'FOPEN_READ_WRITE_CREATE', 'a+b' );
 defined( 'FOPEN_WRITE_CREATE_STRICT' ) OR define( 'FOPEN_WRITE_CREATE_STRICT', 'xb' );
 defined( 'FOPEN_READ_WRITE_CREATE_STRICT' ) OR define( 'FOPEN_READ_WRITE_CREATE_STRICT', 'x+b' );

 /*
   |--------------------------------------------------------------------------
   | Exit Status Codes
   |--------------------------------------------------------------------------
   |
   | Used to indicate the conditions under which the script is exit()ing.
   | While there is no universal standard for error codes, there are some
   | broad conventions.  Three such conventions are mentioned below, for
   | those who wish to make use of them.  The CodeIgniter defaults were
   | chosen for the least overlap with these conventions, while still
   | leaving room for others to be defined in future versions and user
   | applications.
   |
   | The three main conventions used for determining exit status codes
   | are as follows:
   |
   |    Standard C/C++ Library (stdlibc):
   |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
   |       (This link also contains other GNU-specific conventions)
   |    BSD sysexits.h:
   |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
   |    Bash scripting:
   |       http://tldp.org/LDP/abs/html/exitcodes.html
   |
  */

 defined( 'BASE_URL' ) OR define( 'BASE_URL', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'] ); // no errors
 defined( 'EXIT_SUCCESS' ) OR define( 'EXIT_SUCCESS', 0 ); // no errors
 defined( 'EXIT_ERROR' ) OR define( 'EXIT_ERROR', 1 ); // generic error
 defined( 'EXIT_CONFIG' ) OR define( 'EXIT_CONFIG', 3 ); // configuration error
 defined( 'EXIT_UNKNOWN_FILE' ) OR define( 'EXIT_UNKNOWN_FILE', 4 ); // file not found
 defined( 'EXIT_UNKNOWN_CLASS' ) OR define( 'EXIT_UNKNOWN_CLASS', 5 ); // unknown class
 defined( 'EXIT_UNKNOWN_METHOD' ) OR define( 'EXIT_UNKNOWN_METHOD', 6 ); // unknown class member
 defined( 'EXIT_USER_INPUT' ) OR define( 'EXIT_USER_INPUT', 7 ); // invalid user input
 defined( 'EXIT_DATABASE' ) OR define( 'EXIT_DATABASE', 8 ); // database error
 defined( 'EXIT__AUTO_MIN' ) OR define( 'EXIT__AUTO_MIN', 9 ); // lowest automatically-assigned error code
 defined( 'EXIT__AUTO_MAX' ) OR define( 'EXIT__AUTO_MAX', 125 ); // highest automatically-assigned error code

 /* HEADER STATUS CONSTANTS */
 defined( "UNAUTHORIZED_ACCESS" ) OR define( "UNAUTHORIZED_ACCESS", 401 );
 defined( "NOT_AUTHENTICATED" ) OR define( "NOT_AUTHENTICATED", 403 );
 defined( "ACCESS_TOKEN_NOT_SET" ) OR define( "ACCESS_TOKEN_NOT_SET", 406 );

 /* FIELD DOESNT MATCH */
 defined( "OLD_PASSWORD_MISMATCH" ) OR define( "OLD_PASSWORD_MISMATCH", 490 );
 defined( "PASSWORD_MISMATCH" ) OR define( "PASSWORD_MISMATCH", 491 );
 defined( "NEW_PASSWORD_SAME" ) OR define( "NEW_PASSWORD_SAME", 492 );

 /* User status */
 defined( 'ACTIVE' ) OR define( 'ACTIVE', 1 );
 defined( 'BLOCKED' ) OR define( 'BLOCKED', 2 );
 defined( 'DELETED' ) OR define( 'DELETED', 3 );
 defined( 'INACTIVE' ) OR define( 'INACTIVE', 0 );
 defined( 'DEFAULT_DB_DATE_TIME_FORMAT' ) OR define( 'DEFAULT_DB_DATE_TIME_FORMAT', date( "Y-m-d H:i:s" ) );
 defined( 'COOKIE_EXPIRY_TIME' ) OR define( "COOKIE_EXPIRY_TIME", 86400 * 7 );

// device type

 defined( 'ANDROID' ) OR define( 'ANDROID', 1 );
 defined( 'IPHONE' ) OR define( 'IPHONE', 2 );

//update type
 defined( 'NORMAL' ) OR define( 'NORMAL', 1 );
 defined( 'SKIPPABLE' ) OR define( 'SKIPPABLE', 2 );
 defined( 'FORCEFULLY' ) OR define( 'FORCEFULLY', 3 );

 defined( 'YES' ) OR define( 'YES', 1 );
 defined( 'NO' ) OR define( 'NO', 0 );

 /*
  * Basic Auth UserName and Password
  */
 define( 'AUTH_PASS', '12345' );
 define( 'AUTH_USER', 'admin' );
 /*
  * Upload Directories Constants
  */
 define( "UPLOAD_PATH", "/public/uploads/" );
 define( "PROJECT_NAME", "Reusable Components" );
 define( "UPLOAD_IMAGE_PATH", getcwd().UPLOAD_PATH );
 define( "IMAGE_PATH", 'http://'.$_SERVER['HTTP_HOST'].UPLOAD_PATH );
 define( "UPLOAD_THUMB_IMAGE_PATH", getcwd().UPLOAD_PATH."thumbs/" );
 define( "THUMB_IMAGE_PATH", 'http://'.$_SERVER['HTTP_HOST'].UPLOAD_PATH."thumbs/" );
 define( "DEFAULT_IMAGE", 'public/images/default.png' );
 define( "TIMEZONE", 'UTC' );

//----------------ERROR MESSAGE CODE FOR CLIENT SIDE VALIDATIONS ---------------------------//

 define( 'SUCCESS_CODE', 200 );
 define( 'TRY_AGAIN_CODE', 201 );

 define( 'NO_DATA_FOUND', 202 );

 define( 'PARAM_REQ', 418 );

 define( 'INVALID_EMAIL', 419 );
 define( 'EMAIL_ALREADY_EXIST', 420 );

 define( 'ERROR_UPLOAD_FILE', 421 );
 define( 'INVALID_DATE_FORMAT', 423 );

 define( 'INVALID_MAX_LENGTHEMAIL', 425 );
 define( 'INVALID_PASSWORD_FORMAT', 426 );

 define( 'INVALID_LOGIN', 410 );
 define( 'SUCCESS_LOGIN', 200 );

 define( 'MISSING_HEADER', 207 );
 define( 'INVALID_HEADER', 206 );
 /*
  * Email Contants
  */
 define( 'EMAIL_SEND_SUCCESS', 200 );
 define( 'EMAIl_SEND_FAILED', 211 );

 /*
  * Access Token
  */
 define( 'INVALID_ACCESS_TOKEN', 100 );
 define( 'ACCESS_TOKEN_EXPIRED', 101 );
 define( 'MISSING_PARAMETER', 102 );

// ===================login type constants =========================//
 define( 'IS_SINGLE_DEVICE_LOGIN', 0 );
 define( 'LIMITED', 3 );
 define( 'ACCOUNT_BLOCKED', 101 );
 define( 'INVALID_CREDENTIALS', 102 );
 define( 'ACCOUNT_INACTIVE', 103 );
 /*
  * Forgot Password Codes
  */
 define( 'EMAIL_NOT_EXIST', 302 );
 /*
  * Reset password Codes
  */
 define( 'PASSWORD_ALREADY_SET', 301 );

 define( 'RECORD_NOT_EXISTS', 307 );

 /*
  * Friend Request Codes
  */
 define( 'REQUEST_ALREADY_SENT', 500 );
 define( 'REQUEST_ALREADY_RECEIVED', 501 );
 /*
  * Review already exist
  */

 define( 'MULTIPLE_REVIEW_ALLOWED', 1 );
 define( 'REVIEW_ALREADY_EXISTS', 505 );

 /*
  * Following Request Codes
  */

 define( 'ALREADY_FOLLOWING', 506 );
 /*
  * Already Favorite
  */
 define( 'ALREADY_FAVORITE', 507 );
 /*
  * Feeds
  */
 define( 'EMPTY_FEED', 510 );
 /*
  * Push Type
  */
 define( 'PUSH_SOUND', 'beep.mp3' );
 define( 'REQUEST_PUSH', 1 );
 define( 'REQUEST_ACCEPT_PUSH', 2 );
 define( 'FAVORITE_PUSH', 3 );
 define( 'FOLLOW_PUSH', 4 );
 define( 'COMMENT_PUSH', 5 );
 define( 'REVIEW_PUSH', 6 );
 define( 'CHAT_PUSH', 7 );
 /*
  * Encrypt Key
  */
 defined( "OPEN_SSL_KEY" ) OR define( 'OPEN_SSL_KEY', '011b519a043dcb915314695e1ce560dd4e29dae06867cdb701ffc96350e18caf' );

 /* Custom privacy key */
 defined( 'CUSTOM' ) OR define( 'CUSTOM', 3 );

 /*
  * Manage Feed Codes
  */
 define( 'FEED_ALREADY_REPORTED', 202 );
 define( 'UNLIKE', 2 );

 /* S3 */
 define( 'BUCKET', 'appinventiv-development' );
//defined("AWS_ACCESSKEY")               OR define('AWS_ACCESSKEY', 'AKIAI65JDY3WE5MNU4NQ');
 defined( "AWS_ACCESSKEY" ) OR define( 'AWS_ACCESSKEY', 'AKIAIGTT2CNXI3KAGXSQ' );
//defined('AWS_SECRET_KEY')              OR define('AWS_SECRET_KEY', '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');
 defined( 'AWS_SECRET_KEY' ) OR define( 'AWS_SECRET_KEY', '22omXosExOVht2jJX00jvZa9sig8zmqj7OfTJffC' );
 defined( 'AWS_URI' ) OR define( 'AWS_URI', '' );
 defined( 'PATH_S3' ) OR define( 'PATH_S3', 'https://appinventiv-development.s3.amazonaws.com/' );
 defined( 'S3_DEV_URL' ) OR define( 'S3_DEV_URL', 'android/' );

 /* feed section notification part */
 defined( 'LIKE_POST' ) OR define( 'LIKE_POST', 1 );
 defined( 'COMMENT_POST' ) OR define( 'COMMENT_POST', 2 );
 defined( 'IN_COMMENT_POST' ) OR define( 'IN_COMMENT_POST', 3 );
 defined( 'SHARE_POST' ) OR define( 'SHARE_POST', 4 );
 defined( 'LIKE_TAGGED_POST' ) OR define( 'LIKE_TAGGED_POST', 5 );
 defined( 'TAGGED_POST' ) OR define( 'TAGGED_POST', 6 );
 defined( 'COMMENT_TAGGED_POST' ) OR define( 'COMMENT_TAGGED_POST', 7 );

 /*  Social media login related constants */

 defined( 'MALE_GENDER' ) OR define( 'MALE_GENDER', 1 );
 defined( 'FEMALE_GENDER' ) OR define( 'FEMALE_GENDER', 2 );
 defined( 'OTHER_GENDER' ) OR define( 'OTHER_GENDER', 3 );

 defined( 'FACEBOOK_LOGIN' ) OR define( 'FACEBOOK_LOGIN', 1 );
 defined( 'LINKEDIN_LOGIN' ) OR define( 'LINKEDIN_LOGIN', 2 );
 defined( 'GOOGLE_LOGIN' ) OR define( 'GOOGLE_LOGIN', 3 );
 defined( 'TWITTER_LOGIN' ) OR define( 'TWITTER_LOGIN', 4 );
 defined( 'INSTAGRAM_LOGIN' ) OR define( 'INSTAGRAM_LOGIN', 5 );

 defined( 'TWITTER_CONSUMER_TOKEN' ) OR define( 'TWITTER_CONSUMER_TOKEN', 'gfrcekIKbXZ2ZLd26FHUNYLaf' );
 defined( 'TWITTER_CONSUMER_SECRET' ) OR define( 'TWITTER_CONSUMER_SECRET', 'SADipjqwSXC5liot6rCJR2vdlc0HzvA1PrzNlOgypR0ZSfYiKH' );
 /*
  * Date Format
  */
 define( 'DATE_FORMAT', '%d %M %Y %H:%i %A' );

//Subscriptions - Adminpanel
 defined( 'RECURRING' ) OR define( 'RECURRING', 1 );
 defined( 'ONE_TIME' ) OR define( 'ONE_TIME', 2 );

 defined( 'RECURRING_DAY' ) OR define( 'RECURRING_DAY', 1 );
 defined( 'RECURRING_WEEK' ) OR define( 'RECURRING_WEEK', 2 );
 defined( 'RECURRING_MONTH' ) OR define( 'RECURRING_MONTH', 3 );
 defined( 'RECURRING_YEAR' ) OR define( 'RECURRING_YEAR', 4 );


//Social Login
 defined( 'INSTA_CLIENT_ID' ) OR define( 'INSTA_CLIENT_ID', 'b71478fff73e4b7498ec9570cae70d6f' );
 defined( 'INSTA_REDIRECT_URL' ) OR define( 'INSTA_REDIRECT_URL', 'http://reusable.applaurels.com/web/instagram' );
 defined( 'INSTA_AUTH_URL' ) OR define( 'INSTA_AUTH_URL', 'https://api.instagram.com/oauth/authorize/?client_id=' );
 defined( 'INSTA_ACCESS_TOKEN' ) OR define( 'INSTA_ACCESS_TOKEN', '7077480637.d90570a.999c8b99e2bc4ef78d6f06d0088928b6' );
 defined( 'INSTA_URL' ) OR define( 'INSTA_URL', 'https://api.instagram.com/v1/users/self/?access_token=' );

 define( 'INACTIVE_POST', 207 );


//API CONSTANT
 defined( 'LIMIT' ) OR define( 'LIMIT', 20 );
 defined( 'CHAT_LIMIT' ) OR define( 'CHAT_LIMIT', 5 );
 defined( 'FRIEND_LIMIT' ) OR define( 'FRIEND_LIMIT', 10 );
 defined( 'EXPORT_LIMIT' ) OR define( "EXPORT_LIMIT", 65000 );
