<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
if(CModule::IncludeModule('iblock')){
    $iblockProvider = new \CIblockElement(false);
    $userProvider = new \CUser(false);
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
}?>
<div class="flex justify-start items-start my-2">
    <div class="mx-4 w-48 text-gray-1000">
        Роль
    </div>
    <div class="text-gray-1000">
        Сотрудники
    </div>
</div>
<div class="permission-container">
    <?if(count($arResult['PERMISSIONS_LIST']) > 0){
        foreach ($arResult['PERMISSIONS_LIST'] as $permissionId => $permission):?>
            <div class="flex justify-between items-center role-container h-auto my-1">
                <div class="mx-2 w-48 px-6 text-blue-1000 border-r-2 flex-shrink-0">
                    <?=$permission['TITLE']?>
                </div>
                <div class="mx-6 text-blue-1000"><?=$permission['USERS']?></div>
                <div class="flex mx-4">
                    <div class="mx-2 flex justify-center items-center w-8 h-8 shadow-my rounded cursor-pointer edit-permission" onclick="editPermission(<?=$permissionId?>)">
                        <div>
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.41065 2.0155L0.808079 8.61855C0.774861 8.65186 0.750876 8.69393 0.739454 8.73914L0.00762279 11.6766C-0.0142684 11.765 0.0117155 11.859 0.0762469 11.9235C0.125074 11.9724 0.191604 11.9994 0.259657 11.9994C0.280501 11.9994 0.301821 11.9968 0.32257 11.9916L3.25999 11.2597C3.30577 11.2483 3.34736 11.2244 3.38058 11.1911L9.98372 4.58857L7.41065 2.0155Z" fill="#28315F"/>
                                <path d="M11.6195 1.11523L10.8845 0.38026C10.3933 -0.110959 9.53718 -0.110483 9.04653 0.38026L8.14624 1.28056L10.7192 3.85353L11.6195 2.95323C11.8649 2.70796 12 2.38149 12 2.03428C12 1.68707 11.8649 1.3606 11.6195 1.11523Z" fill="#28315F"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mx-2">
                        <div class="mx-2 flex justify-center items-center w-8 h-8 shadow-my rounded cursor-pointer delete-permission" onclick="deletePermission(<?=$permissionId?>)">
                            <div>
                                <svg width="10" height="12" viewBox="0 0 10 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.9375 1.5H6.875V1.125C6.875 0.503672 6.37133 0 5.75 0H4.25C3.62867 0 3.125 0.503672 3.125 1.125V1.5H1.0625C0.544742 1.5 0.125 1.91974 0.125 2.4375V3.1875C0.125 3.39462 0.292883 3.5625 0.5 3.5625H9.5C9.70712 3.5625 9.875 3.39462 9.875 3.1875V2.4375C9.875 1.91974 9.45526 1.5 8.9375 1.5ZM3.875 1.125C3.875 0.918281 4.04328 0.75 4.25 0.75H5.75C5.95672 0.75 6.125 0.918281 6.125 1.125V1.5H3.875V1.125Z" fill="#EA4335"/>
                                    <path d="M0.836604 4.3125C0.76969 4.3125 0.71637 4.36842 0.719557 4.43527L1.02893 10.9284C1.05753 11.5294 1.55112 12 2.15253 12H7.84737C8.44878 12 8.94237 11.5294 8.97096 10.9284L9.28034 4.43527C9.28353 4.36842 9.23021 4.3125 9.16329 4.3125H0.836604ZM6.49995 5.25C6.49995 5.04281 6.66776 4.875 6.87495 4.875C7.08214 4.875 7.24995 5.04281 7.24995 5.25V10.125C7.24995 10.3322 7.08214 10.5 6.87495 10.5C6.66776 10.5 6.49995 10.3322 6.49995 10.125V5.25ZM4.62495 5.25C4.62495 5.04281 4.79276 4.875 4.99995 4.875C5.20714 4.875 5.37495 5.04281 5.37495 5.25V10.125C5.37495 10.3322 5.20714 10.5 4.99995 10.5C4.79276 10.5 4.62495 10.3322 4.62495 10.125V5.25ZM2.74995 5.25C2.74995 5.04281 2.91776 4.875 3.12495 4.875C3.33214 4.875 3.49995 5.04281 3.49995 5.25V10.125C3.49995 10.3322 3.33214 10.5 3.12495 10.5C2.91776 10.5 2.74995 10.3322 2.74995 10.125V5.25Z" fill="#EA4335"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?endforeach;
    }else{?>
        <div class="flex justify-center items-center role-container">
            Прав доступа не создано
        </div>
    <?}?>
</div>
<div class="flex items-center justify-center my-4">
    <div>
        <button class="mx-4 text-blue-1000 rounded-2xl h-9 w-52 flex justify-center items-center font-semibold px-2 bg-gray-1000" onclick="addPermission()">Добавить роль</button>
    </div>
    <div>
        <button class="mx-4 text-blue-1000 rounded-2xl h-9 w-52 flex justify-center items-center font-semibold px-2 bg-gray-1000" onclick="toggleModal('.perms-modal')">Отмена</button>
    </div>
    <div>
        <button class="mx-4 text-blue-1000 border rounded-2xl h-9 w-32 flex justify-center items-center font-semibold active px-1" onclick="toggleModal('.perms-modal')">
            Сохранить
        </button>
    </div>
</div>
