<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
$iblockProvider = new \CIblockElement(false);
$request = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 33,
    'ID' => $_POST['requestId']
],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_118','PROPERTY_120'])->Fetch();
$iL = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 34,
    'ID' => $request['PROPERTY_118_VALUE']
],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_245'])->Fetch();
$iblockProvider->SetPropertyValuesEx($iL['PROPERTY_245_VALUE'], false, [
        '224' => $_POST['companyInn'],
        '225' => $_POST['companyOgrn'],
        '226' => $_POST['companyCreateDate'],
        '232' => $_POST['companyOtvetchik'],
        '233' => $_POST['companyIstec'],
        '234' => $_POST['companyDirector'],
        '235' => $_POST['companyUchasnik'],
        '236' => $_POST['companyBeneficiary'],
        '237' => json_decode($_POST['poruchitels']),
        '238' => $_POST['companyResident'] !== 'false'?'140':'141',
        '239' => $_POST['companyDiler']!== 'false'?'142':'143',
        '240' => $_POST['companyManufacturer']!== 'false'?'144':'145',
    ]
);
$legalFields = [
    '176' => $_POST['urConclusion'],
    '203' => $_POST['legalDate'],
    '204' => $_POST['legalResponsible'],
];
if($_POST['action'] === 'save')$legalFields['299'] = '155';else{$legalFields['299'] = '156';}
if(!$request['PROPERTY_120_VALUE']){
    $legalResult = $iblockProvider->Add([
        'IBLOCK_ID' => 36,
        'NAME' => 'Заключение',
        'ACTIVE' => 'Y',
        'PROPERTY_VALUES' => $legalFields,
    ]);
    $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
            '120' => $legalResult,
        ]
    );
}else{
    $iblockProvider->SetPropertyValuesEx($request['PROPERTY_120_VALUE'], false, $legalFields);
}

