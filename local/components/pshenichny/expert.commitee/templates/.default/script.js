BX.ready(function (){
   var filterManagerDialog = new BX.UI.EntitySelector.Dialog({
      targetNode: document.querySelector('.manager'),
      enableSearch: true,
      context: 'MY_MODULE_CONTEXT',
      // preselectedItems: preSelectedOptions,
      events: {
         'Item:onSelect': (event) => {
            var usersContainer = document.querySelector('.manager');
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
   document.querySelector('.manager').addEventListener('click', function() {
      filterManagerDialog.show();
   });
   var cleanFilterData = document.querySelector('.clean-filter-data');
   var filterDate = document.querySelector('.filter-date');
   var filterCompany = document.querySelector('.filter-company-name');
   var filterStatus = document.querySelector('.filter-status');
   cleanFilterData.addEventListener('click',function (){
      filterDate.value = '';
      filterCompany.value = '';
      filterStatus.selectedIndex = 0;
      filterManagerDialog.deselectAll();
      var requests = document.querySelectorAll('.parent-request-container');
      [...requests].forEach(function(el){
         el.classList.remove('hidden');
      });
   });
   var search = document.querySelector('.filter-search');

   search.addEventListener('click',function (){
      var usersObjects = filterManagerDialog.getSelectedItems();
      var users = [];
      usersObjects.forEach(function(user){
         users.push(user.id);
      });
      users = JSON.stringify(users);

      BX.ajax.post(
         '/local/tools/expertCommitee/backend/filter.php',
         {
            date:filterDate.value,
            company:filterCompany.value,
            filterStatus:filterStatus.value,
            users:users,
         },
         function (data){
            data = JSON.parse(data);
            data = Object.values(data);
            var requests = document.querySelectorAll('.parent-request-container');
            console.log(requests);
            [...requests].forEach(function(el){
               el.classList.remove('hidden');
               if(!data.includes(el.dataset.requestId))el.classList.add('hidden');
            });
         }
      );
   });




   var openmodal = document.querySelectorAll('.modal-open')
   for (var i = 0; i < openmodal.length; i++) {
      openmodal[i].addEventListener('click', function(event){
         event.preventDefault();
         cancel();
         setTimeout(()=>{
            toggleModal()
         },300)

      })
   }
   const overlay = document.querySelector('.modal-overlay')
   overlay.addEventListener('click', toggleModal)
   var closemodal = document.querySelectorAll('.modal-close')
   for (var i = 0; i < closemodal.length; i++) {
      closemodal[i].addEventListener('click', toggleModal)
   }
   document.onkeydown = function(evt) {
      evt = evt || window.event
      var isEscape = false
      if ("key" in evt) {
         isEscape = (evt.key === "Escape" || evt.key === "Esc")
      } else {
         isEscape = (evt.keyCode === 27)
      }
      if (isEscape && document.body.classList.contains('modal-active')) {
         toggleModal()
      }
   };
   var fillListButtons = document.querySelectorAll('.fill-list');
   for (var i = 0; i < fillListButtons.length; i++) {
      fillListButtons[i].addEventListener('click', function(event){
         event.preventDefault()
         var statusBar = document.querySelector('.status-bar[data-requestid="'+event.target.dataset.id+'"]');
         statusBar.classList = 'progress-request status-bar';
         statusBar.innerText = 'На рассмотрении';
         BX.ajax.post(
            '/local/tools/expertCommitee/managerPage.php',
            {
               requestId:event.target.dataset.id
            },
            function (data){
               var modalContainer = document.querySelector('.modal-container');
               modalContainer.classList.remove('md:max-w-6xl');
               modalContainer.classList.add('md:max-w-4xl');
               var permissionContainer = document.querySelector('.modal-body');
               permissionContainer.classList.add('overflow-y-auto','h-100');
               permissionContainer.innerHTML = data;
               document.querySelector('.modal-title').innerHTML = '';
               toggleModal();
            }
         );
      })
   }

   var fillSavedListsButtons = document.querySelectorAll('.fill-list-saved');
   for (var i = 0; i < fillSavedListsButtons.length; i++) {
      fillSavedListsButtons[i].addEventListener('click', function(event){
         event.preventDefault()
         var statusBar = document.querySelector('.status-bar[data-requestid="'+event.target.dataset.id+'"]');
         statusBar.classList = 'progress-request status-bar';
         statusBar.innerText = 'На рассмотрении';
         BX.ajax.post(
            '/local/tools/expertCommitee/managerPage.php',
            {
               requestId:event.target.dataset.id,
               mode:'save'
            },
            function (data){
               var modalContainer = document.querySelector('.modal-container');
               modalContainer.classList.remove('md:max-w-6xl');
               modalContainer.classList.add('md:max-w-4xl');
               var permissionContainer = document.querySelector('.modal-body');
               permissionContainer.classList.add('overflow-y-auto','h-100');
               permissionContainer.innerHTML = data;
               document.querySelector('.modal-title').innerHTML = '';
               toggleModal();
            }
         );
      })
   }









   var editSbDisabledButtons = document.querySelectorAll('.edit-sb-disabled');
   for (var i = 0; i < editSbDisabledButtons.length; i++) {
      editSbDisabledButtons[i].addEventListener('click', function(event){
         event.preventDefault();
         BX.ajax.post(
            '/local/tools/expertCommitee/backend/catchRequest.php',
            {
               action:'activateSb',
               requestId:event.target.dataset.id
            },
            function (data){
               var statusBar = document.querySelector('.status-bar[data-requestId="'+event.target.dataset.id+'"]');
               statusBar.classList.remove('new-request');
               statusBar.classList.add('progress-request');
               statusBar.innerText = 'На рассмотрении';
               var responsible = document.querySelector('.sb-responsible[data-id="'+event.target.dataset.id+'"]');
               responsible.innerText = data;
               var editButton = event.target
               editButton.classList.remove('edit-sb-disabled');
               editButton.innerText = 'Приступить';
               editButton.onclick = function(){
                  BX.ajax.post(
                     '/local/tools/expertCommitee/securityPage.php',
                     {
                        action:'create',
                     },
                     function (data){
                        var permissionContainer = document.querySelector('.modal-body');
                        permissionContainer.classList.remove('h-100');
                        permissionContainer.innerHTML = data;
                        permissionContainer.classList.add('overflow-y-auto','h-100');
                        permissionContainer.innerHTML = data;
                        document.querySelector('.modal-title').classList.add('hidden');
                        toggleModal();
                     }
                  );
               };
            }
         );
      })
   }
   var editSbButtons = document.querySelectorAll('.edit-sb');
   for (var i = 0; i < editSbButtons.length; i++) {
      editSbButtons[i].addEventListener('click', function(event){
         event.preventDefault();
         BX.ajax.post(
            '/local/tools/expertCommitee/securityPage.php',
            {
               requestId:event.target.dataset.id,
               'action':event.target.dataset.action
            },
            function (data){
               var permissionContainer = document.querySelector('.modal-body');
               permissionContainer.classList.remove('h-100');
               permissionContainer.innerHTML = data;
               permissionContainer.classList.add('overflow-y-auto','h-100');
               permissionContainer.innerHTML = data;
               document.querySelector('.modal-title').classList.add('hidden');
               document.querySelector('.modal-container').classList.add('h-100','w-11/12');
               toggleModal();
            }
         );
      })
   }

   var editLegalDisabledButtons = document.querySelectorAll('.edit-legal-disabled');
   for (var i = 0; i < editLegalDisabledButtons.length; i++) {
      editLegalDisabledButtons[i].addEventListener('click', function(event){
         event.preventDefault();
         BX.ajax.post(
            '/local/tools/expertCommitee/backend/catchRequest.php',
            {
               action:'activateLegal',
               requestId:event.target.dataset.id
            },
            function (data){

               var statusBar = document.querySelector('.status-bar[data-requestId="'+event.target.dataset.id+'"]');

               statusBar.classList.remove('new-request');
               statusBar.classList.add('progress-request');
               statusBar.innerText = 'На рассмотрении';
               var responsible = document.querySelector('.legal-responsible[data-id="'+event.target.dataset.id+'"]');
               responsible.innerText = data;

               var editButton = event.target;
               editButton.classList.remove('edit-legal-disabled');
               editButton.innerText = 'Приступить';
               editButton.onclick = function(){
                  BX.ajax.post(
                     '/local/tools/expertCommitee/legalPage.php',
                     {
                        action:'create',
                        requestId:event.target.dataset.id
                     },
                     function (data){
                        var permissionContainer = document.querySelector('.modal-body');
                        console.log(permissionContainer)
                        permissionContainer.classList.add('overflow-y-auto','h-100');
                        permissionContainer.innerHTML = data;
                        document.querySelector('.modal-container').classList.add('h-100','w-11/12');
                        //document.querySelector('.modal-title').classList.add('hidden');
                        toggleModal();
                     }
                  );
               };
            }
         );
      })
   }
   var editLegalButtons = document.querySelectorAll('.edit-legal');
   for (var i = 0; i < editLegalButtons.length; i++) {
      editLegalButtons[i].addEventListener('click', function(event){
         event.preventDefault();
         BX.ajax.post(
            '/local/tools/expertCommitee/legalPage.php',
            {
               requestId:event.target.dataset.id,
               'action':event.target.dataset.action
            },
            function (data){
               var permissionContainer = document.querySelector('.modal-body');
               permissionContainer.classList.remove('h-100');
               permissionContainer.innerHTML = data;
               permissionContainer.classList.add('overflow-y-auto','h-100');
               permissionContainer.innerHTML = data;
               document.querySelector('.modal-title').classList.add('hidden');
               document.querySelector('.modal-container').classList.add('h-100','w-11/12');
               toggleModal();
            }
         );
      })
   }

   var editFinanceDisabledButtons = document.querySelectorAll('.edit-finance-disabled');
   for (var i = 0; i < editFinanceDisabledButtons.length; i++) {
      editFinanceDisabledButtons[i].addEventListener('click', function(event){
         event.preventDefault();
         BX.ajax.post(
            '/local/tools/expertCommitee/backend/catchRequest.php',
            {
               action:'activateFinance',
               requestId:event.target.dataset.id,
            },
            function (data){

               var statusBar = document.querySelector('.status-bar[data-requestId="'+event.target.dataset.id+'"]');

               statusBar.classList.remove('new-request');
               statusBar.classList.add('progress-request');
               statusBar.innerText = 'На рассмотрении';
               var responsible = document.querySelector('.finance-responsible[data-id="'+event.target.dataset.id+'"]');

               responsible.innerText = data;

               var editButton = event.target
               editButton.classList.remove('edit-finance-disabled');
               editButton.innerText = 'Приступить';
               editButton.onclick = function(){
                  BX.ajax.post(
                     '/local/tools/expertCommitee/financePage.php',
                     {
                        action:'create',
                        requestId:event.target.dataset.id
                     },
                     function (data){
                        var permissionContainer = document.querySelector('.modal-body');
                        permissionContainer.classList.remove('h-100');
                        permissionContainer.innerHTML = data;
                        permissionContainer.classList.add('overflow-y-auto','h-100');
                        permissionContainer.innerHTML = data;
                        document.querySelector('.modal-title').classList.add('hidden');
                        toggleModal();
                     }
                  );
               };
            }
         );
      })
   }
   var editFinanceButtons = document.querySelectorAll('.edit-finance');
   for (var i = 0; i < editFinanceButtons.length; i++) {
      editFinanceButtons[i].addEventListener('click', function(event){
         event.preventDefault();
         BX.ajax.post(
            '/local/tools/expertCommitee/financePage.php',
            {
               requestId:event.target.dataset.id,
               'action':event.target.dataset.action
            },
            function (data){
               var permissionContainer = document.querySelector('.modal-body');
               permissionContainer.classList.remove('h-100');
               permissionContainer.innerHTML = data;
               permissionContainer.classList.add('overflow-y-auto','h-100');
               permissionContainer.innerHTML = data;
               document.querySelector('.modal-title').classList.add('hidden');
               document.querySelector('.modal-container').classList.add('h-100','w-11/12');
               toggleModal();
            }
         );
      })
   }

   var showConclusionButtons = document.querySelectorAll('.show-conclusion');
   for(var i = 0; i < showConclusionButtons.length; i++){
      showConclusionButtons[i].addEventListener('click', function(event){
         event.preventDefault();
         BX.ajax.post(
            '/local/tools/expertCommitee/disabledMode/'+event.target.dataset.service+'.php',
            {
               requestId:event.target.dataset.id,
            },
            function (data){
               var permissionContainer = document.querySelector('.modal-body');
               permissionContainer.classList.remove('h-100');
               permissionContainer.innerHTML = data;
               permissionContainer.classList.add('overflow-y-auto','h-100');
               permissionContainer.innerHTML = data;
               document.querySelector('.modal-title').classList.add('hidden');
               toggleModal();
            }
         );
      })
   }




   var acceptionsBlocks = document.querySelectorAll('[name*=result]');
   for (var i = 0; i < acceptionsBlocks.length; i++) {
      acceptionsBlocks[i].addEventListener('change', function(e){
         var iconContainers = document.querySelectorAll('.result'+e.target.dataset.id);
         [...iconContainers].forEach(function(el){
            el.classList.remove('bg-gray-1000');
         });
         console.log(e.target)
         e.target.parentElement.classList.add('bg-gray-1000');
      })
   }

});
function toggleModal () {
   const body = document.querySelector('body')
   const modal = document.querySelector('.modal')
   modal.classList.toggle('opacity-0')
   modal.classList.toggle('pointer-events-none')
   body.classList.toggle('modal-active')
}
function cancel(){

   BX.ajax.post(
      '/local/tools/expertCommitee/cancelPermission.php',
      {
      },
      function (data){
         var permissionContainer = document.querySelector('.modal-body');
         permissionContainer.classList.remove('h-100');
         permissionContainer.classList.add('overflow-y-auto','h-100');
         permissionContainer.innerHTML = data;
         var modalContainer = document.querySelector('.modal-container');
         modalContainer.classList.remove('md:max-w-6xl');
         modalContainer.classList.add('md:max-w-4xl');
      }
   );
}
function deletePermission(Id){
   BX.ajax.post(
      '/local/tools/expertCommitee/permission.php',
      {
         permissionId:Id,
         action:'delete',
      },
      function (data){
         var permissionContainer = document.querySelector('.permission-container');
         permissionContainer.innerHTML = data;
      }
   );
}
function editPermission(Id){
   BX.ajax.post(
      '/local/tools/expertCommitee/permission.php',
      {
         action:'edit',
         permissionId:Id,
      },
      function (data){
         var modalContainer = document.querySelector('.modal-container');
         modalContainer.classList.remove('md:max-w-4xl');
         modalContainer.classList.add('md:max-w-6xl');
         var permissionContainer = document.querySelector('.modal-body');
         permissionContainer.innerHTML = data;
         var permsModal  = document.querySelector('.perms-modal');
         permsModal.classList.remove('z-9998');
         permsModal.style = 'z-index:1249!important;';
      }
   );
}
function addList(Id){
   BX.ajax.post(
      '/local/tools/expertCommitee/managerPage.php',
      {
         action:'edit',
         permissionId:Id,
      },
      function (data){
         var modalContainer = document.querySelector('.modal-container');
         modalContainer.classList.remove('md:max-w-6xl');
         modalContainer.classList.add('md:max-w-4xl');
         var permissionContainer = document.querySelector('.modal-body');
         permissionContainer.classList.add('overflow-y-auto','h-100');
         permissionContainer.innerHTML = data;
         document.querySelector('.modal-title').innerHTML = '';
      }
   );
}
function managerListPopup(btn){
   var requestId = btn.dataset.id;
   BX.ajax.post(
      '/local/tools/expertCommitee/managerList.php',
      {
         requestId:requestId,
      },
      function (data){
         var permissionContainer = document.querySelector('.modal-body');
         permissionContainer.classList.remove('h-100');
         permissionContainer.innerHTML = data;
         var modalContainer = document.querySelector('.modal-container');
         modalContainer.classList.remove('md:max-w-6xl','md:max-w-4xl');
         modalContainer.classList.add('md:max-w-2xl','rounded-2xl');
         document.querySelector('.modal-title').classList.add('hidden');
         toggleModal();
      }
   );
}
function addPermission(){
   BX.ajax.post(
      '/local/tools/expertCommitee/permission.php',
      {
         action:'create',
      },
      function (data){
         var permissionContainer = document.querySelector('.permission-container');
         permissionContainer.innerHTML = data;
      }
   );
}
function copyToClipboard(text) {
   const elem = document.createElement('textarea');
   text.replaceAll('/','\\');
   elem.value = text;
   document.body.appendChild(elem);
   elem.select();
   document.execCommand('copy');
   document.body.removeChild(elem);
}
function sendToCommitte(btn){
   var requestId = btn.dataset.id;
   var committee = document.querySelector('.commitee[data-id="'+requestId+'"]').value;
   BX.ajax.post(
      '/local/tools/expertCommitee/backend/commitee.php',
      {
         requestId:requestId,
         committee:committee,
      },
      function(res){
         var parentRequestContainer = document.querySelector('.parent-request-container[data-request-id="'+requestId+'"]');
         parentRequestContainer.remove();
         window.location.reload();
      }
   )
}

function sendCommiteeMackResult(el){
   var action = document.querySelector('[name="result'+el.dataset.id+'"]:checked').value;
   var commiteeResult = document.querySelector('textarea[data-id="'+el.dataset.id+'"]');
   if(action === 'edit' || action === 'decline'){

      if(commiteeResult.value === ''){
         commiteeResult.classList.remove('border-gray-1000');
         commiteeResult.classList.add('border-red-600');
         return false;
      }
   }
   BX.ajax.post(
      '/local/tools/expertCommitee/backend/commiteeAction.php',
      {
         action:action,
         requestId:el.dataset.id,
         comment:commiteeResult.value,
         commitee:'mack'
      },
      function(result){
         console.log(result)
         var data = JSON.parse(result)
         if(data.status == 'accept'){
            var parentContainer = el.parentElement.parentElement
            var status = parentContainer.querySelector('.status')
            var dejstvije = parentContainer.querySelector('.dejstvije');
            dejstvije.innerHTML = '-';
            var conclusion = parentContainer.querySelector('.conclusion');
            conclusion.innerHTML = data.conclusion;
            var conclusionTime = parentContainer.querySelector('.conclusion-time');
            conclusionTime.innerHTML = data.actionDate;
            status.innerHTML = `
            <div class="flex justify-center items-center">
               <svg width="37" height="30" viewBox="0 0 37 30" fill="none" xmlns="http://www.w3.org/2000/svg">
               <rect width="37" height="30" rx="6" fill="#91B23B"/>
               <path d="M24.1394 19.1181C24.1394 19.0978 24.1362 19.0785 24.1335 19.0589V14.5025H24.1333L24.1335 14.5009C24.1335 14.4446 24.1224 14.3888 24.1008 14.3367C24.0792 14.2846 24.0476 14.2373 24.0077 14.1974C23.9679 14.1576 23.9205 14.126 23.8684 14.1044C23.8164 14.0828 23.7605 14.0718 23.7042 14.0718C23.6962 14.0718 23.6889 14.0736 23.6811 14.0741H22.869V14.0772C22.758 14.0803 22.6525 14.1263 22.5748 14.2057C22.4971 14.285 22.4533 14.3914 22.4525 14.5025H22.4512V19.1567H22.4584C22.4681 19.2601 22.5151 19.3564 22.5907 19.4277C22.6663 19.4989 22.7652 19.5402 22.869 19.5437V19.5474H23.7103C23.9472 19.5474 24.1394 19.3552 24.1394 19.1181Z" fill="white"/>
               <path d="M21.6014 19.8323V13.6566L21.6016 13.6551C21.6016 13.5987 21.5905 13.5429 21.5689 13.4908C21.5473 13.4387 21.5157 13.3914 21.4758 13.3515C21.4359 13.3117 21.3886 13.2801 21.3365 13.2585C21.2844 13.2369 21.2286 13.2259 21.1722 13.2259C21.1644 13.2259 21.157 13.2277 21.1492 13.2283H19.4684C19.4434 13.1882 19.4129 13.1519 19.3777 13.1205L17.7057 10.2241L17.7015 10.2266C17.5339 9.92994 17.2195 9.72974 16.8587 9.72974C16.3221 9.72974 15.887 10.1711 15.887 10.7155H15.8771V13.2281H13.5614V13.2311C13.4504 13.2342 13.3449 13.2803 13.2672 13.3596C13.1895 13.4389 13.1457 13.5454 13.1449 13.6564H13.1436V18.4206H13.1446C13.1446 18.4753 13.1656 18.5299 13.2068 18.5719L13.2063 18.5724L14.8587 20.2248L14.8628 20.2207C14.9103 20.2598 14.9687 20.2745 15.0264 20.2668H21.1362C21.1485 20.2678 21.1604 20.2705 21.1729 20.2705C21.2827 20.2703 21.3882 20.228 21.4677 20.1522C21.5471 20.0765 21.5944 19.9731 21.5999 19.8635H21.6012V19.8503C21.6012 19.8472 21.6021 19.8444 21.6021 19.8413C21.6021 19.8383 21.6016 19.8354 21.6014 19.8323ZM13.6501 18.4206L13.6572 18.4286L13.6492 18.4206H13.6501Z" fill="white"/>
               </svg>
            </div>
`;
         }else if(data.status == 'decline'){
            var parentContainer = el.parentElement.parentElement
            var status = parentContainer.querySelector('.status')
            var dejstvije = parentContainer.querySelector('.dejstvije');
            dejstvije.innerHTML = '-';
            var conclusion = parentContainer.querySelector('.conclusion');
            conclusion.innerHTML = data.conclusion;
            var conclusionTime = parentContainer.querySelector('.conclusion-time');
            conclusionTime.innerHTML = data.actionDate;
            status.innerHTML = `
            <div class="flex justify-center items-center">
               <svg width="37" height="30" viewBox="0 0 37 30" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect width="37" height="30" rx="6" fill="#EA4335"/>
<path d="M13.1399 11.8819C13.1399 11.9022 13.1431 11.9215 13.1458 11.9411L13.1458 16.4975L13.146 16.4975L13.1458 16.4991C13.1458 16.5554 13.1569 16.6112 13.1785 16.6633C13.2001 16.7154 13.2317 16.7627 13.2716 16.8026C13.3114 16.8424 13.3588 16.874 13.4108 16.8956C13.4629 16.9172 13.5188 16.9282 13.5751 16.9282C13.5831 16.9282 13.5904 16.9264 13.5982 16.9259L14.4103 16.9259L14.4103 16.9228C14.5213 16.9197 14.6268 16.8737 14.7045 16.7943C14.7822 16.715 14.826 16.6086 14.8268 16.4975L14.8281 16.4975L14.8281 11.8433L14.8208 11.8433C14.8112 11.7399 14.7642 11.6436 14.6886 11.5723C14.613 11.5011 14.5141 11.4598 14.4103 11.4563L14.4103 11.4526L13.569 11.4526C13.3321 11.4526 13.1399 11.6448 13.1399 11.8819Z" fill="white"/>
<path d="M15.6779 11.1677L15.6779 17.3434L15.6777 17.3449C15.6777 17.4013 15.6888 17.4571 15.7104 17.5092C15.732 17.5613 15.7636 17.6086 15.8035 17.6485C15.8434 17.6883 15.8907 17.7199 15.9428 17.7415C15.9949 17.7631 16.0507 17.7741 16.1071 17.7741C16.1149 17.7741 16.1223 17.7723 16.1301 17.7717L17.8109 17.7717C17.8359 17.8118 17.8664 17.8481 17.9016 17.8795L19.5736 20.7759L19.5778 20.7734C19.7454 21.0701 20.0598 21.2703 20.4206 21.2703C20.9572 21.2703 21.3923 20.8289 21.3923 20.2845L21.4021 20.2845L21.4021 17.7719L23.7179 17.7719L23.7179 17.7689C23.8289 17.7658 23.9344 17.7197 24.0121 17.6404C24.0898 17.5611 24.1336 17.4546 24.1344 17.3436L24.1357 17.3436L24.1357 12.5794L24.1347 12.5794C24.1347 12.5247 24.1137 12.4701 24.0725 12.4281L24.073 12.4276L22.4206 10.7752L22.4165 10.7793C22.369 10.7402 22.3106 10.7255 22.2529 10.7332L16.1431 10.7332C16.1308 10.7322 16.1189 10.7295 16.1064 10.7295C15.9966 10.7297 15.8911 10.772 15.8116 10.8478C15.7322 10.9235 15.6848 11.0269 15.6794 11.1365L15.6781 11.1365L15.6781 11.1497C15.6781 11.1528 15.6772 11.1556 15.6772 11.1587C15.6772 11.1617 15.6777 11.1646 15.6779 11.1677ZM23.6292 12.5794L23.6221 12.5714L23.6301 12.5794L23.6292 12.5794Z" fill="white"/>
</svg>
            </div>
`;
         } else if(data.status == 'ignore'){
            var parentContainer = el.parentElement.parentElement
            var status = parentContainer.querySelector('.status')
            var dejstvije = parentContainer.querySelector('.dejstvije');
            dejstvije.innerHTML = '-';
            var conclusion = parentContainer.querySelector('.conclusion');
            conclusion.innerHTML = data.conclusion;
            var conclusionTime = parentContainer.querySelector('.conclusion-time');
            conclusionTime.innerHTML = data.actionDate;
            status.innerHTML = `
            <div class="flex justify-center items-center">
               <svg width="37" height="30" viewBox="0 0 37 30" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect width="37" height="30" rx="6" fill="#FFC700"/>
<path d="M23.0302 12.2308C23.4126 12.2308 23.7225 11.9208 23.7225 11.5385C23.7225 11.1561 23.4126 10.8462 23.0302 10.8462C22.6478 10.8462 22.3379 11.1561 22.3379 11.5385C22.3379 11.9208 22.6478 12.2308 23.0302 12.2308Z" fill="white"/>
<path d="M21.1845 10.8462C21.5668 10.8462 21.8768 10.5362 21.8768 10.1539C21.8768 9.7715 21.5668 9.46155 21.1845 9.46155C20.8021 9.46155 20.4922 9.7715 20.4922 10.1539C20.4922 10.5362 20.8021 10.8462 21.1845 10.8462Z" fill="white"/>
<path d="M19.3378 10.3846C19.7202 10.3846 20.0301 10.0747 20.0301 9.69231C20.0301 9.30996 19.7202 9 19.3378 9C18.9555 9 18.6455 9.30996 18.6455 9.69231C18.6455 10.0747 18.9555 10.3846 19.3378 10.3846Z" fill="white"/>
<path d="M17.4921 11.3077C17.8745 11.3077 18.1844 10.9978 18.1844 10.6154C18.1844 10.2331 17.8745 9.9231 17.4921 9.9231C17.1098 9.9231 16.7998 10.2331 16.7998 10.6154C16.7998 10.9978 17.1098 11.3077 17.4921 11.3077Z" fill="white"/>
<path d="M22.3379 11.5384V14.5384C22.3379 14.6658 22.2345 14.7692 22.1071 14.7692C21.9797 14.7692 21.8763 14.6658 21.8763 14.5384V10.1538H20.4917V14.0769C20.4917 14.2043 20.3883 14.3076 20.2609 14.3076C20.1335 14.3076 20.0302 14.2043 20.0302 14.0769V9.69226H18.6455V14.0769C18.6455 14.2043 18.5422 14.3076 18.4148 14.3076C18.2874 14.3076 18.184 14.2043 18.184 14.0769V10.6153H16.7994V16.3846L15.8648 15.1056C15.5879 14.6787 15.0474 14.5356 14.6514 14.7803C14.2568 15.0304 14.1589 15.576 14.4322 16.0015C14.4322 16.0015 15.9395 18.2829 16.582 19.2595C17.2245 20.2361 18.2652 21 20.2115 21C23.434 21 23.7225 18.5113 23.7225 17.7692C23.7225 17.027 23.7225 11.5384 23.7225 11.5384H22.3379Z" fill="white"/>
</svg>

            </div>
`;
         }
      }
   );
}
function sendCommiteeBackResult(el){
   var action = document.querySelector('[name="result'+el.dataset.id+'"]:checked').value;
   var commiteeResult = document.querySelector('textarea[data-id="'+el.dataset.id+'"]');
   if(action === 'edit' || action === 'decline'){

      if(commiteeResult.value === ''){
         commiteeResult.classList.remove('border-gray-1000');
         commiteeResult.classList.add('border-red-600');
         return false;
      }
   }
   BX.ajax.post(
      '/local/tools/expertCommitee/backend/commiteeAction.php',
      {
         action:action,
         requestId:el.dataset.id,
         comment:commiteeResult.value,
         commitee:'back'
      },
      function(result){
         console.log(result)
         var data = JSON.parse(result)
         if(data.status == 'accept'){
            var parentContainer = el.parentElement.parentElement
            var status = parentContainer.querySelector('.status')
            var dejstvije = parentContainer.querySelector('.dejstvije');
            dejstvije.innerHTML = '-';
            var conclusion = parentContainer.querySelector('.conclusion');
            conclusion.innerHTML = data.conclusion;
            var conclusionTime = parentContainer.querySelector('.conclusion-time');
            conclusionTime.innerHTML = data.actionDate;
            status.innerHTML = `
            <div class="flex justify-center items-center">
               <svg width="37" height="30" viewBox="0 0 37 30" fill="none" xmlns="http://www.w3.org/2000/svg">
               <rect width="37" height="30" rx="6" fill="#91B23B"/>
               <path d="M24.1394 19.1181C24.1394 19.0978 24.1362 19.0785 24.1335 19.0589V14.5025H24.1333L24.1335 14.5009C24.1335 14.4446 24.1224 14.3888 24.1008 14.3367C24.0792 14.2846 24.0476 14.2373 24.0077 14.1974C23.9679 14.1576 23.9205 14.126 23.8684 14.1044C23.8164 14.0828 23.7605 14.0718 23.7042 14.0718C23.6962 14.0718 23.6889 14.0736 23.6811 14.0741H22.869V14.0772C22.758 14.0803 22.6525 14.1263 22.5748 14.2057C22.4971 14.285 22.4533 14.3914 22.4525 14.5025H22.4512V19.1567H22.4584C22.4681 19.2601 22.5151 19.3564 22.5907 19.4277C22.6663 19.4989 22.7652 19.5402 22.869 19.5437V19.5474H23.7103C23.9472 19.5474 24.1394 19.3552 24.1394 19.1181Z" fill="white"/>
               <path d="M21.6014 19.8323V13.6566L21.6016 13.6551C21.6016 13.5987 21.5905 13.5429 21.5689 13.4908C21.5473 13.4387 21.5157 13.3914 21.4758 13.3515C21.4359 13.3117 21.3886 13.2801 21.3365 13.2585C21.2844 13.2369 21.2286 13.2259 21.1722 13.2259C21.1644 13.2259 21.157 13.2277 21.1492 13.2283H19.4684C19.4434 13.1882 19.4129 13.1519 19.3777 13.1205L17.7057 10.2241L17.7015 10.2266C17.5339 9.92994 17.2195 9.72974 16.8587 9.72974C16.3221 9.72974 15.887 10.1711 15.887 10.7155H15.8771V13.2281H13.5614V13.2311C13.4504 13.2342 13.3449 13.2803 13.2672 13.3596C13.1895 13.4389 13.1457 13.5454 13.1449 13.6564H13.1436V18.4206H13.1446C13.1446 18.4753 13.1656 18.5299 13.2068 18.5719L13.2063 18.5724L14.8587 20.2248L14.8628 20.2207C14.9103 20.2598 14.9687 20.2745 15.0264 20.2668H21.1362C21.1485 20.2678 21.1604 20.2705 21.1729 20.2705C21.2827 20.2703 21.3882 20.228 21.4677 20.1522C21.5471 20.0765 21.5944 19.9731 21.5999 19.8635H21.6012V19.8503C21.6012 19.8472 21.6021 19.8444 21.6021 19.8413C21.6021 19.8383 21.6016 19.8354 21.6014 19.8323ZM13.6501 18.4206L13.6572 18.4286L13.6492 18.4206H13.6501Z" fill="white"/>
               </svg>
            </div>
`;
         }else if(data.status == 'decline'){
            var parentContainer = el.parentElement.parentElement
            var status = parentContainer.querySelector('.status')
            var dejstvije = parentContainer.querySelector('.dejstvije');
            dejstvije.innerHTML = '-';
            var conclusion = parentContainer.querySelector('.conclusion');
            conclusion.innerHTML = data.conclusion;
            var conclusionTime = parentContainer.querySelector('.conclusion-time');
            conclusionTime.innerHTML = data.actionDate;
            status.innerHTML = `
            <div class="flex justify-center items-center">
               <svg width="37" height="30" viewBox="0 0 37 30" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect width="37" height="30" rx="6" fill="#EA4335"/>
<path d="M13.1399 11.8819C13.1399 11.9022 13.1431 11.9215 13.1458 11.9411L13.1458 16.4975L13.146 16.4975L13.1458 16.4991C13.1458 16.5554 13.1569 16.6112 13.1785 16.6633C13.2001 16.7154 13.2317 16.7627 13.2716 16.8026C13.3114 16.8424 13.3588 16.874 13.4108 16.8956C13.4629 16.9172 13.5188 16.9282 13.5751 16.9282C13.5831 16.9282 13.5904 16.9264 13.5982 16.9259L14.4103 16.9259L14.4103 16.9228C14.5213 16.9197 14.6268 16.8737 14.7045 16.7943C14.7822 16.715 14.826 16.6086 14.8268 16.4975L14.8281 16.4975L14.8281 11.8433L14.8208 11.8433C14.8112 11.7399 14.7642 11.6436 14.6886 11.5723C14.613 11.5011 14.5141 11.4598 14.4103 11.4563L14.4103 11.4526L13.569 11.4526C13.3321 11.4526 13.1399 11.6448 13.1399 11.8819Z" fill="white"/>
<path d="M15.6779 11.1677L15.6779 17.3434L15.6777 17.3449C15.6777 17.4013 15.6888 17.4571 15.7104 17.5092C15.732 17.5613 15.7636 17.6086 15.8035 17.6485C15.8434 17.6883 15.8907 17.7199 15.9428 17.7415C15.9949 17.7631 16.0507 17.7741 16.1071 17.7741C16.1149 17.7741 16.1223 17.7723 16.1301 17.7717L17.8109 17.7717C17.8359 17.8118 17.8664 17.8481 17.9016 17.8795L19.5736 20.7759L19.5778 20.7734C19.7454 21.0701 20.0598 21.2703 20.4206 21.2703C20.9572 21.2703 21.3923 20.8289 21.3923 20.2845L21.4021 20.2845L21.4021 17.7719L23.7179 17.7719L23.7179 17.7689C23.8289 17.7658 23.9344 17.7197 24.0121 17.6404C24.0898 17.5611 24.1336 17.4546 24.1344 17.3436L24.1357 17.3436L24.1357 12.5794L24.1347 12.5794C24.1347 12.5247 24.1137 12.4701 24.0725 12.4281L24.073 12.4276L22.4206 10.7752L22.4165 10.7793C22.369 10.7402 22.3106 10.7255 22.2529 10.7332L16.1431 10.7332C16.1308 10.7322 16.1189 10.7295 16.1064 10.7295C15.9966 10.7297 15.8911 10.772 15.8116 10.8478C15.7322 10.9235 15.6848 11.0269 15.6794 11.1365L15.6781 11.1365L15.6781 11.1497C15.6781 11.1528 15.6772 11.1556 15.6772 11.1587C15.6772 11.1617 15.6777 11.1646 15.6779 11.1677ZM23.6292 12.5794L23.6221 12.5714L23.6301 12.5794L23.6292 12.5794Z" fill="white"/>
</svg>
            </div>
`;
         } else if(data.status == 'ignore'){
            var parentContainer = el.parentElement.parentElement
            var status = parentContainer.querySelector('.status')
            var dejstvije = parentContainer.querySelector('.dejstvije');
            dejstvije.innerHTML = '-';
            var conclusion = parentContainer.querySelector('.conclusion');
            conclusion.innerHTML = data.conclusion;
            var conclusionTime = parentContainer.querySelector('.conclusion-time');
            conclusionTime.innerHTML = data.actionDate;
            status.innerHTML = `
            <div class="flex justify-center items-center">
               <svg width="37" height="30" viewBox="0 0 37 30" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect width="37" height="30" rx="6" fill="#FFC700"/>
<path d="M23.0302 12.2308C23.4126 12.2308 23.7225 11.9208 23.7225 11.5385C23.7225 11.1561 23.4126 10.8462 23.0302 10.8462C22.6478 10.8462 22.3379 11.1561 22.3379 11.5385C22.3379 11.9208 22.6478 12.2308 23.0302 12.2308Z" fill="white"/>
<path d="M21.1845 10.8462C21.5668 10.8462 21.8768 10.5362 21.8768 10.1539C21.8768 9.7715 21.5668 9.46155 21.1845 9.46155C20.8021 9.46155 20.4922 9.7715 20.4922 10.1539C20.4922 10.5362 20.8021 10.8462 21.1845 10.8462Z" fill="white"/>
<path d="M19.3378 10.3846C19.7202 10.3846 20.0301 10.0747 20.0301 9.69231C20.0301 9.30996 19.7202 9 19.3378 9C18.9555 9 18.6455 9.30996 18.6455 9.69231C18.6455 10.0747 18.9555 10.3846 19.3378 10.3846Z" fill="white"/>
<path d="M17.4921 11.3077C17.8745 11.3077 18.1844 10.9978 18.1844 10.6154C18.1844 10.2331 17.8745 9.9231 17.4921 9.9231C17.1098 9.9231 16.7998 10.2331 16.7998 10.6154C16.7998 10.9978 17.1098 11.3077 17.4921 11.3077Z" fill="white"/>
<path d="M22.3379 11.5384V14.5384C22.3379 14.6658 22.2345 14.7692 22.1071 14.7692C21.9797 14.7692 21.8763 14.6658 21.8763 14.5384V10.1538H20.4917V14.0769C20.4917 14.2043 20.3883 14.3076 20.2609 14.3076C20.1335 14.3076 20.0302 14.2043 20.0302 14.0769V9.69226H18.6455V14.0769C18.6455 14.2043 18.5422 14.3076 18.4148 14.3076C18.2874 14.3076 18.184 14.2043 18.184 14.0769V10.6153H16.7994V16.3846L15.8648 15.1056C15.5879 14.6787 15.0474 14.5356 14.6514 14.7803C14.2568 15.0304 14.1589 15.576 14.4322 16.0015C14.4322 16.0015 15.9395 18.2829 16.582 19.2595C17.2245 20.2361 18.2652 21 20.2115 21C23.434 21 23.7225 18.5113 23.7225 17.7692C23.7225 17.027 23.7225 11.5384 23.7225 11.5384H22.3379Z" fill="white"/>
</svg>

            </div>
`;
         }
      }
   );
}
function sendBackCommiteeHeadresult(btn){
   BX.ajax.post(
      '/local/tools/expertCommitee/backend/commiteeAction.php',
      {
         commitee:'backHead',
         requestId:btn.dataset.id,
         action:btn.dataset.action
      },
      function (data){
         window.location.reload();
      }
   );
}
function sendUnderCommiteeHeadresult(btn){
   var comment = document.querySelector('textarea[data-id="'+btn.dataset.id+'"]').value;
   if(btn.dataset.action === 'sendToBack'){
      var status = document.getElementsByName('result'+btn.dataset.id);
      var action;
      var flag = true;
      for(let i = 0; i < status.length; i++){
         if(status[i].checked){
            action=status[i].value;
            flag = false;
            break;
         }
      }
      if(flag){
         alert('Необходимо согласовать либо отклонить заявку перед отправкой на БЭК!');
         return false;
      }
      if(action === 'decline' && comment === ''){
         alert('Необходимо указать в заключении причину отказа!');
         return false;
      }
   }
   if(btn.dataset.action === 'decline' && comment === ''){
      alert('Необходимо указать в заключении причину отказа!');
      return false;
   }

   BX.ajax.post(
      '/local/tools/expertCommitee/backend/commiteeAction.php',
      {
         commitee:'underwriting',
         requestId:btn.dataset.id,
         action:btn.dataset.action,
         comment:comment
      },
      function (data){
         window.location.reload();
      }
   );
}
var Litepicker=function(t){var e={};function i(n){if(e[n])return e[n].exports;var o=e[n]={i:n,l:!1,exports:{}};return t[n].call(o.exports,o,o.exports,i),o.l=!0,o.exports}return i.m=t,i.c=e,i.d=function(t,e,n){i.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},i.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},i.t=function(t,e){if(1&e&&(t=i(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(i.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var o in t)i.d(n,o,function(e){return t[e]}.bind(null,o));return n},i.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return i.d(e,"a",e),e},i.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},i.p="",i(i.s=4)}([function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n=function(){function t(e,i,n){void 0===e&&(e=null),void 0===i&&(i=null),void 0===n&&(n="en-US"),this.dateInstance="object"==typeof i&&null!==i?i.parse(e instanceof t?e.clone().toJSDate():e):"string"==typeof i?t.parseDateTime(e,i,n):e?t.parseDateTime(e):t.parseDateTime(new Date),this.lang=n}return t.parseDateTime=function(e,i,n){if(void 0===i&&(i="YYYY-MM-DD"),void 0===n&&(n="en-US"),!e)return new Date(NaN);if(e instanceof Date)return new Date(e);if(e instanceof t)return e.clone().toJSDate();if(/^-?\d{10,}$/.test(e))return t.getDateZeroTime(new Date(Number(e)));if("string"==typeof e){for(var o=[],s=null;null!=(s=t.regex.exec(i));)"\\"!==s[1]&&o.push(s);if(o.length){var r={year:null,month:null,shortMonth:null,longMonth:null,day:null,value:""};o[0].index>0&&(r.value+=".*?");for(var a=0,l=Object.entries(o);a<l.length;a++){var c=l[a],h=c[0],p=c[1],d=Number(h),u=t.formatPatterns(p[0],n),m=u.group,f=u.pattern;r[m]=d+1,r.value+=f,r.value+=".*?"}var g=new RegExp("^"+r.value+"$");if(g.test(e)){var v=g.exec(e),y=Number(v[r.year]),b=null;r.month?b=Number(v[r.month])-1:r.shortMonth?b=t.shortMonths(n).indexOf(v[r.shortMonth]):r.longMonth&&(b=t.longMonths(n).indexOf(v[r.longMonth]));var k=Number(v[r.day])||1;return new Date(y,b,k,0,0,0,0)}}}return t.getDateZeroTime(new Date(e))},t.convertArray=function(e,i){return e.map((function(e){return e instanceof Array?e.map((function(e){return new t(e,i)})):new t(e,i)}))},t.getDateZeroTime=function(t){return new Date(t.getFullYear(),t.getMonth(),t.getDate(),0,0,0,0)},t.shortMonths=function(e){return t.MONTH_JS.map((function(t){return new Date(2019,t).toLocaleString(e,{month:"short"})}))},t.longMonths=function(e){return t.MONTH_JS.map((function(t){return new Date(2019,t).toLocaleString(e,{month:"long"})}))},t.formatPatterns=function(e,i){switch(e){case"YY":case"YYYY":return{group:"year",pattern:"(\\d{"+e.length+"})"};case"M":return{group:"month",pattern:"(\\d{1,2})"};case"MM":return{group:"month",pattern:"(\\d{2})"};case"MMM":return{group:"shortMonth",pattern:"("+t.shortMonths(i).join("|")+")"};case"MMMM":return{group:"longMonth",pattern:"("+t.longMonths(i).join("|")+")"};case"D":return{group:"day",pattern:"(\\d{1,2})"};case"DD":return{group:"day",pattern:"(\\d{2})"}}},t.prototype.toJSDate=function(){return this.dateInstance},t.prototype.toLocaleString=function(t,e){return this.dateInstance.toLocaleString(t,e)},t.prototype.toDateString=function(){return this.dateInstance.toDateString()},t.prototype.getSeconds=function(){return this.dateInstance.getSeconds()},t.prototype.getDay=function(){return this.dateInstance.getDay()},t.prototype.getTime=function(){return this.dateInstance.getTime()},t.prototype.getDate=function(){return this.dateInstance.getDate()},t.prototype.getMonth=function(){return this.dateInstance.getMonth()},t.prototype.getFullYear=function(){return this.dateInstance.getFullYear()},t.prototype.setMonth=function(t){return this.dateInstance.setMonth(t)},t.prototype.setHours=function(t,e,i,n){void 0===t&&(t=0),void 0===e&&(e=0),void 0===i&&(i=0),void 0===n&&(n=0),this.dateInstance.setHours(t,e,i,n)},t.prototype.setSeconds=function(t){return this.dateInstance.setSeconds(t)},t.prototype.setDate=function(t){return this.dateInstance.setDate(t)},t.prototype.setFullYear=function(t){return this.dateInstance.setFullYear(t)},t.prototype.getWeek=function(t){var e=new Date(this.timestamp()),i=(this.getDay()+(7-t))%7;e.setDate(e.getDate()-i);var n=e.getTime();return e.setMonth(0,1),e.getDay()!==t&&e.setMonth(0,1+(4-e.getDay()+7)%7),1+Math.ceil((n-e.getTime())/6048e5)},t.prototype.clone=function(){return new t(this.toJSDate())},t.prototype.isBetween=function(t,e,i){switch(void 0===i&&(i="()"),i){default:case"()":return this.timestamp()>t.getTime()&&this.timestamp()<e.getTime();case"[)":return this.timestamp()>=t.getTime()&&this.timestamp()<e.getTime();case"(]":return this.timestamp()>t.getTime()&&this.timestamp()<=e.getTime();case"[]":return this.timestamp()>=t.getTime()&&this.timestamp()<=e.getTime()}},t.prototype.isBefore=function(t,e){switch(void 0===e&&(e="seconds"),e){case"second":case"seconds":return t.getTime()>this.getTime();case"day":case"days":return new Date(t.getFullYear(),t.getMonth(),t.getDate()).getTime()>new Date(this.getFullYear(),this.getMonth(),this.getDate()).getTime();case"month":case"months":return new Date(t.getFullYear(),t.getMonth(),1).getTime()>new Date(this.getFullYear(),this.getMonth(),1).getTime();case"year":case"years":return t.getFullYear()>this.getFullYear()}throw new Error("isBefore: Invalid unit!")},t.prototype.isSameOrBefore=function(t,e){switch(void 0===e&&(e="seconds"),e){case"second":case"seconds":return t.getTime()>=this.getTime();case"day":case"days":return new Date(t.getFullYear(),t.getMonth(),t.getDate()).getTime()>=new Date(this.getFullYear(),this.getMonth(),this.getDate()).getTime();case"month":case"months":return new Date(t.getFullYear(),t.getMonth(),1).getTime()>=new Date(this.getFullYear(),this.getMonth(),1).getTime()}throw new Error("isSameOrBefore: Invalid unit!")},t.prototype.isAfter=function(t,e){switch(void 0===e&&(e="seconds"),e){case"second":case"seconds":return this.getTime()>t.getTime();case"day":case"days":return new Date(this.getFullYear(),this.getMonth(),this.getDate()).getTime()>new Date(t.getFullYear(),t.getMonth(),t.getDate()).getTime();case"month":case"months":return new Date(this.getFullYear(),this.getMonth(),1).getTime()>new Date(t.getFullYear(),t.getMonth(),1).getTime();case"year":case"years":return this.getFullYear()>t.getFullYear()}throw new Error("isAfter: Invalid unit!")},t.prototype.isSameOrAfter=function(t,e){switch(void 0===e&&(e="seconds"),e){case"second":case"seconds":return this.getTime()>=t.getTime();case"day":case"days":return new Date(this.getFullYear(),this.getMonth(),this.getDate()).getTime()>=new Date(t.getFullYear(),t.getMonth(),t.getDate()).getTime();case"month":case"months":return new Date(this.getFullYear(),this.getMonth(),1).getTime()>=new Date(t.getFullYear(),t.getMonth(),1).getTime()}throw new Error("isSameOrAfter: Invalid unit!")},t.prototype.isSame=function(t,e){switch(void 0===e&&(e="seconds"),e){case"second":case"seconds":return this.getTime()===t.getTime();case"day":case"days":return new Date(this.getFullYear(),this.getMonth(),this.getDate()).getTime()===new Date(t.getFullYear(),t.getMonth(),t.getDate()).getTime();case"month":case"months":return new Date(this.getFullYear(),this.getMonth(),1).getTime()===new Date(t.getFullYear(),t.getMonth(),1).getTime()}throw new Error("isSame: Invalid unit!")},t.prototype.add=function(t,e){switch(void 0===e&&(e="seconds"),e){case"second":case"seconds":this.setSeconds(this.getSeconds()+t);break;case"day":case"days":this.setDate(this.getDate()+t);break;case"month":case"months":this.setMonth(this.getMonth()+t)}return this},t.prototype.subtract=function(t,e){switch(void 0===e&&(e="seconds"),e){case"second":case"seconds":this.setSeconds(this.getSeconds()-t);break;case"day":case"days":this.setDate(this.getDate()-t);break;case"month":case"months":this.setMonth(this.getMonth()-t)}return this},t.prototype.diff=function(t,e){void 0===e&&(e="seconds");switch(e){default:case"second":case"seconds":return this.getTime()-t.getTime();case"day":case"days":return Math.round((this.timestamp()-t.getTime())/864e5);case"month":case"months":}},t.prototype.format=function(e,i){if(void 0===i&&(i="en-US"),"object"==typeof e)return e.output(this.clone().toJSDate());for(var n="",o=[],s=null;null!=(s=t.regex.exec(e));)"\\"!==s[1]&&o.push(s);if(o.length){o[0].index>0&&(n+=e.substring(0,o[0].index));for(var r=0,a=Object.entries(o);r<a.length;r++){var l=a[r],c=l[0],h=l[1],p=Number(c);n+=this.formatTokens(h[0],i),o[p+1]&&(n+=e.substring(h.index+h[0].length,o[p+1].index)),p===o.length-1&&(n+=e.substring(h.index+h[0].length))}}return n.replace(/\\/g,"")},t.prototype.timestamp=function(){return new Date(this.getFullYear(),this.getMonth(),this.getDate(),0,0,0,0).getTime()},t.prototype.formatTokens=function(e,i){switch(e){case"YY":return String(this.getFullYear()).slice(-2);case"YYYY":return String(this.getFullYear());case"M":return String(this.getMonth()+1);case"MM":return("0"+(this.getMonth()+1)).slice(-2);case"MMM":return t.shortMonths(i)[this.getMonth()];case"MMMM":return t.longMonths(i)[this.getMonth()];case"D":return String(this.getDate());case"DD":return("0"+this.getDate()).slice(-2);default:return""}},t.regex=/(\\)?(Y{2,4}|M{1,4}|D{1,2}|d{1,4})/g,t.MONTH_JS=[0,1,2,3,4,5,6,7,8,9,10,11],t}();e.DateTime=n},function(t,e,i){"use strict";var n,o=this&&this.__extends||(n=function(t,e){return(n=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(t,e){t.__proto__=e}||function(t,e){for(var i in e)e.hasOwnProperty(i)&&(t[i]=e[i])})(t,e)},function(t,e){function i(){this.constructor=t}n(t,e),t.prototype=null===e?Object.create(e):(i.prototype=e.prototype,new i)}),s=this&&this.__spreadArrays||function(){for(var t=0,e=0,i=arguments.length;e<i;e++)t+=arguments[e].length;var n=Array(t),o=0;for(e=0;e<i;e++)for(var s=arguments[e],r=0,a=s.length;r<a;r++,o++)n[o]=s[r];return n};Object.defineProperty(e,"__esModule",{value:!0});var r=i(5),a=i(0),l=i(3),c=i(2),h=function(t){function e(e){var i=t.call(this,e)||this;return i.preventClick=!1,i.bindEvents(),i}return o(e,t),e.prototype.scrollToDate=function(t){if(this.options.scrollToDate){var e=this.options.startDate instanceof a.DateTime?this.options.startDate.clone():null,i=this.options.endDate instanceof a.DateTime?this.options.endDate.clone():null;!this.options.startDate||t&&t!==this.options.element?t&&this.options.endDate&&t===this.options.elementEnd&&(i.setDate(1),this.options.numberOfMonths>1&&i.isAfter(e)&&i.setMonth(i.getMonth()-(this.options.numberOfMonths-1)),this.calendars[0]=i.clone()):(e.setDate(1),this.calendars[0]=e.clone())}},e.prototype.bindEvents=function(){document.addEventListener("click",this.onClick.bind(this),!0),this.ui=document.createElement("div"),this.ui.className=l.litepicker,this.ui.style.display="none",this.ui.addEventListener("mouseenter",this.onMouseEnter.bind(this),!0),this.ui.addEventListener("mouseleave",this.onMouseLeave.bind(this),!1),this.options.autoRefresh?(this.options.element instanceof HTMLElement&&this.options.element.addEventListener("keyup",this.onInput.bind(this),!0),this.options.elementEnd instanceof HTMLElement&&this.options.elementEnd.addEventListener("keyup",this.onInput.bind(this),!0)):(this.options.element instanceof HTMLElement&&this.options.element.addEventListener("change",this.onInput.bind(this),!0),this.options.elementEnd instanceof HTMLElement&&this.options.elementEnd.addEventListener("change",this.onInput.bind(this),!0)),this.options.parentEl?this.options.parentEl instanceof HTMLElement?this.options.parentEl.appendChild(this.ui):document.querySelector(this.options.parentEl).appendChild(this.ui):this.options.inlineMode?this.options.element instanceof HTMLInputElement?this.options.element.parentNode.appendChild(this.ui):this.options.element.appendChild(this.ui):document.body.appendChild(this.ui),this.updateInput(),this.init(),"function"==typeof this.options.setup&&this.options.setup.call(this,this),this.render(),this.options.inlineMode&&this.show()},e.prototype.updateInput=function(){if(this.options.element instanceof HTMLInputElement){var t=this.options.startDate,e=this.options.endDate;if(this.options.singleMode&&t)this.options.element.value=t.format(this.options.format,this.options.lang);else if(!this.options.singleMode&&t&&e){var i=t.format(this.options.format,this.options.lang),n=e.format(this.options.format,this.options.lang);this.options.elementEnd instanceof HTMLInputElement?(this.options.element.value=i,this.options.elementEnd.value=n):this.options.element.value=""+i+this.options.delimiter+n}t||e||(this.options.element.value="",this.options.elementEnd instanceof HTMLInputElement&&(this.options.elementEnd.value=""))}},e.prototype.isSamePicker=function(t){return t.closest("."+l.litepicker)===this.ui},e.prototype.shouldShown=function(t){return!t.disabled&&(t===this.options.element||this.options.elementEnd&&t===this.options.elementEnd)},e.prototype.shouldResetDatePicked=function(){return this.options.singleMode||2===this.datePicked.length},e.prototype.shouldSwapDatePicked=function(){return 2===this.datePicked.length&&this.datePicked[0].getTime()>this.datePicked[1].getTime()},e.prototype.shouldCheckLockDays=function(){return this.options.disallowLockDaysInRange&&2===this.datePicked.length},e.prototype.onClick=function(t){var e=t.target;if(e&&this.ui)if(this.shouldShown(e))this.show(e);else if(e.closest("."+l.litepicker)||!this.isShowning()){if(this.isSamePicker(e))if(this.emit("before:click",e),this.preventClick)this.preventClick=!1;else{if(e.classList.contains(l.dayItem)){if(t.preventDefault(),e.classList.contains(l.isLocked))return;if(this.shouldResetDatePicked()&&(this.datePicked.length=0),this.datePicked[this.datePicked.length]=new a.DateTime(e.dataset.time),this.shouldSwapDatePicked()){var i=this.datePicked[1].clone();this.datePicked[1]=this.datePicked[0].clone(),this.datePicked[0]=i.clone()}if(this.shouldCheckLockDays())c.rangeIsLocked(this.datePicked,this.options)&&(this.emit("error:range",this.datePicked),this.datePicked.length=0);return this.render(),this.emit.apply(this,s(["preselect"],s(this.datePicked).map((function(t){return t.clone()})))),void(this.options.autoApply&&(this.options.singleMode&&this.datePicked.length?(this.setDate(this.datePicked[0]),this.hide()):this.options.singleMode||2!==this.datePicked.length||(this.setDateRange(this.datePicked[0],this.datePicked[1]),this.hide())))}if(e.classList.contains(l.buttonPreviousMonth)){t.preventDefault();var n=0,o=this.options.switchingMonths||this.options.numberOfMonths;if(this.options.splitView){var r=e.closest("."+l.monthItem);n=c.findNestedMonthItem(r),o=1}return this.calendars[n].setMonth(this.calendars[n].getMonth()-o),this.gotoDate(this.calendars[n],n),void this.emit("change:month",this.calendars[n],n)}if(e.classList.contains(l.buttonNextMonth)){t.preventDefault();n=0,o=this.options.switchingMonths||this.options.numberOfMonths;if(this.options.splitView){r=e.closest("."+l.monthItem);n=c.findNestedMonthItem(r),o=1}return this.calendars[n].setMonth(this.calendars[n].getMonth()+o),this.gotoDate(this.calendars[n],n),void this.emit("change:month",this.calendars[n],n)}e.classList.contains(l.buttonCancel)&&(t.preventDefault(),this.hide(),this.emit("button:cancel")),e.classList.contains(l.buttonApply)&&(t.preventDefault(),this.options.singleMode&&this.datePicked.length?this.setDate(this.datePicked[0]):this.options.singleMode||2!==this.datePicked.length||this.setDateRange(this.datePicked[0],this.datePicked[1]),this.hide(),this.emit("button:apply",this.options.startDate,this.options.endDate))}}else this.hide()},e.prototype.showTooltip=function(t,e){var i=this.ui.querySelector("."+l.containerTooltip);i.style.visibility="visible",i.innerHTML=e;var n=this.ui.getBoundingClientRect(),o=i.getBoundingClientRect(),s=t.getBoundingClientRect(),r=s.top,a=s.left;if(this.options.inlineMode&&this.options.parentEl){var c=this.ui.parentNode.getBoundingClientRect();r-=c.top,a-=c.left}else r-=n.top,a-=n.left;r-=o.height,a-=o.width/2,a+=s.width/2,i.style.top=r+"px",i.style.left=a+"px",this.emit("tooltip",i,t)},e.prototype.hideTooltip=function(){this.ui.querySelector("."+l.containerTooltip).style.visibility="hidden"},e.prototype.shouldAllowMouseEnter=function(t){return!this.options.singleMode&&!t.classList.contains(l.isLocked)},e.prototype.shouldAllowRepick=function(){return this.options.elementEnd&&this.options.allowRepick&&this.options.startDate&&this.options.endDate},e.prototype.isDayItem=function(t){return t.classList.contains(l.dayItem)},e.prototype.onMouseEnter=function(t){var e=this,i=t.target;if(this.isDayItem(i)&&this.shouldAllowMouseEnter(i)){if(this.shouldAllowRepick()&&(this.triggerElement===this.options.element?this.datePicked[0]=this.options.endDate.clone():this.triggerElement===this.options.elementEnd&&(this.datePicked[0]=this.options.startDate.clone())),1!==this.datePicked.length)return;var n=this.ui.querySelector("."+l.dayItem+'[data-time="'+this.datePicked[0].getTime()+'"]'),o=this.datePicked[0].clone(),s=new a.DateTime(i.dataset.time),r=!1;if(o.getTime()>s.getTime()){var c=o.clone();o=s.clone(),s=c.clone(),r=!0}if(Array.prototype.slice.call(this.ui.querySelectorAll("."+l.dayItem)).forEach((function(t){var i=new a.DateTime(t.dataset.time),n=e.renderDay(i);i.isBetween(o,s)&&n.classList.add(l.isInRange),t.className=n.className})),i.classList.add(l.isEndDate),r?(n&&n.classList.add(l.isFlipped),i.classList.add(l.isFlipped)):(n&&n.classList.remove(l.isFlipped),i.classList.remove(l.isFlipped)),this.options.showTooltip){var h=s.diff(o,"day")+1;if("function"==typeof this.options.tooltipNumber&&(h=this.options.tooltipNumber.call(this,h)),h>0){var p=this.pluralSelector(h),d=h+" "+(this.options.tooltipText[p]?this.options.tooltipText[p]:"["+p+"]");this.showTooltip(i,d);var u=window.navigator.userAgent,m=/(iphone|ipad)/i.test(u),f=/OS 1([0-2])/i.test(u);m&&f&&i.dispatchEvent(new Event("click"))}else this.hideTooltip()}}},e.prototype.onMouseLeave=function(t){t.target;this.options.allowRepick&&(!this.options.allowRepick||this.options.startDate||this.options.endDate)&&(this.datePicked.length=0,this.render())},e.prototype.onInput=function(t){var e=this.parseInput(),i=e[0],n=e[1],o=this.options.format;if(this.options.elementEnd?i instanceof a.DateTime&&n instanceof a.DateTime&&i.format(o)===this.options.element.value&&n.format(o)===this.options.elementEnd.value:this.options.singleMode?i instanceof a.DateTime&&i.format(o)===this.options.element.value:i instanceof a.DateTime&&n instanceof a.DateTime&&""+i.format(o)+this.options.delimiter+n.format(o)===this.options.element.value){if(n&&i.getTime()>n.getTime()){var s=i.clone();i=n.clone(),n=s.clone()}this.options.startDate=new a.DateTime(i,this.options.format,this.options.lang),n&&(this.options.endDate=new a.DateTime(n,this.options.format,this.options.lang)),this.updateInput(),this.render();var r=i.clone(),l=0;(this.options.elementEnd?i.format(o)===t.target.value:t.target.value.startsWith(i.format(o)))||(r=n.clone(),l=this.options.numberOfMonths-1),this.emit("selected",this.getStartDate(),this.getEndDate()),this.gotoDate(r,l)}},e}(r.Calendar);e.Litepicker=h},function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.findNestedMonthItem=function(t){for(var e=t.parentNode.childNodes,i=0;i<e.length;i+=1){if(e.item(i)===t)return i}return 0},e.dateIsLocked=function(t,e,i){var n=!1;return e.lockDays.length&&(n=e.lockDays.filter((function(i){return i instanceof Array?t.isBetween(i[0],i[1],e.lockDaysInclusivity):i.isSame(t,"day")})).length),n||"function"!=typeof e.lockDaysFilter||(n=e.lockDaysFilter.call(this,t.clone(),null,i)),n},e.rangeIsLocked=function(t,e){var i=!1;return e.lockDays.length&&(i=e.lockDays.filter((function(i){if(i instanceof Array){var n=t[0].toDateString()===i[0].toDateString()&&t[1].toDateString()===i[1].toDateString();return i[0].isBetween(t[0],t[1],e.lockDaysInclusivity)||i[1].isBetween(t[0],t[1],e.lockDaysInclusivity)||n}return i.isBetween(t[0],t[1],e.lockDaysInclusivity)})).length),i||"function"!=typeof e.lockDaysFilter||(i=e.lockDaysFilter.call(this,t[0].clone(),t[1].clone(),t)),i}},function(t,e,i){var n=i(8);"string"==typeof n&&(n=[[t.i,n,""]]);var o={insert:function(t){var e=document.querySelector("head"),i=window._lastElementInsertedByStyleLoader;window.disableLitepickerStyles||(i?i.nextSibling?e.insertBefore(t,i.nextSibling):e.appendChild(t):e.insertBefore(t,e.firstChild),window._lastElementInsertedByStyleLoader=t)},singleton:!1};i(10)(n,o);n.locals&&(t.exports=n.locals)},function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n=i(1);e.Litepicker=n.Litepicker,i(11),window.Litepicker=n.Litepicker,e.default=n.Litepicker},function(t,e,i){"use strict";var n,o=this&&this.__extends||(n=function(t,e){return(n=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(t,e){t.__proto__=e}||function(t,e){for(var i in e)e.hasOwnProperty(i)&&(t[i]=e[i])})(t,e)},function(t,e){function i(){this.constructor=t}n(t,e),t.prototype=null===e?Object.create(e):(i.prototype=e.prototype,new i)});Object.defineProperty(e,"__esModule",{value:!0});var s=i(6),r=i(0),a=i(3),l=i(2),c=function(t){function e(e){return t.call(this,e)||this}return o(e,t),e.prototype.render=function(){var t=this;this.emit("before:render",this.ui);var e=document.createElement("div");e.className=a.containerMain;var i=document.createElement("div");i.className=a.containerMonths,a["columns"+this.options.numberOfColumns]&&(i.classList.remove(a.columns2,a.columns3,a.columns4),i.classList.add(a["columns"+this.options.numberOfColumns])),this.options.splitView&&i.classList.add(a.splitView),this.options.showWeekNumbers&&i.classList.add(a.showWeekNumbers);for(var n=this.calendars[0].clone(),o=n.getMonth(),s=n.getMonth()+this.options.numberOfMonths,r=0,l=o;l<s;l+=1){var c=n.clone();c.setDate(1),c.setHours(0,0,0,0),this.options.splitView?c=this.calendars[r].clone():c.setMonth(l),i.appendChild(this.renderMonth(c,r)),r+=1}if(this.ui.innerHTML="",e.appendChild(i),this.options.resetButton){var h=void 0;"function"==typeof this.options.resetButton?h=this.options.resetButton.call(this):((h=document.createElement("button")).type="button",h.className=a.resetButton,h.innerHTML=this.options.buttonText.reset),h.addEventListener("click",(function(e){e.preventDefault(),t.clearSelection()})),e.querySelector("."+a.monthItem+":last-child").querySelector("."+a.monthItemHeader).appendChild(h)}this.ui.appendChild(e),this.options.autoApply&&!this.options.footerHTML||this.ui.appendChild(this.renderFooter()),this.options.showTooltip&&this.ui.appendChild(this.renderTooltip()),this.ui.dataset.plugins=(this.options.plugins||[]).join("|"),this.emit("render",this.ui)},e.prototype.renderMonth=function(t,e){var i=this,n=t.clone(),o=32-new Date(n.getFullYear(),n.getMonth(),32).getDate(),s=document.createElement("div");s.className=a.monthItem;var c=document.createElement("div");c.className=a.monthItemHeader;var h=document.createElement("div");if(this.options.dropdowns.months){var p=document.createElement("select");p.className=a.monthItemName;for(var d=0;d<12;d+=1){var u=document.createElement("option"),m=new r.DateTime(new Date(t.getFullYear(),d,2,0,0,0)),f=new r.DateTime(new Date(t.getFullYear(),d,1,0,0,0));u.value=String(d),u.text=m.toLocaleString(this.options.lang,{month:"long"}),u.disabled=this.options.minDate&&f.isBefore(new r.DateTime(this.options.minDate),"month")||this.options.maxDate&&f.isAfter(new r.DateTime(this.options.maxDate),"month"),u.selected=f.getMonth()===t.getMonth(),p.appendChild(u)}p.addEventListener("change",(function(t){var e=t.target,n=0;if(i.options.splitView){var o=e.closest("."+a.monthItem);n=l.findNestedMonthItem(o)}i.calendars[n].setMonth(Number(e.value)),i.render(),i.emit("change:month",i.calendars[n],n,t)})),h.appendChild(p)}else{(m=document.createElement("strong")).className=a.monthItemName,m.innerHTML=t.toLocaleString(this.options.lang,{month:"long"}),h.appendChild(m)}if(this.options.dropdowns.years){var g=document.createElement("select");g.className=a.monthItemYear;var v=this.options.dropdowns.minYear,y=this.options.dropdowns.maxYear?this.options.dropdowns.maxYear:(new Date).getFullYear();if(t.getFullYear()>y)(u=document.createElement("option")).value=String(t.getFullYear()),u.text=String(t.getFullYear()),u.selected=!0,u.disabled=!0,g.appendChild(u);for(d=y;d>=v;d-=1){var u=document.createElement("option"),b=new r.DateTime(new Date(d,0,1,0,0,0));u.value=String(d),u.text=String(d),u.disabled=this.options.minDate&&b.isBefore(new r.DateTime(this.options.minDate),"year")||this.options.maxDate&&b.isAfter(new r.DateTime(this.options.maxDate),"year"),u.selected=t.getFullYear()===d,g.appendChild(u)}if(t.getFullYear()<v)(u=document.createElement("option")).value=String(t.getFullYear()),u.text=String(t.getFullYear()),u.selected=!0,u.disabled=!0,g.appendChild(u);if("asc"===this.options.dropdowns.years){var k=Array.prototype.slice.call(g.childNodes).reverse();g.innerHTML="",k.forEach((function(t){t.innerHTML=t.value,g.appendChild(t)}))}g.addEventListener("change",(function(t){var e=t.target,n=0;if(i.options.splitView){var o=e.closest("."+a.monthItem);n=l.findNestedMonthItem(o)}i.calendars[n].setFullYear(Number(e.value)),i.render(),i.emit("change:year",i.calendars[n],n,t)})),h.appendChild(g)}else{var D=document.createElement("span");D.className=a.monthItemYear,D.innerHTML=String(t.getFullYear()),h.appendChild(D)}var w=document.createElement("button");w.type="button",w.className=a.buttonPreviousMonth,w.innerHTML=this.options.buttonText.previousMonth;var x=document.createElement("button");x.type="button",x.className=a.buttonNextMonth,x.innerHTML=this.options.buttonText.nextMonth,c.appendChild(w),c.appendChild(h),c.appendChild(x),this.options.minDate&&n.isSameOrBefore(new r.DateTime(this.options.minDate),"month")&&s.classList.add(a.noPreviousMonth),this.options.maxDate&&n.isSameOrAfter(new r.DateTime(this.options.maxDate),"month")&&s.classList.add(a.noNextMonth);var M=document.createElement("div");M.className=a.monthItemWeekdaysRow,this.options.showWeekNumbers&&(M.innerHTML="<div>W</div>");for(var _=1;_<=7;_+=1){var T=3+this.options.firstDay+_,L=document.createElement("div");L.innerHTML=this.weekdayName(T),L.title=this.weekdayName(T,"long"),M.appendChild(L)}var E=document.createElement("div");E.className=a.containerDays;var S=this.calcSkipDays(n);this.options.showWeekNumbers&&S&&E.appendChild(this.renderWeekNumber(n));for(var I=0;I<S;I+=1){var P=document.createElement("div");E.appendChild(P)}for(I=1;I<=o;I+=1)n.setDate(I),this.options.showWeekNumbers&&n.getDay()===this.options.firstDay&&E.appendChild(this.renderWeekNumber(n)),E.appendChild(this.renderDay(n));return s.appendChild(c),s.appendChild(M),s.appendChild(E),this.emit("render:month",s,t),s},e.prototype.renderDay=function(t){t.setHours();var e=document.createElement("div");if(e.className=a.dayItem,e.innerHTML=String(t.getDate()),e.dataset.time=String(t.getTime()),t.toDateString()===(new Date).toDateString()&&e.classList.add(a.isToday),this.datePicked.length)this.datePicked[0].toDateString()===t.toDateString()&&(e.classList.add(a.isStartDate),this.options.singleMode&&e.classList.add(a.isEndDate)),2===this.datePicked.length&&this.datePicked[1].toDateString()===t.toDateString()&&e.classList.add(a.isEndDate),2===this.datePicked.length&&t.isBetween(this.datePicked[0],this.datePicked[1])&&e.classList.add(a.isInRange);else if(this.options.startDate){var i=this.options.startDate,n=this.options.endDate;i.toDateString()===t.toDateString()&&(e.classList.add(a.isStartDate),this.options.singleMode&&e.classList.add(a.isEndDate)),n&&n.toDateString()===t.toDateString()&&e.classList.add(a.isEndDate),i&&n&&t.isBetween(i,n)&&e.classList.add(a.isInRange)}if(this.options.minDate&&t.isBefore(new r.DateTime(this.options.minDate))&&e.classList.add(a.isLocked),this.options.maxDate&&t.isAfter(new r.DateTime(this.options.maxDate))&&e.classList.add(a.isLocked),this.options.minDays>1&&1===this.datePicked.length){var o=this.options.minDays-1,s=this.datePicked[0].clone().subtract(o,"day"),c=this.datePicked[0].clone().add(o,"day");t.isBetween(s,this.datePicked[0],"(]")&&e.classList.add(a.isLocked),t.isBetween(this.datePicked[0],c,"[)")&&e.classList.add(a.isLocked)}if(this.options.maxDays&&1===this.datePicked.length){var h=this.options.maxDays;s=this.datePicked[0].clone().subtract(h,"day"),c=this.datePicked[0].clone().add(h,"day");t.isSameOrBefore(s)&&e.classList.add(a.isLocked),t.isSameOrAfter(c)&&e.classList.add(a.isLocked)}(this.options.selectForward&&1===this.datePicked.length&&t.isBefore(this.datePicked[0])&&e.classList.add(a.isLocked),this.options.selectBackward&&1===this.datePicked.length&&t.isAfter(this.datePicked[0])&&e.classList.add(a.isLocked),l.dateIsLocked(t,this.options,this.datePicked)&&e.classList.add(a.isLocked),this.options.highlightedDays.length)&&(this.options.highlightedDays.filter((function(e){return e instanceof Array?t.isBetween(e[0],e[1],"[]"):e.isSame(t,"day")})).length&&e.classList.add(a.isHighlighted));return e.tabIndex=e.classList.contains("is-locked")?-1:0,this.emit("render:day",e,t),e},e.prototype.renderFooter=function(){var t=document.createElement("div");if(t.className=a.containerFooter,this.options.footerHTML?t.innerHTML=this.options.footerHTML:t.innerHTML='\n      <span class="'+a.previewDateRange+'"></span>\n      <button type="button" class="'+a.buttonCancel+'">'+this.options.buttonText.cancel+'</button>\n      <button type="button" class="'+a.buttonApply+'">'+this.options.buttonText.apply+"</button>\n      ",this.options.singleMode){if(1===this.datePicked.length){var e=this.datePicked[0].format(this.options.format,this.options.lang);t.querySelector("."+a.previewDateRange).innerHTML=e}}else if(1===this.datePicked.length&&t.querySelector("."+a.buttonApply).setAttribute("disabled",""),2===this.datePicked.length){e=this.datePicked[0].format(this.options.format,this.options.lang);var i=this.datePicked[1].format(this.options.format,this.options.lang);t.querySelector("."+a.previewDateRange).innerHTML=""+e+this.options.delimiter+i}return this.emit("render:footer",t),t},e.prototype.renderWeekNumber=function(t){var e=document.createElement("div"),i=t.getWeek(this.options.firstDay);return e.className=a.weekNumber,e.innerHTML=53===i&&0===t.getMonth()?"53 / 1":i,e},e.prototype.renderTooltip=function(){var t=document.createElement("div");return t.className=a.containerTooltip,t},e.prototype.weekdayName=function(t,e){return void 0===e&&(e="short"),new Date(1970,0,t,12,0,0,0).toLocaleString(this.options.lang,{weekday:e})},e.prototype.calcSkipDays=function(t){var e=t.getDay()-this.options.firstDay;return e<0&&(e+=7),e},e}(s.LPCore);e.Calendar=c},function(t,e,i){"use strict";var n,o=this&&this.__extends||(n=function(t,e){return(n=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(t,e){t.__proto__=e}||function(t,e){for(var i in e)e.hasOwnProperty(i)&&(t[i]=e[i])})(t,e)},function(t,e){function i(){this.constructor=t}n(t,e),t.prototype=null===e?Object.create(e):(i.prototype=e.prototype,new i)}),s=this&&this.__assign||function(){return(s=Object.assign||function(t){for(var e,i=1,n=arguments.length;i<n;i++)for(var o in e=arguments[i])Object.prototype.hasOwnProperty.call(e,o)&&(t[o]=e[o]);return t}).apply(this,arguments)};Object.defineProperty(e,"__esModule",{value:!0});var r=i(7),a=i(0),l=i(1),c=function(t){function e(e){var i=t.call(this)||this;i.datePicked=[],i.calendars=[],i.options={element:null,elementEnd:null,parentEl:null,firstDay:1,format:"YYYY-MM-DD",lang:"en-US",delimiter:" - ",numberOfMonths:1,numberOfColumns:1,startDate:null,endDate:null,zIndex:9999,position:"auto",selectForward:!1,selectBackward:!1,splitView:!1,inlineMode:!1,singleMode:!0,autoApply:!0,allowRepick:!1,showWeekNumbers:!1,showTooltip:!0,scrollToDate:!0,mobileFriendly:!0,resetButton:!1,autoRefresh:!1,lockDaysFormat:"YYYY-MM-DD",lockDays:[],disallowLockDaysInRange:!1,lockDaysInclusivity:"[]",highlightedDaysFormat:"YYYY-MM-DD",highlightedDays:[],dropdowns:{minYear:1990,maxYear:null,months:!1,years:!1},buttonText:{apply:"Apply",cancel:"Cancel",previousMonth:'<svg width="11" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M7.919 0l2.748 2.667L5.333 8l5.334 5.333L7.919 16 0 8z" fill-rule="nonzero"/></svg>',nextMonth:'<svg width="11" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M2.748 16L0 13.333 5.333 8 0 2.667 2.748 0l7.919 8z" fill-rule="nonzero"/></svg>',reset:'<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">\n        <path d="M0 0h24v24H0z" fill="none"/>\n        <path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/>\n      </svg>'},tooltipText:{one:"day",other:"days"}},i.options=s(s({},i.options),e.element.dataset),Object.keys(i.options).forEach((function(t){"true"!==i.options[t]&&"false"!==i.options[t]||(i.options[t]="true"===i.options[t])}));var n=s(s({},i.options.dropdowns),e.dropdowns),o=s(s({},i.options.buttonText),e.buttonText),r=s(s({},i.options.tooltipText),e.tooltipText);i.options=s(s({},i.options),e),i.options.dropdowns=s({},n),i.options.buttonText=s({},o),i.options.tooltipText=s({},r),i.options.elementEnd||(i.options.allowRepick=!1),i.options.lockDays.length&&(i.options.lockDays=a.DateTime.convertArray(i.options.lockDays,i.options.lockDaysFormat)),i.options.highlightedDays.length&&(i.options.highlightedDays=a.DateTime.convertArray(i.options.highlightedDays,i.options.highlightedDaysFormat));var l=i.parseInput(),c=l[0],h=l[1];i.options.startDate&&(i.options.singleMode||i.options.endDate)&&(c=new a.DateTime(i.options.startDate,i.options.format,i.options.lang)),c&&i.options.endDate&&(h=new a.DateTime(i.options.endDate,i.options.format,i.options.lang)),c instanceof a.DateTime&&!isNaN(c.getTime())&&(i.options.startDate=c),i.options.startDate&&h instanceof a.DateTime&&!isNaN(h.getTime())&&(i.options.endDate=h),!i.options.singleMode||i.options.startDate instanceof a.DateTime||(i.options.startDate=null),i.options.singleMode||i.options.startDate instanceof a.DateTime&&i.options.endDate instanceof a.DateTime||(i.options.startDate=null,i.options.endDate=null);for(var p=0;p<i.options.numberOfMonths;p+=1){var d=i.options.startDate instanceof a.DateTime?i.options.startDate.clone():new a.DateTime;if(!i.options.startDate&&(0===p||i.options.splitView)){var u=i.options.maxDate?new a.DateTime(i.options.maxDate):null,m=i.options.minDate?new a.DateTime(i.options.minDate):null,f=i.options.numberOfMonths-1;m&&u&&d.isAfter(u)?(d=m.clone()).setDate(1):!m&&u&&d.isAfter(u)&&((d=u.clone()).setDate(1),d.setMonth(d.getMonth()-f))}d.setDate(1),d.setMonth(d.getMonth()+p),i.calendars[p]=d}if(i.options.showTooltip)if(i.options.tooltipPluralSelector)i.pluralSelector=i.options.tooltipPluralSelector;else try{var g=new Intl.PluralRules(i.options.lang);i.pluralSelector=g.select.bind(g)}catch(t){i.pluralSelector=function(t){return 0===Math.abs(t)?"one":"other"}}return i}return o(e,t),e.add=function(t,e){l.Litepicker.prototype[t]=e},e.prototype.DateTime=function(t,e){return t?new a.DateTime(t,e):new a.DateTime},e.prototype.init=function(){var t=this;this.options.plugins&&this.options.plugins.length&&this.options.plugins.forEach((function(e){l.Litepicker.prototype.hasOwnProperty(e)?l.Litepicker.prototype[e].init.call(t,t):console.warn("Litepicker: plugin «"+e+"» not found.")}))},e.prototype.parseInput=function(){var t=this.options.delimiter,e=new RegExp(""+t),i=this.options.element instanceof HTMLInputElement?this.options.element.value.split(t):[];if(this.options.elementEnd){if(this.options.element instanceof HTMLInputElement&&this.options.element.value.length&&this.options.elementEnd instanceof HTMLInputElement&&this.options.elementEnd.value.length)return[new a.DateTime(this.options.element.value,this.options.format),new a.DateTime(this.options.elementEnd.value,this.options.format)]}else if(this.options.singleMode){if(this.options.element instanceof HTMLInputElement&&this.options.element.value.length)return[new a.DateTime(this.options.element.value,this.options.format)]}else if(this.options.element instanceof HTMLInputElement&&e.test(this.options.element.value)&&i.length&&i.length%2==0){var n=i.slice(0,i.length/2).join(t),o=i.slice(i.length/2).join(t);return[new a.DateTime(n,this.options.format),new a.DateTime(o,this.options.format)]}return[]},e.prototype.isShowning=function(){return this.ui&&"none"!==this.ui.style.display},e.prototype.findPosition=function(t){var e=t.getBoundingClientRect(),i=this.ui.getBoundingClientRect(),n=this.options.position.split(" "),o=window.scrollX||window.pageXOffset,s=window.scrollY||window.pageYOffset,r=0,a=0;if("auto"!==n[0]&&/top|bottom/.test(n[0]))r=e[n[0]]+s,"top"===n[0]&&(r-=i.height);else{r=e.bottom+s;var l=e.bottom+i.height>window.innerHeight,c=e.top+s-i.height>=i.height;l&&c&&(r=e.top+s-i.height)}if(/left|right/.test(n[0])||n[1]&&"auto"!==n[1]&&/left|right/.test(n[1]))a=/left|right/.test(n[0])?e[n[0]]+o:e[n[1]]+o,"right"!==n[0]&&"right"!==n[1]||(a-=i.width);else{a=e.left+o;l=e.left+i.width>window.innerWidth;var h=e.right+o-i.width>=0;l&&h&&(a=e.right+o-i.width)}return{left:a,top:r}},e}(r.EventEmitter);e.LPCore=c},function(t,e,i){"use strict";var n,o="object"==typeof Reflect?Reflect:null,s=o&&"function"==typeof o.apply?o.apply:function(t,e,i){return Function.prototype.apply.call(t,e,i)};n=o&&"function"==typeof o.ownKeys?o.ownKeys:Object.getOwnPropertySymbols?function(t){return Object.getOwnPropertyNames(t).concat(Object.getOwnPropertySymbols(t))}:function(t){return Object.getOwnPropertyNames(t)};var r=Number.isNaN||function(t){return t!=t};function a(){a.init.call(this)}t.exports=a,a.EventEmitter=a,a.prototype._events=void 0,a.prototype._eventsCount=0,a.prototype._maxListeners=void 0;var l=10;function c(t){return void 0===t._maxListeners?a.defaultMaxListeners:t._maxListeners}function h(t,e,i,n){var o,s,r,a;if("function"!=typeof i)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof i);if(void 0===(s=t._events)?(s=t._events=Object.create(null),t._eventsCount=0):(void 0!==s.newListener&&(t.emit("newListener",e,i.listener?i.listener:i),s=t._events),r=s[e]),void 0===r)r=s[e]=i,++t._eventsCount;else if("function"==typeof r?r=s[e]=n?[i,r]:[r,i]:n?r.unshift(i):r.push(i),(o=c(t))>0&&r.length>o&&!r.warned){r.warned=!0;var l=new Error("Possible EventEmitter memory leak detected. "+r.length+" "+String(e)+" listeners added. Use emitter.setMaxListeners() to increase limit");l.name="MaxListenersExceededWarning",l.emitter=t,l.type=e,l.count=r.length,a=l,console&&console.warn&&console.warn(a)}return t}function p(){for(var t=[],e=0;e<arguments.length;e++)t.push(arguments[e]);this.fired||(this.target.removeListener(this.type,this.wrapFn),this.fired=!0,s(this.listener,this.target,t))}function d(t,e,i){var n={fired:!1,wrapFn:void 0,target:t,type:e,listener:i},o=p.bind(n);return o.listener=i,n.wrapFn=o,o}function u(t,e,i){var n=t._events;if(void 0===n)return[];var o=n[e];return void 0===o?[]:"function"==typeof o?i?[o.listener||o]:[o]:i?function(t){for(var e=new Array(t.length),i=0;i<e.length;++i)e[i]=t[i].listener||t[i];return e}(o):f(o,o.length)}function m(t){var e=this._events;if(void 0!==e){var i=e[t];if("function"==typeof i)return 1;if(void 0!==i)return i.length}return 0}function f(t,e){for(var i=new Array(e),n=0;n<e;++n)i[n]=t[n];return i}Object.defineProperty(a,"defaultMaxListeners",{enumerable:!0,get:function(){return l},set:function(t){if("number"!=typeof t||t<0||r(t))throw new RangeError('The value of "defaultMaxListeners" is out of range. It must be a non-negative number. Received '+t+".");l=t}}),a.init=function(){void 0!==this._events&&this._events!==Object.getPrototypeOf(this)._events||(this._events=Object.create(null),this._eventsCount=0),this._maxListeners=this._maxListeners||void 0},a.prototype.setMaxListeners=function(t){if("number"!=typeof t||t<0||r(t))throw new RangeError('The value of "n" is out of range. It must be a non-negative number. Received '+t+".");return this._maxListeners=t,this},a.prototype.getMaxListeners=function(){return c(this)},a.prototype.emit=function(t){for(var e=[],i=1;i<arguments.length;i++)e.push(arguments[i]);var n="error"===t,o=this._events;if(void 0!==o)n=n&&void 0===o.error;else if(!n)return!1;if(n){var r;if(e.length>0&&(r=e[0]),r instanceof Error)throw r;var a=new Error("Unhandled error."+(r?" ("+r.message+")":""));throw a.context=r,a}var l=o[t];if(void 0===l)return!1;if("function"==typeof l)s(l,this,e);else{var c=l.length,h=f(l,c);for(i=0;i<c;++i)s(h[i],this,e)}return!0},a.prototype.addListener=function(t,e){return h(this,t,e,!1)},a.prototype.on=a.prototype.addListener,a.prototype.prependListener=function(t,e){return h(this,t,e,!0)},a.prototype.once=function(t,e){if("function"!=typeof e)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof e);return this.on(t,d(this,t,e)),this},a.prototype.prependOnceListener=function(t,e){if("function"!=typeof e)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof e);return this.prependListener(t,d(this,t,e)),this},a.prototype.removeListener=function(t,e){var i,n,o,s,r;if("function"!=typeof e)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof e);if(void 0===(n=this._events))return this;if(void 0===(i=n[t]))return this;if(i===e||i.listener===e)0==--this._eventsCount?this._events=Object.create(null):(delete n[t],n.removeListener&&this.emit("removeListener",t,i.listener||e));else if("function"!=typeof i){for(o=-1,s=i.length-1;s>=0;s--)if(i[s]===e||i[s].listener===e){r=i[s].listener,o=s;break}if(o<0)return this;0===o?i.shift():function(t,e){for(;e+1<t.length;e++)t[e]=t[e+1];t.pop()}(i,o),1===i.length&&(n[t]=i[0]),void 0!==n.removeListener&&this.emit("removeListener",t,r||e)}return this},a.prototype.off=a.prototype.removeListener,a.prototype.removeAllListeners=function(t){var e,i,n;if(void 0===(i=this._events))return this;if(void 0===i.removeListener)return 0===arguments.length?(this._events=Object.create(null),this._eventsCount=0):void 0!==i[t]&&(0==--this._eventsCount?this._events=Object.create(null):delete i[t]),this;if(0===arguments.length){var o,s=Object.keys(i);for(n=0;n<s.length;++n)"removeListener"!==(o=s[n])&&this.removeAllListeners(o);return this.removeAllListeners("removeListener"),this._events=Object.create(null),this._eventsCount=0,this}if("function"==typeof(e=i[t]))this.removeListener(t,e);else if(void 0!==e)for(n=e.length-1;n>=0;n--)this.removeListener(t,e[n]);return this},a.prototype.listeners=function(t){return u(this,t,!0)},a.prototype.rawListeners=function(t){return u(this,t,!1)},a.listenerCount=function(t,e){return"function"==typeof t.listenerCount?t.listenerCount(e):m.call(t,e)},a.prototype.listenerCount=m,a.prototype.eventNames=function(){return this._eventsCount>0?n(this._events):[]}},function(t,e,i){(e=i(9)(!1)).push([t.i,':root{--litepicker-container-months-color-bg: #fff;--litepicker-container-months-box-shadow-color: #ddd;--litepicker-footer-color-bg: #fafafa;--litepicker-footer-box-shadow-color: #ddd;--litepicker-tooltip-color-bg: #fff;--litepicker-month-header-color: #333;--litepicker-button-prev-month-color: #9e9e9e;--litepicker-button-next-month-color: #9e9e9e;--litepicker-button-prev-month-color-hover: #2196f3;--litepicker-button-next-month-color-hover: #2196f3;--litepicker-month-width: calc(var(--litepicker-day-width) * 7);--litepicker-month-weekday-color: #9e9e9e;--litepicker-month-week-number-color: #9e9e9e;--litepicker-day-width: 38px;--litepicker-day-color: #333;--litepicker-day-color-hover: #2196f3;--litepicker-is-today-color: #f44336;--litepicker-is-in-range-color: #bbdefb;--litepicker-is-locked-color: #9e9e9e;--litepicker-is-start-color: #fff;--litepicker-is-start-color-bg: #2196f3;--litepicker-is-end-color: #fff;--litepicker-is-end-color-bg: #2196f3;--litepicker-button-cancel-color: #fff;--litepicker-button-cancel-color-bg: #9e9e9e;--litepicker-button-apply-color: #fff;--litepicker-button-apply-color-bg: #2196f3;--litepicker-button-reset-color: #909090;--litepicker-button-reset-color-hover: #2196f3;--litepicker-highlighted-day-color: #333;--litepicker-highlighted-day-color-bg: #ffeb3b}.show-week-numbers{--litepicker-month-width: calc(var(--litepicker-day-width) * 8)}.litepicker{font-family:-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;font-size:0.8em;display:none}.litepicker button{border:none;background:none}.litepicker .container__main{display:-webkit-box;display:-ms-flexbox;display:flex}.litepicker .container__months{display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;background-color:var(--litepicker-container-months-color-bg);border-radius:5px;-webkit-box-shadow:0 0 5px var(--litepicker-container-months-box-shadow-color);box-shadow:0 0 5px var(--litepicker-container-months-box-shadow-color);width:calc(var(--litepicker-month-width) + 10px);-webkit-box-sizing:content-box;box-sizing:content-box}.litepicker .container__months.columns-2{width:calc((var(--litepicker-month-width) * 2) + 20px)}.litepicker .container__months.columns-3{width:calc((var(--litepicker-month-width) * 3) + 30px)}.litepicker .container__months.columns-4{width:calc((var(--litepicker-month-width) * 4) + 40px)}.litepicker .container__months.split-view .month-item-header .button-previous-month,.litepicker .container__months.split-view .month-item-header .button-next-month{visibility:visible}.litepicker .container__months .month-item{padding:5px;width:var(--litepicker-month-width);-webkit-box-sizing:content-box;box-sizing:content-box}.litepicker .container__months .month-item-header{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;font-weight:500;padding:10px 5px;text-align:center;-webkit-box-align:center;-ms-flex-align:center;align-items:center;color:var(--litepicker-month-header-color)}.litepicker .container__months .month-item-header div{-webkit-box-flex:1;-ms-flex:1;flex:1}.litepicker .container__months .month-item-header div>.month-item-name{margin-right:5px}.litepicker .container__months .month-item-header div>.month-item-year{padding:0}.litepicker .container__months .month-item-header .reset-button{color:var(--litepicker-button-reset-color)}.litepicker .container__months .month-item-header .reset-button>svg{fill:var(--litepicker-button-reset-color)}.litepicker .container__months .month-item-header .reset-button *{pointer-events:none}.litepicker .container__months .month-item-header .reset-button:hover{color:var(--litepicker-button-reset-color-hover)}.litepicker .container__months .month-item-header .reset-button:hover>svg{fill:var(--litepicker-button-reset-color-hover)}.litepicker .container__months .month-item-header .button-previous-month,.litepicker .container__months .month-item-header .button-next-month{visibility:hidden;text-decoration:none;padding:3px 5px;border-radius:3px;-webkit-transition:color 0.3s, border 0.3s;transition:color 0.3s, border 0.3s;cursor:default}.litepicker .container__months .month-item-header .button-previous-month *,.litepicker .container__months .month-item-header .button-next-month *{pointer-events:none}.litepicker .container__months .month-item-header .button-previous-month{color:var(--litepicker-button-prev-month-color)}.litepicker .container__months .month-item-header .button-previous-month>svg,.litepicker .container__months .month-item-header .button-previous-month>img{fill:var(--litepicker-button-prev-month-color)}.litepicker .container__months .month-item-header .button-previous-month:hover{color:var(--litepicker-button-prev-month-color-hover)}.litepicker .container__months .month-item-header .button-previous-month:hover>svg{fill:var(--litepicker-button-prev-month-color-hover)}.litepicker .container__months .month-item-header .button-next-month{color:var(--litepicker-button-next-month-color)}.litepicker .container__months .month-item-header .button-next-month>svg,.litepicker .container__months .month-item-header .button-next-month>img{fill:var(--litepicker-button-next-month-color)}.litepicker .container__months .month-item-header .button-next-month:hover{color:var(--litepicker-button-next-month-color-hover)}.litepicker .container__months .month-item-header .button-next-month:hover>svg{fill:var(--litepicker-button-next-month-color-hover)}.litepicker .container__months .month-item-weekdays-row{display:-webkit-box;display:-ms-flexbox;display:flex;justify-self:center;-webkit-box-pack:start;-ms-flex-pack:start;justify-content:flex-start;color:var(--litepicker-month-weekday-color)}.litepicker .container__months .month-item-weekdays-row>div{padding:5px 0;font-size:85%;-webkit-box-flex:1;-ms-flex:1;flex:1;width:var(--litepicker-day-width);text-align:center}.litepicker .container__months .month-item:first-child .button-previous-month{visibility:visible}.litepicker .container__months .month-item:last-child .button-next-month{visibility:visible}.litepicker .container__months .month-item.no-previous-month .button-previous-month{visibility:hidden}.litepicker .container__months .month-item.no-next-month .button-next-month{visibility:hidden}.litepicker .container__days{display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;justify-self:center;-webkit-box-pack:start;-ms-flex-pack:start;justify-content:flex-start;text-align:center;-webkit-box-sizing:content-box;box-sizing:content-box}.litepicker .container__days>div,.litepicker .container__days>a{padding:5px 0;width:var(--litepicker-day-width)}.litepicker .container__days .day-item{color:var(--litepicker-day-color);text-align:center;text-decoration:none;border-radius:3px;-webkit-transition:color 0.3s, border 0.3s;transition:color 0.3s, border 0.3s;cursor:default}.litepicker .container__days .day-item:hover{color:var(--litepicker-day-color-hover);-webkit-box-shadow:inset 0 0 0 1px var(--litepicker-day-color-hover);box-shadow:inset 0 0 0 1px var(--litepicker-day-color-hover)}.litepicker .container__days .day-item.is-today{color:var(--litepicker-is-today-color)}.litepicker .container__days .day-item.is-locked{color:var(--litepicker-is-locked-color)}.litepicker .container__days .day-item.is-locked:hover{color:var(--litepicker-is-locked-color);-webkit-box-shadow:none;box-shadow:none;cursor:default}.litepicker .container__days .day-item.is-in-range{background-color:var(--litepicker-is-in-range-color);border-radius:0}.litepicker .container__days .day-item.is-start-date{color:var(--litepicker-is-start-color);background-color:var(--litepicker-is-start-color-bg);border-top-left-radius:5px;border-bottom-left-radius:5px;border-top-right-radius:0;border-bottom-right-radius:0}.litepicker .container__days .day-item.is-start-date.is-flipped{border-top-left-radius:0;border-bottom-left-radius:0;border-top-right-radius:5px;border-bottom-right-radius:5px}.litepicker .container__days .day-item.is-end-date{color:var(--litepicker-is-end-color);background-color:var(--litepicker-is-end-color-bg);border-top-left-radius:0;border-bottom-left-radius:0;border-top-right-radius:5px;border-bottom-right-radius:5px}.litepicker .container__days .day-item.is-end-date.is-flipped{border-top-left-radius:5px;border-bottom-left-radius:5px;border-top-right-radius:0;border-bottom-right-radius:0}.litepicker .container__days .day-item.is-start-date.is-end-date{border-top-left-radius:5px;border-bottom-left-radius:5px;border-top-right-radius:5px;border-bottom-right-radius:5px}.litepicker .container__days .day-item.is-highlighted{color:var(--litepicker-highlighted-day-color);background-color:var(--litepicker-highlighted-day-color-bg)}.litepicker .container__days .week-number{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center;color:var(--litepicker-month-week-number-color);font-size:85%}.litepicker .container__footer{text-align:right;padding:10px 5px;margin:0 5px;background-color:var(--litepicker-footer-color-bg);-webkit-box-shadow:inset 0px 3px 3px 0px var(--litepicker-footer-box-shadow-color);box-shadow:inset 0px 3px 3px 0px var(--litepicker-footer-box-shadow-color);border-bottom-left-radius:5px;border-bottom-right-radius:5px}.litepicker .container__footer .preview-date-range{margin-right:10px;font-size:90%}.litepicker .container__footer .button-cancel{background-color:var(--litepicker-button-cancel-color-bg);color:var(--litepicker-button-cancel-color);border:0;padding:3px 7px 4px;border-radius:3px}.litepicker .container__footer .button-cancel *{pointer-events:none}.litepicker .container__footer .button-apply{background-color:var(--litepicker-button-apply-color-bg);color:var(--litepicker-button-apply-color);border:0;padding:3px 7px 4px;border-radius:3px;margin-left:10px;margin-right:10px}.litepicker .container__footer .button-apply:disabled{opacity:0.7}.litepicker .container__footer .button-apply *{pointer-events:none}.litepicker .container__tooltip{position:absolute;margin-top:-4px;padding:4px 8px;border-radius:4px;background-color:var(--litepicker-tooltip-color-bg);-webkit-box-shadow:0 1px 3px rgba(0,0,0,0.25);box-shadow:0 1px 3px rgba(0,0,0,0.25);white-space:nowrap;font-size:11px;pointer-events:none;visibility:hidden}.litepicker .container__tooltip:before{position:absolute;bottom:-5px;left:calc(50% - 5px);border-top:5px solid rgba(0,0,0,0.12);border-right:5px solid transparent;border-left:5px solid transparent;content:""}.litepicker .container__tooltip:after{position:absolute;bottom:-4px;left:calc(50% - 4px);border-top:4px solid var(--litepicker-tooltip-color-bg);border-right:4px solid transparent;border-left:4px solid transparent;content:""}\n',""]),e.locals={showWeekNumbers:"show-week-numbers",litepicker:"litepicker",containerMain:"container__main",containerMonths:"container__months",columns2:"columns-2",columns3:"columns-3",columns4:"columns-4",splitView:"split-view",monthItemHeader:"month-item-header",buttonPreviousMonth:"button-previous-month",buttonNextMonth:"button-next-month",monthItem:"month-item",monthItemName:"month-item-name",monthItemYear:"month-item-year",resetButton:"reset-button",monthItemWeekdaysRow:"month-item-weekdays-row",noPreviousMonth:"no-previous-month",noNextMonth:"no-next-month",containerDays:"container__days",dayItem:"day-item",isToday:"is-today",isLocked:"is-locked",isInRange:"is-in-range",isStartDate:"is-start-date",isFlipped:"is-flipped",isEndDate:"is-end-date",isHighlighted:"is-highlighted",weekNumber:"week-number",containerFooter:"container__footer",previewDateRange:"preview-date-range",buttonCancel:"button-cancel",buttonApply:"button-apply",containerTooltip:"container__tooltip"},t.exports=e},function(t,e,i){"use strict";t.exports=function(t){var e=[];return e.toString=function(){return this.map((function(e){var i=function(t,e){var i=t[1]||"",n=t[3];if(!n)return i;if(e&&"function"==typeof btoa){var o=(r=n,a=btoa(unescape(encodeURIComponent(JSON.stringify(r)))),l="sourceMappingURL=data:application/json;charset=utf-8;base64,".concat(a),"/*# ".concat(l," */")),s=n.sources.map((function(t){return"/*# sourceURL=".concat(n.sourceRoot||"").concat(t," */")}));return[i].concat(s).concat([o]).join("\n")}var r,a,l;return[i].join("\n")}(e,t);return e[2]?"@media ".concat(e[2]," {").concat(i,"}"):i})).join("")},e.i=function(t,i,n){"string"==typeof t&&(t=[[null,t,""]]);var o={};if(n)for(var s=0;s<this.length;s++){var r=this[s][0];null!=r&&(o[r]=!0)}for(var a=0;a<t.length;a++){var l=[].concat(t[a]);n&&o[l[0]]||(i&&(l[2]?l[2]="".concat(i," and ").concat(l[2]):l[2]=i),e.push(l))}},e}},function(t,e,i){"use strict";var n,o={},s=function(){return void 0===n&&(n=Boolean(window&&document&&document.all&&!window.atob)),n},r=function(){var t={};return function(e){if(void 0===t[e]){var i=document.querySelector(e);if(window.HTMLIFrameElement&&i instanceof window.HTMLIFrameElement)try{i=i.contentDocument.head}catch(t){i=null}t[e]=i}return t[e]}}();function a(t,e){for(var i=[],n={},o=0;o<t.length;o++){var s=t[o],r=e.base?s[0]+e.base:s[0],a={css:s[1],media:s[2],sourceMap:s[3]};n[r]?n[r].parts.push(a):i.push(n[r]={id:r,parts:[a]})}return i}function l(t,e){for(var i=0;i<t.length;i++){var n=t[i],s=o[n.id],r=0;if(s){for(s.refs++;r<s.parts.length;r++)s.parts[r](n.parts[r]);for(;r<n.parts.length;r++)s.parts.push(g(n.parts[r],e))}else{for(var a=[];r<n.parts.length;r++)a.push(g(n.parts[r],e));o[n.id]={id:n.id,refs:1,parts:a}}}}function c(t){var e=document.createElement("style");if(void 0===t.attributes.nonce){var n=i.nc;n&&(t.attributes.nonce=n)}if(Object.keys(t.attributes).forEach((function(i){e.setAttribute(i,t.attributes[i])})),"function"==typeof t.insert)t.insert(e);else{var o=r(t.insert||"head");if(!o)throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");o.appendChild(e)}return e}var h,p=(h=[],function(t,e){return h[t]=e,h.filter(Boolean).join("\n")});function d(t,e,i,n){var o=i?"":n.css;if(t.styleSheet)t.styleSheet.cssText=p(e,o);else{var s=document.createTextNode(o),r=t.childNodes;r[e]&&t.removeChild(r[e]),r.length?t.insertBefore(s,r[e]):t.appendChild(s)}}function u(t,e,i){var n=i.css,o=i.media,s=i.sourceMap;if(o&&t.setAttribute("media",o),s&&btoa&&(n+="\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(s))))," */")),t.styleSheet)t.styleSheet.cssText=n;else{for(;t.firstChild;)t.removeChild(t.firstChild);t.appendChild(document.createTextNode(n))}}var m=null,f=0;function g(t,e){var i,n,o;if(e.singleton){var s=f++;i=m||(m=c(e)),n=d.bind(null,i,s,!1),o=d.bind(null,i,s,!0)}else i=c(e),n=u.bind(null,i,e),o=function(){!function(t){if(null===t.parentNode)return!1;t.parentNode.removeChild(t)}(i)};return n(t),function(e){if(e){if(e.css===t.css&&e.media===t.media&&e.sourceMap===t.sourceMap)return;n(t=e)}else o()}}t.exports=function(t,e){(e=e||{}).attributes="object"==typeof e.attributes?e.attributes:{},e.singleton||"boolean"==typeof e.singleton||(e.singleton=s());var i=a(t,e);return l(i,e),function(t){for(var n=[],s=0;s<i.length;s++){var r=i[s],c=o[r.id];c&&(c.refs--,n.push(c))}t&&l(a(t,e),e);for(var h=0;h<n.length;h++){var p=n[h];if(0===p.refs){for(var d=0;d<p.parts.length;d++)p.parts[d]();delete o[p.id]}}}}},function(t,e,i){"use strict";var n=this&&this.__assign||function(){return(n=Object.assign||function(t){for(var e,i=1,n=arguments.length;i<n;i++)for(var o in e=arguments[i])Object.prototype.hasOwnProperty.call(e,o)&&(t[o]=e[o]);return t}).apply(this,arguments)};Object.defineProperty(e,"__esModule",{value:!0});var o=i(0),s=i(1),r=i(2);s.Litepicker.prototype.show=function(t){void 0===t&&(t=null),this.emit("before:show",t);var e=t||this.options.element;if(this.triggerElement=e,!this.isShowning()){if(this.options.inlineMode)return this.ui.style.position="relative",this.ui.style.display="inline-block",this.ui.style.top=null,this.ui.style.left=null,this.ui.style.bottom=null,void(this.ui.style.right=null);this.scrollToDate(t),this.render(),this.ui.style.position="absolute",this.ui.style.display="block",this.ui.style.zIndex=this.options.zIndex;var i=this.findPosition(e);this.ui.style.top=i.top+"px",this.ui.style.left=i.left+"px",this.ui.style.right=null,this.ui.style.bottom=null,this.emit("show",t)}},s.Litepicker.prototype.hide=function(){this.isShowning()&&(this.datePicked.length=0,this.updateInput(),this.options.inlineMode?this.render():(this.ui.style.display="none",this.emit("hide")))},s.Litepicker.prototype.getDate=function(){return this.getStartDate()},s.Litepicker.prototype.getStartDate=function(){return this.options.startDate?this.options.startDate.clone():null},s.Litepicker.prototype.getEndDate=function(){return this.options.endDate?this.options.endDate.clone():null},s.Litepicker.prototype.setDate=function(t,e){void 0===e&&(e=!1);var i=new o.DateTime(t,this.options.format,this.options.lang);r.dateIsLocked(i,this.options,[i])&&!e?this.emit("error:date",i):(this.setStartDate(t),this.options.inlineMode&&this.render(),this.emit("selected",this.getDate()))},s.Litepicker.prototype.setStartDate=function(t){t&&(this.options.startDate=new o.DateTime(t,this.options.format,this.options.lang),this.updateInput())},s.Litepicker.prototype.setEndDate=function(t){t&&(this.options.endDate=new o.DateTime(t,this.options.format,this.options.lang),this.options.startDate.getTime()>this.options.endDate.getTime()&&(this.options.endDate=this.options.startDate.clone(),this.options.startDate=new o.DateTime(t,this.options.format,this.options.lang)),this.updateInput())},s.Litepicker.prototype.setDateRange=function(t,e,i){void 0===i&&(i=!1),this.triggerElement=void 0;var n=new o.DateTime(t,this.options.format,this.options.lang),s=new o.DateTime(e,this.options.format,this.options.lang);(this.options.disallowLockDaysInRange?r.rangeIsLocked([n,s],this.options):r.dateIsLocked(n,this.options,[n,s])||r.dateIsLocked(s,this.options,[n,s]))&&!i?this.emit("error:range",[n,s]):(this.setStartDate(n),this.setEndDate(s),this.options.inlineMode&&this.render(),this.updateInput(),this.emit("selected",this.getStartDate(),this.getEndDate()))},s.Litepicker.prototype.gotoDate=function(t,e){void 0===e&&(e=0);var i=new o.DateTime(t);i.setDate(1),this.calendars[e]=i.clone(),this.render()},s.Litepicker.prototype.setLockDays=function(t){this.options.lockDays=o.DateTime.convertArray(t,this.options.lockDaysFormat),this.render()},s.Litepicker.prototype.setHighlightedDays=function(t){this.options.highlightedDays=o.DateTime.convertArray(t,this.options.highlightedDaysFormat),this.render()},s.Litepicker.prototype.setOptions=function(t){delete t.element,delete t.elementEnd,delete t.parentEl,t.startDate&&(t.startDate=new o.DateTime(t.startDate,this.options.format,this.options.lang)),t.endDate&&(t.endDate=new o.DateTime(t.endDate,this.options.format,this.options.lang));var e=n(n({},this.options.dropdowns),t.dropdowns),i=n(n({},this.options.buttonText),t.buttonText),s=n(n({},this.options.tooltipText),t.tooltipText);this.options=n(n({},this.options),t),this.options.dropdowns=n({},e),this.options.buttonText=n({},i),this.options.tooltipText=n({},s),!this.options.singleMode||this.options.startDate instanceof o.DateTime||(this.options.startDate=null,this.options.endDate=null),this.options.singleMode||this.options.startDate instanceof o.DateTime&&this.options.endDate instanceof o.DateTime||(this.options.startDate=null,this.options.endDate=null);for(var r=0;r<this.options.numberOfMonths;r+=1){var a=this.options.startDate?this.options.startDate.clone():new o.DateTime;a.setDate(1),a.setMonth(a.getMonth()+r),this.calendars[r]=a}this.options.lockDays.length&&(this.options.lockDays=o.DateTime.convertArray(this.options.lockDays,this.options.lockDaysFormat)),this.options.highlightedDays.length&&(this.options.highlightedDays=o.DateTime.convertArray(this.options.highlightedDays,this.options.highlightedDaysFormat)),this.render(),this.options.inlineMode&&this.show(),this.updateInput()},s.Litepicker.prototype.clearSelection=function(){this.options.startDate=null,this.options.endDate=null,this.datePicked.length=0,this.updateInput(),this.isShowning()&&this.render(),this.emit("clear:selection")},s.Litepicker.prototype.destroy=function(){this.ui&&this.ui.parentNode&&(this.ui.parentNode.removeChild(this.ui),this.ui=null),this.emit("destroy")}}]).Litepicker;
!function(e){var n={};function t(r){if(n[r])return n[r].exports;var a=n[r]={i:r,l:!1,exports:{}};return e[r].call(a.exports,a,a.exports,t),a.l=!0,a.exports}t.m=e,t.c=n,t.d=function(e,n,r){t.o(e,n)||Object.defineProperty(e,n,{enumerable:!0,get:r})},t.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},t.t=function(e,n){if(1&n&&(e=t(e)),8&n)return e;if(4&n&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(t.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&n&&"string"!=typeof e)for(var a in e)t.d(r,a,function(n){return e[n]}.bind(null,a));return r},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,n){return Object.prototype.hasOwnProperty.call(e,n)},t.p="",t(t.s=8)}([function(e,n,t){"use strict";e.exports=function(e){var n=[];return n.toString=function(){return this.map((function(n){var t=function(e,n){var t=e[1]||"",r=e[3];if(!r)return t;if(n&&"function"==typeof btoa){var a=(i=r,s=btoa(unescape(encodeURIComponent(JSON.stringify(i)))),c="sourceMappingURL=data:application/json;charset=utf-8;base64,".concat(s),"/*# ".concat(c," */")),o=r.sources.map((function(e){return"/*# sourceURL=".concat(r.sourceRoot||"").concat(e," */")}));return[t].concat(o).concat([a]).join("\n")}var i,s,c;return[t].join("\n")}(n,e);return n[2]?"@media ".concat(n[2]," {").concat(t,"}"):t})).join("")},n.i=function(e,t,r){"string"==typeof e&&(e=[[null,e,""]]);var a={};if(r)for(var o=0;o<this.length;o++){var i=this[o][0];null!=i&&(a[i]=!0)}for(var s=0;s<e.length;s++){var c=[].concat(e[s]);r&&a[c[0]]||(t&&(c[2]?c[2]="".concat(t," and ").concat(c[2]):c[2]=t),n.push(c))}},n}},function(e,n,t){"use strict";var r,a={},o=function(){return void 0===r&&(r=Boolean(window&&document&&document.all&&!window.atob)),r},i=function(){var e={};return function(n){if(void 0===e[n]){var t=document.querySelector(n);if(window.HTMLIFrameElement&&t instanceof window.HTMLIFrameElement)try{t=t.contentDocument.head}catch(e){t=null}e[n]=t}return e[n]}}();function s(e,n){for(var t=[],r={},a=0;a<e.length;a++){var o=e[a],i=n.base?o[0]+n.base:o[0],s={css:o[1],media:o[2],sourceMap:o[3]};r[i]?r[i].parts.push(s):t.push(r[i]={id:i,parts:[s]})}return t}function c(e,n){for(var t=0;t<e.length;t++){var r=e[t],o=a[r.id],i=0;if(o){for(o.refs++;i<o.parts.length;i++)o.parts[i](r.parts[i]);for(;i<r.parts.length;i++)o.parts.push(m(r.parts[i],n))}else{for(var s=[];i<r.parts.length;i++)s.push(m(r.parts[i],n));a[r.id]={id:r.id,refs:1,parts:s}}}}function l(e){var n=document.createElement("style");if(void 0===e.attributes.nonce){var r=t.nc;r&&(e.attributes.nonce=r)}if(Object.keys(e.attributes).forEach((function(t){n.setAttribute(t,e.attributes[t])})),"function"==typeof e.insert)e.insert(n);else{var a=i(e.insert||"head");if(!a)throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");a.appendChild(n)}return n}var u,p=(u=[],function(e,n){return u[e]=n,u.filter(Boolean).join("\n")});function d(e,n,t,r){var a=t?"":r.css;if(e.styleSheet)e.styleSheet.cssText=p(n,a);else{var o=document.createTextNode(a),i=e.childNodes;i[n]&&e.removeChild(i[n]),i.length?e.insertBefore(o,i[n]):e.appendChild(o)}}function f(e,n,t){var r=t.css,a=t.media,o=t.sourceMap;if(a&&e.setAttribute("media",a),o&&btoa&&(r+="\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(o))))," */")),e.styleSheet)e.styleSheet.cssText=r;else{for(;e.firstChild;)e.removeChild(e.firstChild);e.appendChild(document.createTextNode(r))}}var g=null,b=0;function m(e,n){var t,r,a;if(n.singleton){var o=b++;t=g||(g=l(n)),r=d.bind(null,t,o,!1),a=d.bind(null,t,o,!0)}else t=l(n),r=f.bind(null,t,n),a=function(){!function(e){if(null===e.parentNode)return!1;e.parentNode.removeChild(e)}(t)};return r(e),function(n){if(n){if(n.css===e.css&&n.media===e.media&&n.sourceMap===e.sourceMap)return;r(e=n)}else a()}}e.exports=function(e,n){(n=n||{}).attributes="object"==typeof n.attributes?n.attributes:{},n.singleton||"boolean"==typeof n.singleton||(n.singleton=o());var t=s(e,n);return c(t,n),function(e){for(var r=[],o=0;o<t.length;o++){var i=t[o],l=a[i.id];l&&(l.refs--,r.push(l))}e&&c(s(e,n),n);for(var u=0;u<r.length;u++){var p=r[u];if(0===p.refs){for(var d=0;d<p.parts.length;d++)p.parts[d]();delete a[p.id]}}}}},,,,,,,function(e,n,t){"use strict";t.r(n);t(9);function r(e,n){var t=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);n&&(r=r.filter((function(n){return Object.getOwnPropertyDescriptor(e,n).enumerable}))),t.push.apply(t,r)}return t}function a(e){for(var n=1;n<arguments.length;n++){var t=null!=arguments[n]?arguments[n]:{};n%2?r(Object(t),!0).forEach((function(n){o(e,n,t[n])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(t)):r(Object(t)).forEach((function(n){Object.defineProperty(e,n,Object.getOwnPropertyDescriptor(t,n))}))}return e}function o(e,n,t){return n in e?Object.defineProperty(e,n,{value:t,enumerable:!0,configurable:!0,writable:!0}):e[n]=t,e}Litepicker.add("ranges",{init:function(e){var n={position:"left",customRanges:{},force:!1,autoApply:e.options.autoApply};if(e.options.ranges=a(a({},n),e.options.ranges),e.options.singleMode=!1,!Object.keys(e.options.ranges.customRanges).length){var t=e.DateTime();e.options.ranges.customRanges={Today:[t.clone(),t.clone()],Yesterday:[t.clone().subtract(1,"day"),t.clone().subtract(1,"day")],"Last 7 Days":[t.clone().subtract(6,"day"),t],"Last 30 Days":[t.clone().subtract(29,"day"),t],"This Month":function(e){var n=e.clone();return n.setDate(1),[n,new Date(e.getFullYear(),e.getMonth()+1,0)]}(t),"Last Month":function(e){var n=e.clone();return n.setDate(1),n.setMonth(e.getMonth()-1),[n,new Date(e.getFullYear(),e.getMonth(),0)]}(t)}}var r=e.options.ranges;e.on("render",(function(n){var t=document.createElement("div");t.className="container__predefined-ranges",e.ui.dataset.rangesPosition=r.position,Object.keys(r.customRanges).forEach((function(a){var o=r.customRanges[a],i=document.createElement("button");i.innerText=a,i.tabIndex=n.dataset.plugins.indexOf("keyboardnav")>=0?1:-1,i.dataset.start=o[0].getTime(),i.dataset.end=o[1].getTime(),i.addEventListener("click",(function(n){var t=n.target;if(t){var a=e.DateTime(Number(t.dataset.start)),o=e.DateTime(Number(t.dataset.end));r.autoApply?(e.setDateRange(a,o,r.force),e.emit("ranges.selected",a,o),e.hide()):(e.datePicked=[a,o],e.emit("ranges.preselect",a,o)),!e.options.inlineMode&&r.autoApply||e.gotoDate(a)}})),t.appendChild(i)})),n.querySelector(".container__main").prepend(t)}))}})},function(e,n,t){var r=t(10);"string"==typeof r&&(r=[[e.i,r,""]]);var a={insert:function(e){var n=document.querySelector("head"),t=window._lastElementInsertedByStyleLoader;window.disableLitepickerStyles||(t?t.nextSibling?n.insertBefore(e,t.nextSibling):n.appendChild(e):n.insertBefore(e,n.firstChild),window._lastElementInsertedByStyleLoader=e)},singleton:!1};t(1)(r,a);r.locals&&(e.exports=r.locals)},function(e,n,t){(n=t(0)(!1)).push([e.i,'.litepicker[data-plugins*="ranges"] > .container__main > .container__predefined-ranges {\n  display: flex;\n  flex-direction: column;\n  align-items: flex-start;\n  background: var(--litepicker-container-months-color-bg);\n  box-shadow: -2px 0px 5px var(--litepicker-footer-box-shadow-color);\n  border-radius: 3px;\n}\n.litepicker[data-plugins*="ranges"][data-ranges-position="left"] > .container__main {\n  /* */\n}\n.litepicker[data-plugins*="ranges"][data-ranges-position="right"] > .container__main{\n  flex-direction: row-reverse;\n}\n.litepicker[data-plugins*="ranges"][data-ranges-position="right"] > .container__main > .container__predefined-ranges {\n  box-shadow: 2px 0px 2px var(--litepicker-footer-box-shadow-color);\n}\n.litepicker[data-plugins*="ranges"][data-ranges-position="top"] > .container__main {\n  flex-direction: column;\n}\n.litepicker[data-plugins*="ranges"][data-ranges-position="top"] > .container__main > .container__predefined-ranges {\n  flex-direction: row;\n  box-shadow: 2px 0px 2px var(--litepicker-footer-box-shadow-color);\n}\n.litepicker[data-plugins*="ranges"][data-ranges-position="bottom"] > .container__main {\n  flex-direction: column-reverse;\n}\n.litepicker[data-plugins*="ranges"][data-ranges-position="bottom"] > .container__main > .container__predefined-ranges {\n  flex-direction: row;\n  box-shadow: 2px 0px 2px var(--litepicker-footer-box-shadow-color);\n}\n.litepicker[data-plugins*="ranges"] > .container__main > .container__predefined-ranges button {\n  padding: 5px;\n  margin: 2px 0;\n}\n.litepicker[data-plugins*="ranges"][data-ranges-position="left"] > .container__main > .container__predefined-ranges button,\n.litepicker[data-plugins*="ranges"][data-ranges-position="right"] > .container__main > .container__predefined-ranges button{\n  width: 100%;\n  text-align: left;\n}\n.litepicker[data-plugins*="ranges"] > .container__main > .container__predefined-ranges button:hover {\n  cursor: default;\n  opacity: .6;\n}',""]),e.exports=n}]);