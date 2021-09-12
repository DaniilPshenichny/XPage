<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
$iblockProvider = new \CIblockElement(false);
$request = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 33,
    'ID' => $_POST['requestId']
],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_118'])->Fetch();
$il = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 34,
    'ID' => $request['PROPERTY_118_VALUE']
],false,false,['ID','IBLOCK_ID','NAME','DATE_CREATE','PROPERTY_245'])->Fetch();
$leasee = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 43,
    'ID' => $request['PROPERTY_245_VALUE']
],false,false,['ID','IBLOCK_ID','NAME'])->Fetch();
foreach ($_FILES as $fileKey => $file) {
    if (strpos($fileKey, 'nbkiUL-files') !== false) {
        $generalFiles[] = $file;
    } elseif (strpos($fileKey, 'nkbi-fl-files') !== false) {
        $directorFiles[] = $file;
    } elseif (strpos($fileKey, 'founde-nkbi-fl-files') !== false) {
        $founderFiles[] = $file;
    }
}



$iblockProvider->SetPropertyValuesEx($leasee['ID'], false, [
        '275' => $_POST['sotrudniki'],
        '224' => $_POST['inn'],
        '225' => $_POST['ogrn'],
        '226' => $_POST['dateObrazovaniya'],
        '266' => $_POST['sphere'],
        '223' => $_POST['urAddress'],
        '241' => $_POST['factAddress'],
        '242' => $_POST['generalDirector'],
        '243' => json_decode($_POST['founders']),
        '268' => $generalFiles,
        '269' => json_decode($_POST['negativeInvoices']),
        '270' => $_POST['fns'],
        '271' => $_POST['fssp'],
        '272' => $_POST['arbitr'],
        '273' => $_POST['financeLastYear'],
        '274' => json_decode($_POST['leasings']),
        '267' => $_POST['nbkiUL'],
    ]
);
$director = $iblockProvider->Add([
    'IBLOCK_ID' => 44,
    'NAME' => $_POST['directorTitle'],
    'ACTIVE' => 'Y',
    'PROPERTY_VALUES' => [
        '246' => $_POST['nkbi-fl'],
        '247' => $directorFiles,
        '248' => json_decode($_POST['negativeDirectorInvoices']),
        '249' => $_POST['directorFssp'],
        '250' => $_POST['directorPassport'],
        '251' => $_POST['directorMember'],
        '252' => $_POST['directorInfo'],
    ]
]);
$founder = $iblockProvider->Add([
    'IBLOCK_ID' => 45,
    'NAME' => $_POST['founderTitle'],
    'ACTIVE' => 'Y',
    'PROPERTY_VALUES' => [
        '254' => $_POST['founderNbkiUL'],
        '255' => $founderFiles,
        '276' => json_decode($_POST['negativeFounderInvoices']),
        '256' => $_POST['founderFssp'],
        '257' => $_POST['founderPassport'],
        '258' => $_POST['founderMember'],
        '259' => $_POST['founderInfo'],
    ]
]);
$iblockProvider->SetPropertyValuesEx($il['ID'], false, [
        '253' => $director,
        '260' => $founder
    ]
);

$sbResult = $iblockProvider->Add([
    'IBLOCK_ID' => 35,
    'NAME' => 'Заключение',
    'ACTIVE' => 'Y',
    'PROPERTY_VALUES' => [
        '277' => $_POST['sbFinalConclusion'],
        '278' => $_POST['recommendations'],
        '279' => $_POST['sbDate'],
        '280' => $_POST['sbAssigned'],
    ]
]);


$iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
        '119' => $sbResult,
    ]
);