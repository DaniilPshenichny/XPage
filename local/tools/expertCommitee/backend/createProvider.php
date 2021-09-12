<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
$iblockProvider = new \CIblockElement(false);
if(isset($_POST['place']) && $_POST['place'] === 'fromLegal'){
    if($_POST['providerId'] !==''){
        $updateProperties = [
            '151' => $_POST['providerCompanyInn'],
            '152' => $_POST['providerCompanyOgrn'],
            '153' => $_POST['providerCompanyDateCreate'],
            '169' => $_POST['providerOtvetchik'],
            '170' => $_POST['providerIstec'],
            '171' => $_POST['providerDirector'],
            '172' => $_POST['providerMember'],
        ];
        $iblockProvider->SetPropertyValuesEx($_POST['providerId'], false, $updateProperties);
    }else{
        $providerType = $iblockProvider->Add([
            'IBLOCK_ID' => 38,
            'NAME' => $_POST['providerCompanyTitle'],
            'ACTIVE' => 'Y',
            'PROPERTY_VALUES' => [
                '151' => $_POST['providerCompanyInn'],
                '152' => $_POST['providerCompanyOgrn'],
                '153' => $_POST['providerCompanyDateCreate'],
                '169' => $_POST['providerOtvetchik'],
                '170' => $_POST['providerIstec'],
                '171' => $_POST['providerDirector'],
                '172' => $_POST['providerMember'],
            ]
        ]);
        $res = CIBlockElement::GetProperty(34, $_POST['ilId'], "sort", "asc", array("ID" => "154"));
        while ($ob = $res->GetNext())
        {
            $providers[] = $ob['VALUE'];
        }
        $providers[] = $providerType;

        $iblockProvider->SetPropertyValuesEx($_POST['ilId'], false, [
                '154' => $providers,
            ]
        );
        echo json_encode([
            'TITLE' => $_POST['providerCompanyTitle'],
            'ID' => $providerType
        ]);
    }
}else{
    $providerType = $iblockProvider->Add([
        'IBLOCK_ID' => 38,
        'NAME' => $_POST['providerCompanyTitle'],
        'ACTIVE' => 'Y',
        'PROPERTY_VALUES' => [
            '148' => $_POST['providerCompanyType'],
            '149' => $_POST['providerCompanyRepeated']==='true'?109:110,
            '150' => $_POST['providerCompanyAddress'],
            '151' => $_POST['providerCompanyInn'],
            '152' => $_POST['providerCompanyOgrn'],
            '153' => $_POST['providerCompanyDateCreate'],
            '155' => $_POST['providerCompanyDate'],
            '156' => $_POST['providerCompanyResult'],
        ]
    ]);
    $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
            '154' => [$providerType],
        ]
    );
    echo json_encode([
        'TITLE' => $_POST['providerCompanyTitle'],
        'ID' => $providerType
    ]);
}
