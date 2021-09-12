<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
$monthArray = ['января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'];
$date = new DateTime();
$month = $date->format('n')-1;
$iblockProvider = new \CIblockElement(false);
$saveMode = $_POST['mode']==='save'?true:false;
$request = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 33,
    'ID' => $_POST['requestId']
],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_117','PROPERTY_118'])->Fetch();
$informationList = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 34,
    'ID' => $request['PROPERTY_118_VALUE']
],false,false,['ID','IBLOCK_ID','NAME','DATE_CREATE','PROPERTY_245','PROPERTY_137','PROPERTY_138',
    'PROPERTY_139','PROPERTY_140','PROPERTY_141','PROPERTY_142','PROPERTY_143',])->Fetch();
$leasee = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 43,
    'ID' => $request['PROPERTY_245_VALUE']
],false,false,['ID','IBLOCK_ID','NAME','DATE_CREATE','PROPERTY_265','PROPERTY_222','PROPERTY_229','PROPERTY_230',
    'PROPERTY_223','PROPERTY_224','PROPERTY_225','PROPERTY_226','PROPERTY_297','PROPERTY_298','PROPERTY_227',
    'PROPERTY_228','PROPERTY_261','PROPERTY_264','PROPERTY_262','PROPERTY_263'])->Fetch();
$meetingDatesDB = CIBlockElement::GetProperty(43, $leasee['ID'], "sort", "asc", array("ID" => "231"));
while ($ob = $meetingDatesDB->GetNext())
{
    if($ob['VALUE'] !== null) $dates[] = date('Y-m-d', strtotime($ob['VALUE']));
}
$leasingGoalsDB = CIBlockElement::GetProperty(43, $leasee['ID'], "sort", "asc", array("ID" => "244"));
while ($goal = $leasingGoalsDB->GetNext())$goals[] = $goal['VALUE'];
if(CModule::IncludeModule('crm') && $saveMode){
    $leadProvider = new \CCrmLead(false);
    $lead = $leadProvider->GetListEx([],[
         'ID' => $request['PROPERTY_117_VALUE']
    ],false,false,['*','UF_*'])->Fetch();
    $companyProvider = new \CCrmCompany(false);
    $company = $companyProvider->GetListEx([],[
        'ID' => $lead['COMPANY_ID']
    ],false,false,['*','UF_*'])->Fetch();
    $contactProvider = new \CCrmContact(false);
    $contact = $contactProvider->GetListEx([],[
        'ID' => $lead['CONTACT_ID']
    ],false,false,['*','UF_*'])->Fetch();
    $phone = CCrmFieldMulti::GetList(
        ['ID' => 'ASC'],
        ['ENTITY_ID' => 'CONTACT', 'ELEMENT_ID' => $lead['CONTACT_ID']]
    )->Fetch();
    $company['TITLE'] = str_replace('"','',$company['TITLE']);
}
$providerTypesDB = CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>38,'PROPERTY_ID'=>148));
while ($providerType = $providerTypesDB->GetNext()) {
    $providerTypes[$providerType['ID']] = $providerType['VALUE'];
}


$srokFilesDB = CIBlockElement::GetProperty(34, $informationList['ID'], "sort", "asc", array("ID" => "158"));
while ($srokFile = $srokFilesDB->GetNext()){
    $fileFromDB = \CFile::GetFileArray($srokFile['VALUE']);
    $srokFiles[] = [
      'ID' => $fileFromDB['ID'],
      'TITLE' => $fileFromDB['FILE_NAME'],
      'PATH' => $fileFromDB['SRC']
    ];
}
$experienceFilesDB = CIBlockElement::GetProperty(34, $informationList['ID'], "sort", "asc", array("ID" => "159"));
while ($experienceFile = $experienceFilesDB->GetNext()){
    $fileFromDB = \CFile::GetFileArray($experienceFile['VALUE']);
    $experienceFiles[] = [
        'ID' => $fileFromDB['ID'],
        'TITLE' => $fileFromDB['FILE_NAME'],
        'PATH' => $fileFromDB['SRC']
    ];
}
$konkurFilesDB = CIBlockElement::GetProperty(34, $informationList['ID'], "sort", "asc", array("ID" => "160"));
while ($konkurFile = $konkurFilesDB->GetNext()){
    $fileFromDB = \CFile::GetFileArray($konkurFile['VALUE']);
    $konkurFiles[] = [
        'ID' => $fileFromDB['ID'],
        'TITLE' => $fileFromDB['FILE_NAME'],
        'PATH' => $fileFromDB['SRC']
    ];
}
$basisFilesDB = CIBlockElement::GetProperty(34, $informationList['ID'], "sort", "asc", array("ID" => "161"));
while ($basisFile = $basisFilesDB->GetNext()){
    $fileFromDB = \CFile::GetFileArray($basisFile['VALUE']);
    $basisFiles[] = [
        'ID' => $fileFromDB['ID'],
        'TITLE' => $fileFromDB['FILE_NAME'],
        'PATH' => $fileFromDB['SRC']
    ];
}
$primechFilesDB = CIBlockElement::GetProperty(34, $informationList['ID'], "sort", "asc", array("ID" => "162"));
while ($primechFile = $primechFilesDB->GetNext()){
    $fileFromDB = \CFile::GetFileArray($primechFile['VALUE']);
    $primechFiles[] = [
        'ID' => $fileFromDB['ID'],
        'TITLE' => $fileFromDB['FILE_NAME'],
        'PATH' => $fileFromDB['SRC']
    ];
}
?>

