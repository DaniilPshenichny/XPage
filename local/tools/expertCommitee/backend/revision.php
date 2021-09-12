<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
CModule::IncludeModule('iblock')?$iblockProvider = new \CIblockElement(false):$iblockProvider = 'Module is not loaded';
$generalRequest = $iblockProvider->GetList([], [
    'IBLOCK_ID' => 33,
    'ID' => $_POST['requestId']
], false, false, ['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_118', 'PROPERTY_144', 'PROPERTY_145', 'PROPERTY_146'])->Fetch();
$il = $iblockProvider->GetList([], [
    'IBLOCK_ID' => 34,
    'ID' => $generalRequest['PROPERTY_118_VALUE']
], false, false, ['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_245'])->Fetch();
$leasee = $iblockProvider->GetList([], [
    'IBLOCK_ID' => 43,
    'ID' => $generalRequest['PROPERTY_245_VALUE']
], false, false, ['ID', 'IBLOCK_ID', 'NAME'])->Fetch();
if(isset($_POST['service'])){
    if($_POST['service'] === 'sb'){
        $responsibleUser = \CUser::GetByID($generalRequest['PROPERTY_144_VALUE'])->Fetch();
        $arMessageFields = array(
            "FROM_USER_ID" => 0,
            "TO_USER_ID" => 1,
            "NOTIFY_MODULE" => "intranet",
            "NOTIFY_MESSAGE" => "Сотрудник службы: {$responsibleUser['LAST_NAME']} {$responsibleUser['NAME']} {$responsibleUser['SECOND_NAME']}
Лизингополучатель: {$leasee['NAME']}
Причина отклонения: {$_POST['comment']}",
        );
    }
}
if(CModule::IncludeModule('im')){
    CIMNotify::Add($arMessageFields);
}