<?php
require '../vendor/autoload.php';

Dotenv\Dotenv::createImmutable(dirname(__FILE__, 2));
$dotenv->safeLoad();