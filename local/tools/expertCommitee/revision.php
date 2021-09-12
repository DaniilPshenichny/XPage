<div class="flex justify-center items-center">
    <div class="text-2xl text-blue-1000 font-bold">
       Укажите причину отклонения
    </div>
</div>
<br><br>
<div class="flex justify-center">
   <div>
      <textarea  class="border-gray-1000 border rounded-my outline-none resize-none p-2 comment" name="" id="" cols="30" rows="5" ></textarea>
   </div>
</div>
<div class="flex justify-center">
   <button class="text-blue-1000 rounded-2xl h-9 w-32 flex justify-center items-center font-semibold px-2 shadow-my send-sb-revision-result my-4" data-id="<?=$_POST['requestId']?>">Сохранить</button>
</div>
<script>
   var sendSbResFinal = document.querySelector('.send-sb-revision-result');
   sendSbResFinal.addEventListener('click',function (){
      console.dir(document.querySelector('.comment'))
      BX.ajax.post(
         '/local/tools/expertCommitee/backend/revision.php',
         {
            requestId:event.target.dataset.id,
            'service':'sb',
            'comment':document.querySelector('.comment').value
         },
         function (data){
            window.location.reload();
         }
      );
   })
</script>