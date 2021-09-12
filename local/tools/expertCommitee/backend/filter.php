<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
$dateArray = explode(' - ',$_POST['date']);
$users = json_decode($_POST['users']);
CModule::IncludeModule('crm');
$leadProvider = new \CCrmLead(false);
$companyProvider = new \CCrmCompany(false);
$companyDB = $companyProvider->GetList(['ID'=>'DESC'],[
    '%TITLE' => $_POST['company'],
    'CHECK_PERMISSIONS' => 'N',
]);
while($company = $companyDB->Fetch()){
    $companies[] = $company['ID'];
}
if(CModule::IncludeModule('iblock')) {
    $iblockElementProvider = new \CIblockElement(false);
    $iblockFilter = [
        'IBLOCK_ID' => 33,
        'PROPERTY_114' => $_POST['filterStatus']
    ];
    if($_POST['date'] !== ''){
        $iblockFilter['>=DATE_CREATE'] = array(false, ConvertTimeStamp(strtotime($dateArray[0].' 00:00:00'), "FULL"));
        $iblockFilter['<=DATE_CREATE'] = array(false, ConvertTimeStamp(strtotime($dateArray[1].' 23:59:59'), "FULL"));
    }
    $commiteeRequestsDB = $iblockElementProvider->GetList([], $iblockFilter, false, false, ['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_112','PROPERTY_117']);
    while ($commiteeRequest = $commiteeRequestsDB->Fetch()) {

        $lead = $leadProvider->GetByID($commiteeRequest['PROPERTY_117_VALUE']);
        if(!empty($users)){
            if(!in_array($lead['ASSIGNED_BY_ID'],$users))continue;
        }
        if($_POST['company'] !== ''){
            if(!in_array($lead['COMPANY_ID'],$companies))continue;
        }
        $requests[] = $commiteeRequest['ID'];
    }
}




echo json_encode($requests?:[]);