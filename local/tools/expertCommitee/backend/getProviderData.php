<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
$iblockProvider = new \CIblockElement(false);
$provider = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 38,
    'ID' => $_POST['providerId'],

],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_148','PROPERTY_149','PROPERTY_150','PROPERTY_151','PROPERTY_152','PROPERTY_153','PROPERTY_155','PROPERTY_156','PROPERTY_169','PROPERTY_170','PROPERTY_171','PROPERTY_172'])->Fetch();
echo json_encode([
    'NAME' => $provider['NAME'],
    'TYPE' => $provider['PROPERTY_148_ENUM_ID'],
    'ADDRESS' => $provider['PROPERTY_150_VALUE'],
    'INN' => $provider['PROPERTY_151_VALUE'],
    'OGRN' => $provider['PROPERTY_152_VALUE'],
    'DATE' => $provider['PROPERTY_153_VALUE'],
    'CHECKDATE' => $provider['PROPERTY_155_VALUE'],
    'CHECKRESULT' => $provider['PROPERTY_156_VALUE'],
    'REPEATEDPROVIDER' => $provider['PROPERTY_149_VALUE'],
    'OTVETCHIK' => $provider['PROPERTY_169_VALUE'],
    'ISTEC' => $provider['PROPERTY_170_VALUE'],
    'DIRECTOR' => $provider['PROPERTY_171_VALUE'],
    'MEMBER' => $provider['PROPERTY_172_VALUE'],
    'ID' => $_POST['providerId'],
]);
