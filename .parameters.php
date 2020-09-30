<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */
use Bitrix\Main\Loader;

global $USER_FIELD_MANAGER;

if(!Loader::includeModule("iblock"))
	return;

$catalogIncluded = Loader::includeModule("catalog");

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arProperty_UF = array();
$arUserFields = $USER_FIELD_MANAGER->GetUserFields("IBLOCK_".$arCurrentValues["IBLOCK_ID"]."_SECTION", 0, LANGUAGE_ID);
foreach($arUserFields as $FIELD_NAME=>$arUserField)
{
	$arUserField['LIST_COLUMN_LABEL'] = (string)$arUserField['LIST_COLUMN_LABEL'];
	$arProperty_UF[$FIELD_NAME] = $arUserField['LIST_COLUMN_LABEL'] ? '['.$FIELD_NAME.']'.$arUserField['LIST_COLUMN_LABEL'] : $FIELD_NAME;
}

$isProducts = false;
if ($catalogIncluded && (!empty($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID'] > 0))
{
	$catalog = CCatalogSku::GetInfoByIBlock($arCurrentValues['IBLOCK_ID']);
	$isProducts = (!empty($catalog));
	unset($catalog);
}

$countFilterList = array(
	"CNT_ALL" => GetMessage("CP_BCSL_COUNT_ELEMENTS_ALL")
);
$countFilterList["CNT_ACTIVE"] = ($isProducts
	? GetMessage("CP_BCSL_COUNT_PRODUCTS_ACTIVE")
	: GetMessage("CP_BCSL_COUNT_ELEMENTS_ACTIVE")
);
if ($isProducts)
{
	$countFilterList["CNT_AVAILABLE"] = GetMessage("CP_BCSL_COUNT_PRODUCTS_AVAILABLE");
}

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_IBLOCK_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		)
	),
);
