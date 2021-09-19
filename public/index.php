<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

//require '../../../test/vendor/autoload.php';
require dirname(__DIR__).'/vendor/autoload.php';

// (new Dotenv())->bootEnv('../../../test/.env');
(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

echo '<div class="position-absolute alert alert-danger
   " style="
    z-index: 10;
    left: 50%;
    /* position: absolute; */
    -webkit-transform: translateX(-50%);
    transform: translateX(-50%);
    bottom: 10px;
    text-align: center;
">За съжаление услугата, която се използва за вземането на информация на растенията е спряна (05.05.2021г.). Работи се по обработването на базата от данни на тази услуга, за да продължи работата на Terrain Flora Drawer. 
</div>';