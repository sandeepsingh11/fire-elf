<?php

define('WEBSITE_NAME', 'Fire Elf');

define('TIMEZONE', 'America/New_York');
date_default_timezone_set(TIMEZONE);

define('CLIENT_ROOT', 'client/');
define('CLIENT_PAGES_DIR', 'client/public/pages/');

define('ADMIN_URL', 'http://admin.fireelf.xyz/');

define('MEDIA_DIR', 'admin/public/media/');
define('MEDIA_URL', 'http://admin.fireelf.xyz/media/');

// MEDIA_SIZE_LIMIT integer is in Bytes
// https://www.convertunits.com/from/MB/to/B
$upload_max_size = ini_get('upload_max_filesize');
$upload_max_size = return_bytes($upload_max_size);
define('MEDIA_SIZE_LIMIT', $upload_max_size);









// get upload_max_filesize const from php ini. Must convert shorthand byte value
// https://www.php.net/manual/en/function.ini-get.php#96996
function return_bytes ($size_str)
{
    switch (substr ($size_str, -1))
    {
        case 'K': case 'k': return (int)$size_str * 1024;
        case 'M': case 'm': return (int)$size_str * 1048576;
        case 'G': case 'g': return (int)$size_str * 1073741824;
        default: return $size_str;
    }
}