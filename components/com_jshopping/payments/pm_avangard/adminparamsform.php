<div class="col100">
<fieldset class="adminform">
<table class="admintable" width = "100%" >
   <tr>
   <td style="width:250px;" class="key">
     <?php echo 'Линцензия';?>
   </td>
   <td>
<input type = "text" class = "inputbox" name = "pm_params[license]" size="45" value = "<?php echo $params['license']?>" />
     <?php
     echo " ".JHTML::tooltip('Код лицензии полученный при покупке');
     ?>
   </td>
 </tr>
 <tr>
   <td style="width:250px;" class="key">
     <?php echo 'Номер торговой точки';?>
   </td>
   <td>
<input type = "password" class = "inputbox" name = "pm_params[shop_id]" size="45" value = "<?php echo $params['shop_id']?>" />
     <?php
     echo " ".JHTML::tooltip('Уникальный идентификационный номер торговой точки');
     ?>
   </td>
 </tr>
 <tr>
   <td  class="key">
     <?php echo 'Пароль';?>
   </td>
   <td>
     <input type = "text" class = "inputbox" name = "pm_params[shop_password]" size="45" value = "<?php echo $params['shop_password']?>" />
     <?php echo JHTML::tooltip('Пароль для данной торговой точки');?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_TRANSACTION_END;?>
   </td>
   <td>
     <?php
     print JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_end_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_end_status'] );
     echo " ".JHTML::tooltip("Выберите статус заказа, который будет установлен, если транзакция прошла успешно.");
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_TRANSACTION_PENDING;?>
   </td>
   <td>
     <?php
     echo JHTML::_('select.genericlist',$orders->getAllOrderStatus(), 'pm_params[transaction_pending_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_pending_status']);
     echo " ".JHTML::tooltip("Выберите статус заказа, который будет установлен, если транзакция незавершена.");
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_TRANSACTION_FAILED;?>
   </td>
   <td>
     <?php
     echo JHTML::_('select.genericlist',$orders->getAllOrderStatus(), 'pm_params[transaction_failed_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_failed_status']);
     echo " ".JHTML::tooltip("Выберите статус заказа, который будет установлен, если  транзакция прошла неуспешно.");
     ?>
   </td>
 </tr>

</table>
</fieldset>
</div>
<div class="clr"></div>