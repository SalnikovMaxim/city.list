<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $USER;
if ($USER->IsAuthorized()){
?>
<h2>Добавление города:</h2>
<form action="" method="post">
 <p>Название города: <input type="text" name="city" /></p>
 <p><input type="submit" value="Добавить"/></p>
</form>
<?
}
echo "<H3>".$arResult["Mess"]."</H3>";
foreach($arResult["City"] as $city){
	?>
	<p><?=$city["NAME"]?></p>
	<?if ($USER->IsAuthorized()){?>
	<form action="" method="post">
		<input type="hidden" name="cityId" value="<?=$city["ID"]?>"/>
		<input type="submit" value="Удалить"/>
	</form>
	<br>
	<?}
}
