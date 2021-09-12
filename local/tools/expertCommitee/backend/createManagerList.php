<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
$iblockProvider = new \CIblockElement(false);
CModule::IncludeModule('crm');
$leadProvider = new \CCrmLead(false);
$request = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 33,
    'ID' => $_POST['requestId'],
],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_117','PROPERTY_118','PROPERTY_158'])->Fetch();
$listId = $request['PROPERTY_118_VALUE']?:0;
$leadFields = [
    'STATUS_ID' => '4'
];
var_dump($listId);
//$action==='create'?$leadProvider->Update($request['PROPERTY_117_VALUE'],$leadFields):'';
foreach (explode(',',$_POST['providers']) as $provider)$provider!==''?$providersArray[] = $provider:true;
$experienceFiles = [];
$companiesFiles= [];
$basicFiles = [];

$commentsFiles = [];
foreach ($_FILES as $fileKey => $file){
    if(strpos($fileKey,'term-files')!==false){
        $termFiles[]=$file;
    }elseif(strpos($fileKey,'experience-files')!==false){
        $experienceFiles[]=$file;
    }elseif(strpos($fileKey,'companies-files')!==false){
        $companiesFiles[]=$file;
    }elseif(strpos($fileKey,'basic-files')!==false){
        $basicFiles[]=$file;
    }elseif(strpos($fileKey,'comments-files')!==false){
        $commentsFiles[]=$file;
    }
}
$lesseeProperties = [
    '223' => $_POST['urAddress'],
    '224' => $_POST['inn'],
    '225' => $_POST['ogrn'],
    '226' => $_POST['dateObrazovaniya'],
    '227' => $_POST['manGreat'],
    '228' => $_POST['manOwner'],
    '231' => json_decode($_POST['meetingDate']),
    '222' => $_POST['clientRepeat']==='on'?147:146,
    '229' => $_POST['clientRepeatDate'],
    '230' => $_POST['clientRepeatResult'],
    '244' => json_decode($_POST['leasingGoal']),
    '261' => $_POST['totalOpportunity'],
    '262' => $_POST['avancePay'],
    '263' => $_POST['lisingSrok'],
    '264' => $_POST['ndsIncluded'] === 'true'?149:148,
    '265' => $_POST['link'],
    '297' => $_POST['contact'],
    '298' => $_POST['phone']
];
    if($listId > 0){
        $list = $iblockProvider->GetList([],[
            'IBLOCK_ID' => 34,
            'ID' => $listId,
        ],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_245','PROPERTY_133'])->Fetch();
        $iblockProvider->SetPropertyValuesEx($list['PROPERTY_245_VALUE'], false, $lesseeProperties);

        $currentTermFilesDB = CIBlockElement::GetProperty(34, $listId, "sort", "asc", array("ID" => "158"));
        $newTermFilesFromFront = json_decode($_POST['srokFiles']);
        while ($ob = $currentTermFilesDB->GetNext())
        {
            if(in_array($ob['VALUE'],$newTermFilesFromFront))$termFilesBackend[] = $ob['VALUE'];
        }
        foreach ($termFiles as $file){

            $termFilesBackend[] = CFile::SaveFile($file,'iblock');
        }
        foreach ($termFilesBackend as $fileIndex => $file){
            $termFilesBackend[$fileIndex] = CFile::MakeFileArray($file);
        }

        $currentExperienceFilesDB = CIBlockElement::GetProperty(34, $listId, "sort", "asc", array("ID" => "159"));
        $newExperienceFilesFromFront = json_decode($_POST['experienceFiles']);
        while ($ob = $currentExperienceFilesDB->GetNext())
        {
            if(in_array($ob['VALUE'],$newExperienceFilesFromFront))$experienceFilesBackend[] = $ob['VALUE'];
        }
        foreach ($experienceFiles as $file){

            $experienceFilesBackend[] = CFile::SaveFile($file,'iblock');
        }
        foreach ($experienceFilesBackend as $fileIndex => $file){
            $experienceFilesBackend[$fileIndex] = CFile::MakeFileArray($file);
        }

        $currentKonkurFilesDB = CIBlockElement::GetProperty(34, $listId, "sort", "asc", array("ID" => "160"));
        $newKonkurFilesFromFront = json_decode($_POST['konkurFiles']);
        while ($ob = $currentKonkurFilesDB->GetNext())
        {
            if(in_array($ob['VALUE'],$newKonkurFilesFromFront))$konkurFilesBackend[] = $ob['VALUE'];
        }
        foreach ($companiesFiles as $file){

            $konkurFilesBackend[] = CFile::SaveFile($file,'iblock');
        }
        foreach ($konkurFilesBackend as $fileIndex => $file){
            $konkurFilesBackend[$fileIndex] = CFile::MakeFileArray($file);
        }

        $currentBasisFilesDB = CIBlockElement::GetProperty(34, $listId, "sort", "asc", array("ID" => "161"));
        $newBasisFilesFromFront = json_decode($_POST['basisFiles']);
        while ($ob = $currentBasisFilesDB->GetNext())
        {
            if(in_array($ob['VALUE'],$newBasisFilesFromFront))$basisFilesBackend[] = $ob['VALUE'];
        }
        foreach ($basicFiles as $file){

            $basisFilesBackend[] = CFile::SaveFile($file,'iblock');
        }
        foreach ($basisFilesBackend as $fileIndex => $file){
            $basisFilesBackend[$fileIndex] = CFile::MakeFileArray($file);
        }

        $currentPrimechFilesDB = CIBlockElement::GetProperty(34, $listId, "sort", "asc", array("ID" => "161"));
        $newPrimechFilesFromFront = json_decode($_POST['primechFiles']);
        while ($ob = $currentPrimechFilesDB->GetNext())
        {
            if(in_array($ob['VALUE'],$newPrimechFilesFromFront))$primechFilesBackend[] = $ob['VALUE'];
        }
        foreach ($commentsFiles as $file){

            $primechFilesBackend[] = CFile::SaveFile($file,'iblock');
        }
        foreach ($primechFilesBackend as $fileIndex => $file){
            $primechFilesBackend[$fileIndex] = CFile::MakeFileArray($file);
        }







        $iblockProvider->SetPropertyValuesEx($listId, false, [
            '137' => $_POST['term'],
            '138' => $_POST['experience'],
            '139' => $_POST['companies'],
            '140' => $_POST['basic'],
            '141' => $_POST['comments'],
            '142' => $_POST['ilDate'],
            '143' => $_POST['ilAssigned'],
            '154' => $providersArray,
            '158' => $termFilesBackend,
            '159' => $experienceFilesBackend,
            '160' => $konkurFilesBackend,
            '161' => $basisFilesBackend,
            '162' => $primechFilesBackend,
        ]);

//        foreach ($list['PROPERTY_133_VALUE'] as $goal){
//            $iblockProvider->Update($goal['ID'],['NAME']);
//        }

    }else{
        $lesseeId = $iblockProvider->Add([
            'IBLOCK_ID' => 43,
            'NAME' => $_POST['companyTitle'],
            'ACTIVE' => 'Y',
            'PROPERTY_VALUES' => $lesseeProperties,
        ]);
        // блок создания лизингополучателя
        foreach (json_decode($_POST['leasingGoal']) as $goal){
            $goalsID[] = $iblockProvider->Add([
                'IBLOCK_ID' => 42,
                'NAME' => $goal,
                'ACTIVE' => 'Y',
            ]);
        }
        $listId = $iblockProvider->Add([
            'IBLOCK_ID' => 34,
            'NAME' => 'Информационный лист',
            'ACTIVE' => 'Y',
            'PROPERTY_VALUES' => [
                '137' => $_POST['term'],
                '138' => $_POST['experience'],
                '139' => $_POST['companies'],
                '140' => $_POST['basic'],
                '141' => $_POST['comments'],
                '142' => $_POST['ilDate'],
                '143' => $_POST['ilAssigned'],
                '154' => $providersArray,
                '158' => $termFiles,
                '159' => $experienceFiles,
                '160' => $companiesFiles,
                '161' => $basicFiles,
                '162' => $commentsFiles,
                '245' => $lesseeId,
                '133' => $goalsID
            ],
        ]);
        var_dump($listId);
    }

/** отправка заявки на службы */
if($_POST['action'] === 'create'){
    $updateProperties = [
        '115' => '103',
        '114' => '98',
        '147' => time(),
        '118' => $listId
    ];
    $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, $updateProperties);
    $leadProvider->Update($request['PROPERTY_117_VALUE'],$leadFields);
}
/** отправка заявки на службы */


