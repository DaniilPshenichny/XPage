<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
if($requestID === null)$requestID = $_POST['requestId'];
$iblockProvider = new \CIblockElement(false);
$request = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 33,
    'ID' => $requestID
],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_118','PROPERTY_119','PROPERTY_120','PROPERTY_121'])->Fetch();
$il = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 34,
    'ID' => $request['PROPERTY_118_VALUE']
],false,false,['ID','IBLOCK_ID','NAME','DATE_CREATE','PROPERTY_122','PROPERTY_123','PROPERTY_124','PROPERTY_125','PROPERTY_126',
    'PROPERTY_127','PROPERTY_128','PROPERTY_129','PROPERTY_130','PROPERTY_131','PROPERTY_132','PROPERTY_133','PROPERTY_134','PROPERTY_135','PROPERTY_136',
    'PROPERTY_137','PROPERTY_138','PROPERTY_139','PROPERTY_140','PROPERTY_141','PROPERTY_142','PROPERTY_143','PROPERTY_158','PROPERTY_159','PROPERTY_160',
    'PROPERTY_161','PROPERTY_162','PROPERTY_202','PROPERTY_245'])->Fetch();
$leasee = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 43,
    'ID' => $il['PROPERTY_245_VALUE']
],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_222','PROPERTY_223','PROPERTY_265','PROPERTY_229','PROPERTY_230','PROPERTY_224','PROPERTY_225',
    'PROPERTY_226','PROPERTY_227','PROPERTY_228','PROPERTY_261','PROPERTY_262','PROPERTY_263','PROPERTY_264'])->Fetch();
$res = CIBlockElement::GetProperty(34, $il['ID'], "sort", "asc", array("ID" => "133"));
while ($ob = $res->GetNext())
{
    $goals[] = $ob['VALUE'];
}
$financeList = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 37,
    'ID' => $request['PROPERTY_121_VALUE']
],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_205','PROPERTY_206','PROPERTY_207','PROPERTY_208','PROPERTY_209','PROPERTY_210','PROPERTY_211','PROPERTY_212','PROPERTY_213','PROPERTY_214'])->Fetch();
?>

    <div class="flex items-center justify-center text-2xl my-6 text-blue-1000 font-bold">
        Заключение о лизингополучателя от финансового отдела
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
    <div class="my-2">
        <div class="flex text-gray-1000 text-base">
            Отсутствуют документы:
        </div>
        <div class="my-2 w-full">
            <?=$financeList['PROPERTY_208_VALUE']?:'-'?>
        </div>
        <div class="border border-gray-1000 rounded-my px-4 py-4" style="min-height: 140px;">
            <?=$financeList['PROPERTY_209_VALUE']['TEXT']?:'-'?>
        </div>
    </div>
    <div class="my-2">
        <div class="flex text-gray-1000 text-base">
            Замечания:
        </div>
        <div class="my-2 w-full">
            <?=$financeList['PROPERTY_210_VALUE']?:'-'?>
        </div>
        <div class="border border-gray-1000 rounded-my px-4 py-4" style="min-height: 140px;">
            <?=$financeList['PROPERTY_211_VALUE']['TEXT']?:'-'?>
        </div>
    </div>
    <hr class="my-6">
    <div class="mb-4">
        <div class="mb-4 text-xl text-blue-1000 font-semibold">
            Выводы
        </div>
    </div>
    <div class="my-2">
        <div class="flex text-gray-1000 text-base">
            Крупность сделки:
        </div>
        <div class="my-2 w-full">
            <?=$financeList['PROPERTY_212_VALUE']?:'-'?>
        </div>
    </div>
    <div class="my-2">
        <div class="items">
            <?foreach ($goals as $goalIndex => $goal):?>
                <div class="item">
                    <div class="flex">
                        <div class="w-full">
                            <div class="flex text-gray-1000 text-base">
                                Предмет лизинга<span class="text-red-500">*</span>:
                            </div>
                            <div class="my-2 w-full">
                                <?=$goal?:'-'?>
                            </div>
                        </div>
                    </div>
                </div>
            <?endforeach;?>
        </div>
    </div>
    <div class="my-2">
        <div class="flex text-gray-1000 text-base">
            Отношение суммы совокупного среднемесячного лизингового платежа:
        </div>
        <div class="border border-gray-1000 rounded-my px-4 py-4" style="min-height: 140px;">
            <?=$financeList['PROPERTY_213_VALUE']['TEXT']?:'-'?>
        </div>
    </div>
    <div class="my-2">
        <div class="flex text-gray-1000 text-base">
            Дополнительная информация:
        </div>
        <div class="border border-gray-1000 rounded-my px-4 py-4" style="min-height: 140px;">
            <?=$financeList['PROPERTY_214_VALUE']['TEXT']?:'-'?>
        </div>
    </div>
    <div class="my-2">
        <div class="my-4 text-xl text-blue-1000 font-semibold">
            Заключение
        </div>
        <div class="border border-gray-1000 rounded-my px-4 py-4" style="min-height: 140px;">
            <?=$financeList['PROPERTY_205_VALUE']['TEXT']?:'-'?>
        </div>
    </div>
    <hr class="my-12">
    <div class="grid grid-cols-2 gap-4 my-4">
        <div>
            <div class="flex text-gray-1000 text-base">
                Дата:
            </div>
            <div class="my-2 w-full">
                <?=$financeList['PROPERTY_206_VALUE']?:'-'?>
            </div>
        </div>
        <div>
            <div class="flex text-gray-1000 text-base">
                Исполнитель:
            </div>
            <div class="my-2 w-full">
                <?=$financeList['PROPERTY_207_VALUE']?:'-'?>
            </div>
        </div>
    </div>
