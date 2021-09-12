<?php
CJSCore::Init(array('access', 'window'));

if(CModule::IncludeModule('iblock')){
    $iblockProvider = new \CIblockElement(false);
    $userProvider = new \CUser(false);

    $currentUserId = $userProvider->GetID();
    $role = $iblockProvider->GetList([],[
        'IBLOCK_ID' => 32,
        'PROPERTY_112_VALUE' => [$currentUserId]
    ],false,false,['ID','IBLOCK_ID','NAME'])->Fetch();
    if($role['NAME'] === 'Администратор'){
        $arResult['ADMIN'] = 'Y';
    }
    $userPermissionsDB = $iblockProvider->GetList([],[
        'IBLOCK_ID' => 31,
        'PROPERTY_111_VALUE' => $role['ID']
    ],false,false,['ID','IBLOCK_ID','SECTION_ID','IBLOCK_SECTION_ID','PROPERTY_108','PROPERTY_109','PROPERTY_110']);
    while($userPermission = $userPermissionsDB->Fetch()){
        $arResult['userPermissions'][$userPermission['IBLOCK_SECTION_ID']] = [
            'read' => $userPermission['PROPERTY_108_VALUE'],
            'edit' => $userPermission['PROPERTY_109_VALUE'],
            'vote' => $userPermission['PROPERTY_110_VALUE'],
            'sectionName' => CIBlockSection::GetByID($userPermission['IBLOCK_SECTION_ID'])->Fetch()['NAME'],
        ];
    }


    $permsDB = $iblockProvider->GetList([],[
        'IBLOCK_ID' => 32,
    ],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_112']);
    while($perm = $permsDB->Fetch()){
        $perms[] = $perm;
    }
    foreach ($perms as $perm){
        $usersID[] = $perm['PROPERTY_112_VALUE'];
    }
    $usersDB = $userProvider->GetList(($by='ID'),($order='DESC'),[
        'ID' => $usersID
    ]);
    while($user = $usersDB->Fetch()){
        $users[$user['ID']] = $user['LAST_NAME'] . ' ' . substr($user['NAME'],0,2) . '.' . substr($user['SECOND_NAME'],0,2) . '.' ;
    }
    foreach ($perms as $perm){
        $arResult['PERMISSIONS_LIST'][$perm['ID']]['TITLE'] = $perm['NAME'];
        strlen($arResult['PERMISSIONS_LIST'][$perm['ID']]['USERS'])===0?$separator = '':$separator = ', ';
        $arResult['PERMISSIONS_LIST'][$perm['ID']]['USERS'] = $arResult['PERMISSIONS_LIST'][$perm['ID']]['USERS'].$separator.$users[$perm['PROPERTY_112_VALUE']];
    }
    $requestsDB = $iblockProvider->GetList([],[
        'IBLOCK_ID' => 33,
    ],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_114','PROPERTY_115','PROPERTY_117','PROPERTY_118','PROPERTY_119','PROPERTY_120','PROPERTY_121','PROPERTY_144','PROPERTY_145','PROPERTY_146','PROPERTY_147','PROPERTY_180','PROPERTY_183','PROPERTY_184','PROPERTY_185','PROPERTY_193','PROPERTY_198','DATE_CREATE']);
    while($request = $requestsDB -> Fetch()){
        $res = CIBlockElement::GetProperty(33, $request['ID'], "sort", "asc", array("ID" => "177"));
        $agreementsIDS = [];
        while ($ob = $res->GetNext())
        {
            $agreementsIDS[] = $ob['VALUE'];
        }
        $iL = CIBlockElement::GetList([],[
            'ID' => $request['PROPERTY_118_VALUE']
        ],false,false,['ID','IBLOCK_ID','PROPERTY_122','PROPERTY_215','PROPERTY_245'])->Fetch();
        if($request['PROPERTY_119_VALUE'] !== null){
            $sbList = CIBlockElement::GetList([],[
                'ID' => $request['PROPERTY_119_VALUE']
            ],false,false,['ID','IBLOCK_ID','DATE_CREATE'])->Fetch();
            $request['SB_CREATE_TIME'] = strtotime($sbList['DATE_CREATE']);
        }
        if($request['PROPERTY_120_VALUE'] !== null) {
            $legalList = CIBlockElement::GetList([], [
                'ID' => $request['PROPERTY_120_VALUE']
            ], false, false, ['ID', 'IBLOCK_ID', 'DATE_CREATE','PROPERTY_299'])->Fetch();
            $request['PROPERTY_120_VALUE_VALUE'] = $request['PROPERTY_120_VALUE'];
            if($legalList['PROPERTY_299_VALUE'] === 'Да')$request['PROPERTY_120_VALUE'] = NULL;
            $request['LEGAL_CREATE_TIME'] = strtotime($legalList['DATE_CREATE']);
        }
        if($request['PROPERTY_121_VALUE'] !== null) {
            $financeList = CIBlockElement::GetList([], [
                'ID' => $request['PROPERTY_121_VALUE']
            ], false, false, ['ID', 'IBLOCK_ID', 'DATE_CREATE','PROPERTY_300'])->Fetch();
            $request['PROPERTY_121_VALUE_VALUE'] = $request['PROPERTY_121_VALUE'];
            if($financeList['PROPERTY_300_VALUE'] === 'Да')$request['PROPERTY_121_VALUE'] = NULL;
            $request['FINANCE_CREATE_TIME'] = strtotime($financeList['DATE_CREATE']);
        }
        if($iL['PROPERTY_245_VALUE'] !== null){
            $leasee = CIBlockElement::GetList([], [
                'ID' => $iL['PROPERTY_245_VALUE']
            ], false, false, ['ID', 'IBLOCK_ID', 'DATE_CREATE','PROPERTY_265'])->Fetch();
            $request['PROPERTY_122'] = $leasee['PROPERTY_265_VALUE'];
        }

        $request['PROPERTY_177_VALUE'] = $agreementsIDS;
        //$request['PROPERTY_122'] = $iL['PROPERTY_122_VALUE'];
        $request['IL_SAVED'] = $iL?true:false;


//        $legal = CIBlockElement::GetList([], [
//            'ID' => $request['PROPERTY_120_VALUE']
//        ], false, false, ['ID', 'IBLOCK_ID', 'DATE_CREATE','PROPERTY_299'])->Fetch();
//        if($legal['PROPERTY_299'] === 'Да')$request['PROPERTY_120_VALUE'] = NULL;
//
//        $finance = CIBlockElement::GetList([], [
//            'ID' => $request['PROPERTY_121_VALUE']
//        ], false, false, ['ID', 'IBLOCK_ID', 'DATE_CREATE','PROPERTY_300'])->Fetch();
//        if($legal['PROPERTY_300'] === 'Да')$request['PROPERTY_121_VALUE'] = NULL;



        $requests[] = $request;

        $leadIds[] = $request['PROPERTY_117_VALUE'];
    }
    if(CModule::IncludeModule('crm')){
        $leadProvider = new \CCrmLead(false);
        $companyProvider = new \CCrmCompany(false);
        $leadDB =  $leadProvider->GetList(['ID'=>'DESC'],[
            'ID' => $leadIds,
            'CHECK_PERMISSIONS' => 'N',
        ]);
        while($lead = $leadDB->Fetch()){
            $leads[$lead['ID']] = $lead;
            $companyIds[] = $lead['COMPANY_ID'];
        }
        $companyDB =  $companyProvider->GetList(['ID'=>'DESC'],[
            'ID' => $companyIds,
            'CHECK_PERMISSIONS' => 'N',
        ]);
        while($company = $companyDB->Fetch()){
            $companies[$company['ID']] = $company;
        }
        $i = 0;
        $managerCounter = 0;
        $servicesCounter = 0;
        $commiteeCounter = 0;
        foreach ($requests as $request){
            switch($request['PROPERTY_114_VALUE']){
                case 'Новая':
                    $status = '<div class="new-request status-bar" data-requestId="'.$request['ID'].'">Новая</div>';
                    break;
                case 'На рассмотрении':
                    $status = '<div class="progress-request status-bar" data-requestId="'.$request['ID'].'">На рассмотрении</div>';
                    break;
                case 'Завершено':
                    $status = '<div class="finished-request status-bar" data-requestId="'.$request['ID'].'">Завершено</div>';
                    break;
            }
            $key = 'default';
            switch($request['PROPERTY_115_VALUE']) {
                case 'Заполнение сведений менеджером':
                    $key = 'manager';
                    $request['PROPERTY_114_VALUE'] === 'На рассмотрении'?$managerCounter++:true;
                    break;
                case 'Рассмотрение службами':
                    $key = 'services';
                    $request['PROPERTY_114_VALUE'] === 'На рассмотрении'?$servicesCounter++:true;
                    break;
                case 'Рассмотрение комитетом':

                    $key = 'committe';
                    break;
            }
            switch($request['PROPERTY_180_VALUE']) {
                case 'МЭК':
                    $key = 'mack';
                    $request['PROPERTY_114_VALUE'] === 'На рассмотрении'?$mackCounter++:true;
                    break;
                case 'БЭК':
                    $key = 'back';
                    $request['PROPERTY_114_VALUE'] === 'На рассмотрении'?$backCounter++:true;
                    break;
                case 'Андеррайтинг':
                    $key = 'underwriting';
                    $request['PROPERTY_114_VALUE'] === 'На рассмотрении'?$underwritingCounter++:true;
                    break;
            }
            $requestFuckup = false;
            if(((time()-$request['PROPERTY_147_VALUE'])/3600) > 8)$requestFuckup = true;

            //back commitee start//
            $agreementsBackDB = $iblockProvider->GetList([],[
                'IBLOCK_ID' => 41,
                'ID' => $request['PROPERTY_177_VALUE'],
                'PROPERTY_201' => '131',

            ],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_181','PROPERTY_182','PROPERTY_192','PROPERTY_196','PROPERTY_197']);

            $agreementsBack = [];
            while($agreementBack = $agreementsBackDB->Fetch()){
                $agreementsBack[] = $agreementBack;
            }
            $backResults = [];
            foreach ($agreementsBack as $agreement){
                $user = $userProvider->GetByID($agreement['PROPERTY_192_VALUE'])->Fetch();
                $date = $agreement['PROPERTY_197_VALUE'] !== null?date('d.m.Y H:i:s',$agreement['PROPERTY_197_VALUE']):$agreement['PROPERTY_197_VALUE'];
                $head = $agreement['PROPERTY_196_VALUE']==='Да'?'YES':'NO';
                $backResults[] = [
                    'ID' => $agreement['ID'],
                    'FIO' => $user['LAST_NAME'] . ' ' . substr($user['NAME'],0,2) . '.' . substr($user['SECOND_NAME'],0,2) . '.' ,
                    'STATUS' => $agreement['PROPERTY_181_VALUE'],
                    'RESULT' => $agreement['PROPERTY_182_VALUE'],
                    'DATE' =>  $date,
                    'HEAD' => $head
                ];
            }
            //back commitee end//

            //underwriting commitee start//
            $agreementsUnderwritingDB = $iblockProvider->GetList([],[
                'IBLOCK_ID' => 41,
                'ID' => $request['PROPERTY_177_VALUE'],
                'PROPERTY_201' => '133',
            ],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_181','PROPERTY_182','PROPERTY_192','PROPERTY_196','PROPERTY_197']);
            $agreementsUnderwriting = [];
            while($agreementUnderwriting = $agreementsUnderwritingDB->Fetch()){
                $agreementsUnderwriting[] = $agreementUnderwriting;
            }
            $UnderwritingResults= [];
            foreach ($agreementsUnderwriting as $agreement){
                $user = $userProvider->GetByID($agreement['PROPERTY_192_VALUE'])->Fetch();
                $date = $agreement['PROPERTY_197_VALUE'] !== null?date('d.m.Y H:i:s',$agreement['PROPERTY_197_VALUE']):$agreement['PROPERTY_197_VALUE'];
                $head = $agreement['PROPERTY_196_VALUE']==='Да'?'YES':'NO';
                $UnderwritingResults[] = [
                    'ID' => $agreement['ID'],
                    'FIO' => $user['LAST_NAME'] . ' ' . substr($user['NAME'],0,2) . '.' . substr($user['SECOND_NAME'],0,2) . '.' ,
                    'STATUS' => $agreement['PROPERTY_181_VALUE'],
                    'RESULT' => $agreement['PROPERTY_182_VALUE'],
                    'DATE' =>  $date,
                    'HEAD' => $head
                ];
            }
            //underwriting commitee end//


            //mack commitee start//
            $agreementsMackDB = $iblockProvider->GetList([],[
                'IBLOCK_ID' => 41,
                'ID' => $request['PROPERTY_177_VALUE'],
                'PROPERTY_201' => '132',
            ],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_181','PROPERTY_182','PROPERTY_192','PROPERTY_196','PROPERTY_197']);
            $agreementsMack = [];
            while($agreementMack = $agreementsMackDB->Fetch()){
                $agreementsMack[] = $agreementMack;
            }

            $mackResults = [];

            foreach ($agreementsMack as $agreement){
                $user = $userProvider->GetByID($agreement['PROPERTY_192_VALUE'])->Fetch();
                $date = $agreement['PROPERTY_197_VALUE'] !== null?date('d.m.Y H:i:s',$agreement['PROPERTY_197_VALUE']):$agreement['PROPERTY_197_VALUE'];
                $head = $agreement['PROPERTY_196_VALUE']==='Да'?'YES':'NO';
                $mackResults[] = [
                    'ID' => $agreement['ID'],
                    'FIO' => $user['LAST_NAME'] . ' ' . substr($user['NAME'],0,2) . '.' . substr($user['SECOND_NAME'],0,2) . '.' ,
                    'STATUS' => $agreement['PROPERTY_181_VALUE'],
                    'RESULT' => $agreement['PROPERTY_182_VALUE'],
                    'DATE' =>  $date,
                    'HEAD' => $head
                ];
            }
            //mack commitee end//







            switch($request['PROPERTY_180_VALUE']) {
                case 'МЭК':
                    $key = 'mack';
                    break;
                case 'БЭК':
                    $key = 'back';
                    break;
                case 'Андеррайтинг':
                    $key = 'underwriting';
                    break;
            }
            $dataArray = [
                'DATE_CREATE' => $request['DATE_CREATE'],
                'MANAGER_ID' => $leads[$request['PROPERTY_117_VALUE']]['ASSIGNED_BY_ID'],
                'CURRENT_USER_ID' => $currentUserId,
                'MANAGER' => $leads[$request['PROPERTY_117_VALUE']]['ASSIGNED_BY_LAST_NAME'] . ' ' . substr($leads[$request['PROPERTY_117_VALUE']]['ASSIGNED_BY_NAME'],0,2) . '.' . substr($leads[$request['PROPERTY_117_VALUE']]['ASSIGNED_BY_SECOND_NAME'],0,2) . '.' ,
                'LISING_OWNER' => $companies[$leads[$request['PROPERTY_117_VALUE']]['COMPANY_ID']]['TITLE'],
                'LISING_GOAL' => $leads[$request['PROPERTY_117_VALUE']]['UF_CRM_1586959397'],
                'STATUS' => $status,
                'STATUS_CLEAN' => $request['PROPERTY_114_VALUE'],
                'LEAD_ID' => $request['PROPERTY_117_VALUE'],
                'ID' => $request['ID'],
                'IL_SAVED' => $request['IL_SAVED'],
                'SB' => $request['PROPERTY_119_VALUE'],
                'SB_CREATE_TIME' => $request['SB_CREATE_TIME'],
                'LEGAL' => $request['PROPERTY_120_VALUE'],
                'LEGAL_CREATE_TIME' => $request['LEGAL_CREATE_TIME'],
                'FINANCE' => $request['PROPERTY_121_VALUE'],
                'FINANCE_CREATE_TIME' => $request['FINANCE_CREATE_TIME'],
                'SB_RESPONSIBLE' => $request['PROPERTY_144_VALUE']?$userProvider->GetList(($by = "ID"),($order = 'DESC'),['ID' => $request['PROPERTY_144_VALUE']],['FIELDS'=>['ID','LAST_NAME','NAME','SECOND_NAME']])->Fetch():'',
                'LEGAL_RESPONSIBLE' => $request['PROPERTY_145_VALUE']?$userProvider->GetList(($by = "ID"),($order = 'DESC'),['ID' => $request['PROPERTY_145_VALUE']],['FIELDS'=>['ID','LAST_NAME','NAME','SECOND_NAME']])->Fetch():'',
                'FINANCE_RESPONSIBLE' => $request['PROPERTY_146_VALUE']?$userProvider->GetList(($by = "ID"),($order = 'DESC'),['ID' => $request['PROPERTY_146_VALUE']],['FIELDS'=>['ID','LAST_NAME','NAME','SECOND_NAME']])->Fetch():'',
                'SB_ID' => $request['PROPERTY_119_VALUE_VALUE']?:0,
                'LEGAL_ID' => $request['PROPERTY_120_VALUE_VALUE']?:0,
                'FINANCE_ID' => $request['PROPERTY_121_VALUE_VALUE']?:0,
                'FUCKUP' => $requestFuckup,
                'LINK' => str_replace('\\','/',$request['PROPERTY_122']),
            ];
            if($request['PROPERTY_180_VALUE'] === 'МЭК'){
                $dataArray['MACK_TIME'] = date('d.m.Y H:i:s',$request['PROPERTY_194_VALUE']);
                $dataArray['MACK_RESULTS'] = $mackResults;
            }

            if($request['PROPERTY_180_VALUE'] === 'БЭК'){
                $dataArray['BACK_TIME'] =  date('d.m.Y H:i:s',$request['PROPERTY_193_VALUE']);
                $dataArray['BACK_RESULTS'] = $backResults;
                $dataArray['BACK_PERCENTS'] = $request['PROPERTY_185_VALUE'];
                $dataArray['BACK_PLAN_PEOPLE'] = $request['PROPERTY_183_VALUE'] - 1;
                $dataArray['BACK_FACT_PEOPLE'] = (int)$request['PROPERTY_198_VALUE'];
            }
            if($request['PROPERTY_180_VALUE'] === 'Андеррайтинг' || count($UnderwritingResults) > 0){

                $dataArray['UNDERWRITING_TIME'] =  date('d.m.Y H:i:s',$request['PROPERTY_195_VALUE']);
                $dataArray['UNDERWRITING_RESULTS'] = $UnderwritingResults;
            }
            $arResult['requests'][$key][] = $dataArray;

        }
//        echo '<pre>';
//        print_r($arResult['requests']);
        $arResult['servicesCounter'] = $servicesCounter?:0;
        $arResult['managerCounter'] = $managerCounter?:0;
        $arResult['mackCounter'] =  $mackCounter?:0;
        $arResult['backCounter']  = $backCounter?:0;
        $arResult['underwritingCounter']  = $underwritingCounter?:0;

    }
}


























$this->includeComponentTemplate();