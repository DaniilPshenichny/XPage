<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
$requestID = $_POST['requestId'];
$iblockProvider = new \CIblockElement(false);
$saveMode = $_POST['action']==='save'?true:false;
$request = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 33,
    'ID' => $_POST['requestId']
],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_117','PROPERTY_118','PROPERTY_120'])->Fetch();
$informationList = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 34,
    'ID' => $request['PROPERTY_118_VALUE']
],false,false,['ID','IBLOCK_ID','NAME','DATE_CREATE','PROPERTY_245','PROPERTY_137','PROPERTY_138',
    'PROPERTY_139','PROPERTY_140','PROPERTY_141','PROPERTY_142','PROPERTY_143',])->Fetch();
$leaseeSB = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 43,
    'ID' => $request['PROPERTY_245_VALUE']
],false,false,['ID','IBLOCK_ID','NAME','DATE_CREATE','PROPERTY_232',
    'PROPERTY_233','PROPERTY_234','PROPERTY_235','PROPERTY_236','PROPERTY_238','PROPERTY_239','PROPERTY_240',])->Fetch();




$securityResult = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 36,
    'ID' => $request['PROPERTY_120_VALUE']
],false,false,['ID','IBLOCK_ID','NAME','DATE_CREATE','PROPERTY_176','PROPERTY_203','PROPERTY_204'])->Fetch();
$res = CIBlockElement::GetProperty(34, $informationList['ID'], "sort", "asc", array("ID" => "154"));
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
?>
<div class="app" x-data="setup()">
    <div class="flex item-center justify-start w-max">

        <div class="ml-2">
            <button class="shadow-my rounded-t h-9 px-4 border-b" :class="activeTab===0 ? 'active' : ''" @click="activeTab = 0;">
                Информационный лист
            </button>
        </div>
        <?if($request['PROPERTY_119_VALUE']){?>
        <div class="mr-2">
            <button class="rounded-t h-9 px-4 shadow-my border-b" :class="activeTab===1 ? 'active' : ''" @click="activeTab = 1;">
                Служба безопасности
            </button>
        </div>
       <?}?>
        <div class="mr-2">
            <button class="rounded-t h-9 px-4 shadow-my border-b" :class="activeTab===2 ? 'active' : ''" @click="activeTab = 2;">
                Юридический отдел
            </button>
        </div>
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
    <div x-show="activeTab===1">

    </div>
    <div x-show="activeTab===2">
        <div class="flex items-center justify-center text-2xl my-6 text-blue-1000 font-bold">
            Заключение о лизингополучателя от юридического отдела
        </div>
        <div class="mb-8 mt-4">
            <div class="mb-4 text-xl text-blue-1000 font-semibold">
                Лизингополучатель:
            </div>
            <div>
                <div class="flex text-gray-1000 text-base">
                    Название компании-лизингополучателя<span class="text-red-500">*</span>:
                </div>
                <div class="my-2 w-full">
                    <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base comapny-title" type="text" placeholder="Введите название компании" value="<?=$leaseeSB['NAME']?>">
                </div>
            </div>
        </div>
        <div class="flex justify-between my-2">
            <div>
                <div class="flex text-gray-1000 text-base">
                    Инн:
                </div>
                <div class="my-2 w-full">
                    <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-inn" type="text" placeholder="Введите ИНН" value="<?=$leaseeSB['PROPERTY_224_VALUE']?>">
                </div>
            </div>
            <div>
                <div class="flex text-gray-1000 text-base">
                    ОГРН:
                </div>
                <div class="my-2 w-full">
                    <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-ogrn" type="text" placeholder="Введите ОГРН" value="<?=$leaseeSB['PROPERTY_225_VALUE']?>">
                </div>
            </div>
            <div>
                <div class="flex text-gray-1000 text-base">
                    Дата образования<span class="text-red-500">*</span>:
                </div>
                <div class="my-2 w-full">
                    <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-create-date" type="date" placeholder="Введите дату" value="<?=$leaseeSB['PROPERTY_226_VALUE']?>">
                </div>
            </div>
        </div>
        <div class="my-2">
            <div class="flex text-gray-1000 text-base">
                Ответчик:
            </div>
            <div class="my-2 w-full">
                <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-otvetchik"
                       type="text" placeholder="Введите ФИО" value="<?=$leaseeSB['PROPERTY_232_VALUE']?$leaseeSB['PROPERTY_232_VALUE']:''?>">
            </div>
        </div>
        <div class="my-2">
            <div class="flex text-gray-1000 text-base">
                Истец:
            </div>
            <div class="my-2 w-full">
                <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-istec"
                       type="text"
                       value="<?=$saveMode&&$leaseeSB['PROPERTY_233_VALUE']?$leaseeSB['PROPERTY_233_VALUE']:''?>"
                       placeholder="Введите ФИО">
            </div>
        </div>
        <div class="my-2">
            <div class="flex text-gray-1000 text-base">
                Директор :
            </div>
            <div class="my-2 w-full">
                <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-director"
                       value="<?=$saveMode&&$leaseeSB['PROPERTY_234_VALUE']?$leaseeSB['PROPERTY_234_VALUE']:''?>"
                       type="text"
                       placeholder="Введите ФИО">
            </div>
        </div>
        <div class="my-2">
            <div class="flex text-gray-1000 text-base">
                Участник :
            </div>
            <div class="my-2 w-full">
                <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-member"
                       type="text"
                       value="<?=$saveMode&&$leaseeSB['PROPERTY_235_VALUE']?$leaseeSB['PROPERTY_235_VALUE']:''?>"
                       placeholder="Введите ФИО">
            </div>
        </div>
        <div class="my-2">
            <div class="flex text-gray-1000 text-base">
                Конечный бенефициар:
            </div>
            <div class="my-2 w-full">
                <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-beneficiary"
                       value="<?=$saveMode&&$leaseeSB['PROPERTY_236_VALUE']?$leaseeSB['PROPERTY_236_VALUE']:''?>"
                       type="text" placeholder="Введите ФИО">
            </div>
        </div>
        <div class="my-2">
            <div class="flex text-gray-1000 text-base">
                Поручитель:
            </div>
           <div class="poruchitels">
              <div class="my-2 w-full">
                 <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base poruchitel" type="text" placeholder="Введите ФИО" data-poruchitel-index="0">
              </div>
           </div>
           <div class="my-4">
              <button class="h-9 w-56 font-semibold text-base shadow-my rounded text-blue-1000 add-poruchitel">
                 Добавить поручителя
              </button>
           </div>
        </div>
        <div class="flex my-8">
            <div>
                <div class="flex justify-center items-center">
                    <div>
                        <input class="w-6 h-6 rounded company-resident" type="checkbox" <?=$saveMode&&$leaseeSB['PROPERTY_238_VALUE']==='Да'?'checked':''?>>
                    </div>
                    <div class="mx-2">
                        Резидент
                    </div>
                </div>
            </div>
            <div>
                <div class="flex justify-center items-center">
                    <div>
                        <input class="w-6 h-6 rounded company-diler" type="checkbox" <?=$saveMode&&$leaseeSB['PROPERTY_239_VALUE']==='Да'?'checked':''?>>
                    </div>
                    <div class="mx-2">
                        Дилер
                    </div>
                </div>
            </div>
            <div>
                <div class="flex justify-center items-center">
                    <div>
                        <input class="w-6 h-6 rounded company-manufacturer" type="checkbox" <?=$saveMode&&$leaseeSB['PROPERTY_240_VALUE']==='Да'?'checked':''?>>
                    </div>
                    <div class="mx-2">
                        Производитель
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="mb-8 mt-8">
            <div class="mb-4 text-xl text-blue-1000 font-semibold">
                Поставщик:
            </div>
           <div>
              <div class="flex text-gray-1000 text-base">
                 Добавленные поставщики:
              </div>
              <div class="my-2 w-full company-postavki relative">
                 <select class="w-11/12 py-8 rounded-my shadow-my mx-4  px-4 outline-none providers">
                    <option disabled selected>Не выбрано</option>
                    <?foreach ($providers as $provider):?>
                       <option value="<?=$provider['ID']?>"><?=$provider['NAME']?></option>
                    <?endforeach;?>
                 </select>
              </div>
              <div>
                 <div class="flex items-center justify-center shadow-my h-10 w-56 font-semibold rounded mx-2 text-lg text-blue-1000 cursor-pointer add-provider" onclick="cleanForm();">Добавить поставщика</div>
              </div>
           </div>
           <div class="postavki-form">
              <div>
                 <div class="flex text-gray-1000 text-base">
                    Название компании-поставщика<span class="text-red-500">*</span>:
                 </div>
                 <div class="my-2 w-full">
                    <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-provider-title" type="text" placeholder="Введите название компании">
                 </div>
              </div>
              <div class="flex justify-between">
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
              <div>
                 <div class="flex text-gray-1000 text-base">
                    Ответчик:
                 </div>
                 <div class="my-2 w-full">
                    <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base provider-otvetchik" type="text" placeholder="Введите ФИО" data-provider-id="0">
                 </div>
              </div>
              <div class="my-2">
                 <div class="flex text-gray-1000 text-base">
                    Истец:
                 </div>
                 <div class="my-2 w-full">
                    <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base provider-istec" type="text" placeholder="Введите ФИО" data-provider-id="0">
                 </div>
              </div>
              <div class="my-2">
                 <div class="flex text-gray-1000 text-base">
                    Директор :
                 </div>
                 <div class="my-2 w-full">
                    <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base provider-director" type="text" placeholder="Введите ФИО" data-provider-id="0">
                 </div>
              </div>
              <div class="my-2">
                 <div class="flex text-gray-1000 text-base">
                    Участник :
                 </div>
                 <div class="my-2 w-full">
                    <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base provider-member" type="text" placeholder="Введите ФИО" data-provider-id="0">
                 </div>
              </div>
              <div class="flex mt-4">
                 <button class="text-blue-1000 border rounded-my h-9 w-48 flex justify-center items-center font-semibold active px-1 save-provider">
                    Сохранить поставщика
                 </button>
              </div>
              <input type="hidden" class="company-provider" value="">
           </div>
        </div>
        <hr class="my-8">
        <div class="my-2">
            <div class="my-4 text-xl text-blue-1000 font-semibold">
                Заключение
            </div>
            <div>
                <textarea name="" id="ur-conclusion" cols="30" rows="10">
                   <?=$saveMode&&$securityResult['PROPERTY_176_VALUE']?$securityResult['PROPERTY_176_VALUE']['TEXT']:'';?>
                </textarea>
            </div>
        </div>
        <hr class="my-12">
        <div class="grid grid-cols-2 gap-4 my-4">
            <div>
                <div class="flex text-gray-1000 text-base">
                    Дата:
                </div>
                <div class="my-2 w-full">
                    <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base legal-form-date"
                           value="<?=$saveMode&&$securityResult['PROPERTY_203_VALUE']?$securityResult['PROPERTY_203_VALUE']:'';?>"
                           type="date" placeholder="Введите дату">
                </div>
            </div>
            <div>
                <div class="flex text-gray-1000 text-base">
                    Исполнитель:
                </div>
                <div class="my-2 w-full">
                    <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base legal-form-responsible"
                           value="<?=$saveMode&&$securityResult['PROPERTY_204_VALUE']?$securityResult['PROPERTY_204_VALUE']:'';?>"
                           type="text" placeholder="Введите ФИО">
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end mb-4">
            <div class="mx-4">
                <button class="text-blue-1000 rounded-2xl h-9 w-32 flex justify-center items-center font-semibold px-2 shadow-my legal-on-edit">На доработку</button>
            </div>
            <div class="mx-4">
                <button class="text-blue-1000 rounded-2xl h-9 w-32 flex justify-center items-center font-semibold px-2 shadow-my save-legal-result-form">Сохранить</button>
            </div>
            <div class="mx-4">
                <button class="text-blue-1000 border rounded-2xl h-9 w-32 flex justify-center items-center font-semibold active px-1 send-legal-result-form">
                    Отправить
                </button>
            </div>
        </div>
    </div>
   <div x-show="activeTab===3">
       <?
       ob_start();
       include 'disabledMode/finance.php';
       $out1 = ob_get_contents();
       ob_end_clean();
       echo $out1;
       ?>
   </div>
