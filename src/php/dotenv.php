<?php
require dirname(__FILE__, 3) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__, 3));
$dotenv->safeLoad();