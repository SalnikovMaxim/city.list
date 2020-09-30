<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

use Bitrix\Main\Loader,
	Bitrix\Main,
	Bitrix\Iblock;

/*************************************************************************
	Processing of received parameters
*************************************************************************/
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

/*************************************************************************
			Work with cache
*************************************************************************/

if($this->startResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), $_POST["cityId"], $_POST["city"])))
{
   if(!Loader::includeModule("iblock"))
   {
      $this->abortResultCache();
      ShowError("Iblock module not installed");
      return;
   }

	if(isset($_POST["city"])){
	$el = new CIBlockElement;

	$arLoadProductArray = Array(
	  "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
	  "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
	  "IBLOCK_ID"      => 1,
	  "PROPERTY_VALUES"=> $PROP,
	  "NAME"           => $_POST["city"],
	  "ACTIVE"         => "Y",            // активен
	  );

	if($PRODUCT_ID = $el->Add($arLoadProductArray))
	 $arResult["Mess"] = "ID нового города: ".$PRODUCT_ID;
	else
	  $arResult["Mess"] = "Error: ".$el->LAST_ERROR;

	}

	if(isset($_POST["cityId"])){
	  if(CIBlock::GetPermission($arParams["IBLOCK_ID"])>='W')
	  {
	      $DB->StartTransaction();
	      if(!CIBlockElement::Delete($_POST["cityId"]))
	      {
	          $strWarning .= 'Error!';
	          $DB->Rollback();
	      }
	      else
	          $DB->Commit();
	  }

	}

	$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
	$arFilter = Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
	$res = CIBlockElement::GetList(Array("ID" => "DESC"), $arFilter, false, Array("nPageSize"=>1000), $arSelect);
	$arCity = Array();
	while($ob = $res->GetNextElement())
	{
	 	$arFields = $ob->GetFields();
		$arCity[$arFields["ID"]]["NAME"] = $arFields["NAME"];
		$arCity[$arFields["ID"]]["ID"] = $arFields["ID"];
	}
	$arResult["City"] = $arCity;
	   $this->EndResultCache();
	 }

	$this->IncludeComponentTemplate();
