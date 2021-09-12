<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
CModule::IncludeModule('iblock')?$iblockProvider = new \CIblockElement(false):$iblockProvider = 'Module is not loaded';
if($_POST['committee'] === 'mack'){
    $sectionListDB = CIBlockSection::GetList(array('left_margin' => 'asc','SORT'=>'ASC'),[
        'IBLOCK_ID' => 31,
        'ID' => 70
    ],false,['IBLOCK_ID','UF_ROLES']);
    while($sectionList = $sectionListDB->GetNext())
    {
        $sectionLists[] = $sectionList;
    }
    $rolesArray = $sectionLists[0]['UF_ROLES'];
    $rolesDB = $iblockProvider->GetList([],[
        'IBLOCK_ID' => 32,
        'ID' => $rolesArray
    ],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_112']);
    $roles = [];
    while($role = $rolesDB->Fetch()){
        if(strpos($role['NAME'],'Председатель') !== false){
            $committeeHead = $role['PROPERTY_112_VALUE'];
            continue;
        }
        $roles[$role['NAME']][] = $role['PROPERTY_112_VALUE'];
    }
    $mackAgreements = [];
    foreach ($roles as $id){
        $agreement = $iblockProvider->Add([
            'IBLOCK_ID' => 41,
            'NAME' => 'Согласование',
            'ACTIVE' => 'Y',
            'PROPERTY_VALUES' => [
                '192' => $id,
                '201' => '132'
            ]
        ]);

        $mackAgreements[] = $agreement;
    }
    $agreement = $iblockProvider->Add([
        'IBLOCK_ID' => 41,
        'NAME' => 'Согласование',
        'ACTIVE' => 'Y',
        'PROPERTY_VALUES' => [
            '192' => $committeeHead,
            '196' => '129',
            '201' => '132'
        ]
    ]);
    $mackAgreements[] = $agreement;

    $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
            '177' => $mackAgreements,
            '186' => count($mackAgreements),
            '180' => '122',
            '115' => '104',
            '114' => '98',
            '194' => time(),
        ]
    );
}elseif($_POST['committee'] === 'back'){

    $sectionListDB = CIBlockSection::GetList(array('left_margin' => 'asc','SORT'=>'ASC'),[
        'IBLOCK_ID' => 31,
        'ID' => 71
    ],false,['IBLOCK_ID','UF_ROLES']);
    while($sectionList = $sectionListDB->GetNext())
    {
        $sectionLists[] = $sectionList;
    }
    $rolesArray = $sectionLists[0]['UF_ROLES'];
    $rolesDB = $iblockProvider->GetList([],[
        'IBLOCK_ID' => 32,
        'ID' => $rolesArray
    ],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_112']);
    $roles = [];
    while($role = $rolesDB->Fetch()){
        if(strpos($role['NAME'],'Председатель') !== false){
            $committeeHead = $role['PROPERTY_112_VALUE'];
            continue;
        }
        $roles[$role['NAME']][] = $role['PROPERTY_112_VALUE'];
    }




    $backAgreements = [];
    foreach ($roles as $id){
        $agreement = $iblockProvider->Add([
            'IBLOCK_ID' => 41,
            'NAME' => 'Согласование',
            'ACTIVE' => 'Y',
            'PROPERTY_VALUES' => [
                '192' => $id,
                '201' => '131'
            ]
        ]);
        $backAgreements[] = $agreement;
    }
    $agreement = $iblockProvider->Add([
        'IBLOCK_ID' => 41,
        'NAME' => 'Согласование',
        'ACTIVE' => 'Y',
        'PROPERTY_VALUES' => [
            '192' => $committeeHead,
            '196' => '129',
            '201' => '131'
        ]
    ]);
    $backAgreements[] = $agreement;

    $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
            '177' => $backAgreements,
            '183' => count($backAgreements),
            '180' => '123',
            '115' => '104',
            '114' => '98',
            '193' => time(),
        ]
    );
}elseif($_POST['committee'] === 'underwriting') {
    if(isset($_POST['action'])){
        //if()
    }else {
        $sectionListDB = CIBlockSection::GetList(array('left_margin' => 'asc', 'SORT' => 'ASC'), [
            'IBLOCK_ID' => 31,
            'ID' => 69
        ], false, ['IBLOCK_ID', 'UF_ROLES']);
        while ($sectionList = $sectionListDB->GetNext()) {
            $sectionLists[] = $sectionList;
        }
        $rolesArray = $sectionLists[0]['UF_ROLES'];
        $rolesDB = $iblockProvider->GetList([], [
            'IBLOCK_ID' => 32,
            'ID' => $rolesArray
        ], false, false, ['ID', 'IBLOCK_ID', 'NAME', 'IBLOCK_SECTION_ID', 'PROPERTY_112']);
        $roles = [];
        while ($role = $rolesDB->Fetch()) {
            if (strpos($role['NAME'], 'Председатель') !== false) {
                $committeeHead = $role['PROPERTY_112_VALUE'];
                continue;
            }
            $roles[$role['NAME']][] = $role['PROPERTY_112_VALUE'];
        }
        $agreement = $iblockProvider->Add([
            'IBLOCK_ID' => 41,
            'NAME' => 'Согласование',
            'ACTIVE' => 'Y',
            'PROPERTY_VALUES' => [
                '192' => $committeeHead,
                '196' => '129',
                '201' => '133'
            ]
        ]);
        $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
                '177' => [$agreement],
                '189' => count($agreement),
                '180' => '124',
                '115' => '104',
                '114' => '98',
                '195' => time(),
            ]
        );
    }
}
