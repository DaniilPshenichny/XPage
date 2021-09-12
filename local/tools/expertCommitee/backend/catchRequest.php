<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
$iblockProvider = new \CIblockElement(false);
$userProvider = new \CUser(false);
if($_POST['action'] === 'activateSb'){
    $userId = CUser::GetID();
    $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
            '144' => [$userId],
            '114' => '99',
        ]
    );
    $user = $userProvider->GetByID($userId)->Fetch();
    echo $user['LAST_NAME'] . ' ' . substr($user['NAME'],0,2) . '.' . substr($user['SECOND_NAME'],0,2) . '.' ;
}
if($_POST['action'] === 'activateLegal'){
    $userId = CUser::GetID();
    $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
            '145' => [$userId],
            '114' => '99',
        ]
    );
    $user = $userProvider->GetByID($userId)->Fetch();
    echo $user['LAST_NAME'] . ' ' . substr($user['NAME'],0,2) . '.' . substr($user['SECOND_NAME'],0,2) . '.' ;
}
if($_POST['action'] === 'activateFinance'){
    $userId = CUser::GetID();
    $iblockProvider->SetPropertyValuesEx($_POST['requestId'], false, [
            '146' => [$userId],
            '114' => '99',
        ]
    );
    $user = $userProvider->GetByID($userId)->Fetch();
    echo $user['LAST_NAME'] . ' ' . substr($user['NAME'],0,2) . '.' . substr($user['SECOND_NAME'],0,2) . '.' ;
}