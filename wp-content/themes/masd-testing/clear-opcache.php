<?php
if (function_exists('opcache_reset')) {
    // Clear the OPcache
    opcache_reset();
    echo "OPcache has been cleared successfully.";
} else {
    echo "OPcache is not enabled.";
}
