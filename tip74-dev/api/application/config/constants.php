<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

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
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

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
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code



/*---------Site Settings--------*/
/*------------------------------*/  

/*Site Related Settings*/
define('SITE_NAME', 'Tip74');
define('SITE_CONTACT_EMAIL', '-');
define('MULTISESSION', true);
define('PHONE_NO_VERIFICATION', false);
define('DATE_FORMAT',"%Y-%m-%d %H:%i:%s"); /* dd-mm-yyyy */
define('FOOTBALL_SPORT_API_NAME', 'SPORTMONKS');
define('SAVE_LIVE_FLAGS', true); // data feads 

define('ADMIN_ID', 1);
define('DEFAULT_SOURCE_ID', 1);
define('DEFAULT_DEVICE_TYPE_ID', 1);
define('DEFAULT_CURRENCY', 'USD');
define('REFERRAL_SIGNUP_BONUS', 50);
define('DEFAULT_PLAYER_CREDITS', 6.5);
define('DEFAULT_TIMEZONE', '+00:00');
define('TIMEZONE_DIFF_IN_SECONDS', 0);
define('IS_VICECAPTAIN', true);
define('MATCH_TIME_IN_HOUR', 300);

/* for push Notification */
define('CHANNEL_NAME', 'Alert*****');
define('ANDROID_SERVER_KEY', '********************************');

/*Social */
define('FACEBOOK_URL', 'https://www.facebook.com/');
define('TWITTER_URL', 'https://twitter.com/');
define('LINKEDIN_URL', 'https://www.linkedin.com/company/');
define('INSTAGRAM_URL', 'https://www.instagram.com/');

/* SportMonks Sports API Details */
define('SPORTMONKS_API_URL', 'https://soccer.sportmonks.com/api/v2.0/');
define('SPORTMONKS_TOKEN', 'nYCSju7NO1hXhJrZxs0LMhQ6xEVggh7n0bfQ7YE3OpVU5KfqReTc2Kd3RvTS');

/* PayUMoney Details */
define('PAYUMONEY_MERCHANT_KEY', '');
define('PAYUMONEY_MERCHANT_ID', '');
define('PAYUMONEY_SALT', '');

/* SMS API Details */
define('SMS_API_URL', 'https://login.bulksmsgateway.in/sendmessage.php');
define('SMS_API_USERNAME', '***');
define('SMS_API_PASSWORD', '***');

/* SENDINBLUE SMS API Details */
define('SENDINBLUE_SMS_API_URL', 'https://api.sendinblue.com/v3/transactionalSMS/sms');
define('SENDINBLUE_SMS_SENDER', '');
define('SENDINBLUE_SMS_API_KEY', '');


/* MSG91 SMS API Details */
define('MSG91_AUTH_KEY', '');
define('MSG91_SENDER_ID', '');
define('MSG91_FROM_EMAIL', '');

/* SENDGRID EMAIL TEMPLATE*/
define('SIGN_UP', 'd-2e22189d1aa3462aa2dd44145a40ed78');
define('CHANGE_PASSWORD', 'd-79a1bf2b9f494563a1bdc1c24c5201fb');
define('RECOVERY', 'd-9eec646277854d638357f2e9fb1c6ad3');

