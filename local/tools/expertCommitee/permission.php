<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
\Bitrix\Main\UI\Extension::load('ui.entity-selector');
CModule::IncludeModule('iblock')?$iblockProvider = new \CIblockElement(false):$iblockProvider = 'Module is not loaded';
if($_POST['action'] === 'delete'){
    $permsDB = $iblockProvider->GetList([],[
        'IBLOCK_ID' => 31,
        'PROPERTY_111_VALUE' => $_POST['permissionId']
    ],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_108','PROPERTY_109','PROPERTY_110']);
    $perms = [];
    while($perm = $permsDB->Fetch()){
        $perms[] = $perm['ID'];
    }
    foreach ($perms as $perm){
        $iblockProvider->Delete($perm);
    }
    $iblockProvider->Delete($_POST['permissionId']);

}elseif($_POST['action'] === 'create'){
    $sectionListDB = CIBlockSection::GetList(array('left_margin' => 'asc'),[
        'IBLOCK_ID' => 31
    ]);
    while($sectionList = $sectionListDB->GetNext())
    {
        $sectionLists[] = $sectionList['ID'];
    }
    $iblockElementID = $iblockProvider->Add([
        'IBLOCK_ID' => 32,
        'NAME' => 'Новая роль',
        'ACTIVE' => 'Y',
    ]);
    foreach ($sectionLists as $id){
        $iblockProvider->Add([
            'IBLOCK_ID' => 31,
            'NAME' => 'Права доступа',
            'ACTIVE' => 'Y',
            'IBLOCK_SECTION_ID' => $id,
            'PROPERTY_VALUES' => [
               '108' => 'N',
               '109' => 'N',
               '110' => 'N',
               '111' => $iblockElementID
            ]
        ]);
    }
}elseif ($_POST['action'] === 'edit'){
    $sectionListDB = CIBlockSection::GetList(array('left_margin' => 'asc','SORT'=>'ASC'),[
        'IBLOCK_ID' => 31
    ]);
    while($sectionList = $sectionListDB->GetNext())
    {
        $sectionLists[] = $sectionList;
    }
   $permsDB = $iblockProvider->GetList([],[
       'IBLOCK_ID' => 31,
       'PROPERTY_111_VALUE' => $_POST['permissionId']
   ],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_108','PROPERTY_109','PROPERTY_110']);
   $perms = [];
   while($perm = $permsDB->Fetch()){
       $perms[$perm['IBLOCK_SECTION_ID']] = [
            'read' => $perm['PROPERTY_108_VALUE'],
           'edit' => $perm['PROPERTY_109_VALUE'],
           'vote' => $perm['PROPERTY_110_VALUE'],
       ];
   }
    $userProvider = new \CUser(false);
    $rolesDB = $iblockProvider->GetList([],[
        'IBLOCK_ID' => 32,
        'ID' => $_POST['permissionId']
    ],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_112']);
    $roles = [];
    while($role = $rolesDB->Fetch()){
        $roles[] = $role;
    }
    foreach ($roles as $role){
       if($role['PROPERTY_112_VALUE'] !== "") {
           $usersID[] = $role['PROPERTY_112_VALUE'];
       }
    }
    $users = [];
    if(!empty($usersID)){
       $userString = implode('|',$usersID);
        $usersDB = $userProvider->GetList(($by='ID'),($order='DESC'),[
            'ID' => $userString
        ]);
        while($user = $usersDB->Fetch()){
            $users[$user['ID']] = $user['LAST_NAME'] . ' ' . substr($user['NAME'],0,2) . '.' . substr($user['SECOND_NAME'],0,2) . '.' ;
            $preSelectedItems[] = [
                 'user',
                $user['ID']
            ];
        }
    }
    ?>
    <div class="my-4 ml-2">
        <div class="flex justify-start items-center my-2">
            <div class="text-gray-1000">
                Роль
            </div>
        </div>
        <div class="flex justify-start items-center my-2">
            <div>
                <input class="rounded-xl w-72 h-9 px-4 bg-gray-1000 role-title" type="text" value="<?=$roles[0]['NAME']?>" />
            </div>
        </div>
    </div>
    <div class="my-4 ml-2">
        <div class="flex justify-start items-center my-2">
            <div class="text-gray-1000">
                Сотрудники
            </div>
        </div>
        <div class="flex justify-start items-center my-2">
            <div>

               <div class="z-9999">
                  <div class="rounded-xl w-72 h-9 px-4 responsible-perms  flex justify-start items-center">
                     <div class="flex justify-start items-center users overflow-y-scroll max-h-12 min-w-full">
                        <div class="flex justify-start items-center user mx-2 min-w-100">
                            <?if(count($users) > 0)
                            {
                                foreach ($users as $id => $user){?>
                                    <div class="flex justify-start items-center user mx-2 min-w-100" data-user-id="<?=$id?>">
                                         <div class="text-blue-1000 mx-2 whitespace-nowrap">
                                             <?=$user?>
                                         </div>
                                    </div>
                                <?}
                            }else
                            {?>
                              <div class="flex justify-start items-center user mx-2 min-w-100 users-not-selected">
                                  <div class="text-blue-1000 mx-2">
                                     Сотрудники не выбраны
                                  </div>
                              </div>
                            <?}?>
                     </div>
                  </div>
               </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-4 gap-4">
        <div class="mx-4 text-gray-1000">
            Раздел
        </div>
        <div class="text-gray-1000">
            Чтение
        </div>
        <div class="text-gray-1000">
            Редактирование
        </div>
        <div class="text-gray-1000">
            Голосование
        </div>
    </div>
    <div class="permission-container">
        <?foreach ($sectionLists as $section):?>
        <div class="flex justify-between items-center role-container my-2">
            <div class="grid grid-cols-4 gap-4 w-full">
                <div class="ml-4 text-base text-blue-1000 flex justify-start items-center min-w-100">
                    <div><?=$section['NAME']?></div>
                </div>
                <div class="flex justify-start items-center">
                    <select id="read-<?=$section['ID']?>" data-section-id="<?=$section['ID']?>" class="text-blue-1000 bg-gray-1000 rounded-xl w-48 h-10 px-4">
                        <option <?=$perms[$section['ID']]['read']==='N'?'selected':''?> value="N">Нет доступа</option>
                        <option <?=$perms[$section['ID']]['read']==='A'?'selected':''?> value="A">Свои</option>
                        <option <?=$perms[$section['ID']]['read']==='D'?'selected':''?> value="D">Свои + своего отдела</option>
                        <option <?=$perms[$section['ID']]['read']==='X'?'selected':''?> value="X">Все</option>
                    </select>
                </div>
                <div class="flex justify-start items-center">
                    <select id="edit-<?=$section['ID']?>" data-section-id="<?=$section['ID']?>" class="text-blue-1000 bg-gray-1000 rounded-xl w-48 h-10 px-4">
                        <option <?=$perms[$section['ID']]['edit']==='N'?'selected':''?> value="N">Нет доступа</option>
                        <option <?=$perms[$section['ID']]['edit']==='A'?'selected':''?> value="A">Свои</option>
                        <option <?=$perms[$section['ID']]['edit']==='D'?'selected':''?> value="D">Свои + своего отдела</option>
                        <option <?=$perms[$section['ID']]['edit']==='X'?'selected':''?> value="X">Все</option>
                    </select>
                </div>
                <div class="flex justify-start items-center">
                    <select id="vote-<?=$section['ID']?>" data-section-id="<?=$section['ID']?>" class="text-blue-1000 bg-gray-1000 rounded-xl w-48 h-10 px-4">
                        <option <?=$perms[$section['ID']]['vote']==='N'?'selected':''?> value="N">Нет доступа</option>
                        <option <?=$perms[$section['ID']]['vote']==='A'?'selected':''?> value="A">Свои</option>
                        <option <?=$perms[$section['ID']]['vote']==='D'?'selected':''?> value="D">Свои + своего отдела</option>
                        <option <?=$perms[$section['ID']]['vote']==='X'?'selected':''?> value="X">Все</option>
                    </select>
                </div>
            </div>
        </div>
        <?endforeach;?>
    </div>
    <div class="flex items-center justify-center my-4">
        <div>
            <button class="mx-4 text-blue-1000 rounded-2xl h-9 w-52 flex justify-center items-center font-semibold px-2 bg-gray-1000" onclick="cancel()">Отмена</button>
        </div>
        <div>
            <button class="mx-4 text-blue-1000 border rounded-2xl h-9 w-32 flex justify-center items-center font-semibold active px-1 save-role">
                Сохранить
            </button>
        </div>
    </div>
   <script>

      var button = document.querySelector('.responsible-perms');
      var preSelectedOptions = <?=CUtil::PhpToJSObject($preSelectedItems)?>;
      var dialog = new BX.UI.EntitySelector.Dialog({
         targetNode: button,
         enableSearch: true,
         context: 'MY_MODULE_CONTEXT',
         preselectedItems: preSelectedOptions,
         popupOptions:{
            zIndexOptions:{
               zIndex: 10000,
            }
         },
         zIndex: 10000,
         events: {
            'Item:onSelect': (event) => {
               var usersContainer = document.querySelector('.users');
               var notSelected = document.querySelector('.users-not-selected');
               notSelected?notSelected.style = 'display:none':'';
               usersContainer.insertAdjacentHTML('beforeend', `<div class="flex justify-start items-center user mx-2 min-w-100" data-user-id="${event.data.item.id}">
                  <div class="text-blue-1000 whitespace-nowrap">
                     ${event.data.item.title}
                  </div>
               </div>`);
            },
            'Item:onDeselect': (event) => {
               var user = document.querySelector('[data-user-id="'+event.data.item.id+'"]');
               console.log(event)
               user.remove();
            },
         },
         entities: [
            {
               id: 'user',
            },
         ],
      });
      button.addEventListener('click', function() {
         dialog.show();
      });
      var saveRole = document.querySelector('.save-role');
      saveRole.addEventListener('click',function (){
         var roleName = document.querySelector('.role-title');
         var users = document.querySelectorAll('.user');
         var usersId = [];
         for(let i = 0; i < users.length; i++){
            if(users[i].dataset.userId)
            usersId.push(users[i].dataset.userId);
         }
         var permissionContainer = document.querySelector('.permission-container');
         var readPerms = permissionContainer.querySelectorAll('[id*="read"]');
         var permissions = [];
         for(let i = 0; i < readPerms.length; i++){
            permissions[readPerms[i].dataset.sectionId] = {id:readPerms[i].dataset.sectionId,read:readPerms[i].selectedOptions[0].value};
         }
         var editPerms = permissionContainer.querySelectorAll('[id*="edit"]');
         for(let i = 0; i < editPerms.length; i++){
            permissions[editPerms[i].dataset.sectionId].edit = editPerms[i].selectedOptions[0].value;
         }
         var votePerms = permissionContainer.querySelectorAll('[id*="vote"]');
         for(let i = 0; i < votePerms.length; i++){
            permissions[votePerms[i].dataset.sectionId].vote = votePerms[i].selectedOptions[0].value;
         }
         BX.ajax.post(
            '/local/tools/expertCommitee/savePermissions.php',
            {
               roleTitle:roleName.value,
               users:JSON.stringify(usersId),
               permissions:JSON.stringify(permissions),
               id:<?=$_POST['permissionId']?>,
            },
            function (data){
               var permissionContainer = document.querySelector('.modal-body');
               permissionContainer.innerHTML = data;
               var modalContainer = document.querySelector('.modal-container');
               modalContainer.classList.remove('md:max-w-6xl');
               modalContainer.classList.add('md:max-w-4xl');
            }
         );

      });
   </script>
<?
    die();
}
$userProvider = new \CUser(false);
$permsDB = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 32,
],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_112']);
$perms = [];
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
if(count($arResult['PERMISSIONS_LIST']) > 0){
    foreach ($arResult['PERMISSIONS_LIST'] as $permissionId => $permission):?>
        <div class="flex justify-between items-center role-container">
            <div class="mx-2 w-48 px-6 text-blue-1000 border-r-2">
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
    <div class="flex justify-center items-center">
        Прав доступа не создано
    </div>
<?}?>
