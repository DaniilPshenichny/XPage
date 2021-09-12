<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
CModule::IncludeModule('iblock')?$iblockProvider = new \CIblockElement(false):$iblockProvider = 'Module is not loaded';
if($_POST['commitee'] === 'back') {
    $generalRequest = $iblockProvider->GetList([], [
        'IBLOCK_ID' => 33,
        'PROPERTY_177' => [$_POST['requestId']]
    ], false, false, ['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_117', 'PROPERTY_183', 'PROPERTY_184', 'PROPERTY_198'])->Fetch();
    switch ($_POST['action']) {
        case 'accept':
            $action = 125;
            break;
        case 'decline':
            $action = 126;
            break;
        case 'ignore':
            $action = 127;
            break;
        case 'edit':
            $action = 128;
            break;
    }

    $date = time();

    /** Обновление согласования */
    $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
            '197' => $date,
            '182' => $_POST['comment'],
            '181' => $action,
        ]
    );
    /** Обновление согласования */

    /** Обновление полей БЭК в основной заявке */
    $_POST['action'] === 'accept' ? $generalRequest['PROPERTY_184_VALUE']++ : '';
    $percent = (round((int)$generalRequest['PROPERTY_184_VALUE'] / $generalRequest['PROPERTY_183_VALUE'], 2) * 100);
    $totalVotes = $generalRequest['PROPERTY_198_VALUE'] + 1;
    $iblockProvider->SetPropertyValuesEx($generalRequest['ID'], false, [
            '198' => $totalVotes,
            '184' => $generalRequest['PROPERTY_184_VALUE'],
            '185' => $percent . '%'
        ]
    );
    /** Обновление полей БЭК в основной заявке */

    /** проверка на последнего голосующего и обновление лида */
    if ($generalRequest['PROPERTY_183_VALUE'] == $totalVotes) {
        if ((int)$percent >= 60) {
            if (CModule::IncludeModule('crm')) {
                $leadProvider = new \CCrmLead(false);
                $leadFields = [
                    'STATUS_ID' => '5'
                ];
                $leadProvider->Update($generalRequest['PROPERTY_117_VALUE'], $leadFields);
            }
            /** установить статус заявки в завершено */
            $iblockProvider->SetPropertyValuesEx($generalRequest['ID'], false, [
                    '114' => '101',
                    '296' => '154',
                ]
            );
        } else {
            if (CModule::IncludeModule('crm')) {
                $leadProvider = new \CCrmLead(false);
                $leadFields = [
                    'STATUS_ID' => '5'
                ];
                $leadProvider->Update($generalRequest['PROPERTY_117_VALUE'], $leadFields);
            }
            /** установить статус заявки в завершено */
            $iblockProvider->SetPropertyValuesEx($generalRequest['ID'], false, [
                    '114' => '101',
                    '296' => '153',
                ]
            );
        }
    }
    echo json_encode([
        'status' => $_POST['action'],
        'actionDate' => date('d.m.Y H:i:s', $date),
        'conclusion' => $_POST['comment'] ?: ''
    ]);
}
elseif ($_POST['commitee'] === 'mack'){
    $generalRequest = $iblockProvider->GetList([], [
        'IBLOCK_ID' => 33,
        'PROPERTY_177' => [$_POST['requestId']]
    ], false, false, ['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_117', 'PROPERTY_186','PROPERTY_187', 'PROPERTY_188', 'PROPERTY_199'])->Fetch();
    switch ($_POST['action']) {
        case 'accept':
            $action = 125;
            break;
        case 'decline':
            $action = 126;
            break;
        case 'ignore':
            $action = 127;
            break;
        case 'edit':
            $action = 128;
            break;
    }

    $date = time();

    /** Обновление согласования */
    $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
            '197' => $date,
            '182' => $_POST['comment'],
            '181' => $action,
        ]
    );
    /** Обновление согласования */

    /** Обновление полей МЭК в основной заявке */
    $_POST['action'] === 'accept' ? $generalRequest['PROPERTY_187_VALUE']++ : '';
    $percent = (round((int)$generalRequest['PROPERTY_187_VALUE'] / $generalRequest['PROPERTY_186_VALUE'], 2) * 100);
    $totalVotes = $generalRequest['PROPERTY_198_VALUE'] + 1;
    $iblockProvider->SetPropertyValuesEx($generalRequest['ID'], false, [
            '199' => $totalVotes,
            '187' => $generalRequest['PROPERTY_187_VALUE'],
            '188' => $percent . '%'
        ]
    );
    /** Обновление полей МЭК в основной заявке */
    echo json_encode([
        'status' => $_POST['action'],
        'actionDate' => date('d.m.Y H:i:s', $date),
        'conclusion' => $_POST['comment'] ?: ''
    ]);
}
elseif ($_POST['commitee'] === 'backHead'){
    $generalDecision = 0;
    switch($_POST['action']){
        case 'accept':
            $action = 125;
            $generalDecision = 154;
            $notifyAction = 'согласовал';
            break;
        case 'decline':
            $action = 126;
            $generalDecision = 153;
            $notifyAction = 'отклонил';
            break;
        case 'ignore':
            $action = 127;
            break;
        case 'edit':
            $action = 128;
            break;
    }
    $date = time();
    $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
            '197' => $date,
            '181' => $action,
        ]
    );
    $generalRequest = $iblockProvider->GetList([],[
        'IBLOCK_ID' => 33,
        'PROPERTY_177' => [$_POST['requestId']]
    ],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_183','PROPERTY_184','PROPERTY_118','PROPERTY_198'])->Fetch();
    $il = $iblockProvider->GetList([],[
        'IBLOCK_ID' => 34,
        'ID' => $generalRequest['PROPERTY_118_VALUE']
    ],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_183','PROPERTY_245'])->Fetch();
    $leasee = $iblockProvider->GetList([],[
        'IBLOCK_ID' => 43,
        'ID' => $il['PROPERTY_245_VALUE']
    ],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_183','PROPERTY_244'])->Fetch();
    $item = $iblockProvider->GetList([],[
        'IBLOCK_ID' => 42,
        'ID' => $il['PROPERTY_244_VALUE']
    ],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_183','PROPERTY_244'])->Fetch();

    $iblockProvider->SetPropertyValuesEx($generalRequest['ID'], false, [
            '114' => '101',
            '198' =>(int)++$generalRequest['PROPERTY_198_VALUE'],
            '296' => $generalDecision
        ]
    );
    $arMessageFields = array(
        "FROM_USER_ID" => 0,
        "TO_USER_ID" => 1,
        "NOTIFY_MODULE" => "intranet",
        "NOTIFY_MESSAGE" => "Экспертный комитет: БЭК {$notifyAction} заявку:
Лизингополучатель: {$leasee['NAME']}
Предмет лизинга: {$item['NAME']}
",
    );
    if(CModule::IncludeModule('im')){
        CIMNotify::Add($arMessageFields);
    }
}
elseif($_POST['commitee'] === 'underwriting'){

//    switch($_POST['action']){
//        case 'accept':
//            $action = 125;
//            break;
//        case 'decline':
//            $action = 126;
//            break;
//        case 'ignore':
//            $action = 127;
//            break;
//        case 'edit':
//            $action = 128;
//            break;
//    }
//    $date = time();
//    $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
//            '197' => $date,
//            '181' => $action,
//            '182' => $_POST['comment']
//        ]
//    );
//    $generalRequest = $iblockProvider->GetList([],[
//        'IBLOCK_ID' => 33,
//        'PROPERTY_177' => [$_POST['requestId']]
//    ],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_183','PROPERTY_184','PROPERTY_198'])->Fetch();
//    $iblockProvider->SetPropertyValuesEx($generalRequest['ID'], false, [
//            '114' => '101',
//        ]
//    );

    if($_POST['action'] === 'sendToBack'){
        //блок отправки на бэк


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
        $generalRequest = $iblockProvider->GetList([],[
            'IBLOCK_ID' => 33,
            'PROPERTY_177' => [$_POST['requestId']]
        ],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_177'])->Fetch();

        $res = CIBlockElement::GetProperty(33, $generalRequest['ID'], "sort", "asc", array("ID" => "177"));
        while ($ob = $res->GetNext())
        {
            $agreements[] = $ob['VALUE'];
        }
        $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
                '197' => time(),
                '182' => $_POST['comment'],
                '181' => $_POST['action']==='accept'?'125':'126',
            ]
        );
        $iblockProvider->SetPropertyValuesEx($generalRequest['ID'], false, [
                '177' => $agreements + $backAgreements,
                '183' => count($backAgreements),
                '180' => '123',
                '115' => '104',
                '114' => '98',
                '193' => time(),
            ]
        );

        //блок отправки на бэк
    }

    if($_POST['action'] === 'accept'){
        /** установить статус решения председателя комитета */
        $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
                '197' => time(),
                '181' => '125',
                '182' => $_POST['comment']
            ]
        );

        /** получить основную заявку */
        $generalRequest = $iblockProvider->GetList([],[
            'IBLOCK_ID' => 33,
            'PROPERTY_177' => [$_POST['requestId']]
        ],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_117'])->Fetch();

        /** обновляем лид */

        if(CModule::IncludeModule('crm')){
            $leadProvider = new \CCrmLead(false);
            $leadFields = [
                'STATUS_ID' => '5'
            ];
            $leadProvider->Update($generalRequest['PROPERTY_117_VALUE'],$leadFields);
        }
        /** установить статус заявки в завершено */
        $iblockProvider->SetPropertyValuesEx($generalRequest['ID'], false, [
                '114' => '101',
                '296' => '154',
            ]
        );
    }
    if($_POST['action'] === 'decline'){
        /** установить статус решения председателя комитета */
        $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
                '197' => time(),
                '181' => '126',
                '182' => $_POST['comment']
            ]
        );

        /** получить основную заявку */
        $generalRequest = $iblockProvider->GetList([],[
            'IBLOCK_ID' => 33,
            'PROPERTY_177' => [$_POST['requestId']]
        ],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_117'])->Fetch();

        /** обновляем лид */

        if(CModule::IncludeModule('crm')){
            $leadProvider = new \CCrmLead(false);
            $leadFields = [
                'STATUS_ID' => '5'
            ];
            $leadProvider->Update($generalRequest['PROPERTY_117_VALUE'],$leadFields);
        }
        /** установить статус заявки в завершено */
        $iblockProvider->SetPropertyValuesEx($generalRequest['ID'], false, [
                '114' => '101',
                '296' => '153',
            ]
        );
    }


}



