<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
$iblockProvider = new \CIblockElement(false);
if($requestId === null)$requestId = $_POST['requestId'];
$request = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 33,
    'ID' => $requestId
],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_117','PROPERTY_118','PROPERTY_120',])->Fetch();

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
    'PROPERTY_226','PROPERTY_227','PROPERTY_228','PROPERTY_238','PROPERTY_239','PROPERTY_240','PROPERTY_232','PROPERTY_233','PROPERTY_234','PROPERTY_235','PROPERTY_236'])->Fetch();

$res = CIBlockElement::GetProperty(34, $il['ID'], "sort", "asc", array("ID" => "168"));
while ($ob = $res->GetNext())
{
    $poruchitels[] = $ob['VALUE'];
}
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

$urList = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 36,
    'ID' => $request['PROPERTY_120_VALUE']
],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_176','PROPERTY_203','PROPERTY_204',])->Fetch();
?>
<div>
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
                <?=$leasee['NAME']?>
            </div>
        </div>
    </div>
    <div class="flex justify-between my-2">
        <div>
            <div class="flex text-gray-1000 text-base">
                Инн:
            </div>
            <div class="my-2 w-full">
                <?=$leasee['PROPERTY_224_VALUE']?:'-'?>
            </div>
        </div>
        <div>
            <div class="flex text-gray-1000 text-base">
                ОГРН:
            </div>
            <div class="my-2 w-full">
                <?=$leasee['PROPERTY_225_VALUE']?:'-'?>
            </div>
        </div>
        <div>
            <div class="flex text-gray-1000 text-base">
                Дата образования<span class="text-red-500">*</span>:
            </div>
            <div class="my-2 w-full">
                <?=$leasee['PROPERTY_226_VALUE']?:'-'?>
            </div>
        </div>
    </div>
    <div class="my-2">
        <div class="flex text-gray-1000 text-base">
            Ответчик:
        </div>
        <div class="my-2 w-full">
            <?=$leasee['PROPERTY_232_VALUE']?:'-'?>
        </div>
    </div>
    <div class="my-2">
        <div class="flex text-gray-1000 text-base">
            Истец:
        </div>
        <div class="my-2 w-full">
            <?=$leasee['PROPERTY_233_VALUE'?:'-']?>
        </div>
    </div>
    <div class="my-2">
        <div class="flex text-gray-1000 text-base">
            Директор :
        </div>
        <div class="my-2 w-full">
            <?=$leasee['PROPERTY_234_VALUE']?:'-'?>
        </div>
    </div>
    <div class="my-2">
        <div class="flex text-gray-1000 text-base">
            Участник :
        </div>
        <div class="my-2 w-full">
            <?=$leasee['PROPERTY_235_VALUE']?:'-'?>
        </div>
    </div>
    <div class="my-2">
        <div class="flex text-gray-1000 text-base">
            Конечный бенефициар:
        </div>
        <div class="my-2 w-full">
            <?=$leasee['PROPERTY_236_VALUE']?:'-'?>
        </div>
    </div>
    <div class="my-2">
        <div class="flex text-gray-1000 text-base">
            Поручитель:
        </div>
        <div class="poruchitels">
            <?foreach ($poruchitels as $poruchitel):?>
            <div class="my-2 w-full">
                <?=$poruchitel?>
            </div>
            <?endforeach;?>
        </div>
    </div>
    <div class="flex my-8">
        <div>
            <div class="flex justify-center items-center">
                <div>
                    <input class="w-6 h-6 rounded company-resident" <?=$leasee['PROPERTY_238_ENUM_ID']==='141'?'checked':'';?> disabled type="checkbox">
                </div>
                <div class="mx-2">
                    Резидент
                </div>
            </div>
        </div>
        <div>
            <div class="flex justify-center items-center">
                <div>
                    <input class="w-6 h-6 rounded company-diler" <?=$leasee['PROPERTY_239_ENUM_ID']==='143'?'checked':'';?> disabled type="checkbox">
                </div>
                <div class="mx-2">
                    Дилер
                </div>
            </div>
        </div>
        <div>
            <div class="flex justify-center items-center">
                <div>
                    <input class="w-6 h-6 rounded company-manufacturer" <?=$leasee['PROPERTY_240_ENUM_ID']==='145'?'checked':'';?> disabled type="checkbox">
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
        </div>
        <div class="postavki-form">
            <div>
                <div class="flex text-gray-1000 text-base">
                    Название компании-поставщика<span class="text-red-500">*</span>:
                </div>
                <div class="my-2 w-full company-provider-title">

                </div>
            </div>
            <div class="flex justify-between">
                <div class="my-2">
                    <div class="flex text-gray-1000 text-base">
                        ИНН<span class="text-red-500">*</span>:
                    </div>
                    <div class="my-2 w-full company-provider-inn">
                    </div>
                </div>
                <div class="my-2">
                    <div class="flex text-gray-1000 text-base">
                        ОГРН:
                    </div>
                    <div class="my-2 w-full company-provider-ogrn">
                    </div>
                </div>
                <div class="my-2">
                    <div class="flex text-gray-1000 text-base">
                        Дата образования<span class="text-red-500">*</span>:
                    </div>
                    <div class="my-2 w-full company-provider-date-create">
                    </div>
                </div>
            </div>
            <div>
                <div class="flex text-gray-1000 text-base">
                    Ответчик:
                </div>
                <div class="my-2 w-full provider-otvetchik">
                </div>
            </div>
            <div class="my-2">
                <div class="flex text-gray-1000 text-base">
                    Истец:
                </div>
                <div class="my-2 w-full provider-istec">
                </div>
            </div>
            <div class="my-2">
                <div class="flex text-gray-1000 text-base">
                    Директор :
                </div>
                <div class="my-2 w-full provider-director">
                </div>
            </div>
            <div class="my-2">
                <div class="flex text-gray-1000 text-base">
                    Участник :
                </div>
                <div class="my-2 w-full provider-member">
                </div>
            </div>
        </div>
    </div>
    <hr class="my-8">
    <div class="my-2">
        <div class="my-4 text-xl text-blue-1000 font-semibold">
            Заключение
        </div>
        <div class="border border-gray-1000 rounded-my px-4 py-4" style="min-height: 140px;">
            <?=$urList['PROPERTY_176_VALUE']['TEXT']?:'-'?>
        </div>
    </div>
    <hr class="my-12">
    <div class="grid grid-cols-2 gap-4 my-4">
        <div>
            <div class="flex text-gray-1000 text-base">
                Дата:
            </div>
            <div class="my-2 w-full">
                <?=$urList['PROPERTY_203_VALUE']?:'-'?>
            </div>
        </div>
        <div>
            <div class="flex text-gray-1000 text-base">
                Исполнитель:
            </div>
            <div class="my-2 w-full">
                <?=$urList['PROPERTY_204_VALUE']?:'-'?>
            </div>
        </div>
    </div>
</div>
<script>
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
      BX.ajax.post('/local/tools/expertCommitee/backend/getProviderData.php',
         {
            providerId:e.target.value
         },
         function(data){
            data = JSON.parse(data);
            providerCompanyTitle.innerText = data.NAME;
            providerCompanyInn.innerText = data.INN;
            providerCompanyOgrn.innerText = data.OGRN;
            providerCompanyDateCreate.innerText = data.DATE;
            providerOtvetchik.innerText = data.OTVETCHIK;
            providerIstec.innerText = data.ISTEC;
            providerDirector.innerText = data.DIRECTOR;
            providerMember.innerText = data.MEMBER;
         }
      );
   })
</script>
