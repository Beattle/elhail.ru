<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;?>

<div class="advantages">
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<div class="adv-item">		
			<div class="adv-icon">
				<i class="fa<?=(!empty($arItem['DISPLAY_PROPERTIES']['ICON']['VALUE'])) ? ' '.$arItem['DISPLAY_PROPERTIES']['ICON']['VALUE'] : ''?>"></i>
			</div>
			<div class="adv-text">
				<?=$arItem['NAME']?>
			</div>
		</div>
	<?endforeach;?>
</div>