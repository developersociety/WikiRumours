<?php

    /*	---------------------------------------------------------------
		All libraries are loaded in the order in which they appear here
		---------------------------------------------------------------	*/

$tl->backEndLibraries = [
    'phpass' => array(
        'local_path' => 'phpass/phpass_0-3/PasswordHash.php',
    ),
    'phpmailer' => array(
        'local_path' => 'phpmailer/phpmailer_6-07/src/PHPMailer.php',
    ),
    'phpmailerexception' => array(
        'local_path' => 'phpmailer/phpmailer_6-07/src/Exception.php',
    ),
    'phpmailersmtp' => array(
        'local_path' => 'phpmailer/phpmailer_6-07/src/SMTP.php',
    ),

];

$tl->frontEndLibraries = [
    'jQuery' => array(
        'version' => '2.2.1',
        'local_js_path' => 'jquery/jquery-2.2.1.min.js',
    ),
    'Bootstrap' => array(
        'version' => '3.3.4',
        'local_css_path' => 'bootstrap/bootstrap-3.3.4-dist/css/bootstrap.min.css',
        'local_js_path' => 'bootstrap/bootstrap-3.3.4-dist/js/bootstrap.min.js',
    ),

    'Bootstrap Switch' => array(
        'version' => '3.0',
        'local_css_path' => 'bootstrap-switch/bootstrap_switch_3-0/dist/css/bootstrap3/bootstrap-switch.min.css',
        'local_js_path' => 'bootstrap-switch/bootstrap_switch_3-0/dist/js/bootstrap-switch.min.js',
    ),

    'Bootstrap DateTimePicker' => array(
        'local_css_path' => 'bootstrap-datetimepicker-master/css/bootstrap-datetimepicker.min.css',
        'local_js_path' => 'bootstrap-datetimepicker-master/js/bootstrap-datetimepicker.js',
    ),

    'Bootstrap DateTimePicker (French)' => array(
        'local_js_path' => 'bootstrap-datetimepicker-master/js/locales/bootstrap-datetimepicker.fr.js',
    ),

    'Font Awesome' => array(
        'version' => '4.3.0',
        'local_css_path' => 'font_awesome/font-awesome_4-3-0/font-awesome.min.css',
    ),
    'Select2' => array(
        'version' => '4.0.2-rc-1',
        'local_css_path' => 'select2/4-0-2-rc-1/dist/css/select2.min.css',
        'local_js_path' => 'select2/4-0-2-rc-1/dist/js/select2.min.js',
    ),
    'Dropzone' => array(
        'version' => '3.8.4',
        'local_css_path' => 'dropzone/dropzone_3-8-4/downloads/css/dropzone.css',
        'local_js_path' => 'dropzone/dropzone_3-8-4/downloads/dropzone.js',
    ),
    'Moment.js' => array(
        'version' => '2.8.3',
        'local_js_path' => 'moment-js/moment.min.js',
    ),
    'Moment.js (Timezones)' => array(
        'version' => '0.2.4',
        'local_js_path' => 'moment-js/moment-timezone-with-data.min.js',
    ),
    'Google Maps' => array(
        'remote_js_path' => 'https://maps.googleapis.com/maps/api/js?libraries=visualization&sensor=true_or_false&key=AIzaSyDfSQydYkTB7pD5wit-2XT5aDgQGE8P6vw',
    ),

    'Google Charts (AJAX)' => array(
        'remote_js_path' => 'https://www.google.com/jsapi',
    ),

];
