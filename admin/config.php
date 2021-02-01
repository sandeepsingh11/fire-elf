<?php

define('WEBSITE_NAME', 'Demo');

define('TIMEZONE', 'America/New_York');
date_default_timezone_set(TIMEZONE);

define('CLIENT_ROOT', 'client/');
define('CLIENT_PAGES_DIR', 'client/public/pages/');

define('ADMIN_URL', 'http://admin.fireelf.xyz/');

define('MEDIA_DIR', 'admin/media/');
define('MEDIA_URL', 'http://admin.fireelf.xyz/media/');

// MEDIA_SIZE_LIMIT integer is in Bytes (5242880 B = 5 MB)
// https://www.convertunits.com/from/MB/to/B
define('MEDIA_SIZE_LIMIT', 5242880);