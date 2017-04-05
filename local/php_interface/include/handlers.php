<?php
/**
 * User: Hipno
 * Date: 04.04.2017
 * Time: 13:48
 * Project: elhail.ru
 */

use Bitrix\Main;

AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "DoIBlockAfterSave");
AddEventHandler("iblock", "OnAfterIBlockElementAdd", "DoIBlockAfterSave");
AddEventHandler("catalog", "OnPriceAdd", "DoIBlockAfterSave");
AddEventHandler("catalog", "OnPriceUpdate", "DoIBlockAfterSave");

AddEventHandler('catalog','OnGetOptimalPrice',array('\ninjacat\MyPrice',"MyGetOptimalPrice1"));


Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleBasketBeforeSaved',
    array('\ninjacat\MyBasket','updateBasket')

);