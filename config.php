<?php
// config.php
define('UPLOAD_DIR', '/var/www/html/upload/');
define('BASE_URL', 'https://yourwebsite.com/upload/');
define('TIMEZONE', 'America/New_York');
define('DATE_TIME_FORMAT', 'm/d/y  H:i');
define('TOTAL_UPLOAD_SIZE', '20G');
date_default_timezone_set(TIMEZONE);
?>