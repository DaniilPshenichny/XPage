<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
$iblockProvider = new \CIblockElement(false);
$request = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 33,
    'ID' => $_POST['requestId']
],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_118','PROPERTY_121'])->Fetch();
$iblockProvider->SetPropertyValuesEx($_POST['ilId'], false, [
        '133' => json_decode($_POST['goals']),
    ]
);
$financeFields = [
    '205' => $_POST['financeConclusion'],
    '206' => $_POST['financeFormDate'],
    '207' => $_POST['financeFormResponsible'],
    '208' => $_POST['docsTitle'],
    '209' => $_POST['docsComments'],
    '210' => $_POST['remarks'],
    '211' => $_POST['remarksComments'],
    '212' => $_POST['dealSize'],
    '213' => $_POST['aggregateSum'],
    '214' => $_POST['additionalInfo'],
];
if(!$request['PROPERTY_121']){
    $financeResult = $iblockProvider->Add([
        'IBLOCK_ID' => 37,
        'NAME' => 'Заключение',
        'ACTIVE' => 'Y',
        'PROPERTY_VALUES' => $financeFields
    ]);
    $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
            '121' => $financeResult,
        ]
    );
}else{
    $iblockProvider->SetPropertyValuesEx($request['PROPERTY_121_VALUE'], false, $financeFields);
}
