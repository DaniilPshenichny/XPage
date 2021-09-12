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
       <?if($request['PROPERTY_120_VALUE']){?>
      <div class="mr-2">
         <button class="rounded-t h-9 px-4 shadow-my border-b" :class="activeTab===2 ? 'active' : ''" @click="activeTab = 2;">
            Юридический отдел
         </button>
      </div>
       <?}?>
          <div class="mr-2">
             <button class="rounded-t h-9 px-4 shadow-my border-b" :class="activeTab===3 ? 'active' : ''" @click="activeTab = 3;">
                Финансовый отдел
             </button>
          </div>

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
       <?
       ob_start();
       include 'disabledMode/legal.php';
       $out1 = ob_get_contents();
       ob_end_clean();
       echo $out1;
       ?>
   </div>
   <div class="finance-tab" x-show="activeTab===3">
       <?php
       require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
       $requestID = $_POST['requestId'];
       $iblockProvider = new \CIblockElement(false);
       $saveMode = $_POST['action']==='save'?true:false;
       $request = $iblockProvider->GetList([],[
           'IBLOCK_ID' => 33,
           'ID' => $requestID
       ],false,false,['ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_118','PROPERTY_119','PROPERTY_120','PROPERTY_121'])->Fetch();
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
       $res = CIBlockElement::GetProperty(43, $leasee['ID'], "sort", "asc", array("ID" => "244"));
       while ($ob = $res->GetNext())
       {
           $goals[] = $ob['VALUE'];
       }
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
               <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base company-title" type="text" placeholder="Введите название компании" value="<?=$leasee['NAME']?>">
            </div>
         </div>
      </div>
      <div class="my-2">
         <div class="flex text-gray-1000 text-base">
            Отсутствуют документы:
         </div>
         <div class="my-2 w-full">
            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base docs-title" type="text" placeholder="Введите название документов">
         </div>
         <div class="my-2 w-full">
            <textarea id="docs-comments" placeholder="Комментарий"></textarea>
         </div>
      </div>
      <div class="my-2">
         <div class="flex text-gray-1000 text-base">
            Замечания:
         </div>
         <div class="my-2 w-full">
            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base remarks" type="text" placeholder="Введите замечания">
         </div>
         <div class="my-2 w-full">
            <textarea id="remarks-comments" placeholder="Комментарий"></textarea>
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
            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base deal-size" type="text" placeholder="Введите название документов">
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
                            <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base leasing-goal" type="text" placeholder="Введите предмет" data-goal-index="<?=$goalIndex?>" value="<?=$goal?>">
                         </div>
                      </div>
                       <?if($goalIndex > 0):?>
                          <div class="ml-2 flex items-end mb-3 ml-6">
                             <button class="h-10 w-10 shadow-my rounded-md flex justify-center items-center" data-goal-index="<?=$goalIndex?>" onclick="deleteGoalInput(this);">
                                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                   <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                                </svg>
                             </button>
                          </div>
                       <?endif;?>
                   </div>
                </div>
             <?endforeach;?>
         </div>
         <div class="my-4">
            <button class="h-9 w-56 font-semibold text-base shadow-my rounded text-blue-1000 add-goal">
               Добавить предмет
            </button>
         </div>
      </div>
      <div class="my-2">
         <div class="flex text-gray-1000 text-base">
            Отношение суммы совокупного среднемесячного лизингового платежа<span class="text-red-500">*</span>:
         </div>
         <div class="my-2 w-full">
            <textarea id="aggregate-sum"></textarea>
         </div>
      </div>
      <div class="my-2">
         <div class="flex text-gray-1000 text-base">
            Дополнительная информация:
         </div>
         <div class="my-2 w-full">
            <textarea id="additional-info"></textarea>
         </div>
      </div>
      <div class="my-2">
         <div class="my-4 text-xl text-blue-1000 font-semibold">
            Заключение<span class="text-red-500">*</span>:
         </div>
         <div>
            <textarea name="" id="finance-conclusion" cols="30" rows="10"></textarea>
         </div>
      </div>
      <hr class="my-12">
      <div class="grid grid-cols-2 gap-4 my-4">
         <div>
            <div class="flex text-gray-1000 text-base">
               Дата:
            </div>
            <div class="my-2 w-full">
               <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base finance-form-date" type="date" placeholder="Введите дату">
            </div>
         </div>
         <div>
            <div class="flex text-gray-1000 text-base">
               Исполнитель:
            </div>
            <div class="my-2 w-full">
               <input class="border-b-2 outline-none w-full pb-2 placeholder-blue-900 text-base finance-form-responsible" type="text" placeholder="Введите ФИО">
            </div>
         </div>
      </div>
      <div class="flex items-center justify-end mb-4">
         <div class="mx-4">
            <button class="text-blue-1000 rounded-2xl h-9 w-32 flex justify-center items-center font-semibold px-2 shadow-my finance-on-edit">На доработку</button>
         </div>
         <div class="mx-4">
            <button class="text-blue-1000 rounded-2xl h-9 w-32 flex justify-center items-center font-semibold px-2 shadow-my save-finance-result-form">Сохранить</button>
         </div>
         <div class="mx-4">
            <button class="text-blue-1000 border rounded-2xl h-9 w-32 flex justify-center items-center font-semibold active px-1 send-finance-result-form">
               Отправить
            </button>
         </div>
      </div>
      <script>
         ClassicEditor.create( document.querySelector( '#docs-comments' ) ,{language:'ru'}).catch( error => console.error( error ));
         ClassicEditor.create( document.querySelector( '#remarks-comments' ) ,{language:'ru'}).catch( error => console.error( error ));
         ClassicEditor.create( document.querySelector( '#aggregate-sum' ) ,{language:'ru'}).catch( error => console.error( error ));
         ClassicEditor.create( document.querySelector( '#finance-conclusion' ) ,{language:'ru'}).catch( error => console.error( error ));
         ClassicEditor.create( document.querySelector( '#additional-info' ) ,{language:'ru'}).catch( error => console.error( error ));
         function deleteGoalInput(target){
            document.querySelector('input[data-goal-index="'+target.dataset.goalIndex+'"]').parentElement.parentElement.parentElement.remove();
         }
         document = document.querySelector('.finance-tab');
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
         var sendFinanceResult = document.querySelector('.send-finance-result-form');
         sendFinanceResult.addEventListener('click',function (){
            var errorFlag = false;
            if(document.querySelector('#aggregate-sum').nextSibling.querySelector('div.ck-content').children[0].children.length === 1){
               document.querySelector('#aggregate-sum').nextSibling.querySelector('div.ck-content').style = 'border:1px solid red!important;'
               errorFlag = true;
            }
            if(document.querySelector('#finance-conclusion').nextSibling.querySelector('div.ck-content').children[0].children.length === 1){
               document.querySelector('#finance-conclusion').nextSibling.querySelector('div.ck-content').style = 'border:1px solid red!important;'
               errorFlag = true;
            }
            if(errorFlag){
               alert('Заполните обязательные поля');
               return false;
            }



            var companyTitle = document.querySelector('.company-title');
            var docsTitle = document.querySelector('.docs-title');
            var docsComments = document.querySelector('#docs-comments');
            var remarks = document.querySelector('.remarks');
            var remarksComments = document.querySelector('#remarks-comments');
            var dealSize = document.querySelector('.deal-size');
            var goal = document.querySelectorAll('.goals');
            var goals = [];
            for(let i =0;i<goal.length;i++){
               goals.push(goal[i].value);
            }
            var aggregateSum = document.querySelector('#aggregate-sum');
            var additionalInfo = document.querySelector('#additional-info');
            var financeConclusion = document.querySelector('#finance-conclusion');
            var financeFormDate = document.querySelector('.finance-form-date');
            var financeFormResponsible = document.querySelector('.finance-form-responsible');
            BX.ajax.post(
               '/local/tools/expertCommitee/backend/financeResult.php',
               {
                  companyTitle:companyTitle.value,
                  docsTitle:docsTitle.value,
                  docsComments:docsComments.nextSibling.querySelector('div.ck-content').innerHTML,
                  remarks:remarks.value,
                  remarksComments:remarksComments.nextSibling.querySelector('div.ck-content').innerHTML,
                  goals:JSON.stringify(goals),
                  aggregateSum:aggregateSum.nextSibling.querySelector('div.ck-content').innerHTML,
                  additionalInfo:additionalInfo.nextSibling.querySelector('div.ck-content').innerHTML,
                  financeConclusion:financeConclusion.nextSibling.querySelector('div.ck-content').innerHTML,
                  financeFormDate:financeFormDate.value,
                  financeFormResponsible:financeFormResponsible.value,
                  dealSize:dealSize.value,
                  ilId:<?=$il['ID']?>,
                  requestId:<?=$requestID?>,
               },
               function (data){
                  toggleModal();
                  window.location.reload();
               }
            );
         });
         document.querySelector('.save-finance-result-form').addEventListener('click',function(){
            var companyTitle = document.querySelector('.company-title');
            var docsTitle = document.querySelector('.docs-title');
            var docsComments = document.querySelector('#docs-comments');
            var remarks = document.querySelector('.remarks');
            var remarksComments = document.querySelector('#remarks-comments');
            var dealSize = document.querySelector('.deal-size');
            var goal = document.querySelectorAll('.goals');
            var goals = [];
            for(let i =0;i<goal.length;i++){
               goals.push(goal[i].value);
            }
            var aggregateSum = document.querySelector('#aggregate-sum');
            var additionalInfo = document.querySelector('#additional-info');
            var financeConclusion = document.querySelector('#finance-conclusion');
            var financeFormDate = document.querySelector('.finance-form-date');
            var financeFormResponsible = document.querySelector('.finance-form-responsible');
            BX.ajax.post(
               '/local/tools/expertCommitee/backend/financeResult.php',
               {
                  companyTitle:companyTitle.value,
                  docsTitle:docsTitle.value,
                  docsComments:docsComments.nextSibling.querySelector('div.ck-content').innerHTML,
                  remarks:remarks.value,
                  remarksComments:remarksComments.nextSibling.querySelector('div.ck-content').innerHTML,
                  goals:JSON.stringify(goals),
                  aggregateSum:aggregateSum.nextSibling.querySelector('div.ck-content').innerHTML,
                  additionalInfo:additionalInfo.nextSibling.querySelector('div.ck-content').innerHTML,
                  financeConclusion:financeConclusion.nextSibling.querySelector('div.ck-content').innerHTML,
                  financeFormDate:financeFormDate.value,
                  financeFormResponsible:financeFormResponsible.value,
                  dealSize:dealSize.value,
                  ilId:<?=$il['ID']?>,
                  requestId:<?=$requestID?>,
               },
               function (data){
                  toggleModal();
                  window.location.reload();
               }
            );
         });
         var financeOnEdit = document.querySelector('.finance-on-edit');
         financeOnEdit.addEventListener('click',function (){
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
   </div>
</div>