<div class="app">
    <div class="block">
        <div class="flex text-gray-1000 text-base">
            Ссылка<span class="text-red-500">*</span>:
        </div>
        <div class="my-2 w-full">
            <input class="border-b-2 outline-none w-full placeholder-blue-900 pb-2 link" type="text" value="<?=$saveMode?$leasee['PROPERTY_265_VALUE']:''?>" placeholder="Введите ссылку">
        </div>
    </div>
    <div class="flex items-center justify-center text-2xl my-6 text-blue-1000 font-bold">
        Информационный лист от “<?=$_POST['mode']!=='save'?$date->format('d'):date('d',strtotime($leasee['DATE_CREATE']));?>” <?=$_POST['mode']!=='save'?$monthArray[$month]:$monthArray[date('n',strtotime($il['DATE_CREATE']))-1]?> <?=$_POST['mode']!=='save'?$date->format('Y'):date('Y',strtotime($il['DATE_CREATE']));?> г.
    </div>
    <div class="mb-8">
        <div class="mb-4 text-xl text-blue-1000 font-semibold">
            Лизингополучатель:
        </div>
        <div>
            <div class="flex text-gray-1000 text-base">
                Название компании-лизингополучателя<span class="text-red-500">*</span>:
            </div>
            <div class="my-2 w-full">
               <input class="border-b-2 outline-none w-full placeholder-blue-900 pb-2 company-title" type="text" placeholder="Введите название компании" value="<?=$saveMode?$leasee['NAME']:$company['TITLE']?>">
            </div>
        </div>
    </div>
    <div class="flex justify-start mb-4">
        <div class="mr-6">
            <div class="text-gray-1000 text-lg mb-2">Клиент повторный:</div>
            <div>
               <label>
                  <input type="checkbox" class="hidden client-repeat" <?=$saveMode&&$leasee['PROPERTY_222_VALUE']==='Да'?'checked':''?> >
                  <div class="flex">
                     <div class="flex items-center justify-center shadow-my h-10 w-10 rounded mx-2 text-lg text-blue-1000 client-repeat-no cursor-pointer <?=$saveMode&&$leasee['PROPERTY_222_VALUE']==='Да'?'':'active'?>">Нет</div>
                     <div class="flex items-center justify-center shadow-my h-10 w-10 rounded mx-2 text-lg client-repeat-yes cursor-pointer <?=$saveMode&&$leasee['PROPERTY_222_VALUE']==='Да'?'active':''?>">Да</div>
                  </div>
               </label>
            </div>
        </div>
        <div class="mx-4 date-check  <?=$saveMode&&$leasee['PROPERTY_222_VALUE']==='Да'?'':'hidden'?>">
            <div class="flex text-gray-1000 text-lg mb-2 ">
                Дата:
            </div>
            <div class="my-4 w-full">
                <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base client-repeat-date" type="date" placeholder="Дата">
            </div>
        </div>
    </div>
   <div class="check-result <?=$saveMode&&$leasee['PROPERTY_222_VALUE']==='Да'?'':'hidden'?>">
      <div class="flex text-gray-1000 text-lg mb-2">
         Результат проверки:
      </div>
      <div class="my-4 w-full">
         <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base client-repeat-result" type="text" placeholder="Введите результат">
      </div>
   </div>
   <div class="my-2">
      <div class="flex text-gray-1000 text-base">
         Юридический адрес:
      </div>
      <div class="my-2 w-full">
         <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base ur-address" type="text" value="<?=$saveMode?$leasee['PROPERTY_223_VALUE']:''?>" placeholder="Введите юридический адрес">
      </div>
   </div>
   <div class="flex justify-between my-2">
      <div>
         <div class="flex text-gray-1000 text-base">
            ИНН:
         </div>
         <div class="my-2 w-full">
            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base inn" type="text" placeholder="Введите ИНН" value="<?=$saveMode?$leasee['PROPERTY_224_VALUE']:''?>">
         </div>
      </div>
      <div>
         <div class="flex text-gray-1000 text-base">
            ОГРН:
         </div>
         <div class="my-2 w-full">
            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base ogrn" type="text" placeholder="Введите ОГРН" value="<?=$saveMode?$leasee['PROPERTY_225_VALUE']:''?>">
         </div>
      </div>
      <div>
         <div class="flex text-gray-1000 text-base">
            Дата образования<span class="text-red-500">*</span>:
         </div>
         <div class="my-2 w-full">
            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base date-obrazovaniya" type="date" placeholder="Введите дату" value="<?=$saveMode?$leasee['PROPERTY_226_VALUE']:''?>">
         </div>
      </div>
   </div>
   <div class="grid grid-cols-2 gap-4 my-2">
      <div>
         <div class="flex text-gray-1000 text-base">
            Контакт<span class="text-red-500">*</span>:
         </div>
         <div class="my-2 w-full">
            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base contact" type="text"
                   placeholder="Введите контакт"
                   value="<?=$saveMode?$leasee['PROPERTY_297_VALUE']:$contact['FULL_NAME']?>"
            />
         </div>
      </div>
      <div>
         <div class="flex text-gray-1000 text-base">
            Телефон:
         </div>
         <div class="my-2 w-full">
            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base phone" type="text"
                   placeholder="Введите телефон"
                   value="<?=$saveMode?$leasee['PROPERTY_298_VALUE']:$phone['VALUE']?>"
            />
         </div>
      </div>
   </div>
   <div class="my-2">
      <div class="flex text-gray-1000 text-base">
         Лицо, принимающее решение<span class="text-red-500">*</span>:
      </div>
      <div class="my-2 w-full">
         <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base man-great" type="text" placeholder="Введите ФИО" value="<?=$saveMode?$leasee['PROPERTY_227_VALUE']:''?>">
      </div>
   </div>
   <div class="my-2">
      <div class="flex text-gray-1000 text-base">
         Лицо, курирующее сделку:
      </div>
      <div class="my-2 w-full">
         <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base man-owner" type="text" placeholder="Введите ФИО" value="<?=$saveMode?$leasee['PROPERTY_228_VALUE']:''?>">
      </div>
   </div>
   <div>
      <div class="dates">
         <?
         if(count($dates) > 0 && $saveMode){?>
           <?foreach($dates as $dateIndex => $date){?>
               <div class="flex" >
                  <div>
                     <div class="my-2 w-full" style="min-width: 221.5px;">
                        <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base text-blue-1000 meeting-date" type="date" value="<?=$date?>" data-date-index="<?=$dateIndex?>">
                     </div>
                  </div>
                  <div class="ml-2 flex items-end mb-3 ml-6">
                     <button class="h-10 w-10 shadow-my rounded-md flex justify-center items-center" data-date-index="<?=$dateIndex?>" onclick="deleteDateInput(this);">
                        <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                           <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                        </svg>
                     </button>
                  </div>
               </div>
            <?}?>
         <?}else{?>
         <div class="flex">
            <div>
               <div class="flex text-gray-1000 text-base">
                  Дата проведения встречи:
               </div>
               <div class="my-2 w-full">
                  <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base text-blue-1000 meeting-date" type="date" value="" data-date-index="0">
               </div>
            </div>
         </div>
         <?}?>
      </div>
      <div class="my-4">
         <button class="h-9 w-36 font-semibold text-base shadow-my rounded text-blue-1000 add-date">
            Добавить дату
         </button>
      </div>
   </div>
   <div>
      <div class="items">
          <?
          if(count($goals) > 0 && $saveMode){?>
              <?foreach($goals as $goalIndex => $goal){?>
                <div class="item">
                   <div class="flex">
                      <div class="w-full">
                         <div class="flex text-gray-1000 text-base">
                            Предмет лизинга<span class="text-red-500">*</span>:
                         </div>
                         <div class="my-2 w-full">
                            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base leasing-goal" type="text" placeholder="Введите предмет" data-goal-index="<?=$goalIndex?>" value="<?=$goal?>">
                         </div>
                      </div>
                      <div class="ml-2 flex items-end mb-3 ml-6">
                         <button class="h-10 w-10 shadow-my rounded-md flex justify-center items-center" data-goal-index="<?=$goalIndex?>" onclick="deleteGoalInput(this);">
                            <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                               <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                            </svg>
                         </button>
                      </div>
                   </div>
                </div>
              <?}?>
          <?}else{?>
             <div class="item">
                <div class="flex">
                   <div class="w-full">
                      <div class="flex text-gray-1000 text-base">
                         Предмет лизинга<span class="text-red-500">*</span>:
                      </div>
                      <div class="my-2 w-full">
                         <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base leasing-goal" type="text" placeholder="Введите предмет" value="<?=$lead['UF_CRM_1586959397']?>">
                      </div>
                   </div>
                </div>
             </div>
          <?}?>
      </div>
      <div class="my-4">
         <button class="h-9 w-56 font-semibold text-base shadow-my rounded text-blue-1000 add-goal">
            Добавить предмет
         </button>
      </div>
   </div>
   <div class="flex justify-start my-6">
      <div>
         <div class="flex text-gray-1000 text-base total-cost">
            Общая стоимость<span class="text-red-500">*</span>:
         </div>
         <div class="my-2 w-full">
            <input class="border-b-2  outline-none w-full placeholder-blue-900 text-base text-blue-1000 pb-2 total-opportunity" type="text"
                   placeholder="Введите стоимость" value="<?=$saveMode?$leasee['PROPERTY_261_VALUE']:''?>">
         </div>
      </div>
      <div class="ml-4 flex justify-end mt-4">
         <div class="flex justify-center items-center">
            <div>
               <input class="w-6 h-6 rounded nds-included" type="checkbox"
                   <?=$saveMode&&$leasee['PROPERTY_264_VALUE']==='Да'?'checked':''?>
               />
            </div>
            <div class="mx-2">
               Включая НДС
            </div>
         </div>
      </div>
   </div>
   <div class="grid grid-cols-2 gap-4 my-2">
      <div>
         <div class="flex text-gray-1000 text-base">
            Авансовый платеж:
         </div>
         <div class="my-2 w-full">
            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base avance-pay" type="text"
                   value="<?=$saveMode?$leasee['PROPERTY_262_VALUE']:''?>" placeholder="Введите сумму платежа"
            />
         </div>
      </div>
      <div>
         <div class="flex text-gray-1000 text-base">
            Срок лизинга:
         </div>
         <div class="my-2 w-full">
            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base lising-srok"
                   value="<?=$saveMode?$leasee['PROPERTY_263_VALUE']:''?>" type="text" placeholder="Введите срок">
         </div>
      </div>
   </div>
   <hr>
   <div class="mb-8">
      <div class="mb-4 text-xl text-blue-1000 font-semibold">
         Поставщик:
      </div>
      <div>
         <div class="flex text-gray-1000 text-base">
            Добавленные поставщики:
         </div>
         <div class="my-2 w-full company-postavki relative">
            <select class="w-11/12 py-8 rounded-my shadow-my mx-4  px-4 outline-none providers hidden">
               <option disabled selected>Не выбрано</option>
            </select>
         </div>
         <div>
            <div class="flex items-center justify-center shadow-my h-10 w-56 font-semibold rounded mx-2 text-lg text-blue-1000 cursor-pointer add-provider">Добавить поставщика</div>
         </div>
      </div>
      <div class="postavki-form">
         <div class="grid grid-cols-2 gap-4 my-2">
            <div>
               <div class="flex text-gray-1000 text-base">
                  Название компании-поставщика<span class="text-red-500">*</span>:
               </div>
               <div class="my-2 w-full">
                  <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-provider-title" type="text" placeholder="Введите название компании">
               </div>
            </div>
            <div>
               <div class="flex text-gray-1000 text-base">
                  Тип поставщика:
               </div>
               <div class="my-2 w-full">
                  <select class="company-provider-type border-b-2 pt-0.5 text-blue-1000 outline-none w-full pb-2 placeholder-blue-900 text-base">
                     <option selected>Не заполнено</option>
                     <?foreach ($providerTypes as $id => $title){?>
                        <option value="<?=$id?>"><?=$title?></option>
                     <?}?>
                  </select>
               </div>
            </div>
         </div>
         <div class="flex justify-start mb-4">
            <div class="mr-6">
               <div class="text-gray-1000 text-lg mb-2">Поставщик повторный:</div>
               <div>
                  <div>
                     <input type="checkbox" class="hidden postavki-repeat">
                     <div class="flex">
                        <div class="flex items-center justify-center shadow-my h-10 w-10 rounded mx-2 text-lg text-blue-1000 postavki-repeat-no cursor-pointer active">Нет</div>
                        <div class="flex items-center justify-center shadow-my h-10 w-10 rounded mx-2 text-lg postavki-repeat-yes cursor-pointer">Да</div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="mx-4 postavki-date-check hidden">
               <div class="flex text-gray-1000 text-lg mb-2 ">
                  Дата:
               </div>
               <div class="my-4 w-full">
                  <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base postavki-repeat-date" type="date" placeholder="Дата">
               </div>
            </div>
         </div>
         <div class="postavki-check-result hidden">
            <div class="flex text-gray-1000 text-lg mb-2">
               Результат проверки:
            </div>
            <div class="my-4 w-full">
               <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base postavki-repeat-result" type="text" placeholder="Введите результат">
            </div>
         </div>
         <div class="my-2">
            <div class="flex text-gray-1000 text-base">
               Адрес поставщика:
            </div>
            <div class="my-2 w-full">
               <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base provider-company-address" type="text" placeholder="Введите адрес">
            </div>
         </div>
         <div class="flex justify-between mb-4">
            <div class="my-2">
               <div class="flex text-gray-1000 text-base">
                  ИНН<span class="text-red-500">*</span>:
               </div>
               <div class="my-2 w-full">
                  <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-provider-inn" type="text" placeholder="Введите ИНН">
               </div>
            </div>
            <div class="my-2">
               <div class="flex text-gray-1000 text-base">
                  ОГРН:
               </div>
               <div class="my-2 w-full">
                  <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-provider-ogrn" type="text" placeholder="Введите ОГРН">
               </div>
            </div>
            <div class="my-2">
               <div class="flex text-gray-1000 text-base">
                  Дата образования<span class="text-red-500">*</span>:
               </div>
               <div class="my-2 w-full">
                  <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-provider-date-create" type="date" placeholder="Введите дату">
               </div>
            </div>
         </div>
         <div class="flex">
            <button class="text-blue-1000 border rounded-my h-9 w-48 flex justify-center items-center font-semibold active px-1 save-postavki">
               Сохранить поставщика
            </button>
         </div>
         <input type="hidden" class="company-provider" value="">
      </div>
   </div>
   <hr>
   <div class="my-2">
      <div class="my-4 text-gray-1000 text-base">
         Сроки реализации проекта:
      </div>
      <div>
         <textarea name="" id="term" cols="30" rows="10">
             <?=$saveMode?$informationList['PROPERTY_137_VALUE']['TEXT']:''?>
         </textarea>
      </div>
      <div class="flex">
         <label for="term-files" class="cursor-pointer">
            <div class="flex items-center">
               <div class="w-8 h-8 mt-4 shadow-my flex justify-center items-center rounded-md cursor-pointer">
                  <svg width="13" height="15" viewBox="0 0 13 15" class="transform text-blue-1000" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path d="M1.26129 7.574L6.56529 2.27C6.82071 2.01464 7.12393 1.8121 7.45763 1.67393C7.79133 1.53575 8.14897 1.46466 8.51015 1.46471C8.87132 1.46475 9.22895 1.53594 9.56261 1.6742C9.89627 1.81246 10.1994 2.01508 10.4548 2.2705C10.7101 2.52592 10.9127 2.82914 11.0509 3.16284C11.189 3.49653 11.2601 3.85418 11.2601 4.21535C11.26 4.57653 11.1889 4.93415 11.0506 5.26782C10.9123 5.60148 10.7097 5.90464 10.4543 6.16L4.09029 12.524C3.85571 12.7586 3.53754 12.8904 3.20579 12.8904C2.87404 12.8904 2.55588 12.7586 2.32129 12.524C2.08671 12.2894 1.95492 11.9713 1.95492 11.6395C1.95492 11.3077 2.08671 10.9896 2.32129 10.755L7.97829 5.098C8.11077 4.95583 8.1829 4.76778 8.17947 4.57348C8.17604 4.37918 8.09733 4.19379 7.95991 4.05638C7.8225 3.91897 7.63712 3.84025 7.44281 3.83683C7.24851 3.8334 7.06047 3.90552 6.91829 4.038L1.26129 9.695C0.745446 10.2108 0.455647 10.9105 0.455647 11.64C0.455647 12.3695 0.745446 13.0692 1.26129 13.585C1.77714 14.1008 2.47678 14.3906 3.20629 14.3906C3.93581 14.3906 4.63545 14.1008 5.15129 13.585L11.5143 7.22C12.296 6.41987 12.7307 5.34383 12.7242 4.22525C12.7177 3.10667 12.2705 2.03576 11.4795 1.2448C10.6885 0.453829 9.61762 0.00658612 8.49904 7.2159e-05C7.38046 -0.0064418 6.30442 0.428298 5.50429 1.21L0.201292 6.513C0.0688116 6.65518 -0.00331137 6.84322 0.000116847 7.03752C0.00354506 7.23182 0.0822568 7.41721 0.21967 7.55462C0.357083 7.69203 0.542468 7.77075 0.736769 7.77417C0.93107 7.7776 1.11912 7.70548 1.26129 7.573V7.574Z" fill="#28315F"/>
                  </svg>
               </div>
               <div class="mt-5 ml-4">
                  <input id="term-files" type="file"  multiple class="hidden term-files files">
                  <span>Прикрепить файл</span>
               </div>
            </div>
         </label>
         <div class="ml-4 term-file-title flex items-center mt-5">
            <?if($srokFiles){?>
               <?foreach ($srokFiles as $srokFile):?>
                  <div class="flex items-center justify-start mx-4">
                     <div class="file-title">
                        <?=$srokFile['TITLE']?>
                     </div>
                     <div class="file-bin cursor-pointer delete-file" data-input-id="term-files" data-file-name="<?=$srokFile['TITLE']?>" data-file-id="<?=$srokFile['ID']?>" onclick="deleteDBFile(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-1000" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                     </div>
                     <input class="srok-files file-input" type="hidden" value="<?=$srokFile['ID']?>">
                  </div>
                <?endforeach;?>
            <?}?>
         </div>
      </div>
   </div>
   <div class="my-2">
      <div class="my-4 text-gray-1000 text-base">
         Опыт работы с лизингом<span class="text-red-500">*</span>:
      </div>
      <div>
         <textarea name="" id="experience" cols="30" rows="10">
             <?=$saveMode?$informationList['PROPERTY_138_VALUE']['TEXT']:''?>
         </textarea>
      </div>
      <div class="flex">
         <label for="experience-files">
            <div class="flex items-center">
               <div class="w-8 h-8 mt-4 shadow-my flex justify-center items-center rounded-md cursor-pointer">
                  <svg width="13" height="15" viewBox="0 0 13 15" class="transform text-blue-1000" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path d="M1.26129 7.574L6.56529 2.27C6.82071 2.01464 7.12393 1.8121 7.45763 1.67393C7.79133 1.53575 8.14897 1.46466 8.51015 1.46471C8.87132 1.46475 9.22895 1.53594 9.56261 1.6742C9.89627 1.81246 10.1994 2.01508 10.4548 2.2705C10.7101 2.52592 10.9127 2.82914 11.0509 3.16284C11.189 3.49653 11.2601 3.85418 11.2601 4.21535C11.26 4.57653 11.1889 4.93415 11.0506 5.26782C10.9123 5.60148 10.7097 5.90464 10.4543 6.16L4.09029 12.524C3.85571 12.7586 3.53754 12.8904 3.20579 12.8904C2.87404 12.8904 2.55588 12.7586 2.32129 12.524C2.08671 12.2894 1.95492 11.9713 1.95492 11.6395C1.95492 11.3077 2.08671 10.9896 2.32129 10.755L7.97829 5.098C8.11077 4.95583 8.1829 4.76778 8.17947 4.57348C8.17604 4.37918 8.09733 4.19379 7.95991 4.05638C7.8225 3.91897 7.63712 3.84025 7.44281 3.83683C7.24851 3.8334 7.06047 3.90552 6.91829 4.038L1.26129 9.695C0.745446 10.2108 0.455647 10.9105 0.455647 11.64C0.455647 12.3695 0.745446 13.0692 1.26129 13.585C1.77714 14.1008 2.47678 14.3906 3.20629 14.3906C3.93581 14.3906 4.63545 14.1008 5.15129 13.585L11.5143 7.22C12.296 6.41987 12.7307 5.34383 12.7242 4.22525C12.7177 3.10667 12.2705 2.03576 11.4795 1.2448C10.6885 0.453829 9.61762 0.00658612 8.49904 7.2159e-05C7.38046 -0.0064418 6.30442 0.428298 5.50429 1.21L0.201292 6.513C0.0688116 6.65518 -0.00331137 6.84322 0.000116847 7.03752C0.00354506 7.23182 0.0822568 7.41721 0.21967 7.55462C0.357083 7.69203 0.542468 7.77075 0.736769 7.77417C0.93107 7.7776 1.11912 7.70548 1.26129 7.573V7.574Z" fill="#28315F"/>
                  </svg>
               </div>
               <div class="mt-5 ml-4">
                  <input id="experience-files" multiple type="file" class="hidden files">
                  <span>Прикрепить файл</span>
               </div>
            </div>
         </label>
         <div class="ml-4 term-file-title flex items-center mt-5">
             <?if(count($experienceFiles)>0){?>
                 <?foreach ($experienceFiles as $file):?>
                   <div class="flex items-center justify-start mx-4">
                      <div class="file-title">
                          <?=$file['TITLE']?>
                      </div>
                      <div class="file-bin cursor-pointer delete-file" data-input-id="term-files" data-file-name="<?=$file['TITLE']?>" data-file-id="<?=$file['ID']?>" onclick="deleteDBFile(this)">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-1000" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                         </svg>
                      </div>
                      <input class="experience-files file-input" type="hidden" value="<?=$file['ID']?>">
                   </div>
                 <?endforeach;?>
             <?}?>
         </div>
      </div>
   </div>
   <div class="my-2">
      <div class="my-4 text-gray-1000 text-base">
         Наши конкуренты:
      </div>
      <div>
         <textarea name="" id="companies" cols="30" rows="10">
             <?=$saveMode?$informationList['PROPERTY_139_VALUE']['TEXT']:''?>
         </textarea>
      </div>
      <div class="flex">
         <label for="companies-files">
            <div class="flex items-center">
               <div class="w-8 h-8 mt-4 shadow-my flex justify-center items-center rounded-md cursor-pointer">
                  <svg width="13" height="15" viewBox="0 0 13 15" class="transform text-blue-1000" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path d="M1.26129 7.574L6.56529 2.27C6.82071 2.01464 7.12393 1.8121 7.45763 1.67393C7.79133 1.53575 8.14897 1.46466 8.51015 1.46471C8.87132 1.46475 9.22895 1.53594 9.56261 1.6742C9.89627 1.81246 10.1994 2.01508 10.4548 2.2705C10.7101 2.52592 10.9127 2.82914 11.0509 3.16284C11.189 3.49653 11.2601 3.85418 11.2601 4.21535C11.26 4.57653 11.1889 4.93415 11.0506 5.26782C10.9123 5.60148 10.7097 5.90464 10.4543 6.16L4.09029 12.524C3.85571 12.7586 3.53754 12.8904 3.20579 12.8904C2.87404 12.8904 2.55588 12.7586 2.32129 12.524C2.08671 12.2894 1.95492 11.9713 1.95492 11.6395C1.95492 11.3077 2.08671 10.9896 2.32129 10.755L7.97829 5.098C8.11077 4.95583 8.1829 4.76778 8.17947 4.57348C8.17604 4.37918 8.09733 4.19379 7.95991 4.05638C7.8225 3.91897 7.63712 3.84025 7.44281 3.83683C7.24851 3.8334 7.06047 3.90552 6.91829 4.038L1.26129 9.695C0.745446 10.2108 0.455647 10.9105 0.455647 11.64C0.455647 12.3695 0.745446 13.0692 1.26129 13.585C1.77714 14.1008 2.47678 14.3906 3.20629 14.3906C3.93581 14.3906 4.63545 14.1008 5.15129 13.585L11.5143 7.22C12.296 6.41987 12.7307 5.34383 12.7242 4.22525C12.7177 3.10667 12.2705 2.03576 11.4795 1.2448C10.6885 0.453829 9.61762 0.00658612 8.49904 7.2159e-05C7.38046 -0.0064418 6.30442 0.428298 5.50429 1.21L0.201292 6.513C0.0688116 6.65518 -0.00331137 6.84322 0.000116847 7.03752C0.00354506 7.23182 0.0822568 7.41721 0.21967 7.55462C0.357083 7.69203 0.542468 7.77075 0.736769 7.77417C0.93107 7.7776 1.11912 7.70548 1.26129 7.573V7.574Z" fill="#28315F"/>
                  </svg>
               </div>
               <div class="mt-5 ml-4">
                  <input id="companies-files" type="file" multiple class="hidden files">
                  <span>Прикрепить файл</span>
               </div>
            </div>
         </label>
         <div class="ml-4 term-file-title flex items-center mt-5">
             <?if(count($konkurFiles)>0){?>
                 <?foreach ($konkurFiles as $file):?>
                   <div class="flex items-center justify-start mx-4">
                      <div class="file-title">
                          <?=$file['TITLE']?>
                      </div>
                      <div class="file-bin cursor-pointer delete-file" data-input-id="term-files" data-file-name="<?=$file['TITLE']?>" data-file-id="<?=$file['ID']?>" onclick="deleteDBFile(this)">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-1000" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                         </svg>
                      </div>
                      <input class="konkur-files file-input" type="hidden" value="<?=$file['ID']?>">
                   </div>
                 <?endforeach;?>
             <?}?>
         </div>
      </div>
   </div>
   <div class="my-2">
      <div class="my-4 text-gray-1000 text-base">
         Основа для принятия решения:
      </div>
      <div>
         <textarea name="basic" id="basic" cols="30" rows="10">
             <?=$saveMode?$informationList['PROPERTY_140_VALUE']['TEXT']:''?>
         </textarea>
      </div>
      <div class="flex">
         <label for="basic-files">
            <div class="flex items-center">
               <div class="w-8 h-8 mt-4 shadow-my flex justify-center items-center rounded-md cursor-pointer">
                  <svg width="13" height="15" viewBox="0 0 13 15" class="transform text-blue-1000" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path d="M1.26129 7.574L6.56529 2.27C6.82071 2.01464 7.12393 1.8121 7.45763 1.67393C7.79133 1.53575 8.14897 1.46466 8.51015 1.46471C8.87132 1.46475 9.22895 1.53594 9.56261 1.6742C9.89627 1.81246 10.1994 2.01508 10.4548 2.2705C10.7101 2.52592 10.9127 2.82914 11.0509 3.16284C11.189 3.49653 11.2601 3.85418 11.2601 4.21535C11.26 4.57653 11.1889 4.93415 11.0506 5.26782C10.9123 5.60148 10.7097 5.90464 10.4543 6.16L4.09029 12.524C3.85571 12.7586 3.53754 12.8904 3.20579 12.8904C2.87404 12.8904 2.55588 12.7586 2.32129 12.524C2.08671 12.2894 1.95492 11.9713 1.95492 11.6395C1.95492 11.3077 2.08671 10.9896 2.32129 10.755L7.97829 5.098C8.11077 4.95583 8.1829 4.76778 8.17947 4.57348C8.17604 4.37918 8.09733 4.19379 7.95991 4.05638C7.8225 3.91897 7.63712 3.84025 7.44281 3.83683C7.24851 3.8334 7.06047 3.90552 6.91829 4.038L1.26129 9.695C0.745446 10.2108 0.455647 10.9105 0.455647 11.64C0.455647 12.3695 0.745446 13.0692 1.26129 13.585C1.77714 14.1008 2.47678 14.3906 3.20629 14.3906C3.93581 14.3906 4.63545 14.1008 5.15129 13.585L11.5143 7.22C12.296 6.41987 12.7307 5.34383 12.7242 4.22525C12.7177 3.10667 12.2705 2.03576 11.4795 1.2448C10.6885 0.453829 9.61762 0.00658612 8.49904 7.2159e-05C7.38046 -0.0064418 6.30442 0.428298 5.50429 1.21L0.201292 6.513C0.0688116 6.65518 -0.00331137 6.84322 0.000116847 7.03752C0.00354506 7.23182 0.0822568 7.41721 0.21967 7.55462C0.357083 7.69203 0.542468 7.77075 0.736769 7.77417C0.93107 7.7776 1.11912 7.70548 1.26129 7.573V7.574Z" fill="#28315F"/>
                  </svg>
               </div>
               <div class="mt-5 ml-4">
                  <input id="basic-files" type="file" multiple class="hidden files">
                  <span>Прикрепить файл</span>
               </div>

            </div>
         </label>
         <div class="ml-4 term-file-title flex items-center mt-5">
             <?if(count($basisFiles)>0){?>
                 <?foreach ($basisFiles as $file):?>
                   <div class="flex items-center justify-start mx-4">
                      <div class="file-title">
                          <?=$file['TITLE']?>
                      </div>
                      <div class="file-bin cursor-pointer delete-file" data-input-id="term-files" data-file-name="<?=$file['TITLE']?>" data-file-id="<?=$file['ID']?>" onclick="deleteDBFile(this)">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-1000" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                         </svg>
                      </div>
                      <input class="basis-files file-input" type="hidden" value="<?=$file['ID']?>">
                   </div>
                 <?endforeach;?>
             <?}?>
         </div>
      </div>
   </div>
   <div class="my-2">
      <div class="my-4 text-gray-1000 text-base">
         Примечания:
      </div>
      <div>
         <textarea name="" id="comments" cols="30" rows="10">
             <?=$saveMode?$informationList['PROPERTY_141_VALUE']['TEXT']:''?>
         </textarea>
      </div>
      <div class="flex">
         <label for="comments-files">
            <div class="flex items-center">
               <div class="w-8 h-8 mt-4 shadow-my flex justify-center items-center rounded-md cursor-pointer">
                  <svg width="13" height="15" viewBox="0 0 13 15" class="transform text-blue-1000" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path d="M1.26129 7.574L6.56529 2.27C6.82071 2.01464 7.12393 1.8121 7.45763 1.67393C7.79133 1.53575 8.14897 1.46466 8.51015 1.46471C8.87132 1.46475 9.22895 1.53594 9.56261 1.6742C9.89627 1.81246 10.1994 2.01508 10.4548 2.2705C10.7101 2.52592 10.9127 2.82914 11.0509 3.16284C11.189 3.49653 11.2601 3.85418 11.2601 4.21535C11.26 4.57653 11.1889 4.93415 11.0506 5.26782C10.9123 5.60148 10.7097 5.90464 10.4543 6.16L4.09029 12.524C3.85571 12.7586 3.53754 12.8904 3.20579 12.8904C2.87404 12.8904 2.55588 12.7586 2.32129 12.524C2.08671 12.2894 1.95492 11.9713 1.95492 11.6395C1.95492 11.3077 2.08671 10.9896 2.32129 10.755L7.97829 5.098C8.11077 4.95583 8.1829 4.76778 8.17947 4.57348C8.17604 4.37918 8.09733 4.19379 7.95991 4.05638C7.8225 3.91897 7.63712 3.84025 7.44281 3.83683C7.24851 3.8334 7.06047 3.90552 6.91829 4.038L1.26129 9.695C0.745446 10.2108 0.455647 10.9105 0.455647 11.64C0.455647 12.3695 0.745446 13.0692 1.26129 13.585C1.77714 14.1008 2.47678 14.3906 3.20629 14.3906C3.93581 14.3906 4.63545 14.1008 5.15129 13.585L11.5143 7.22C12.296 6.41987 12.7307 5.34383 12.7242 4.22525C12.7177 3.10667 12.2705 2.03576 11.4795 1.2448C10.6885 0.453829 9.61762 0.00658612 8.49904 7.2159e-05C7.38046 -0.0064418 6.30442 0.428298 5.50429 1.21L0.201292 6.513C0.0688116 6.65518 -0.00331137 6.84322 0.000116847 7.03752C0.00354506 7.23182 0.0822568 7.41721 0.21967 7.55462C0.357083 7.69203 0.542468 7.77075 0.736769 7.77417C0.93107 7.7776 1.11912 7.70548 1.26129 7.573V7.574Z" fill="#28315F"/>
                  </svg>
               </div>
               <div class="mt-5 ml-4">
                  <input id="comments-files" type="file" multiple class="hidden files">
                  <span>Прикрепить файл</span>
               </div>

            </div>
         </label>
         <div class="ml-4 term-file-title flex items-center mt-5">
             <?if(count($primechFiles)>0){?>
                 <?foreach ($primechFiles as $file):?>
                   <div class="flex items-center justify-start mx-4">
                      <div class="file-title">
                          <?=$file['TITLE']?>
                      </div>
                      <div class="file-bin cursor-pointer delete-file" data-input-id="term-files" data-file-name="<?=$file['TITLE']?>" data-file-id="<?=$file['ID']?>" onclick="deleteDBFile(this)">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-1000" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                         </svg>
                      </div>
                      <input class="primech-files file-input" type="hidden" value="<?=$file['ID']?>">
                   </div>
                 <?endforeach;?>
             <?}?>
         </div>
      </div>
   </div>
   <hr class="my-12">
   <div class="grid grid-cols-2 gap-4 my-4">
      <div>
         <div class="flex text-gray-1000 text-base">
            Дата:
         </div>
         <div class="my-2 w-full">
            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base il-date"
                   value="<?=$saveMode?$informationList['PROPERTY_142_VALUE']:''?>"
                   type="date"
                   placeholder="Введите дату"
            />
         </div>
      </div>
      <div>
         <div class="flex text-gray-1000 text-base">
            Исполнитель:
         </div>
         <div class="my-2 w-full">
            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base il-assigned"
                   type="text"
                   value="<?=$saveMode?$informationList['PROPERTY_143_VALUE']:''?>"
                   placeholder="Введите ФИО">
         </div>
      </div>
   </div>
   <div class="flex items-center justify-end mb-4">
      <div class="mx-4">
         <button class="text-blue-1000 rounded-2xl h-9 w-32 flex justify-center items-center font-semibold px-2 shadow-my save-result">Сохранить</button>
      </div>
      <div class="mx-4">
         <button class="text-blue-1000 border rounded-2xl h-9 w-32 flex justify-center items-center font-semibold active px-1 send-result">
            Отправить
         </button>
      </div>
   </div>
   <div class="hidden">
      <input type="hidden" class="requestId" value="<?=$_POST['requestId']?>">
      <input type="hidden" class="providersId" value="">
   </div>
