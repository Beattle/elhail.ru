<?php
/**
 * User: Hipno
 * Date: 04.04.2017
 * Time: 13:38
 * Project: elhail.ru
 */


use Bitrix\Main\Application;
use Bitrix\Main\Loader;


Loader::includeModule("iblock");
Loader::includeModule("catalog");
Loader::includeModule("sale");

// load Handlers

require_once ($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/handlers.php');
// require_once ($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/functions.php');

// Load classes

Bitrix\Main\Loader::registerAutoLoadClasses(null, array(
    '\ninjacat\MyPrice' => '/local/php_interface/include/classes/my_price.php'

));


