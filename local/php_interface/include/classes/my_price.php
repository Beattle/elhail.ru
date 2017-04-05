<?php
/**
 * User: Hipno
 * Date: 04.04.2017
 * Time: 13:50
 * Project: elhail.ru
 */

namespace ninjacat;


class MyPrice{

    public function death($string = ''){
        die($string);
    }
    private static $price = array();

    public function setPrices($prodId,$arrPrices){
        self::$price[$prodId] = $arrPrices;
    }

    public function getPrices(){
        return self::$price;
    }

    public function Calculate($productID, $quantity = 1, $arUserGroups = array(), $renewal = "N", $arPrices = array(), $siteID = false, $arDiscountCoupons = false){

        $iblockID = (int)\CIBlockElement::GetIBlockByID($productID);
        $currency = \CCurrency::GetBaseCurrency();
        if (!strlen($_SESSION["VREGIONS_REGION"]["PRICE"])){
            return true;
        }


        $priceCode = $_SESSION["VREGIONS_REGION"]["PRICE"] ? $_SESSION["VREGIONS_REGION"]["PRICE"] : 'BASE';


        $arResultPrices = \CIBlockPriceTools::GetCatalogPrices($iblockID, Array($priceCode));
        $priceID = $arResultPrices[$priceCode]["ID"];
        $prod_price = GetCatalogProductPrice(
            $productID,
            $priceID
        );

        $realPrice = $prod_price["PRICE"];

        $arDiscounts = \CCatalogDiscount::GetDiscountByProduct(
            $productID,
            $arUserGroups,
            $renewal,
            Array($priceID),
            $siteID,
            $arDiscountCoupons
        );

        $discountPrice = \CCatalogProduct::CountPriceWithDiscount(
            $prod_price["PRICE"],
            $currency,
            $arDiscounts
        );

        if ($prod_price["CURRENCY"] != $currency){
            $realPrice = \CCurrencyRates::ConvertCurrency($realPrice, $prod_price["CURRENCY"], $currency);
            $realPrice = roundEx($realPrice, 0);

            $discountPrice = \CCurrencyRates::ConvertCurrency($discountPrice, $prod_price["CURRENCY"], $currency);
            $discountPrice = roundEx($discountPrice, 0);
        }

        $unroundDiscountPrice = $discountPrice;
        $discountPrice = \Bitrix\Catalog\Product\Price::roundPrice(
            $priceID,
            $discountPrice,
            $currency
        );
        $discountValue = $realPrice - $discountPrice;

        $answer = array();
        $answer['PRICE'] = array(
            'ID'                => $prod_price["ID"],
            'CATALOG_GROUP_ID'  => $priceID,
            'PRICE'             => $realPrice,
            'CURRENCY'          => $currency,
            'ELEMENT_IBLOCK_ID' => $iblockID,
            'VAT_RATE'          => 0,
            'VAT_INCLUDED'      => 'N',
        );
        // echo "<pre>";
        // print_r($answer['PRICE']);
        // echo "</pre>";
        $answer['RESULT_PRICE'] = array(
            'BASE_PRICE'             => $realPrice,
            'DISCOUNT_PRICE'         => $discountPrice,
            'UNROUND_DISCOUNT_PRICE' => $unroundDiscountPrice,
            'CURRENCY'               => $currency,
            'DISCOUNT'               => $discountValue ? $discountValue : 0,
            'PERCENT'                => $discountValue ? $discountValue / $realPrice * 100 : 0,
            'VAT_RATE'               => 0,
            'VAT_INCLUDED'           => 'Y',
        );
        // echo "<pre>";
        // print_r($answer['RESULT_PRICE']);
        // echo "</pre>";
        $answer["DISCOUNT_PRICE"] = $discountPrice;
        $answer["PRODUCT_ID"] = $productID;

        if ($arDiscounts[0]){
            $answer["DISCOUNT"] = $arDiscounts[0];
        }
        // echo "<pre>";
        // print_r($answer['DISCOUNT']);
        // echo "</pre>";
        $answer["DISCOUNT_LIST"] = $arDiscounts;
        // echo "<pre>";
        // print_r($answer['DISCOUNT_LIST']);

        self::setPrices($productID,$answer);
        return $answer;

    }

    public static function MyGetOptimalPrice1($productID, $quantity = 1, $arUserGroups = array(), $renewal = "N", $arPrices = array(), $siteID = false, $arDiscountCoupons = false){

        return  self::Calculate($productID,$quantity,$arUserGroups,$renewal,$arPrices,$siteID,$arDiscountCoupons);

    }
};