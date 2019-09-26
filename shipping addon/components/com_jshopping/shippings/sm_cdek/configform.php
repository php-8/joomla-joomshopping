<div class="col100">
<fieldset class="adminform">
<table class="admintable" width = "100%" >
<tr>
   <td style="width:250px;" class="key">
     Наценка за объявленную стоимость (в процентах, только число)
   </td>
   <td>
     <input type = "text" class = "inputbox" name = "params[price_tax]" size="45" value = "<?php echo $config['price_tax']?>" />
   </td>
 </tr>
 <tr>
   <td style="width:250px;" class="key">
     Коэффициент поправки веса (пример 1,1)
   </td>
   <td>
     <input type = "text" class = "inputbox" name = "params[weight_factor]" size="45" value = "<?php echo $config['weight_factor']?>" />
   </td>
 </tr>
 <tr>
   <td class="key">
     Общий коэффициент (пример 1,1)
   </td>
   <td>
     <input type = "text" class = "inputbox" name = "params[general_factor]" size="45" value = "<?php echo $config['general_factor']?>" />
   </td>
 </tr>
 <tr>
   <td class="key">
     Отладка
   </td>
   <td>
     <input type="radio" id="jform_params_debug0" name="params[debug]" value="0"<?php echo $checkedNo; ?>>
       <label for="jform_params_debug0">Нет</label>
       <input type="radio" id="jform_params_debug1" name="params[debug]" value="1"<?php echo $checkedYes; ?>>
       <label for="jform_params_debug1">Да</label>
   </td>
 </tr>

</table>
</fieldset>
</div>
<div class="clr"></div>