<?php
//To test locally
if (!defined('BASE_URL')) {
    define('BASE_URL', '/public_html/');
}
//To go live
// if (!defined('BASE_URL')) {
//     define('BASE_URL', '/');
// }

if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/');
}

if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', BASE_PATH . 'public_html/');
}

//Send emails credentials
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);

define('SMTP_USER', 'newvisionbarbersupplies@gmail.com');
define('SMTP_PASS', 'ybncjweojjjehlde');

define('SMTP_FROM_EMAIL', 'newvisionbarbersupplies@gmail.com');
define('SMTP_FROM_NAME', 'New Vision Barber Supplies');