//$properties = [
//    '122' => $_POST['link'],
//    '123' => $_POST['companyTitle'],
//    '124' => $_POST['clientRepeat']==='on'?107:108,
//    '125' => $_POST['clientRepeatDate'],
//    '126' => $_POST['clientRepeatResult'],
//    '127' => $_POST['inn'],
//    '128' => $_POST['ogrn'],
//    '129' => $_POST['dateObrazovaniya'],
//    '130' => $_POST['manGreat'],
//    '131' => $_POST['manOwner'],
//    '132' => json_decode($_POST['meetingDate']),
//    '133' => $_POST['leasingGoal'],
//    '134' => $_POST['totalOpportunity'],
//    '135' => $_POST['avancePay'],
//    '136' => $_POST['lisingSrok'],
//
//    '157' => $_POST['ndsIncluded'] === 'true'?115:114,
//    '202' => $_POST['ur-address'],
//];
//if($_POST['action'] === 'save'){
//    $properties['215'] = 134;
//}
//
//
//$properties['245'] = $lesseeId;
//
//
//
//if($request['PROPERTY_118_VALUE'] === NULL){
//    $listId = $iblockProvider->Add([
//        'IBLOCK_ID' => 34,
//        'NAME' => 'Информационный лист',
//        'ACTIVE' => 'Y',
//        'PROPERTY_VALUES' => $properties,
//    ]);
//}else{
//
//
//}
//if($_POST['action'] !== 'save'){

//}else{
//    if($request['PROPERTY_118_VALUE'] === NULL)
//    $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
//            '118' => $listId,
//        ]
//    );
//}