</div>
<script>
   function setup() {
      return {
         activeTab: 0,
      };
   };
   ClassicEditor.create( document.querySelector( '#ur-conclusion' ) ,{language:'ru'}).catch( error => {
         console.error( error );
      } );
   function deletePoruchitelInput(target){
      document.querySelector('input[data-poruchitel-index="'+target.dataset.poruchitelIndex+'"]').parentElement.parentElement.parentElement.remove();
   }
   var addPoruchitel = document.querySelector('.add-poruchitel');
   addPoruchitel.addEventListener('click',function (){
      var poruchitelFields = document.querySelectorAll('.poruchitel');
      var fieldIndex = poruchitelFields.length-1;
      fieldIndex++;
      var poruchitelContainer = document.querySelector('.poruchitels');
      poruchitelContainer.insertAdjacentHTML('beforeend',`
         <div class="flex w-full" >
         <div class="w-full">
<div class="my-2 w-full">
           <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base poruchitel" type="text" placeholder="Введите ФИО" data-poruchitel-index="${fieldIndex}">
        </div>
         </div>
         <div class="ml-2 flex items-end mb-3 ml-6">
            <button class="h-10 w-10 shadow-my rounded-md flex justify-center items-center" data-poruchitel-index="${fieldIndex}" onclick="deletePoruchitelInput(this);">
               <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                  <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
               </svg>
            </button>
         </div>
      </div>
        `);
   })

   var saveProvider = document.querySelector('.save-provider');
   saveProvider.addEventListener('click',function (event){
      var providerCompanyTitle = document.querySelector('.company-provider-title');
      var providerCompanyInn = document.querySelector('.company-provider-inn');
      var providerCompanyOgrn = document.querySelector('.company-provider-ogrn');
      var providerCompanyDateCreate = document.querySelector('.company-provider-date-create');
      var providerOtvetchik = document.querySelector('.provider-otvetchik');
      var providerIstec = document.querySelector('.provider-istec');
      var providerDirector = document.querySelector('.provider-director');
      var providerMember = document.querySelector('.provider-member');
      var providerId = document.querySelector('.company-provider');
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
            providerCompanyInn:providerCompanyInn.value,
            providerCompanyOgrn:providerCompanyOgrn.value,
            providerCompanyDateCreate:providerCompanyDateCreate.value,
            providerOtvetchik:providerOtvetchik.value,
            providerIstec:providerIstec.value,
            providerDirector:providerDirector.value,
            providerMember:providerMember.value,
            ilId:<?=$il['ID']?>,
            place:'fromLegal',
            providerId:providerId.value
         },
         function (result){
            event.target.setAttribute('disabled','disabled');
            alert('Поставщик добавлен успешно!');
            cleanForm();
            if(result){
               var data = JSON.parse(result);
               var option = document.createElement("option");
               option.text = data.TITLE;
               option.value = data.ID;
               option.setAttribute('selected','selected')
               document.querySelector('.providers').add(option);
               document.querySelector('.providers').classList.remove('hidden');
            }
         }
      );
   });
   var providers = document.querySelector('.providers');
   providers.addEventListener('change',function (e){
      var providerCompanyTitle = document.querySelector('.company-provider-title');
      var providerCompanyInn = document.querySelector('.company-provider-inn');
      var providerCompanyOgrn = document.querySelector('.company-provider-ogrn');
      var providerCompanyDateCreate = document.querySelector('.company-provider-date-create');
      var providerOtvetchik = document.querySelector('.provider-otvetchik');
      var providerIstec = document.querySelector('.provider-istec');
      var providerDirector = document.querySelector('.provider-director');
      var providerMember = document.querySelector('.provider-member');
      var providerId = document.querySelector('.company-provider');
      BX.ajax.post('/local/tools/expertCommitee/backend/getProviderData.php',
         {
            providerId:e.target.value
         },
         function(data){
            data = JSON.parse(data);
            providerCompanyTitle.value = data.NAME;
            providerCompanyInn.value = data.INN;
            providerCompanyOgrn.value = data.OGRN;
            providerCompanyDateCreate.value = data.DATE;
            providerOtvetchik.value = data.OTVETCHIK;
            providerIstec.value = data.ISTEC;
            providerDirector.value = data.DIRECTOR;
            providerMember.value = data.MEMBER;
            providerId.value = data.ID;
         }
      );
   })
   function cleanForm(){
      var providerCompanyTitle = document.querySelector('.company-provider-title');
      var providerCompanyInn = document.querySelector('.company-provider-inn');
      var providerCompanyOgrn = document.querySelector('.company-provider-ogrn');
      var providerCompanyDateCreate = document.querySelector('.company-provider-date-create');
      var providerOtvetchik = document.querySelector('.provider-otvetchik');
      var providerIstec = document.querySelector('.provider-istec');
      var providerDirector = document.querySelector('.provider-director');
      var providerMember = document.querySelector('.provider-member');
      var providerId = document.querySelector('.company-provider');
      providerCompanyTitle.value = '';
      providerCompanyInn.value = '';
      providerCompanyOgrn.value = '';
      providerCompanyDateCreate.value = '';
      providerOtvetchik.value = '';
      providerIstec.value = '';
      providerDirector.value = '';
      providerMember.value = '';
      providerId.value = '';
      document.querySelector('.providers').selectedIndex = '';
   }
   var saveLegalResult  = document.querySelector('.save-legal-result-form');
   saveLegalResult.addEventListener('click',function(){
      var comapnyTitle = document.querySelector('.comapny-title');
      var companyInn = document.querySelector('.company-inn');
      var companyOgrn = document.querySelector('.company-ogrn');
      var companyCreateDate = document.querySelector('.company-create-date');
      var companyOtvetchik = document.querySelector('.company-otvetchik');
      var companyIstec = document.querySelector('.company-istec');
      var companyUchasnik = document.querySelector('.company-member');
      var companyDirector = document.querySelector('.company-director');
      var companyMember = document.querySelector('.company-member');
      var companyBeneficiary = document.querySelector('.company-beneficiary');
      var poruchitel = document.querySelector('.poruchitel');
      var companyResident = document.querySelector('.company-resident');
      var companyDiler = document.querySelector('.company-diler');
      var companyManufacturer = document.querySelector('.company-manufacturer');
      var urConclusion = document.querySelector('#ur-conclusion');
      var legalDate = document.querySelector('.legal-form-date');
      var legalResponsible = document.querySelector('.legal-form-responsible');
      var poruchitels = [];
      for(let i =0;i<poruchitel.length;i++){
         poruchitels.push(poruchitel[i].value);
      }
      BX.ajax.post(
         '/local/tools/expertCommitee/backend/legalResult.php',
         {
            comapnyTitle:comapnyTitle.value,
            companyInn:companyInn.value,
            companyOgrn:companyOgrn.value,
            companyCreateDate:companyCreateDate.value,
            companyOtvetchik:companyOtvetchik.value,
            companyIstec:companyIstec.value,
            companyUchasnik:companyUchasnik.value,
            companyDirector:companyDirector.value,
            companyMember:companyMember.value,
            companyBeneficiary:companyBeneficiary.value,
            companyResident:companyResident.checked,
            companyDiler:companyDiler.checked,
            companyManufacturer:companyManufacturer.checked,
            poruchitels:JSON.stringify(poruchitels),
            ilId:<?=$leaseeSB['ID']?>,
            requestId:<?=$requestID?>,
            urConclusion:urConclusion.nextSibling.querySelector('div.ck-content').innerHTML,
            legalDate:legalDate.value,
            legalResponsible:legalResponsible.value,
            'action':'save',
         },
         function (data){
            toggleModal();
            window.location.reload();
         }
      );
   })

   var sendLegalResult = document.querySelector('.send-legal-result-form');
   sendLegalResult.addEventListener('click',function (){
      //блок лизингполучатель start
      var comapnyTitle = document.querySelector('.comapny-title');
      var companyInn = document.querySelector('.company-inn');
      var companyOgrn = document.querySelector('.company-ogrn');
      var companyCreateDate = document.querySelector('.company-create-date');
      var companyOtvetchik = document.querySelector('.company-otvetchik');
      var companyIstec = document.querySelector('.company-istec');
      var companyUchasnik = document.querySelector('.company-member');
      var companyDirector = document.querySelector('.company-director');
      var companyMember = document.querySelector('.company-member');
      var companyBeneficiary = document.querySelector('.company-beneficiary');
      var poruchitel = document.querySelector('.poruchitel');
      var companyResident = document.querySelector('.company-resident');
      var companyDiler = document.querySelector('.company-diler');
      var companyManufacturer = document.querySelector('.company-manufacturer');
      var urConclusion = document.querySelector('#ur-conclusion');
      var legalDate = document.querySelector('.legal-form-date');
      var legalResponsible = document.querySelector('.legal-form-responsible');
      var poruchitels = [];
      for(let i =0;i<poruchitel.length;i++){
         poruchitels.push(poruchitel[i].value);
      }
      BX.ajax.post(
         '/local/tools/expertCommitee/backend/legalResult.php',
         {
            comapnyTitle:comapnyTitle.value,
            companyInn:companyInn.value,
            companyOgrn:companyOgrn.value,
            companyCreateDate:companyCreateDate.value,
            companyOtvetchik:companyOtvetchik.value,
            companyIstec:companyIstec.value,
            companyUchasnik:companyUchasnik.value,
            companyDirector:companyDirector.value,
            companyMember:companyMember.value,
            companyBeneficiary:companyBeneficiary.value,
            companyResident:companyResident.checked,
            companyDiler:companyDiler.checked,
            companyManufacturer:companyManufacturer.checked,
            poruchitels:JSON.stringify(poruchitels),
            ilId:<?=$leaseeSB['ID']?>,
            requestId:<?=$requestID?>,
            urConclusion:urConclusion.nextSibling.querySelector('div.ck-content').innerHTML,
            legalDate:legalDate.value,
            legalResponsible:legalResponsible.value,
         },
         function (data){
            toggleModal();
            window.location.reload();
         }
      );
      //блок лизингполучатель end
   });
   var legalOnEdit = document.querySelector('.legal-on-edit');
   legalOnEdit.addEventListener('click',function (){
      BX.ajax.post(
         '/local/tools/expertCommitee/revision.php',
         {
            requestId:<?=$requestID?>,
         },
         function (data){
            var permissionContainer = document.querySelector('.modal-container');
            permissionContainer.classList.remove('h-100','w-11/12','overflow-y-auto');
            permissionContainer.classList.add('w-1/2','h-96');
            document.querySelector('.modal-body').innerHTML = data;
         }
      );
   })
</script>