define('APP_LINK', 'http://');
switch (ENVIRONMENT)
{
  case 'local':
    /*Paths*/
    define('SITE_HOST', 'http://localhost/');
    define('ROOT_FOLDER', 'tip74-dev/');

    /*SMTP Settings*/
    define('SMTP_PROTOCOL', 'smtp');
    define('SMTP_HOST', 'smtp.gmail.com');
    define('SMTP_PORT', '587');
    define('SMTP_USER', '');
    define('SMTP_PASS', '');
    define('SMTP_CRYPTO', 'tls'); /*ssl*/

    /*From Email Settings*/
    define('FROM_EMAIL', 'info@tip74.in');
    define('FROM_EMAIL_NAME', SITE_NAME);

    /*No-Reply Email Settings*/
    define('NOREPLY_EMAIL', SITE_NAME);
    define('NOREPLY_NAME', "info@tip74.in");

    /*Site Related Settings*/
    define('API_SAVE_LOG', false);
    define('CRON_SAVE_LOG', false);

    define('PAGESIZE_MAX', 100);
    define('PAGESIZE_DEFAULT', 15);

    /* Stripe Details */
    define("STRIPE_API_KEY", "sk_test_htZuRrdkLDTjNGanHlkK8F1M");
    define("STRIPE_PUBLISHABLE_KEY", "pk_test_KpEfqzfA4YdlqiGb4HPiOsTF");

  break;

  case 'testing':
    
    /*Paths*/
    define('SITE_HOST', 'http://*******/');
    define('ROOT_FOLDER', 'tip74-dev/');

    /*SMTP Settings*/
    define('SMTP_PROTOCOL', 'smtp');
    define('SMTP_HOST', 'smtp.gmail.com');
    define('SMTP_PORT', '587');
    define('SMTP_USER', '*****');
    define('SMTP_PASS', '*****');
    define('SMTP_CRYPTO', 'tls'); /*ssl*/

    /*From Email Settings*/
    define('FROM_EMAIL', '*****');
    define('FROM_EMAIL_NAME', SITE_NAME);

    /*No-Reply Email Settings*/
    define('NOREPLY_EMAIL', SITE_NAME);
    define('NOREPLY_NAME', "*****");

    /*Site Related Settings*/
    define('API_SAVE_LOG', false);
    define('CRON_SAVE_LOG', true);

    /* Stripe Details */
    define("STRIPE_API_KEY", "sk_test_htZuRrdkLDTjNGanHlkK8F1M");
    define("STRIPE_PUBLISHABLE_KEY", "pk_test_KpEfqzfA4YdlqiGb4HPiOsTF");

    define('PAGESIZE_MAX', 100);
    define('PAGESIZE_DEFAULT', 15);

  break;
  case 'demo':
    /*Paths*/
    define('SITE_HOST', '');
    define('ROOT_FOLDER', '');

    /*SMTP Settings*/
    define('SMTP_PROTOCOL', 'smtp');
    define('SMTP_HOST', 'smtp.gmail.com');
    define('SMTP_PORT', '587');
    define('SMTP_USER', '');
    define('SMTP_PASS', '');
    define('SMTP_CRYPTO', 'tls'); /*ssl*/

    /*From Email Settings*/
    define('FROM_EMAIL', 'info@expertteam.in');
    define('FROM_EMAIL_NAME', SITE_NAME);

    /*No-Reply Email Settings*/
    define('NOREPLY_EMAIL', SITE_NAME);
    define('NOREPLY_NAME', "info@expertteam.in");

    /*Site Related Settings*/
    define('API_SAVE_LOG', true);
    define('CRON_SAVE_LOG', true);

    /* Stripe Details */
    define("STRIPE_API_KEY", "sk_test_htZuRrdkLDTjNGanHlkK8F1M");
    define("STRIPE_PUBLISHABLE_KEY", "pk_test_KpEfqzfA4YdlqiGb4HPiOsTF");

    define('PAGESIZE_MAX', 100);
    define('PAGESIZE_DEFAULT', 15);

  break;
  case 'production':
    /*Paths*/
    define('SITE_HOST', '');
    define('ROOT_FOLDER', '');

    /*SMTP Settings*/
    define('SMTP_PROTOCOL', 'smtp');
    define('SMTP_HOST', 'smtp.gmail.com');
    define('SMTP_PORT', '587');
    define('SMTP_USER', '');
    define('SMTP_PASS', '');
    define('SMTP_CRYPTO', 'tls'); /*ssl*/

    /*From Email Settings*/
    define('FROM_EMAIL', 'info@expertteam.in');
    define('FROM_EMAIL_NAME', SITE_NAME);

    /*No-Reply Email Settings*/
    define('NOREPLY_EMAIL', SITE_NAME);
    define('NOREPLY_NAME', "info@expertteam.in");

    /*Site Related Settings*/
    define('API_SAVE_LOG', false);
    define('CRON_SAVE_LOG', true);

    /* Stripe Details */
    define("STRIPE_API_KEY", "sk_test_htZuRrdkLDTjNGanHlkK8F1M");
    define("STRIPE_PUBLISHABLE_KEY", "pk_test_KpEfqzfA4YdlqiGb4HPiOsTF");

    define('PAGESIZE_MAX', 100);
    define('PAGESIZE_DEFAULT', 15);
  break;
}

define('BASE_URL', SITE_HOST . ROOT_FOLDER .'api/');
define('ASSET_BASE_URL', BASE_URL . 'asset/');
define('PROFILE_PICTURE_URL', BASE_URL . 'uploads/profile/picture');
define('POST_PICTURE_URL', BASE_URL . 'uploads/Post/');