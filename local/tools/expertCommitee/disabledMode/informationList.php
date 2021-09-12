<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
$monthArray = ['января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'];
$iblockProvider = new \CIblockElement(false);
$requestId = 1348;
$request = $iblockProvider->GetList([],[
    'IBLOCK_ID' => 33,
    'ID' => $requestId
],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_117','PROPERTY_118'])->Fetch();
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

//files start
$res = CIBlockElement::GetProperty(34, $il['ID'], "sort", "asc", array("ID" => "158"));
while ($ob = $res->GetNext())
{
if($ob['VALUE'] !== null) {
    $files['PROPERTY_158'][] = [
        'BITRIX_INFO' => CFile::GetByID($ob['VALUE'])->Fetch(),
        'PATH' => CFile::GetPath($ob['VALUE']),
    ];
}
}
$res = CIBlockElement::GetProperty(34, $il['ID'], "sort", "asc", array("ID" => "159"));
while ($ob = $res->GetNext())
{
if($ob['VALUE'] !== null) {
    $files['PROPERTY_159'][] = [
        'BITRIX_INFO' => CFile::GetByID($ob['VALUE'])->Fetch(),
        'PATH' => CFile::GetPath($ob['VALUE']),
    ];
}
}
$res = CIBlockElement::GetProperty(34, $il['ID'], "sort", "asc", array("ID" => "160"));
while ($ob = $res->GetNext())
{
if($ob['VALUE'] !== null) {
    $files['PROPERTY_160'][] = [
        'BITRIX_INFO' => CFile::GetByID($ob['VALUE'])->Fetch(),
        'PATH' => CFile::GetPath($ob['VALUE']),
    ];
}
}
$res = CIBlockElement::GetProperty(34, $il['ID'], "sort", "asc", array("ID" => "161"));
while ($ob = $res->GetNext())
{
   if($ob['VALUE'] !== null) {
       $files['PROPERTY_161'][] = [
           'BITRIX_INFO' => CFile::GetByID($ob['VALUE'])->Fetch(),
           'PATH' => CFile::GetPath($ob['VALUE']),
       ];
   }
}
$res = CIBlockElement::GetProperty(34, $il['ID'], "sort", "asc", array("ID" => "162"));
while ($ob = $res->GetNext())
{
   if($ob['VALUE'] !== null) {
       $files['PROPERTY_162'][] = [
           'BITRIX_INFO' => CFile::GetByID($ob['VALUE'])->Fetch(),
           'PATH' => CFile::GetPath($ob['VALUE']),
       ];
   }
}
//files end
$res = CIBlockElement::GetProperty(34, $il['ID'], "sort", "asc", array("ID" => "231"));
while ($ob = $res->GetNext())
{
if($ob['VALUE'] !== null) {
    $dates[] = date('d.m.Y', strtotime($ob['VALUE']));
}
}
$res = CIBlockElement::GetProperty(34, $il['ID'], "sort", "asc", array("ID" => "244"));
while ($ob = $res->GetNext())
{
    $goals[] = $ob['VALUE'];
}



$date = new DateTime();
$date->setTimestamp(strtotime($il['DATE_CREATE']));
$month = $date->format('n')-1;
if(CModule::IncludeModule('crm')){
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
}
?>


<div class="manager-page mt-4">
    <div class="block">
        <div class="flex text-gray-1000 text-base">
            Ссылка<span class="text-red-500">*</span>:
        </div>
        <div class="my-2 w-full">
            <?=$leasee['PROPERTY_265_VALUE']?>
        </div>
    </div>
    <div class="flex items-center justify-center text-2xl my-6 text-blue-1000 font-bold">
       Информационный лист от “<?=$date->format('d');?>” <?=$monthArray[$month]?> <?=$date->format('Y');?> г.
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
                <?=$leasee['NAME']?>
            </div>
        </div>
    </div>
    <div class="flex justify-start mb-4">
        <div class="mr-6">
            <div class="text-gray-1000 text-lg mb-2">Клиент повторный:</div>
            <div>
                <label>
                    <div class="flex">
                        <div class="flex items-center justify-center shadow-my h-10 w-10 rounded mx-2 text-lg text-blue-1000" <?=$leasee['PROPERTY_222_ENUM_ID']!=='147'?'active':''?>>Нет</div>
                        <div class="flex items-center justify-center shadow-my h-10 w-10 rounded <?=$leasee['PROPERTY_222_ENUM_ID']==='147'?'active':''?> mx-2 text-lg">Да</div>
                    </div>
                </label>
            </div>
        </div>
        <div class="mx-4">
            <div class="flex text-gray-1000 text-lg mb-2">
                Дата:
            </div>
            <div class="my-4 w-full">
                <?=$leasee['PROPERTY_229_VALUE']?:'-'?>
            </div>
        </div>
    </div>
   <div class="mx-4">
      <div class="flex text-gray-1000 text-lg mb-2">
         Результат проверки:
      </div>
      <div class="my-4 w-full">
          <?=$leasee['PROPERTY_230_VALUE']?:'-'?>
      </div>
   </div>
    <div class="my-2">
        <div class="flex text-gray-1000 text-base">
            Юридический адрес:
        </div>
        <div class="my-2 w-full">
            <?=$leasee['PROPERTY_223_VALUE']?:'-'?>
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
    <div class="grid grid-cols-2 gap-4 my-2">
        <div>
            <div class="flex text-gray-1000 text-base">
                Контакт<span class="text-red-500">*</span>:
            </div>
            <div class="my-2 w-full">
                <?=$contact['FULL_NAME']?:'-'?>
            </div>
        </div>
        <div>
            <div class="flex text-gray-1000 text-base">
                Телефон:
            </div>
            <div class="my-2 w-full">
               <?=$phone['VALUE']?:'-'?>
            </div>
        </div>
    </div>
    <div class="my-2">
        <div class="flex text-gray-1000 text-base">
            Лицо, принимающее решение<span class="text-red-500">*</span>:
        </div>
        <div class="my-2 w-full">
            <?=$leasee['PROPERTY_227_VALUE']?:'-'?>
        </div>
    </div>
    <div class="my-2">
        <div class="flex text-gray-1000 text-base">
            Лицо, курирующее сделку:
        </div>
        <div class="my-2 w-full">
            <?=$leasee['PROPERTY_228_VALUE']?:'-'?>
        </div>
    </div>
    <div>
        <div class="dates">
            <div class="flex">
                <div>
                    <div class="flex text-gray-1000 text-base">
                        Дата проведения встречи:
                    </div>
                    <div class="my-2 w-full">
                        <?=$dates?implode(' , ',$dates):'-'?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="items-disabled">
            <div class="flex item">
                <div>
                    <div class="flex text-gray-1000 text-base">
                        Предмет лизинга<span class="text-red-500">*</span>:
                    </div>
                    <div class="my-2 w-full">
                        <?=implode(' , ',$goals)?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex justify-start my-6">
        <div>
            <div class="flex text-gray-1000 text-base">
                Общая стоимость<span class="text-red-500">*</span>:
            </div>
            <div class="my-2 w-full">
                <?=$leasee['PROPERTY_261_VALUE']?:'-'?>
            </div>
        </div>
        <div class="ml-4 flex justify-end mt-4">
            <div class="flex justify-center items-center">
                <div>
                    <input class="w-6 h-6 rounded" disabled <?=$leasee['PROPERTY_264_ENUM_ID']==='149'?'checked':''?> type="checkbox">
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
                <?=$leasee['PROPERTY_262_VALUE']?:'-'?>
            </div>
        </div>
        <div>
            <div class="flex text-gray-1000 text-base">
                Срок лизинга:
            </div>
            <div class="my-2 w-full">
                <?=$leasee['PROPERTY_263_VALUE']?:'-'?>
            </div>
        </div>
    </div>
    <hr>
    <!-- поставщик-->
    <hr>
    <div class="my-2">
        <div class="my-4 text-gray-1000 text-base">
            Сроки реализации проекта:
        </div>
       <div class="border border-gray-1000 rounded-my px-4 py-4" style="min-height: 140px;">
           <?=$il['PROPERTY_137_VALUE']['TEXT']?:'-'?>
        </div>
        <div>
            <div class="flex items-center">
                <div class="w-8 h-8 mt-4 shadow-my flex justify-center items-center rounded-md cursor-pointer">
                    <svg width="13" height="15" viewBox="0 0 13 15" class="transform text-blue-1000" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.26129 7.574L6.56529 2.27C6.82071 2.01464 7.12393 1.8121 7.45763 1.67393C7.79133 1.53575 8.14897 1.46466 8.51015 1.46471C8.87132 1.46475 9.22895 1.53594 9.56261 1.6742C9.89627 1.81246 10.1994 2.01508 10.4548 2.2705C10.7101 2.52592 10.9127 2.82914 11.0509 3.16284C11.189 3.49653 11.2601 3.85418 11.2601 4.21535C11.26 4.57653 11.1889 4.93415 11.0506 5.26782C10.9123 5.60148 10.7097 5.90464 10.4543 6.16L4.09029 12.524C3.85571 12.7586 3.53754 12.8904 3.20579 12.8904C2.87404 12.8904 2.55588 12.7586 2.32129 12.524C2.08671 12.2894 1.95492 11.9713 1.95492 11.6395C1.95492 11.3077 2.08671 10.9896 2.32129 10.755L7.97829 5.098C8.11077 4.95583 8.1829 4.76778 8.17947 4.57348C8.17604 4.37918 8.09733 4.19379 7.95991 4.05638C7.8225 3.91897 7.63712 3.84025 7.44281 3.83683C7.24851 3.8334 7.06047 3.90552 6.91829 4.038L1.26129 9.695C0.745446 10.2108 0.455647 10.9105 0.455647 11.64C0.455647 12.3695 0.745446 13.0692 1.26129 13.585C1.77714 14.1008 2.47678 14.3906 3.20629 14.3906C3.93581 14.3906 4.63545 14.1008 5.15129 13.585L11.5143 7.22C12.296 6.41987 12.7307 5.34383 12.7242 4.22525C12.7177 3.10667 12.2705 2.03576 11.4795 1.2448C10.6885 0.453829 9.61762 0.00658612 8.49904 7.2159e-05C7.38046 -0.0064418 6.30442 0.428298 5.50429 1.21L0.201292 6.513C0.0688116 6.65518 -0.00331137 6.84322 0.000116847 7.03752C0.00354506 7.23182 0.0822568 7.41721 0.21967 7.55462C0.357083 7.69203 0.542468 7.77075 0.736769 7.77417C0.93107 7.7776 1.11912 7.70548 1.26129 7.573V7.574Z" fill="#28315F"/>
                    </svg>
                </div>
                <?foreach ($files['PROPERTY_158'] as $file):?>
                <div class="mt-5 ml-4">
                     <a href="<?=$file['PATH']?>"><?=$file['BITRIX_INFO']['FILE_NAME']?></a>,
                </div>
               <?endforeach;?>
            </div>
        </div>
    </div>
    <div class="my-2">
        <div class="my-4 text-gray-1000 text-base">
            Опыт работы с лизингом<span class="text-red-500">*</span>:
        </div>
       <div class="border border-gray-1000 rounded-my px-4 py-4" style="min-height: 140px;">
           <?=$il['PROPERTY_138_VALUE']['TEXT']?:'-'?>
        </div>
        <div>
            <div class="flex items-center">
                <div class="w-8 h-8 mt-4 shadow-my flex justify-center items-center rounded-md cursor-pointer">
                    <svg width="13" height="15" viewBox="0 0 13 15" class="transform text-blue-1000" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.26129 7.574L6.56529 2.27C6.82071 2.01464 7.12393 1.8121 7.45763 1.67393C7.79133 1.53575 8.14897 1.46466 8.51015 1.46471C8.87132 1.46475 9.22895 1.53594 9.56261 1.6742C9.89627 1.81246 10.1994 2.01508 10.4548 2.2705C10.7101 2.52592 10.9127 2.82914 11.0509 3.16284C11.189 3.49653 11.2601 3.85418 11.2601 4.21535C11.26 4.57653 11.1889 4.93415 11.0506 5.26782C10.9123 5.60148 10.7097 5.90464 10.4543 6.16L4.09029 12.524C3.85571 12.7586 3.53754 12.8904 3.20579 12.8904C2.87404 12.8904 2.55588 12.7586 2.32129 12.524C2.08671 12.2894 1.95492 11.9713 1.95492 11.6395C1.95492 11.3077 2.08671 10.9896 2.32129 10.755L7.97829 5.098C8.11077 4.95583 8.1829 4.76778 8.17947 4.57348C8.17604 4.37918 8.09733 4.19379 7.95991 4.05638C7.8225 3.91897 7.63712 3.84025 7.44281 3.83683C7.24851 3.8334 7.06047 3.90552 6.91829 4.038L1.26129 9.695C0.745446 10.2108 0.455647 10.9105 0.455647 11.64C0.455647 12.3695 0.745446 13.0692 1.26129 13.585C1.77714 14.1008 2.47678 14.3906 3.20629 14.3906C3.93581 14.3906 4.63545 14.1008 5.15129 13.585L11.5143 7.22C12.296 6.41987 12.7307 5.34383 12.7242 4.22525C12.7177 3.10667 12.2705 2.03576 11.4795 1.2448C10.6885 0.453829 9.61762 0.00658612 8.49904 7.2159e-05C7.38046 -0.0064418 6.30442 0.428298 5.50429 1.21L0.201292 6.513C0.0688116 6.65518 -0.00331137 6.84322 0.000116847 7.03752C0.00354506 7.23182 0.0822568 7.41721 0.21967 7.55462C0.357083 7.69203 0.542468 7.77075 0.736769 7.77417C0.93107 7.7776 1.11912 7.70548 1.26129 7.573V7.574Z" fill="#28315F"/>
                    </svg>
                </div>
                <?foreach ($files['PROPERTY_159'] as $file):?>
                   <div class="mt-5 ml-4">
                      <a href="<?=$file['PATH']?>"><?=$file['BITRIX_INFO']['FILE_NAME']?></a>,
                   </div>
                <?endforeach;?>
            </div>
        </div>
    </div>
    <div class="my-2">
        <div class="my-4 text-gray-1000 text-base">
            Наши конкуренты:
        </div>
       <div class="border border-gray-1000 rounded-my px-4 py-4" style="min-height: 140px;">
           <?=$il['PROPERTY_139_VALUE']['TEXT']?:'-'?>
        </div>
        <div>
            <div class="flex items-center">
                <div class="w-8 h-8 mt-4 shadow-my flex justify-center items-center rounded-md cursor-pointer">
                    <svg width="13" height="15" viewBox="0 0 13 15" class="transform text-blue-1000" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.26129 7.574L6.56529 2.27C6.82071 2.01464 7.12393 1.8121 7.45763 1.67393C7.79133 1.53575 8.14897 1.46466 8.51015 1.46471C8.87132 1.46475 9.22895 1.53594 9.56261 1.6742C9.89627 1.81246 10.1994 2.01508 10.4548 2.2705C10.7101 2.52592 10.9127 2.82914 11.0509 3.16284C11.189 3.49653 11.2601 3.85418 11.2601 4.21535C11.26 4.57653 11.1889 4.93415 11.0506 5.26782C10.9123 5.60148 10.7097 5.90464 10.4543 6.16L4.09029 12.524C3.85571 12.7586 3.53754 12.8904 3.20579 12.8904C2.87404 12.8904 2.55588 12.7586 2.32129 12.524C2.08671 12.2894 1.95492 11.9713 1.95492 11.6395C1.95492 11.3077 2.08671 10.9896 2.32129 10.755L7.97829 5.098C8.11077 4.95583 8.1829 4.76778 8.17947 4.57348C8.17604 4.37918 8.09733 4.19379 7.95991 4.05638C7.8225 3.91897 7.63712 3.84025 7.44281 3.83683C7.24851 3.8334 7.06047 3.90552 6.91829 4.038L1.26129 9.695C0.745446 10.2108 0.455647 10.9105 0.455647 11.64C0.455647 12.3695 0.745446 13.0692 1.26129 13.585C1.77714 14.1008 2.47678 14.3906 3.20629 14.3906C3.93581 14.3906 4.63545 14.1008 5.15129 13.585L11.5143 7.22C12.296 6.41987 12.7307 5.34383 12.7242 4.22525C12.7177 3.10667 12.2705 2.03576 11.4795 1.2448C10.6885 0.453829 9.61762 0.00658612 8.49904 7.2159e-05C7.38046 -0.0064418 6.30442 0.428298 5.50429 1.21L0.201292 6.513C0.0688116 6.65518 -0.00331137 6.84322 0.000116847 7.03752C0.00354506 7.23182 0.0822568 7.41721 0.21967 7.55462C0.357083 7.69203 0.542468 7.77075 0.736769 7.77417C0.93107 7.7776 1.11912 7.70548 1.26129 7.573V7.574Z" fill="#28315F"/>
                    </svg>
                </div>
                <?foreach ($files['PROPERTY_160'] as $file):?>
                   <div class="mt-5 ml-4">
                      <a href="<?=$file['PATH']?>"><?=$file['BITRIX_INFO']['FILE_NAME']?></a>,
                   </div>
                <?endforeach;?>
            </div>
        </div>
    </div>
    <div class="my-2">
        <div class="my-4 text-gray-1000 text-base">
            Основа для принятия решения:
        </div>
       <div class="border border-gray-1000 rounded-my px-4 py-4" style="min-height: 140px;">
           <?=$il['PROPERTY_140_VALUE']['TEXT']?:'-'?>
        </div>
        <div>
            <div class="flex items-center">
                <div class="w-8 h-8 mt-4 shadow-my flex justify-center items-center rounded-md cursor-pointer">
                    <svg width="13" height="15" viewBox="0 0 13 15" class="transform text-blue-1000" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.26129 7.574L6.56529 2.27C6.82071 2.01464 7.12393 1.8121 7.45763 1.67393C7.79133 1.53575 8.14897 1.46466 8.51015 1.46471C8.87132 1.46475 9.22895 1.53594 9.56261 1.6742C9.89627 1.81246 10.1994 2.01508 10.4548 2.2705C10.7101 2.52592 10.9127 2.82914 11.0509 3.16284C11.189 3.49653 11.2601 3.85418 11.2601 4.21535C11.26 4.57653 11.1889 4.93415 11.0506 5.26782C10.9123 5.60148 10.7097 5.90464 10.4543 6.16L4.09029 12.524C3.85571 12.7586 3.53754 12.8904 3.20579 12.8904C2.87404 12.8904 2.55588 12.7586 2.32129 12.524C2.08671 12.2894 1.95492 11.9713 1.95492 11.6395C1.95492 11.3077 2.08671 10.9896 2.32129 10.755L7.97829 5.098C8.11077 4.95583 8.1829 4.76778 8.17947 4.57348C8.17604 4.37918 8.09733 4.19379 7.95991 4.05638C7.8225 3.91897 7.63712 3.84025 7.44281 3.83683C7.24851 3.8334 7.06047 3.90552 6.91829 4.038L1.26129 9.695C0.745446 10.2108 0.455647 10.9105 0.455647 11.64C0.455647 12.3695 0.745446 13.0692 1.26129 13.585C1.77714 14.1008 2.47678 14.3906 3.20629 14.3906C3.93581 14.3906 4.63545 14.1008 5.15129 13.585L11.5143 7.22C12.296 6.41987 12.7307 5.34383 12.7242 4.22525C12.7177 3.10667 12.2705 2.03576 11.4795 1.2448C10.6885 0.453829 9.61762 0.00658612 8.49904 7.2159e-05C7.38046 -0.0064418 6.30442 0.428298 5.50429 1.21L0.201292 6.513C0.0688116 6.65518 -0.00331137 6.84322 0.000116847 7.03752C0.00354506 7.23182 0.0822568 7.41721 0.21967 7.55462C0.357083 7.69203 0.542468 7.77075 0.736769 7.77417C0.93107 7.7776 1.11912 7.70548 1.26129 7.573V7.574Z" fill="#28315F"/>
                    </svg>
                </div>
                <?foreach ($files['PROPERTY_161'] as $file):?>
                   <div class="mt-5 ml-4">
                      <a href="<?=$file['PATH']?>"><?=$file['BITRIX_INFO']['FILE_NAME']?></a>,
                   </div>
                <?endforeach;?>
            </div>
        </div>
    </div>
    <div class="my-2">
        <div class="my-4 text-gray-1000 text-base">
            Примечания:
        </div>
        <div class="border border-gray-1000 rounded-my px-4 py-4" style="min-height: 140px;">
            <?=$il['PROPERTY_141_VALUE']['TEXT']?:'-'?>
        </div>
        <div>
            <div class="flex items-center">
                <div class="w-8 h-8 mt-4 shadow-my flex justify-center items-center rounded-md cursor-pointer">
                    <svg width="13" height="15" viewBox="0 0 13 15" class="transform text-blue-1000" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.26129 7.574L6.56529 2.27C6.82071 2.01464 7.12393 1.8121 7.45763 1.67393C7.79133 1.53575 8.14897 1.46466 8.51015 1.46471C8.87132 1.46475 9.22895 1.53594 9.56261 1.6742C9.89627 1.81246 10.1994 2.01508 10.4548 2.2705C10.7101 2.52592 10.9127 2.82914 11.0509 3.16284C11.189 3.49653 11.2601 3.85418 11.2601 4.21535C11.26 4.57653 11.1889 4.93415 11.0506 5.26782C10.9123 5.60148 10.7097 5.90464 10.4543 6.16L4.09029 12.524C3.85571 12.7586 3.53754 12.8904 3.20579 12.8904C2.87404 12.8904 2.55588 12.7586 2.32129 12.524C2.08671 12.2894 1.95492 11.9713 1.95492 11.6395C1.95492 11.3077 2.08671 10.9896 2.32129 10.755L7.97829 5.098C8.11077 4.95583 8.1829 4.76778 8.17947 4.57348C8.17604 4.37918 8.09733 4.19379 7.95991 4.05638C7.8225 3.91897 7.63712 3.84025 7.44281 3.83683C7.24851 3.8334 7.06047 3.90552 6.91829 4.038L1.26129 9.695C0.745446 10.2108 0.455647 10.9105 0.455647 11.64C0.455647 12.3695 0.745446 13.0692 1.26129 13.585C1.77714 14.1008 2.47678 14.3906 3.20629 14.3906C3.93581 14.3906 4.63545 14.1008 5.15129 13.585L11.5143 7.22C12.296 6.41987 12.7307 5.34383 12.7242 4.22525C12.7177 3.10667 12.2705 2.03576 11.4795 1.2448C10.6885 0.453829 9.61762 0.00658612 8.49904 7.2159e-05C7.38046 -0.0064418 6.30442 0.428298 5.50429 1.21L0.201292 6.513C0.0688116 6.65518 -0.00331137 6.84322 0.000116847 7.03752C0.00354506 7.23182 0.0822568 7.41721 0.21967 7.55462C0.357083 7.69203 0.542468 7.77075 0.736769 7.77417C0.93107 7.7776 1.11912 7.70548 1.26129 7.573V7.574Z" fill="#28315F"/>
                    </svg>
                </div>
                <?foreach ($files['PROPERTY_162'] as $file):?>
                   <div class="mt-5 ml-4">
                      <a href="<?=$file['PATH']?>"><?=$file['BITRIX_INFO']['FILE_NAME']?></a>,
                   </div>
                <?endforeach;?>
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
                <?=$il['PROPERTY_142_VALUE']?:'-'?>
            </div>
        </div>
        <div>
            <div class="flex text-gray-1000 text-base">
                Исполнитель:
            </div>
            <div class="my-2 w-full">
                <?=$il['PROPERTY_143_VALUE']?:'-'?>
            </div>
        </div>
    </div>
</div>