</div>
<script>
   var editor = new Editor();
   editor.createField('#basic');
   editor.createField('#term');
   editor.createField('#comments');
   editor.createField('#companies');
   editor.createField('#experience');
   // ClassicEditor
   //    .create( document.querySelector( '#basic' ),{language:'ru'})
   //    .catch( error => {
   //       console.error( error );
   //    } );
   //
   // ClassicEditor.create( document.querySelector( '#term' ) ,{language:'ru'}).catch( error => {console.error( error );} );
   // ClassicEditor.create( document.querySelector( '#comments' ) ,{language:'ru'}).catch( error => {console.error( error );} );
   // ClassicEditor.create( document.querySelector( '#companies' ) ,{language:'ru'}).catch( error => {console.error( error );} );
   // ClassicEditor.create( document.querySelector( '#experience' ) ,{language:'ru'}).catch( error => {console.error( error );} );
   var clientRepeat = document.querySelector('.client-repeat');
   var clientRepeatYes = document.querySelector('.client-repeat-yes');
   var clientRepeatNo = document.querySelector('.client-repeat-no');
   clientRepeatYes.addEventListener('click',function (){
      clientRepeatNo.classList.remove('active');
      clientRepeatYes.classList.add('active');
      clientRepeat.checked = true;
      var dateCheck = document.querySelector('.date-check');
      dateCheck.classList.remove('hidden');
      var checkResult = document.querySelector('.check-result');
      checkResult.classList.remove('hidden');
   });
   clientRepeatNo.addEventListener('click',function (){
      clientRepeatYes.classList.remove('active');
      clientRepeatNo.classList.add('active');
      clientRepeat.checked = false;
      var dateCheck = document.querySelector('.date-check');
      dateCheck.classList.add('hidden');
      var checkResult = document.querySelector('.check-result');
      checkResult.classList.add('hidden');
   });
   var sendResult = document.querySelector('.send-result');
   sendResult.addEventListener('click',()=>{
      var link = document.querySelector('.link')
      var errorFlag = false;
      if(link.value === ''){
         link.classList.add('field-error');
         errorFlag = true;
      }
      var manGreat = document.querySelector('.man-great');
      if(manGreat.value === ''){
         manGreat.classList.add('field-error');
         errorFlag = true;
      }
      var leasingGoal = document.querySelector('.leasing-goal');
      if(leasingGoal.value === ''){
         leasingGoal.classList.add('field-error');
         errorFlag = true;
      }
      var totalOpportunity = document.querySelector('.total-opportunity');
      if(totalOpportunity.value === ''){
         totalOpportunity.classList.add('field-error');
         errorFlag = true;
      }
      var dateObrazovania = document.querySelector('.date-obrazovaniya');
      if(dateObrazovania.value === ''){
         dateObrazovania.classList.add('field-error');
         errorFlag = true;
      }
      var experience = document.querySelector('#experience');

      if(experience.nextSibling.querySelector('div.ck-content').children[0].children.length === 1){
         experience.nextSibling.querySelector('div.ck-content').style = 'border:1px solid red!important;'
         errorFlag = true;
      }
      if(errorFlag){
         alert('Заполните обязательные поля');
         return false;
      }
      var meetingDates = document.querySelectorAll('.meeting-date');
      var meetingDatesData = [];
      for(let i = 0; i < meetingDates.length; i++){
         meetingDatesData.push(meetingDates[i].value);
      }

      var leasingGoals = document.querySelectorAll('.leasing-goal');
      var leasingGoalsData = [];
      for(let i = 0; i < leasingGoals.length; i++){
         leasingGoalsData.push(leasingGoals[i].value);
      }

      var srokFiles = document.querySelectorAll('.srok-files');
      var srokFilesID = [];
      for(let i = 0; i < srokFiles.length; i++){
         srokFilesID.push(srokFiles[i].value);
      }
      var experienceFiles = document.querySelectorAll('.experience-files');
      var experienceFilesID = [];
      for(let i = 0; i < experienceFiles.length; i++){
         experienceFilesID.push(experienceFiles[i].value);
      }
      var konkurFiles = document.querySelectorAll('.konkur-files');
      var konkurFilesID = [];
      for(let i = 0; i < konkurFiles.length; i++){
         konkurFilesID.push(konkurFiles[i].value);
      }
      var basisFiles = document.querySelectorAll('.basis-files');
      var basisFilesID = [];
      for(let i = 0; i < basisFiles.length; i++){
         basisFilesID.push(basisFiles[i].value);
      }
      var primechFiles = document.querySelectorAll('.primech-files');
      var primechFilesID = [];
      for(let i = 0; i < primechFiles.length; i++){
         primechFilesID.push(primechFiles[i].value);
      }

      let files = document.querySelectorAll('.files');
      var dataAjax = new BX.ajax.FormData();
      if(files) {
         [...files].forEach(function (file,index){
            var filesArray = [];
            for(let i = 0; i < file.files.length;i++){
               dataAjax.append(file.id+i, file.files[i]);
            }

         })
      }
      dataAjax.append("link",link.value);
      dataAjax.append("companyTitle",document.querySelector('.company-title').value);
      dataAjax.append("clientRepeat",document.querySelector('.client-repeat').value);
      dataAjax.append("clientRepeatDate",document.querySelector('.client-repeat-date').value);
      dataAjax.append("clientRepeatResult",document.querySelector('.client-repeat-result').value);
      dataAjax.append("urAddress",document.querySelector('.ur-address').value);
      dataAjax.append("inn",document.querySelector('.inn').value);
      dataAjax.append("ogrn",document.querySelector('.ogrn').value);
      dataAjax.append("dateObrazovaniya",document.querySelector('.date-obrazovaniya').value);
      dataAjax.append("manGreat",document.querySelector('.man-great').value);
      dataAjax.append("manOwner",document.querySelector('.man-owner').value);
      dataAjax.append("meetingDate",JSON.stringify(meetingDatesData));
      dataAjax.append("leasingGoal",JSON.stringify(leasingGoalsData));
      dataAjax.append("totalOpportunity",document.querySelector('.total-opportunity').value);
      dataAjax.append("avancePay",document.querySelector('.avance-pay').value);
      dataAjax.append("lisingSrok",document.querySelector('.lising-srok').value);
      dataAjax.append("term",document.querySelector('#term').nextSibling.querySelector('div.ck-content').innerHTML);
      dataAjax.append("experience",document.querySelector('#experience').nextSibling.querySelector('div.ck-content').innerHTML);
      dataAjax.append("companies",document.querySelector('#companies').nextSibling.querySelector('div.ck-content').innerHTML);
      dataAjax.append("basic",document.querySelector('#basic').nextSibling.querySelector('div.ck-content').innerHTML);
      dataAjax.append("comments",document.querySelector('#comments').nextSibling.querySelector('div.ck-content').innerHTML);
      dataAjax.append("ilDate",document.querySelector('.il-date').value);
      dataAjax.append("ilAssigned",document.querySelector('.il-assigned').value);
      dataAjax.append("requestId",document.querySelector('.requestId').value);
      dataAjax.append("providers",document.querySelector('.providersId').value);
      dataAjax.append("ndsIncluded",document.querySelector('.nds-included').checked);
      dataAjax.append("srokFiles",JSON.stringify(srokFilesID));
      dataAjax.append("experienceFiles",JSON.stringify(experienceFilesID));
      dataAjax.append("konkurFiles",JSON.stringify(konkurFilesID));
      dataAjax.append("basisFiles",JSON.stringify(basisFilesID));
      dataAjax.append("primech",JSON.stringify(primechFilesID));

      dataAjax.append("action",'create');
      dataAjax.send('/local/tools/expertCommitee/backend/createManagerList.php',function (res){
         //
         // toggleModal();
         // window.location.reload();
      },null,function (error){
         console.log(`error: ${error}`)
      });
   });

   var saveResult = document.querySelector('.save-result');
   saveResult.addEventListener('click',function(){
      var link = document.querySelector('.link')
      var errorFlag = false;
      if(link.value === ''){
         link.classList.add('field-error');
         errorFlag = true;
      }
      var manGreat = document.querySelector('.man-great');
      if(manGreat.value === ''){
         manGreat.classList.add('field-error');
         errorFlag = true;
      }
      var leasingGoal = document.querySelector('.leasing-goal');
      if(leasingGoal.value === ''){
         leasingGoal.classList.add('field-error');
         errorFlag = true;
      }
      var totalOpportunity = document.querySelector('.total-opportunity');
      if(totalOpportunity.value === ''){
         totalOpportunity.classList.add('field-error');
         errorFlag = true;
      }
      var dateObrazovania = document.querySelector('.date-obrazovaniya');
      if(dateObrazovania.value === ''){
         dateObrazovania.classList.add('field-error');
         errorFlag = true;
      }
      var experience = document.querySelector('#experience');

      if(experience.nextSibling.querySelector('div.ck-content').children[0].children.length === 1){
         experience.nextSibling.querySelector('div.ck-content').style = 'border:1px solid red!important;'
         errorFlag = true;
      }



      var srokFiles = document.querySelectorAll('.srok-files');
      var srokFilesID = [];
      for(let i = 0; i < srokFiles.length; i++){
         srokFilesID.push(srokFiles[i].value);
      }
      var experienceFiles = document.querySelectorAll('.experience-files');
      var experienceFilesID = [];
      for(let i = 0; i < experienceFiles.length; i++){
         experienceFilesID.push(experienceFiles[i].value);
      }
      var konkurFiles = document.querySelectorAll('.konkur-files');
      var konkurFilesID = [];
      for(let i = 0; i < konkurFiles.length; i++){
         konkurFilesID.push(konkurFiles[i].value);
      }
      var basisFiles = document.querySelectorAll('.basis-files');
      var basisFilesID = [];
      for(let i = 0; i < basisFiles.length; i++){
         basisFilesID.push(basisFiles[i].value);
      }
      var primechFiles = document.querySelectorAll('.primech-files');
      var primechFilesID = [];
      for(let i = 0; i < primechFiles.length; i++){
         primechFilesID.push(primechFiles[i].value);
      }


      var meetingDates = document.querySelectorAll('.meeting-date');
      var meetingDatesData = [];
      for(let i = 0; i < meetingDates.length; i++){
         meetingDatesData.push(meetingDates[i].value);
      }
      let files = document.querySelectorAll('.files');
      var dataAjax = new BX.ajax.FormData();
      if(files) {
         [...files].forEach(function (file,index){
            var filesArray = [];
            for(let i = 0; i < file.files.length;i++){
               dataAjax.append(file.id+i, file.files[i]);
            }

         })
      }
      dataAjax.append("link",link.value);
      dataAjax.append("companyTitle",document.querySelector('.company-title').value);
      dataAjax.append("clientRepeat",document.querySelector('.client-repeat').value);
      dataAjax.append("clientRepeatDate",document.querySelector('.client-repeat-date').value);
      dataAjax.append("clientRepeatResult",document.querySelector('.client-repeat-result').value);
      dataAjax.append("urAddress",document.querySelector('.ur-address').value);
      dataAjax.append("inn",document.querySelector('.inn').value);
      dataAjax.append("ogrn",document.querySelector('.ogrn').value);
      dataAjax.append("dateObrazovaniya",document.querySelector('.date-obrazovaniya').value);
      dataAjax.append("manGreat",document.querySelector('.man-great').value);
      dataAjax.append("manOwner",document.querySelector('.man-owner').value);
      dataAjax.append("meetingDate",JSON.stringify(meetingDatesData));
      dataAjax.append("leasingGoal",document.querySelector('.leasing-goal').value);
      dataAjax.append("totalOpportunity",document.querySelector('.total-opportunity').value);
      dataAjax.append("avancePay",document.querySelector('.avance-pay').value);
      dataAjax.append("lisingSrok",document.querySelector('.lising-srok').value);
      dataAjax.append("term",document.querySelector('#term').nextSibling.querySelector('div.ck-content').innerHTML);
      dataAjax.append("experience",document.querySelector('#experience').nextSibling.querySelector('div.ck-content').innerHTML);
      dataAjax.append("companies",document.querySelector('#companies').nextSibling.querySelector('div.ck-content').innerHTML);
      dataAjax.append("basic",document.querySelector('#basic').nextSibling.querySelector('div.ck-content').innerHTML);
      dataAjax.append("comments",document.querySelector('#comments').nextSibling.querySelector('div.ck-content').innerHTML);
      dataAjax.append("ilDate",document.querySelector('.il-date').value);
      dataAjax.append("ilAssigned",document.querySelector('.il-assigned').value);
      dataAjax.append("requestId",document.querySelector('.requestId').value);
      dataAjax.append("providers",document.querySelector('.providersId').value);
      dataAjax.append("ndsIncluded",document.querySelector('.nds-included').checked);
      dataAjax.append("contact",document.querySelector('.contact').value);
      dataAjax.append("phone",document.querySelector('.phone').value);
      dataAjax.append("action",'save');
      dataAjax.append("srokFiles",JSON.stringify(srokFilesID));
      dataAjax.append("experienceFiles",JSON.stringify(experienceFilesID));
      dataAjax.append("konkurFiles",JSON.stringify(konkurFilesID));
      dataAjax.append("basisFiles",JSON.stringify(basisFilesID));
      dataAjax.append("primech",JSON.stringify(primechFilesID));
      dataAjax.send('/local/tools/expertCommitee/backend/createManagerList.php',function (res){
         //toggleModal();
      },null,function (error){
         console.log(`error: ${error}`)
      });
   })






   var addDateButton = document.querySelector('.add-date');
   addDateButton.addEventListener('click',function (){
      var dateFields = document.querySelectorAll('.meeting-date');
      var fieldIndex = dateFields.length-1;
      fieldIndex++;
      var datesContainer = document.querySelector('.dates');
      datesContainer.insertAdjacentHTML('beforeend',`
         <div class="flex" >
         <div>
            <div class="my-2 w-full" style="min-width: 221.5px;">
               <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base text-blue-1000 meeting-date" type="date" value="" data-date-index="${fieldIndex}">
            </div>
         </div>
         <div class="ml-2 flex items-end mb-3 ml-6">
            <button class="h-10 w-10 shadow-my rounded-md flex justify-center items-center" data-date-index="${fieldIndex}" onclick="deleteDateInput(this);">
               <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                  <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
               </svg>
            </button>
         </div>
      </div>`);
   })
   var addGoalButton = document.querySelector('.add-goal');
   addGoalButton.addEventListener('click',function (){
      var goalFields = document.querySelectorAll('.leasing-goal');
      var fieldIndex = goalFields.length-1;
      fieldIndex++;
      var goalsContainer = document.querySelector('.items');
      goalsContainer.insertAdjacentHTML('beforeend',`
                  <div class="item">
            <div class="flex">
               <div class="w-full">
                  <div class="flex text-gray-1000 text-base">
                     Предмет лизинга<span class="text-red-500">*</span>:
                  </div>
                  <div class="my-2 w-full">
                     <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base leasing-goal" type="text" placeholder="Введите предмет" data-goal-index="${fieldIndex}" value="">
                  </div>
               </div>
                        <div class="ml-2 flex items-end mb-3 ml-6">
            <button class="h-10 w-10 shadow-my rounded-md flex justify-center items-center" data-goal-index="${fieldIndex}" onclick="deleteGoalInput(this);">
               <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                  <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
               </svg>
            </button>
         </div>
            </div>
         </div>`);
   })



   var postavkiRepeat = document.querySelector('.postavki-repeat');
   var postavkiRepeatYes = document.querySelector('.postavki-repeat-yes');
   var postavkiRepeatNo = document.querySelector('.postavki-repeat-no');
   postavkiRepeatYes.addEventListener('click',function (){
      postavkiRepeatNo.classList.remove('active');
      postavkiRepeatYes.classList.add('active');
      postavkiRepeat.checked = true
      var dateCheck = document.querySelector('.postavki-date-check');
      dateCheck.classList.remove('hidden');
      var checkResult = document.querySelector('.postavki-check-result');
      checkResult.classList.remove('hidden');
   });
   postavkiRepeatNo.addEventListener('click',function (){
      postavkiRepeatYes.classList.remove('active');
      postavkiRepeatNo.classList.add('active');
      postavkiRepeat.checked = false;
      var dateCheck = document.querySelector('.postavki-date-check');
      dateCheck.classList.add('hidden');
      var checkResult = document.querySelector('.postavki-check-result');
      checkResult.classList.add('hidden');
   });
   function deleteDateInput(target){
      document.querySelector('input[data-date-index="'+target.dataset.dateIndex+'"]').parentElement.parentElement.parentElement.remove();
   }
   function deleteGoalInput(target){
      document.querySelector('input[data-goal-index="'+target.dataset.goalIndex+'"]').parentElement.parentElement.parentElement.remove();
   }
   var savePostavki = document.querySelector('.save-postavki');
   savePostavki.addEventListener('click',function (event){
      var providerCompanyTitle = document.querySelector('.company-provider-title');
      var providerCompanyRepeated = document.querySelector('.postavki-repeat');
      var providerCompanyDate = document.querySelector('.postavki-repeat-date');
      var providerCompanyResult = document.querySelector('.postavki-repeat-result');
      var providerCompanyAddress = document.querySelector('.provider-company-address');
      var providerCompanyInn = document.querySelector('.company-provider-inn');
      var providerCompanyOgrn = document.querySelector('.company-provider-ogrn');
      var providerCompanyDateCreate = document.querySelector('.company-provider-date-create');
      var providerCompanyType = document.querySelector('.company-provider-type');

      if(providerCompanyTitle.value === ''){
         providerCompanyTitle.classList.add('field-error');
         alert('Заполните обязательное поле "Название компании-поставщика"');
         return false;
      }
      if(providerCompanyInn.value === ''){
         providerCompanyInn.classList.add('field-error');
         alert('Заполните обязательное поле "ИНН"');
         return false;
      }
      if(providerCompanyDateCreate.value === ''){
         providerCompanyDateCreate.classList.add('field-error');
         alert('Заполните обязательное поле "Дата образования"');
         return false;
      }

      BX.ajax.post('/local/tools/expertCommitee/backend/createProvider.php',
         {
            providerCompanyTitle:providerCompanyTitle.value,
            providerCompanyRepeated:providerCompanyRepeated.checked,
            providerCompanyDate:providerCompanyDate.value,
            providerCompanyResult:providerCompanyResult.value,
            providerCompanyAddress:providerCompanyAddress.value,
            providerCompanyInn:providerCompanyInn.value,
            providerCompanyOgrn:providerCompanyOgrn.value,
            providerCompanyDateCreate:providerCompanyDateCreate.value,
            providerCompanyType:providerCompanyType.selectedOptions[0].value,
            requestId:document.querySelector('.requestId').value,
         },
         function (result){
         event.target.setAttribute('disabled','disabled');
         alert('Поставщик добавлен успешно!');
         cleanProviderForm();
         var data = JSON.parse(result);
         var option = document.createElement("option");
         option.text = data.TITLE;
         option.value = data.ID;
         option.setAttribute('selected','selected')
         document.querySelector('.providers').add(option);
         document.querySelector('.providersId').value = document.querySelector('.providersId').value+','+data.ID;
         document.querySelector('.providers').classList.remove('hidden');
         }
      );
   });
   var providers = document.querySelector('.providers');
   providers.addEventListener('change',function (e){
      var providerCompanyTitle = document.querySelector('.company-provider-title');
      var providerCompanyRepeated = document.querySelector('.postavki-repeat');
      var providerCompanyDate = document.querySelector('.postavki-repeat-date');
      var providerCompanyResult = document.querySelector('.postavki-repeat-result');
      var providerCompanyAddress = document.querySelector('.provider-company-address');
      var providerCompanyInn = document.querySelector('.company-provider-inn');
      var providerCompanyOgrn = document.querySelector('.company-provider-ogrn');
      var providerCompanyDateCreate = document.querySelector('.company-provider-date-create');
      var providerCompanyType = document.querySelector('.company-provider-type');

      BX.ajax.post('/local/tools/expertCommitee/backend/getProviderData.php',
         {
            providerId:e.target.value
         },
         function(data){
            data = JSON.parse(data);
            providerCompanyTitle.value = data.NAME;
            providerCompanyDate.value = data.CHECKDATE;
            providerCompanyResult.value = data.CHECKRESULT;
            providerCompanyAddress.value = data.ADDRESS;
            providerCompanyInn.value = data.INN;
            providerCompanyOgrn.value = data.OGRN;
            providerCompanyDateCreate.value = data.DATE;
            if(data.TYPE !== null)providerCompanyType.selectedIndex = document.querySelector('option[value="'+data.TYPE+'"]').index;
            var dateCheck = document.querySelector('.postavki-date-check');
            var checkResult = document.querySelector('.postavki-check-result');
            if(data.REPEATEDPROVIDER === 'Нет'){
               postavkiRepeatYes.classList.remove('active');
               postavkiRepeatNo.classList.add('active');
               postavkiRepeat.checked = false;
               dateCheck.classList.add('hidden');
               checkResult.classList.add('hidden');
            }else{
               postavkiRepeatNo.classList.remove('active');
               postavkiRepeatYes.classList.add('active');
               postavkiRepeat.checked = true;
               dateCheck.classList.remove('hidden');
               checkResult.classList.remove('hidden');
            }
         }
      );
   })
   var addProvider = document.querySelector('.add-provider');
   addProvider.addEventListener('click',function(){
      cleanProviderForm();
   })
   var cleanProviderForm = function(){
      var providerCompanyTitle = document.querySelector('.company-provider-title');
      var providerCompanyRepeated = document.querySelector('.postavki-repeat');
      var providerCompanyDate = document.querySelector('.postavki-repeat-date');
      var providerCompanyResult = document.querySelector('.postavki-repeat-result');
      var providerCompanyAddress = document.querySelector('.provider-company-address');
      var providerCompanyInn = document.querySelector('.company-provider-inn');
      var providerCompanyOgrn = document.querySelector('.company-provider-ogrn');
      var providerCompanyDateCreate = document.querySelector('.company-provider-date-create');
      var providerCompanyType = document.querySelector('.company-provider-type');
      providerCompanyTitle.value = '';
      providerCompanyRepeated.checked = false;
      providerCompanyDate.value = '';
      providerCompanyResult.value = '';
      providerCompanyAddress.value = '';
      providerCompanyInn.value = '';
      providerCompanyOgrn.value = '';
      providerCompanyDateCreate.value = '';
      providerCompanyType.selectedIndex = '';
      postavkiRepeatYes.classList.remove('active');
      postavkiRepeatNo.classList.add('active');
      postavkiRepeat.checked = false;
      var dateCheck = document.querySelector('.postavki-date-check');
      dateCheck.classList.add('hidden');
      var checkResult = document.querySelector('.postavki-check-result');
      checkResult.classList.add('hidden');
      savePostavki.removeAttribute('disabled');
   }
   var filesFields = document.querySelectorAll('.files');
   [...filesFields].forEach(function (file){
      file.addEventListener('change',function (e){
         var filesTitles = [];
         [...e.target.files].forEach(el => filesTitles.push(`
               <div class="flex items-center justify-start mx-4">
                  <div class="file-title">
                        ${el.name}
                  </div>
                  <div class="file-bin cursor-pointer delete-file" data-input-id="${e.target.id}" data-file-name="${el.name}" onclick="deleteFile(this)">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-1000" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                     </svg>
                  </div>
               </div>
         `));
         file.parentElement.parentElement.parentElement.nextElementSibling.insertAdjacentHTML('beforeend',filesTitles.join(''));
      })
   });
   function deleteFile(el){
      var fileInput = document.querySelector('#'+el.dataset.inputId);
      var fileName = el.dataset.fileName;
      var fileIndex = -1;
      for(let i = 0; i < fileInput.files.length; i++){
         if(fileInput.files[i].name === fileName)fileIndex = i;
      }
      const dt = new DataTransfer()
      for (let file of fileInput.files)
         if (file !== fileInput.files[fileIndex])
            dt.items.add(file)
      fileInput.files = dt.files
      el.parentElement.remove();
   }
   function deleteDBFile(el) {
      document.querySelector('.file-input[value="'+el.dataset.fileId+'"]');
      el.parentElement.remove();
   }
</script>
