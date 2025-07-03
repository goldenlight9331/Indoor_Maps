<?php
if (extension_loaded('gd') && function_exists('gd_info')) {
    echo "GD extension is enabled.\n";
    echo "GD version: " . gd_info()['GD Version'] . "\n";
} else {
    echo "GD extension is not enabled or installed.\n";
}