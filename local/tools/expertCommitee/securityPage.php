<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
$requestId = $_POST['requestId'];
if(CModule::IncludeModule('iblock')) {
    $iblockProvider = new \CIblockElement(false);
    $request = $iblockProvider->GetList([],[
        'IBLOCK_ID' => 33,
        'ID' => $requestId,
    ],false,false,['ID','IBLOCK_ID','NAME','PROPERTY_118','PROPERTY_120','PROPERTY_121',])->Fetch();
    $il = $iblockProvider->GetList([],[
        'IBLOCK_ID' => 34,
        'ID' => $request['PROPERTY_118_VALUE']
    ],false,false,['ID','IBLOCK_ID','NAME','DATE_CREATE','PROPERTY_133',
        'PROPERTY_137','PROPERTY_138','PROPERTY_139','PROPERTY_140','PROPERTY_141','PROPERTY_142','PROPERTY_143','PROPERTY_158','PROPERTY_159','PROPERTY_160',
        'PROPERTY_161','PROPERTY_162','PROPERTY_245'])->Fetch();
    $leasee = $iblockProvider->GetList([],[
        'IBLOCK_ID' => 43,
        'ID' => $il['PROPERTY_245_VALUE']
    ],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_222','PROPERTY_223','PROPERTY_265','PROPERTY_229','PROPERTY_230','PROPERTY_224','PROPERTY_225',
        'PROPERTY_226','PROPERTY_227','PROPERTY_228','PROPERTY_261','PROPERTY_262','PROPERTY_263','PROPERTY_264'])->Fetch();
    $res = CIBlockElement::GetProperty(34, $il['ID'], "sort", "asc", array("ID" => "154"));
    while ($ob = $res->GetNext())
    {
        $providersID[] = $ob['VALUE'];
    }
    $providersDB = $iblockProvider->GetList([],[
        'IBLOCK_ID' => 38,
        'ID' => $providersID
    ],false,false,['ID','IBLOCK_ID','NAME']);
    while($provider = $providersDB->Fetch()){
        $providers[] = $provider;
    }
}
?>
<div class="app" x-data="setup()">
   <div class="flex item-center justify-start w-max">
       <?if($request['PROPERTY_118_VALUE']){?>
     <div class="ml-2">
         <button class="shadow-my rounded-t h-9 px-4 border-b" :class="activeTab===0 ? 'active' : ''" @click="activeTab = 0;">
             Информационный лист
         </button>
     </div>
      <?}?>
     <div class="mr-2">
         <button class="rounded-t h-9 px-4 shadow-my border-b" :class="activeTab===1 ? 'active' : ''" @click="activeTab = 1;">
             Служба безопасности
         </button>
     </div>
     <?if($request['PROPERTY_120_VALUE']){?>
    <div class="mr-2">
       <button class="rounded-t h-9 px-4 shadow-my border-b" :class="activeTab===2 ? 'active' : ''" @click="activeTab = 2;">
          Юридический отдел
       </button>
    </div>
     <?}?>
     <?if($request['PROPERTY_121_VALUE']){?>
        <div class="mr-2">
           <button class="rounded-t h-9 px-4 shadow-my border-b" :class="activeTab===3 ? 'active' : ''" @click="activeTab = 3;">
              Финансовый отдел
           </button>
        </div>
     <?}?>
   </div>
   <div x-show="activeTab===0">
       <?
       ob_start();
       include 'disabledMode/informationList.php';
       $out1 = ob_get_contents();
       ob_end_clean();
       echo $out1;
       ?>
   </div>
   <div class="sb-tab" x-show="activeTab===1">
      <div class="flex items-center justify-center text-2xl my-6 text-blue-1000 font-bold">
         Заключение о лизингополучателя от службы безопасности
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
               <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-title" type="text" placeholder="Введите название компании" value="<?=$leasee['NAME']?>">
            </div>
         </div>
      </div>
      <div class="flex justify-start mb-4">
         <div class="mr-6">
            <div class="text-gray-1000 text-lg mb-2">Клиент повторный:</div>
            <div>
               <label>
                  <input type="checkbox" class="hidden client-repeat">
                  <div class="flex">
                     <div class="flex items-center justify-center shadow-my h-10 w-10 rounded mx-2 text-lg text-blue-1000 client-repeat-no cursor-pointer <?=$leasee['PROPERTY_222_ENUM_ID']!=='147'?'active':''?>">Нет</div>
                     <div class="flex items-center justify-center shadow-my h-10 w-10 rounded mx-2 text-lg client-repeat-yes cursor-pointer <?=$leasee['PROPERTY_222_ENUM_ID']==='147'?'active':''?>">Да</div>
                  </div>
               </label>
            </div>
         </div>
         <div class="mx-4 date-check <?=$leasee['PROPERTY_222_ENUM_ID']==='147'?'hidden':''?>">
            <div class="flex text-gray-1000 text-lg mb-2 ">
               Дата:
            </div>
            <div class="my-4 w-full">
               <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base client-repeat-date" type="date" placeholder="Дата">
            </div>
         </div>
         <div class="mx-4 check-result hidden">
            <div class="flex text-gray-1000 text-lg mb-2">
               Результат проверки:
            </div>
            <div class="my-4 w-full">
               <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base client-repeat-result" type="text" placeholder="Введите результат">
            </div>
         </div>
      </div>
      <div class="my-2">
         <div class="flex text-gray-1000 text-base">
            Сотрудники компании
         </div>
         <div class="my-2 w-full">
            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base sotrudniki" type="text" placeholder="Введите сотрудников" value="">
         </div>
      </div>
      <div class="flex justify-between my-2">
         <div>
            <div class="flex text-gray-1000 text-base">
               ИНН:
            </div>
            <div class="my-2 w-full">
               <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base inn" type="text" placeholder="Введите ИНН" value="<?=$leasee['PROPERTY_224_VALUE']?>">
            </div>
         </div>
         <div>
            <div class="flex text-gray-1000 text-base">
               ОГРН:
            </div>
            <div class="my-2 w-full">
               <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base ogrn" type="text" placeholder="Введите ОГРН" value="<?=$leasee['PROPERTY_225_VALUE']?>">
            </div>
         </div>
         <div>
            <div class="flex text-gray-1000 text-base">
               Дата образования<span class="text-red-500">*</span>:
            </div>
            <div class="my-2 w-full">
               <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base date-obrazovaniya" type="date" placeholder="Введите дату" value="<?=$leasee['PROPERTY_226_VALUE']?>">
            </div>
         </div>
      </div>
      <div class="my-2">
         <div class="flex text-gray-1000 text-base">
            Сфера деятельности:
         </div>
         <div class="my-2 w-full">
            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base sphere" type="text" placeholder="Введите юридический адрес" value="">
         </div>
      </div>
      <div class="my-2">
         <div class="flex text-gray-1000 text-base">
            Юридический адрес:
         </div>
         <div class="my-2 w-full">
            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base ur-address" type="text" placeholder="Введите юридический адрес" value="<?=$leasee['PROPERTY_223_VALUE']?>">
         </div>
      </div>
      <div class="my-2">
         <div class="flex text-gray-1000 text-base">
            Фактический адрес:
         </div>
         <div class="my-2 w-full">
            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base fact-address" type="text" placeholder="Введите фактический адрес">
         </div>
      </div>
      <div class="grid grid-cols-2 gap-4 my-2">
         <div>
            <div class="flex text-gray-1000 text-base">
               Генеральный директор:
            </div>
            <div class="my-2 w-full">
               <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base general-director" type="text" placeholder="Введите фио" value="">
            </div>
         </div>
         <div>
            <div class="flex text-gray-1000 text-base">
               ИНН:
            </div>
            <div class="my-2 w-full">
               <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base general-director-inn" type="text" placeholder="Введите инн" value="">
            </div>
         </div>
      </div>
      <div>
         <div class="founders">
            <div class="founder">
               <div class="flex my-2">
                  <div class="w-1/2 mr-2">
                     <div class="flex text-gray-1000 text-base">
                        Учредитель:
                     </div>
                     <div class="my-2 w-full">
                        <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base founder-input" data-founder-index="0" type="text" placeholder="Введите фио" value="">
                     </div>
                  </div>
                  <div class="w-1/2 ml-2">
                     <div class="flex text-gray-1000 text-base">
                        ИНН:
                     </div>
                     <div class="my-2 w-full">
                        <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base founder-input-inn" data-founder-index="0" type="text" placeholder="Введите инн" value="">
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div>
            <div class="flex items-center justify-center shadow-my h-10 w-56 font-semibold rounded mx-2 text-lg text-blue-1000 cursor-pointer add-founder">Добавить учредителя</div>
         </div>
      </div>
      <!-- general info block start -->
      <hr class="my-12">
      <div>
         <div class="my-2">
            <div class="my-4 text-gray-1000 text-base">
               НБКИ ЮЛ:
            </div>
            <div>
               <textarea name="" id="nbkiUL" cols="30" rows="10"></textarea>
            </div>
            <div class="flex">
               <label for="nbkiUL-files">
                  <div class="flex items-center">
                     <div class="w-8 h-8 mt-4 shadow-my flex justify-center items-center rounded-md cursor-pointer">
                        <svg width="13" height="15" viewBox="0 0 13 15" class="transform text-blue-1000" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M1.26129 7.574L6.56529 2.27C6.82071 2.01464 7.12393 1.8121 7.45763 1.67393C7.79133 1.53575 8.14897 1.46466 8.51015 1.46471C8.87132 1.46475 9.22895 1.53594 9.56261 1.6742C9.89627 1.81246 10.1994 2.01508 10.4548 2.2705C10.7101 2.52592 10.9127 2.82914 11.0509 3.16284C11.189 3.49653 11.2601 3.85418 11.2601 4.21535C11.26 4.57653 11.1889 4.93415 11.0506 5.26782C10.9123 5.60148 10.7097 5.90464 10.4543 6.16L4.09029 12.524C3.85571 12.7586 3.53754 12.8904 3.20579 12.8904C2.87404 12.8904 2.55588 12.7586 2.32129 12.524C2.08671 12.2894 1.95492 11.9713 1.95492 11.6395C1.95492 11.3077 2.08671 10.9896 2.32129 10.755L7.97829 5.098C8.11077 4.95583 8.1829 4.76778 8.17947 4.57348C8.17604 4.37918 8.09733 4.19379 7.95991 4.05638C7.8225 3.91897 7.63712 3.84025 7.44281 3.83683C7.24851 3.8334 7.06047 3.90552 6.91829 4.038L1.26129 9.695C0.745446 10.2108 0.455647 10.9105 0.455647 11.64C0.455647 12.3695 0.745446 13.0692 1.26129 13.585C1.77714 14.1008 2.47678 14.3906 3.20629 14.3906C3.93581 14.3906 4.63545 14.1008 5.15129 13.585L11.5143 7.22C12.296 6.41987 12.7307 5.34383 12.7242 4.22525C12.7177 3.10667 12.2705 2.03576 11.4795 1.2448C10.6885 0.453829 9.61762 0.00658612 8.49904 7.2159e-05C7.38046 -0.0064418 6.30442 0.428298 5.50429 1.21L0.201292 6.513C0.0688116 6.65518 -0.00331137 6.84322 0.000116847 7.03752C0.00354506 7.23182 0.0822568 7.41721 0.21967 7.55462C0.357083 7.69203 0.542468 7.77075 0.736769 7.77417C0.93107 7.7776 1.11912 7.70548 1.26129 7.573V7.574Z" fill="#28315F"/>
                        </svg>
                     </div>
                     <div class="mt-5 ml-4">
                        <input id="nbkiUL-files" type="file" multiple class="hidden term-files files">
                        <span>Прикрепить файл</span>
                     </div>
                  </div>
               </label>
               <div class="ml-4 nbkiUL-file-title flex items-center mt-5">

               </div>
            </div>
         </div>
         <div class="my-2">
            <div class="flex justify-start mb-4">
               <div class="my-4 text-gray-1000 text-base">
                  Негативных счетов:
               </div>
               <div class="flex justify-center ml-4">
                  <select class="w-10 rounded-my shadow-my outline-none negative-invoices-counter" name="" id="">
                     <option selected disabled>0</option>
                     <option value="1">1</option>
                     <option value="2">2</option>
                     <option value="3">3</option>
                     <option value="4">4</option>
                     <option value="5">5</option>
                     <option value="6">6</option>
                     <option value="7">7</option>
                     <option value="8">8</option>
                     <option value="9">9</option>
                     <option value="10">10</option>
                  </select>
               </div>
            </div>
            <div class="negative-invoices">

            </div>
         </div>
         <div class="my-2">
            <div class="my-4 text-gray-1000 text-base">
               ФНС:
            </div>
            <div>
               <textarea name="" id="fns" cols="30" rows="10"></textarea>
            </div>
         </div>
         <div class="my-2">
            <div class="my-4 text-gray-1000 text-base">
               ФССП:
            </div>
            <div>
               <textarea name="" id="fssp" cols="30" rows="10"></textarea>
            </div>
         </div>
         <div class="my-2">
            <div class="my-4 text-gray-1000 text-base">
               Арбитраж:
            </div>
            <div>
               <textarea name="" id="arbitr" cols="30" rows="10"></textarea>
            </div>
         </div>
         <div class="my-2">
            <div class="my-4 text-gray-1000 text-base">
               Финансы на конец 2020 года:
            </div>
            <div>
               <textarea name="" id="finance-last-year" cols="30" rows="10"></textarea>
            </div>
         </div>
         <div>
            <div class="leasings-lessee">
               <div class="leasing-lessee">
                  <div class="my-2">
                     <div class="my-4 text-gray-1000 text-base">
                        Лизинг:
                     </div>
                     <div>
                        <textarea name="" id="leasing-lessee-area0" cols="30" rows="10"></textarea>
                     </div>
                  </div>
               </div>
            </div>
            <div>
               <div class="flex items-center justify-center shadow-my h-10 w-56 font-semibold rounded mx-2 text-lg text-blue-1000 cursor-pointer add-leasing">Добавить лизинг</div>
            </div>
         </div>

      </div>
      <!-- general info block end -->
      <hr class="my-12">
      <!-- director block start -->
      <div>
         <div class="mb-8">
            <div class="mb-4 text-xl text-blue-1000 font-semibold">
               Директор:
            </div>
            <div>
               <div class="flex text-gray-1000 text-base">
               </div>
               <div class="my-2 w-full">
                  <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base director-title" type="text" placeholder="Введите ФИО" value="">
               </div>
            </div>
         </div>
         <div class="my-2">
            <div class="my-4 text-gray-1000 text-base">
               НБКИ ФЛ:
            </div>
            <div>
               <textarea name="" id="nkbi-fl" cols="30" rows="10"></textarea>
            </div>
            <div class="flex">
               <label for="nkbi-fl-files">
                  <div class="flex items-center">
                     <div class="w-8 h-8 mt-4 shadow-my flex justify-center items-center rounded-md cursor-pointer">
                        <svg width="13" height="15" viewBox="0 0 13 15" class="transform text-blue-1000" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M1.26129 7.574L6.56529 2.27C6.82071 2.01464 7.12393 1.8121 7.45763 1.67393C7.79133 1.53575 8.14897 1.46466 8.51015 1.46471C8.87132 1.46475 9.22895 1.53594 9.56261 1.6742C9.89627 1.81246 10.1994 2.01508 10.4548 2.2705C10.7101 2.52592 10.9127 2.82914 11.0509 3.16284C11.189 3.49653 11.2601 3.85418 11.2601 4.21535C11.26 4.57653 11.1889 4.93415 11.0506 5.26782C10.9123 5.60148 10.7097 5.90464 10.4543 6.16L4.09029 12.524C3.85571 12.7586 3.53754 12.8904 3.20579 12.8904C2.87404 12.8904 2.55588 12.7586 2.32129 12.524C2.08671 12.2894 1.95492 11.9713 1.95492 11.6395C1.95492 11.3077 2.08671 10.9896 2.32129 10.755L7.97829 5.098C8.11077 4.95583 8.1829 4.76778 8.17947 4.57348C8.17604 4.37918 8.09733 4.19379 7.95991 4.05638C7.8225 3.91897 7.63712 3.84025 7.44281 3.83683C7.24851 3.8334 7.06047 3.90552 6.91829 4.038L1.26129 9.695C0.745446 10.2108 0.455647 10.9105 0.455647 11.64C0.455647 12.3695 0.745446 13.0692 1.26129 13.585C1.77714 14.1008 2.47678 14.3906 3.20629 14.3906C3.93581 14.3906 4.63545 14.1008 5.15129 13.585L11.5143 7.22C12.296 6.41987 12.7307 5.34383 12.7242 4.22525C12.7177 3.10667 12.2705 2.03576 11.4795 1.2448C10.6885 0.453829 9.61762 0.00658612 8.49904 7.2159e-05C7.38046 -0.0064418 6.30442 0.428298 5.50429 1.21L0.201292 6.513C0.0688116 6.65518 -0.00331137 6.84322 0.000116847 7.03752C0.00354506 7.23182 0.0822568 7.41721 0.21967 7.55462C0.357083 7.69203 0.542468 7.77075 0.736769 7.77417C0.93107 7.7776 1.11912 7.70548 1.26129 7.573V7.574Z" fill="#28315F"/>
                        </svg>
                     </div>
                     <div class="mt-5 ml-4">
                        <input id="nkbi-fl-files" type="file" multiple class="hidden term-files files">
                        <span>Прикрепить файл</span>
                     </div>
                  </div>
               </label>
               <div class="ml-4 nkbi-fl-file-title flex items-center mt-5">

               </div>
            </div>
         </div>
         <div class="my-2">
            <div class="flex justify-start mb-4">
               <div class="my-4 text-gray-1000 text-base">
                  Негативных счетов:
               </div>
               <div class="flex justify-center ml-4">
                  <select class="w-10 rounded-my shadow-my outline-none negative-invoices-director-counter" name="" id="">
                     <option selected disabled>0</option>
                     <option value="1">1</option>
                     <option value="2">2</option>
                     <option value="3">3</option>
                     <option value="4">4</option>
                     <option value="5">5</option>
                     <option value="6">6</option>
                     <option value="7">7</option>
                     <option value="8">8</option>
                     <option value="9">9</option>
                     <option value="10">10</option>
                  </select>
               </div>
            </div>
            <div class="negative-director-invoices">

            </div>
         </div>
         <div class="my-2">
            <div class="flex justify-start">
               <div class="my-4 text-gray-1000 text-base">
                  ФССП:
               </div>
            </div>
            <div>
               <textarea name="" id="director-fssp" cols="30" rows="10"></textarea>
            </div>
         </div>
         <div class="my-2">
            <div class="my-4 text-gray-1000 text-base">
               ПАСПОРТ:
            </div>
            <div>
               <textarea name="" id="director-passport" cols="30" rows="10"></textarea>
            </div>
         </div>
         <div class="my-2">
            <div class="my-4 text-gray-1000 text-base">
               Участие в др. обществах:
            </div>
            <div>
               <textarea name="" id="director-member" cols="30" rows="10"></textarea>
            </div>
         </div>
         <div class="my-2">
            <div class="my-4 text-gray-1000 text-base">
               Инф. из региональных источников:
            </div>
            <div>
               <textarea name="" id="director-info" cols="30" rows="10"></textarea>
            </div>
         </div>
      </div>
      <!-- director block end -->
      <hr class="my-12">
      <!-- учредитель block start -->
      <div>
         <div class="mb-8">
            <div class="mb-4 text-xl text-blue-1000 font-semibold">
               Учредитель:
            </div>
            <div>
               <div class="flex text-gray-1000 text-base">
               </div>
               <div class="my-2 w-full">
                  <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base founde-title" type="text" placeholder="Введите ФИО" value="">
               </div>
            </div>
         </div>
         <div class="my-2">
            <div class="my-4 text-gray-1000 text-base">
               НБКИ ФЛ:
            </div>
            <div>
               <textarea name="" id="founde-nkbi-fl" cols="30" rows="10"></textarea>
            </div>
            <div class="flex">
               <label for="founde-nkbi-fl-files">
                  <div class="flex items-center">
                     <div class="w-8 h-8 mt-4 shadow-my flex justify-center items-center rounded-md cursor-pointer">
                        <svg width="13" height="15" viewBox="0 0 13 15" class="transform text-blue-1000" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M1.26129 7.574L6.56529 2.27C6.82071 2.01464 7.12393 1.8121 7.45763 1.67393C7.79133 1.53575 8.14897 1.46466 8.51015 1.46471C8.87132 1.46475 9.22895 1.53594 9.56261 1.6742C9.89627 1.81246 10.1994 2.01508 10.4548 2.2705C10.7101 2.52592 10.9127 2.82914 11.0509 3.16284C11.189 3.49653 11.2601 3.85418 11.2601 4.21535C11.26 4.57653 11.1889 4.93415 11.0506 5.26782C10.9123 5.60148 10.7097 5.90464 10.4543 6.16L4.09029 12.524C3.85571 12.7586 3.53754 12.8904 3.20579 12.8904C2.87404 12.8904 2.55588 12.7586 2.32129 12.524C2.08671 12.2894 1.95492 11.9713 1.95492 11.6395C1.95492 11.3077 2.08671 10.9896 2.32129 10.755L7.97829 5.098C8.11077 4.95583 8.1829 4.76778 8.17947 4.57348C8.17604 4.37918 8.09733 4.19379 7.95991 4.05638C7.8225 3.91897 7.63712 3.84025 7.44281 3.83683C7.24851 3.8334 7.06047 3.90552 6.91829 4.038L1.26129 9.695C0.745446 10.2108 0.455647 10.9105 0.455647 11.64C0.455647 12.3695 0.745446 13.0692 1.26129 13.585C1.77714 14.1008 2.47678 14.3906 3.20629 14.3906C3.93581 14.3906 4.63545 14.1008 5.15129 13.585L11.5143 7.22C12.296 6.41987 12.7307 5.34383 12.7242 4.22525C12.7177 3.10667 12.2705 2.03576 11.4795 1.2448C10.6885 0.453829 9.61762 0.00658612 8.49904 7.2159e-05C7.38046 -0.0064418 6.30442 0.428298 5.50429 1.21L0.201292 6.513C0.0688116 6.65518 -0.00331137 6.84322 0.000116847 7.03752C0.00354506 7.23182 0.0822568 7.41721 0.21967 7.55462C0.357083 7.69203 0.542468 7.77075 0.736769 7.77417C0.93107 7.7776 1.11912 7.70548 1.26129 7.573V7.574Z" fill="#28315F"/>
                        </svg>
                     </div>
                     <div class="mt-5 ml-4">
                        <input id="founde-nkbi-fl-files" type="file" multiple class="hidden term-files files">
                        <span>Прикрепить файл</span>
                     </div>

                  </div>
               </label>
               <div class="ml-4 founde-nkbi-fl-file-title flex items-center mt-5">

               </div>
            </div>
         </div>
         <div class="my-2">
            <div class="flex justify-start mb-4">
               <div class="my-4 text-gray-1000 text-base">
                  Негативных счетов:
               </div>
               <div class="flex justify-center ml-4">
                  <select class="w-10 rounded-my shadow-my outline-none negative-invoices-founder-counter" name="" id="">
                     <option selected disabled>0</option>
                     <option value="1">1</option>
                     <option value="2">2</option>
                     <option value="3">3</option>
                     <option value="4">4</option>
                     <option value="5">5</option>
                     <option value="6">6</option>
                     <option value="7">7</option>
                     <option value="8">8</option>
                     <option value="9">9</option>
                     <option value="10">10</option>
                  </select>
               </div>
            </div>
            <div class="negative-founder-invoices">

            </div>
         </div>
         <div class="my-2">
            <div class="flex justify-start">
               <div class="my-4 text-gray-1000 text-base">
                  ФССП:
               </div>
            </div>
            <div>
               <textarea name="" id="founde-fssp" cols="30" rows="10"></textarea>
            </div>
         </div>
         <div class="my-2">
            <div class="my-4 text-gray-1000 text-base">
               ПАСПОРТ:
            </div>
            <div>
               <textarea name="" id="founde-passport" cols="30" rows="10"></textarea>
            </div>
         </div>
         <div class="my-2">
            <div class="my-4 text-gray-1000 text-base">
               Участие в др. обществах:
            </div>
            <div>
               <textarea name="" id="founde-member" cols="30" rows="10"></textarea>
            </div>
         </div>
         <div class="my-2">
            <div class="my-4 text-gray-1000 text-base">
               Инф. из региональных источников:
            </div>
            <div>
               <textarea name="" id="founde-info" cols="30" rows="10"></textarea>
            </div>
         </div>
      </div>
      <!-- учредитель block end -->
      <!-- поставщик block start -->
      <div>
         <div class="sb-providers">

            <div class="sb-provider">
               <div class="mb-4">
                  <div class="mb-4 text-xl text-blue-1000 font-semibold">
                     Поставщик:
                  </div>
               </div>
               <div class="mb-8">
                  <div class="flex text-gray-1000 text-base">
                     Добавленные поставщики:
                  </div>
                  <div class="my-2 w-full company-postavki relative">
                     <select class="w-11/12 py-8 rounded-my shadow-my mx-4  px-4 outline-none providers-select">
                        <option disabled selected>Не выбрано</option>
                         <?foreach ($providers as $provider):?>
                            <option value="<?=$provider['ID']?>"><?=$provider['NAME']?></option>
                         <?endforeach;?>
                     </select>
                  </div>
               </div>
               <div class="my-2">
                  <div class="my-2 w-full">
                     <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base" type="text" placeholder="Введите ФИО">
                  </div>
               </div>
               <div class="mb-8">
                  <div class="mb-4 text-gray-1000">
                     Сотрудники компании:
                  </div>
               </div>
               <div class="my-2">
                  <div class="my-2 w-full">
                     <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base" type="text" placeholder="Введите сотрудников">
                  </div>
               </div>
               <div class="flex justify-between my-2">
                  <div>
                     <div class="flex text-gray-1000 text-base">
                        ИНН:
                     </div>
                     <div class="my-2 w-full">
                        <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-provider-inn" type="text" placeholder="Введите ИНН">
                     </div>
                  </div>
                  <div>
                     <div class="flex text-gray-1000 text-base">
                        ОГРН:
                     </div>
                     <div class="my-2 w-full">
                        <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-provider-ogrn" type="text" placeholder="Введите ОГРН">
                     </div>
                  </div>
                  <div>
                     <div class="flex text-gray-1000 text-base">
                        Дата образования:
                     </div>
                     <div class="my-2 w-full">
                        <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-provider-date-create" type="date" placeholder="Введите дату">
                     </div>
                  </div>
               </div>
               <div class="my-2">
                  <div class="flex text-gray-1000 text-base">
                     Сфера деятельности:
                  </div>
                  <div class="my-2 w-full">
                     <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base" type="text" placeholder="Введите сферу деятельности">
                  </div>
               </div>
               <div class="my-2">
                  <div class="flex text-gray-1000 text-base">
                     Юридический адрес:
                  </div>
                  <div class="my-2 w-full">
                     <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base ur-address" type="text" placeholder="Введите юридический адрес">
                  </div>
               </div>
               <div class="my-2">
                  <div class="flex text-gray-1000 text-base">
                     Фактический адрес:
                  </div>
                  <div class="my-2 w-full">
                     <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base ur-address" type="text" placeholder="Введите юридический адрес">
                  </div>
               </div>
               <div class="grid grid-cols-2 gap-4 my-2">
                  <div>
                     <div class="flex text-gray-1000 text-base">
                        Генеральный директор:
                     </div>
                     <div class="my-2 w-full">
                        <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base contact" type="text" placeholder="Введите фио" value="">
                     </div>
                  </div>
                  <div>
                     <div class="flex text-gray-1000 text-base">
                        ИНН:
                     </div>
                     <div class="my-2 w-full">
                        <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base phone" type="text" placeholder="Введите инн" value="">
                     </div>
                  </div>
               </div>
               <div class="provider-founders">
                  <div class="provider-founder w-full">
                     <div class="flex justify-start items-center w-full">
                        <div class="mr-2 w-1/2">
                           <div class="flex text-gray-1000 text-base">
                              Учредитель:
                           </div>
                           <div class="my-2 w-full">
                              <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base provider-founder-input" type="text" placeholder="Введите фио" value="">
                           </div>
                        </div>
                        <div class="ml-2 w-1/2">
                           <div class="flex text-gray-1000 text-base">
                              ИНН:
                           </div>
                           <div class="my-2 w-full">
                              <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base provider-founder-input-inn" type="text" placeholder="Введите инн" value="">
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div>
                  <div class="flex items-center justify-center shadow-my h-10 w-56 font-semibold rounded mx-2 text-lg text-blue-1000 cursor-pointer add-provider-founder">Добавить учредителя</div>
               </div>
               <div class="my-2">
                  <div class="my-4 text-gray-1000 text-base">
                     ФНС:
                  </div>
                  <div>
                     <textarea id="fns-provider"></textarea>
                  </div>
               </div>
               <div class="my-2">
                  <div class="my-4 text-gray-1000 text-base">
                     ФССП:
                  </div>
                  <div>
                     <textarea id="fssp-provider"></textarea>
                  </div>
                  <div class="flex">
                     <label for="fssp-provider-files">
                        <div class="flex items-center">
                           <div class="w-8 h-8 mt-4 shadow-my flex justify-center items-center rounded-md cursor-pointer">
                              <svg width="13" height="15" viewBox="0 0 13 15" class="transform text-blue-1000" fill="none" xmlns="http://www.w3.org/2000/svg">
                                 <path d="M1.26129 7.574L6.56529 2.27C6.82071 2.01464 7.12393 1.8121 7.45763 1.67393C7.79133 1.53575 8.14897 1.46466 8.51015 1.46471C8.87132 1.46475 9.22895 1.53594 9.56261 1.6742C9.89627 1.81246 10.1994 2.01508 10.4548 2.2705C10.7101 2.52592 10.9127 2.82914 11.0509 3.16284C11.189 3.49653 11.2601 3.85418 11.2601 4.21535C11.26 4.57653 11.1889 4.93415 11.0506 5.26782C10.9123 5.60148 10.7097 5.90464 10.4543 6.16L4.09029 12.524C3.85571 12.7586 3.53754 12.8904 3.20579 12.8904C2.87404 12.8904 2.55588 12.7586 2.32129 12.524C2.08671 12.2894 1.95492 11.9713 1.95492 11.6395C1.95492 11.3077 2.08671 10.9896 2.32129 10.755L7.97829 5.098C8.11077 4.95583 8.1829 4.76778 8.17947 4.57348C8.17604 4.37918 8.09733 4.19379 7.95991 4.05638C7.8225 3.91897 7.63712 3.84025 7.44281 3.83683C7.24851 3.8334 7.06047 3.90552 6.91829 4.038L1.26129 9.695C0.745446 10.2108 0.455647 10.9105 0.455647 11.64C0.455647 12.3695 0.745446 13.0692 1.26129 13.585C1.77714 14.1008 2.47678 14.3906 3.20629 14.3906C3.93581 14.3906 4.63545 14.1008 5.15129 13.585L11.5143 7.22C12.296 6.41987 12.7307 5.34383 12.7242 4.22525C12.7177 3.10667 12.2705 2.03576 11.4795 1.2448C10.6885 0.453829 9.61762 0.00658612 8.49904 7.2159e-05C7.38046 -0.0064418 6.30442 0.428298 5.50429 1.21L0.201292 6.513C0.0688116 6.65518 -0.00331137 6.84322 0.000116847 7.03752C0.00354506 7.23182 0.0822568 7.41721 0.21967 7.55462C0.357083 7.69203 0.542468 7.77075 0.736769 7.77417C0.93107 7.7776 1.11912 7.70548 1.26129 7.573V7.574Z" fill="#28315F"/>
                              </svg>
                           </div>
                           <div class="mt-5 ml-4">
                              <input id="fssp-provider-files" type="file" multiple class="hidden term-files files">
                              <span>Прикрепить файл</span>
                           </div>

                        </div>
                     </label>
                     <div class="ml-4 fssp-provider-file-title flex items-center mt-5">

                     </div>
                  </div>
               </div>
               <div class="my-2">
                  <div class="my-4 text-gray-1000 text-base">
                     Арбитраж:
                  </div>
                  <div>
                     <textarea id="arbitraz"></textarea>
                  </div>
                  <div class="flex">
                     <label for="arbitraz-provider-files">
                        <div class="flex items-center">
                           <div class="w-8 h-8 mt-4 shadow-my flex justify-center items-center rounded-md cursor-pointer">
                              <svg width="13" height="15" viewBox="0 0 13 15" class="transform text-blue-1000" fill="none" xmlns="http://www.w3.org/2000/svg">
                                 <path d="M1.26129 7.574L6.56529 2.27C6.82071 2.01464 7.12393 1.8121 7.45763 1.67393C7.79133 1.53575 8.14897 1.46466 8.51015 1.46471C8.87132 1.46475 9.22895 1.53594 9.56261 1.6742C9.89627 1.81246 10.1994 2.01508 10.4548 2.2705C10.7101 2.52592 10.9127 2.82914 11.0509 3.16284C11.189 3.49653 11.2601 3.85418 11.2601 4.21535C11.26 4.57653 11.1889 4.93415 11.0506 5.26782C10.9123 5.60148 10.7097 5.90464 10.4543 6.16L4.09029 12.524C3.85571 12.7586 3.53754 12.8904 3.20579 12.8904C2.87404 12.8904 2.55588 12.7586 2.32129 12.524C2.08671 12.2894 1.95492 11.9713 1.95492 11.6395C1.95492 11.3077 2.08671 10.9896 2.32129 10.755L7.97829 5.098C8.11077 4.95583 8.1829 4.76778 8.17947 4.57348C8.17604 4.37918 8.09733 4.19379 7.95991 4.05638C7.8225 3.91897 7.63712 3.84025 7.44281 3.83683C7.24851 3.8334 7.06047 3.90552 6.91829 4.038L1.26129 9.695C0.745446 10.2108 0.455647 10.9105 0.455647 11.64C0.455647 12.3695 0.745446 13.0692 1.26129 13.585C1.77714 14.1008 2.47678 14.3906 3.20629 14.3906C3.93581 14.3906 4.63545 14.1008 5.15129 13.585L11.5143 7.22C12.296 6.41987 12.7307 5.34383 12.7242 4.22525C12.7177 3.10667 12.2705 2.03576 11.4795 1.2448C10.6885 0.453829 9.61762 0.00658612 8.49904 7.2159e-05C7.38046 -0.0064418 6.30442 0.428298 5.50429 1.21L0.201292 6.513C0.0688116 6.65518 -0.00331137 6.84322 0.000116847 7.03752C0.00354506 7.23182 0.0822568 7.41721 0.21967 7.55462C0.357083 7.69203 0.542468 7.77075 0.736769 7.77417C0.93107 7.7776 1.11912 7.70548 1.26129 7.573V7.574Z" fill="#28315F"/>
                              </svg>
                           </div>
                           <div class="mt-5 ml-4">
                              <input id="arbitraz-provider-files" type="file" multiple class="hidden term-files files">
                              <span>Прикрепить файл</span>
                           </div>

                        </div>
                     </label>
                     <div class="ml-4 arbitraz-provider-file-title flex items-center mt-5">

                     </div>
                  </div>
               </div>
               <div class="my-2">
                  <div class="my-4 text-gray-1000 text-base">
                     Финансы на конец 2020 года:
                  </div>
                  <div>
                     <textarea name="" id="finance-provider" cols="30" rows="10"></textarea>
                  </div>
               </div>
               <div class="my-2">
                  <div class="my-4 text-gray-1000 text-base">
                     Налоги и сборы за 2020:
                  </div>
                  <div>
                     <textarea id="nalogi"></textarea>
                  </div>
               </div>
               <div class="mb-8">
                  <div>
                     <div class="flex text-gray-1000 text-base">
                        Лизинг (как характеризующий фактор):
                     </div>
                     <div class="my-2 w-full">
                        <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-title" type="text" placeholder="Введите лизинг" value="">
                     </div>
                  </div>
               </div>
               <hr class="my-12">
               <div class="mb-8">
                  <div class="mb-4 text-xl text-blue-1000 font-semibold">
                     Предмет лизинга:
                  </div>
                  <div class="leasing-sb-items">
                     <div class="leasing-sb-item">
                        <div class="flex justify-start">
                           <div class="w-2/3">
                              <div class="my-2 w-full">
                                 <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-title" type="text" placeholder="Введите предмет" value="">
                              </div>
                           </div>
                           <div class="w-1/3 flex justify-center items-center">
                              <div class="flex justify-center items-center mx-2">
                                 <div>
                                    <input class="w-6 h-6 rounded new" type="checkbox">
                                 </div>
                                 <div class="mx-2">
                                    Новый
                                 </div>
                              </div>
                              <div class="flex justify-center items-center mx-2">
                                 <div>
                                    <input class="w-6 h-6 rounded not-new" type="checkbox">
                                 </div>
                                 <div class="mx-2">
                                    Б/У
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div>
                     <div class="flex items-center justify-center shadow-my h-10 w-56 font-semibold rounded mx-2 text-lg text-blue-1000 cursor-pointer add-leasing-sb-item">Добавить предмет</div>
                  </div>
               </div>
               <div class="my-2">
                  <div class="my-4 text-gray-1000 text-base">
                     Общая информация:
                  </div>
                  <div>
                     <textarea name="" id="term" cols="30" rows="10"></textarea>
                  </div>
               </div>
               <div class="my-2">
                  <div class="my-4 text-gray-1000 text-base">
                     ДТП:
                  </div>
                  <div>
                     <textarea name="" id="term" cols="30" rows="10"></textarea>
                  </div>
               </div>
               <div class="my-2">
                  <div class="my-4 text-gray-1000 text-base">
                     Ограничения:
                  </div>
                  <div>
                     <textarea name="" id="term" cols="30" rows="10"></textarea>
                  </div>
               </div>
               <div class="my-2">
                  <div class="my-4 text-gray-1000 text-base">
                     Нахождение в залоге:
                  </div>
                  <div>
                     <textarea name="" id="term" cols="30" rows="10"></textarea>
                  </div>
               </div>
               <div class="mt-4 mb-8">
                  <div>
                     <div class="flex text-gray-1000 text-base">
                        Аффилированность Поставщика с Лизингополучателем:
                     </div>
                  </div>
                  <div class="flex mt-4">
                     <div class="flex justify-center items-center">
                        <div>
                           <input class="hidden affilirovannost-radio" type="radio" name="affilirovannost" id="affilirovannostYes0">
                           <label for="affilirovannostYes0">
                              <div class="flex justify-center items-center my-checkbox border border-gray-1000">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                 </svg>
                              </div>
                           </label>
                        </div>
                        <div class="mx-2">
                           Установлена
                        </div>
                     </div>
                     <div class="flex justify-center items-center">
                        <div>
                           <input class="hidden affilirovannost-radio" type="radio" name="affilirovannost" id="affilirovannostNo0">
                           <label for="affilirovannostNo0">
                              <div class="flex justify-center items-center bg-color-1000 my-checkbox border border-gray-1000">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                 </svg>
                              </div>
                           </label>
                        </div>
                        <div class="mx-2">
                           Не установлена
                        </div>
                     </div>
                  </div>
               </div>
               <div class="flex">
                  <button class="text-blue-1000 border rounded-my h-9 w-48 flex justify-center items-center font-semibold active px-1 save-provider">
                     Сохранить поставщика
                  </button>
               </div>
            </div>
         </div>
      </div>
      <!-- поставщик block end -->
      <hr class="my-12">
      <!-- заключение -->
      <div>
         <div class="mb-8">
            <div class="mb-4 text-xl text-blue-1000 font-semibold">
               Заключение<span class="text-red-500">*</span>:
            </div>
            <div class="flex justify-start">
               <select class="bg-gray-1000 rounded-my px-4 py-4 w-1/3 sb-final-conclusion">
                  <option disabled selected>Не заполнено</option>
                  <option value="150">Сотрудничество ВОЗМОЖНО</option>
                  <option value="151">ПРОТИВ сотрудничества</option>
                  <option value="152">Сотрудничество РИСКОВАННО</option>
               </select>
            </div>
         </div>
         <div class="my-2">
            <div class="my-4 text-gray-1000 text-base">
               Рекомендации:
            </div>
            <div>
               <textarea name="" id="recommendations" cols="30" rows="10"></textarea>
            </div>
         </div>
      </div>
      <!-- заключение -->
      <div class="grid grid-cols-2 gap-4 my-4">
         <div>
            <div class="flex text-gray-1000 text-base">
               Дата:
            </div>
            <div class="my-2 w-full">
               <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base sb-date" type="date" placeholder="Введите дату">
            </div>
         </div>
         <div>
            <div class="flex text-gray-1000 text-base">
               Исполнитель:
            </div>
            <div class="my-2 w-full">
               <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base sb-assigned" type="text" placeholder="Введите ФИО">
            </div>
         </div>
      </div>
      <div class="flex items-center justify-center mb-4">
         <div class="mx-4">
            <button class="bg-gray-1000 text-blue-1000 rounded-2xl h-9 w-32 flex justify-center items-center font-semibold px-2 shadow-my send-sb-revision">На доработку</button>
         </div>
         <div class="mx-4">
            <button class="bg-gray-1000 text-blue-1000 rounded-2xl h-9 w-32 flex justify-center items-center font-semibold px-2 shadow-my">Сохранить</button>
         </div>
         <div class="mx-4">
            <button class="text-blue-1000 border rounded-2xl h-9 w-32 flex justify-center items-center font-semibold active px-1 send-result-sb">
               Отправить
            </button>
         </div>
      </div>
      <script>
         var sendSbRevision = document.querySelector('.send-sb-revision');
         sendSbRevision.addEventListener('click',function(){
            BX.ajax.post(
               '/local/tools/expertCommitee/revision.php',
               {
                  requestId:<?=$requestId?>,
               },
               function (data){
                  var permissionContainer = document.querySelector('.modal-container');
                  permissionContainer.classList.remove('h-100','w-11/12','overflow-y-auto');
                  permissionContainer.classList.add('w-1/2','h-96');
                  document.querySelector('.modal-body').innerHTML = data;
               }
            );
         })





         var textEditors = document.querySelectorAll('.sb-tab textarea');
         textEditors.forEach(function (el){
            ClassicEditor.create(el,{language:'ru'}).catch( error => console.error(error));
         })
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

         var addFounder = document.querySelector('.add-founder');
         function deleteFounderInput(target){
            document.querySelector('input[data-founder-index="'+target.dataset.founderIndex+'"]').parentElement.parentElement.parentElement.parentElement.remove();
         }
         addFounder.addEventListener('click',function (){
            var founderFields = document.querySelectorAll('.founder');
            var fieldIndex = founderFields.length-1;
            fieldIndex++;
            var founderContainer = document.querySelector('.founders');
            founderContainer.insertAdjacentHTML('beforeend',`
             <div class="founder">
               <div class="flex my-2">
                  <div class="w-1/2 mr-2">
                     <div class="flex text-gray-1000 text-base">
                        Учредитель:
                     </div>
                     <div class="my-2 w-full">
                        <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base founder-input" type="text" placeholder="Введите фио" data-founder-index="${fieldIndex}" value="">
                     </div>
                  </div>
                  <div class="w-1/2 ml-2">
                     <div class="flex text-gray-1000 text-base">
                        ИНН:
                     </div>
                     <div class="my-2 w-full">
                        <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base founder-input-inn" type="text" data-founder-index="${fieldIndex}" placeholder="Введите инн" value="">
                     </div>
                  </div>
                  <div class="flex items-center">
                                       <button class="h-10 w-10 shadow-my rounded-md flex justify-center items-center" data-founder-index="${fieldIndex}" onclick="deleteFounderInput(this);">
                     <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                        <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                     </svg>
                  </button>
                  </div>
               </div>
            </div>`);
         })

         var addProviderFounder = document.querySelector('.add-provider-founder');
         function deleteProviderFounderInput(target){
            document.querySelector('input[data-provider-founder-index="'+target.dataset.providerFounderIndex+'"]').parentElement.parentElement.parentElement.remove();
         }
         addProviderFounder.addEventListener('click',function (){
            var founderFields = document.querySelectorAll('.provider-founder');
            var fieldIndex = founderFields.length-1;
            fieldIndex++;
            var founderContainer = document.querySelector('.provider-founders');
            founderContainer.insertAdjacentHTML('beforeend',`
             <div class="provider-founder">
               <div class="flex my-2">
                  <div class="w-1/2 mr-2">
                     <div class="flex text-gray-1000 text-base">
                        Учредитель:
                     </div>
                     <div class="my-2 w-full">
                        <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base provider-founder-input" type="text" placeholder="Введите фио" data-provider-founder-index="${fieldIndex}" value="">
                     </div>
                  </div>
                  <div class="w-1/2 ml-2">
                     <div class="flex text-gray-1000 text-base">
                        ИНН:
                     </div>
                     <div class="my-2 w-full">
                        <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base provider-founder-input-inn" type="text" data-provider-founder-index="${fieldIndex}" placeholder="Введите инн" value="">
                     </div>
                  </div>
                  <div class="flex items-center">
                                       <button class="h-10 w-10 shadow-my rounded-md flex justify-center items-center" data-provider-founder-index="${fieldIndex}" onclick="deleteProviderFounderInput(this);">
                     <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                        <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                     </svg>
                  </button>
                  </div>
               </div>
            </div>`);
         })



         var addleasingSbItem = document.querySelector('.add-leasing-sb-item');
         function deleteSbItem(target){
            document.querySelector('input[data-leasing-sb-item-index="'+target.dataset.leasingSbItemIndex+'"]').parentElement.parentElement.parentElement.parentElement.remove();
         }
         addleasingSbItem.addEventListener('click',function (){
            var leasingSbItemsFields = document.querySelectorAll('.leasing-sb-item');
            var fieldIndex = leasingSbItemsFields.length-1;
            fieldIndex++;
            var leasingSbItemsContainer = document.querySelector('.leasing-sb-items');

            leasingSbItemsContainer.insertAdjacentHTML('beforeend',`
             <div class="leasing-sb-item">
               <div class="flex justify-start">
                  <div class="w-2/3">
                     <div class="my-2 w-full">
                        <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base leasing-sb-item-input" data-leasing-sb-item-index="${fieldIndex}" type="text" placeholder="Введите предмет" value="">
                     </div>
                  </div>
                  <div class="w-1/3 flex justify-center items-center">
                     <div class="flex justify-center items-center mx-2">
                        <div>
                           <input class="w-6 h-6 rounded new" type="checkbox">
                        </div>
                        <div class="mx-2">
                           Новый
                        </div>
                     </div>
                     <div class="flex justify-center items-center mx-2">
                        <div>
                           <input class="w-6 h-6 rounded not-new" type="checkbox">
                        </div>
                        <div class="mx-2">
                           Б/У
                        </div>
                     </div>
                  </div>
               <div class="flex items-center">
                  <button class="h-10 w-10 shadow-my rounded-md flex justify-center items-center" data-leasing-sb-item-index="${fieldIndex}" onclick="deleteSbItem(this)">
                     <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                        <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                     </svg>
                  </button>
               </div>
               </div>
            </div>`);
         })
         var negativeInvoicesCounter = document.querySelector('.negative-invoices-counter');
         var negativeinvoicesContainer = document.querySelector('.negative-invoices');
         negativeInvoicesCounter.addEventListener('change',function (event){
            for(let i = 0; i < event.target.value; i++){
               negativeinvoicesContainer.insertAdjacentHTML('beforeend',`
                     <div class="negative-invoice my-4">
                        <textarea name="" id="negative-invoice${i}"></textarea>
                     </div>
               `);
               ClassicEditor.create(document.querySelector('#negative-invoice'+i),{language:'ru'}).catch( error => console.error(error));
            }
            event.target.setAttribute('disabled','disabled');
         })
         var negativeFounderInvoicesCounter = document.querySelector('.negative-invoices-founder-counter');
         var negativeFounderinvoicesContainer = document.querySelector('.negative-founder-invoices');
         negativeFounderInvoicesCounter.addEventListener('change',function (event){
            for(let i = 0; i < event.target.value; i++){
               negativeFounderinvoicesContainer.insertAdjacentHTML('beforeend',`
                     <div class="negative-founder-invoice my-4">
                        <textarea name="" id="negative-founder-invoice${i}"></textarea>
                     </div>
               `);
               ClassicEditor.create(document.querySelector('#negative-founder-invoice'+i),{language:'ru'}).catch( error => console.error(error));
            }
            event.target.setAttribute('disabled','disabled');
         })

         var negativeDirectorInvoicesCounter = document.querySelector('.negative-invoices-director-counter');
         var negativeDirectorinvoicesContainer = document.querySelector('.negative-director-invoices');
         negativeDirectorInvoicesCounter.addEventListener('change',function (event){
            for(let i = 0; i < event.target.value; i++){
               negativeDirectorinvoicesContainer.insertAdjacentHTML('beforeend',`
<div class="negative-director-invoice my-4">
   <textarea name="" id="negative-director-invoice${i}"></textarea>
</div>
`);
               ClassicEditor.create(document.querySelector('#negative-director-invoice'+i),{language:'ru'}).catch( error => console.error(error));
            }
            event.target.setAttribute('disabled','disabled');
         })

         function deleteLeasing(el){
            el.parentElement.parentElement.parentElement.remove();
         }
         var leasingContainer = document.querySelector('.leasings-lessee');
         var addLeasingButton = document.querySelector('.add-leasing');
         addLeasingButton.addEventListener('click',function (){
            var leasingCounter = document.querySelectorAll('.leasing-lessee').length;
            leasingContainer.insertAdjacentHTML('beforeend',`
               <div class="leasing-lessee">
                  <div class="my-2">
                     <div class="flex items-center">
                        <div class="my-4 text-gray-1000 text-base ">
                           Лизинг:
                        </div>
                        <div class="flex items-center ml-4">
                              <button class="h-10 w-10 shadow-my rounded-md flex justify-center items-center" data-leasing-index="${leasingCounter}" onclick="this.parentElement.parentElement.parentElement.remove()">
                                 <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                    <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                                 </svg>
                           </button>
                        </div>

                     </div>
                     <div>
                        <textarea name="" id="leasing-lessee-area${leasingCounter}" cols="30" rows="10"></textarea>
                     </div>
                  </div>
               </div>`);
            ClassicEditor.create(document.querySelector('#leasing-lessee-area'+leasingCounter),{language:'ru'}).catch( error => console.error(error));
         })
         var affilirovannostRadios = document.querySelectorAll('.affilirovannost-radio');
         for(let i = 0; i < affilirovannostRadios.length;i++){
            affilirovannostRadios[i].addEventListener('change',function (e){
               var index = e.target.id.replace(/[^0-9]/g,'');
               if(e.target.id === 'affilirovannostYes'+index){
                  e.target.nextElementSibling.children[0].classList.add('bg-color-1000');
                  document.querySelector('#affilirovannostNo'+index).nextElementSibling.children[0].classList.remove('bg-color-1000')
               }else{
                  document.querySelector('#affilirovannostYes'+index).nextElementSibling.children[0].classList.remove('bg-color-1000')
                  e.target.nextElementSibling.children[0].classList.add('bg-color-1000','border','border-gray-1000');
               }
            });
         }
         var sendResultSb = document.querySelector('.send-result-sb');
         sendResultSb.addEventListener('click',function(){

            var foundersLeasee = document.querySelectorAll('.founder-input');
            var foundersLeaseeArray = [];
            for(let i = 0;i < foundersLeasee.length; i++){
               foundersLeaseeArray.push(foundersLeasee[i].value+'@'+document.querySelector('.founder-input-inn[data-founder-index="'+i+'"]').value);
            }
            var negativeInvoices = document.querySelectorAll('.negative-invoice');
            var negativeInvoicesValuesArray = [];
            for(let i = 0; i< negativeInvoices.length; i++){
               negativeInvoicesValuesArray.push(document.querySelector('#negative-invoice'+i).nextSibling.querySelector('div.ck-content').innerHTML);
            }

            var leasings = document.querySelectorAll('.leasing-lessee');
            var leasingsValuesArray = [];
            for(let i = 0; i< leasings.length; i++){
               leasingsValuesArray.push(document.querySelector('#leasing-lessee-area'+i).nextSibling.querySelector('div.ck-content').innerHTML);
            }

            var negativeDirectorInvoices = document.querySelectorAll('.negative-director-invoice');
            var negativeDirectorInvoicesValuesArray = [];
            for(let i = 0; i< negativeDirectorInvoices.length; i++){
               negativeDirectorInvoicesValuesArray.push(document.querySelector('#negative-director-invoice'+i).nextSibling.querySelector('div.ck-content').innerHTML);
            }

            var negativeFounderInvoices = document.querySelectorAll('.negative-founder-invoice');
            var negativeFounderInvoicesValuesArray = [];
            for(let i = 0; i< negativeFounderInvoices.length; i++){
               negativeFounderInvoicesValuesArray.push(document.querySelector('#negative-founder-invoice'+i).nextSibling.querySelector('div.ck-content').innerHTML);
            }






            var dataAjax = new BX.ajax.FormData();
            dataAjax.append("companyTitle",document.querySelector('.company-title').value);
            dataAjax.append("clientRepeat",document.querySelector('.client-repeat').value);
            dataAjax.append("clientRepeatDate",document.querySelector('.client-repeat-date').value);
            dataAjax.append("clientRepeatResult",document.querySelector('.client-repeat-result').value);
            dataAjax.append("sotrudniki",document.querySelector('.sotrudniki').value);
            dataAjax.append("inn",document.querySelector('.inn').value);
            dataAjax.append("ogrn",document.querySelector('.ogrn').value);
            dataAjax.append("dateObrazovaniya",document.querySelector('.date-obrazovaniya').value);
            dataAjax.append("sphere",document.querySelector('.sphere').value);
            dataAjax.append("urAddress",document.querySelector('.ur-address').value);
            dataAjax.append("factAddress",document.querySelector('.fact-address').value);
            dataAjax.append("generalDirector",document.querySelector('.general-director').value+'@'+document.querySelector('.general-director-inn').value);
            dataAjax.append("founders",JSON.stringify(foundersLeaseeArray));
            dataAjax.append("negativeInvoices",JSON.stringify(negativeInvoicesValuesArray));
            dataAjax.append("fns",document.querySelector('#fns').nextSibling.querySelector('div.ck-content').innerHTML);
            dataAjax.append("fssp",document.querySelector('#fssp').nextSibling.querySelector('div.ck-content').innerHTML);
            dataAjax.append("financeLastYear",document.querySelector('#finance-last-year').nextSibling.querySelector('div.ck-content').innerHTML);
            dataAjax.append("arbitr",document.querySelector('#arbitr').nextSibling.querySelector('div.ck-content').innerHTML);
            dataAjax.append("leasings",JSON.stringify(leasingsValuesArray));
            dataAjax.append("nbkiUL",document.querySelector('#nbkiUL').nextSibling.querySelector('div.ck-content').innerHTML);

            dataAjax.append("directorTitle",document.querySelector('.director-title').value);
            dataAjax.append("directornbkiFL",document.querySelector('#nkbi-fl').nextSibling.querySelector('div.ck-content').innerHTML);
            dataAjax.append("directorFssp",document.querySelector('#director-fssp').nextSibling.querySelector('div.ck-content').innerHTML);
            dataAjax.append("directorPassport",document.querySelector('#director-passport').nextSibling.querySelector('div.ck-content').innerHTML);
            dataAjax.append("directorMember",document.querySelector('#director-member').nextSibling.querySelector('div.ck-content').innerHTML);
            dataAjax.append("directorInfo",document.querySelector('#director-info').nextSibling.querySelector('div.ck-content').innerHTML);
            dataAjax.append("negativeDirectorInvoices",JSON.stringify(negativeDirectorInvoicesValuesArray));

            dataAjax.append("founderTitle",document.querySelector('.founde-title').value);
            dataAjax.append("founderNbkiUL",document.querySelector('#founde-nkbi-fl').nextSibling.querySelector('div.ck-content').innerHTML);
            dataAjax.append("founderFssp",document.querySelector('#founde-fssp').nextSibling.querySelector('div.ck-content').innerHTML);
            dataAjax.append("founderPassport",document.querySelector('#founde-passport').nextSibling.querySelector('div.ck-content').innerHTML);
            dataAjax.append("founderMember",document.querySelector('#founde-member').nextSibling.querySelector('div.ck-content').innerHTML);
            dataAjax.append("founderInfo",document.querySelector('#founde-info').nextSibling.querySelector('div.ck-content').innerHTML);
            dataAjax.append("negativeFounderInvoices",JSON.stringify(negativeFounderInvoicesValuesArray));

            dataAjax.append("recommendations",document.querySelector('#recommendations').nextSibling.querySelector('div.ck-content').innerHTML);
            dataAjax.append("sbDate",document.querySelector('.sb-date').value);
            dataAjax.append("sbAssigned",document.querySelector('.sb-assigned').value);
            dataAjax.append("sbFinalConclusion",document.querySelector('.sb-final-conclusion').value);


            let files = document.querySelector('#nkbi-fl-files');
            if(files) {
               for(let i = 0; i < files.files.length;i++){
                  dataAjax.append(files.id+i, files.files[i]);
               }
            }
            files = document.querySelector('#nbkiUL-files');
            if(files) {
               for(let i = 0; i < files.files.length;i++){
                  dataAjax.append(files.id+i, files.files[i]);
               }
            }
            files = document.querySelector('#founde-nkbi-fl-files');
            if(files) {
               for(let i = 0; i < files.files.length;i++){
                  dataAjax.append(files.id+i, files.files[i]);
               }
            }
            dataAjax.append("requestId",<?=$requestId?>);
            dataAjax.send('/local/tools/expertCommitee/backend/sbResult.php',function (res){

               toggleModal();
               window.location.reload();
            },null,function (error){
               console.log(`error: ${error}`)
            });








         });





         var providersSelect = document.querySelector('.providers-select');
         providersSelect.addEventListener('change',function (e){
            var providerCompanyTitle = document.querySelector('.company-provider-title');
            var providerCompanyInn = document.querySelector('.company-provider-inn');

            var providerCompanyOgrn = document.querySelector('.company-provider-ogrn');
            var providerCompanyDateCreate = document.querySelector('.company-provider-date-create');
            var providerOtvetchik = document.querySelector('.provider-otvetchik');
            var providerIstec = document.querySelector('.provider-istec');
            var providerDirector = document.querySelector('.provider-director');
            var providerMember = document.querySelector('.provider-member');
            BX.ajax.post('/local/tools/expertCommitee/backend/getProviderData.php',
               {
                  providerId:e.target.value
               },
               function(data){
                  data = JSON.parse(data);
                  providerCompanyInn.value = data.INN;
                  providerCompanyOgrn.value = data.OGRN;
                  providerCompanyDateCreate.value = data.DATE;
               }
            );
         })

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
   </div>
   <div x-show="activeTab===2">
          <?
          ob_start();
          include 'disabledMode/legal.php';
          $out2 = ob_get_contents();
          ob_end_clean();
          echo $out2;
          ?>
   </div>
   <div x-show="activeTab===3">
       <?
       ob_start();
       include 'disabledMode/finance.php';
       $out3 = ob_get_contents();
       ob_end_clean();
       echo $out3;
       ?>
   </div>
</div>
<script>
   function setup() {
      return {
         activeTab: 1,

      };
   };
</script>