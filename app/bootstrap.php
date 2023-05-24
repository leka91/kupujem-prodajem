<?php

require_once 'config/config.php';

foreach(glob(APP_ROOT . '\helpers\*.php') as $file) {
    require_once $file;
}

spl_autoload_register(function($className) {
    require_once 'lib/' . $className . '.php';
    require_once 'factories/' . $className . '.php';
});