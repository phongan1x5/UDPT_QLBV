<?php
function url($path = '')
{
    $baseUrl = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
    return $baseUrl . ltrim($path, '/');
}

function base_url()
{
    return str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
}

define('BASE_URL', base_url());
