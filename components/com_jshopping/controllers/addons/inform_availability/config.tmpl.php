<?php
JSFactory::loadExtLanguageFile('addon_inform_availability_product');
include_once(JPATH_ROOT.'/components/com_jshopping/addons/inform_availability/inform_availability_helper.php');
?>

<fieldset class = "adminform">
<legend><?php echo 'Inform availability product Configuration'?></legend>
    <table id = "table_add_price" class = "admintable">
        <tr>
            <td><?php echo _IAP_ADMIN_EMAIL_NOTIFY ?>:</td>
            <td style="width: 50px;">
                <input type = "radio" name = "params[admin_email_notify]" id="admin_email_notify1" value = "1" <?php if($this->params['admin_email_notify']==1) echo 'checked="checked";';?> />
                <?php echo _JSHOP_YES; ?>
            </td>
            <td>
                <input type = "radio" name = "params[admin_email_notify]" id="admin_email_notify0" value = "0" <?php if($this->params['admin_email_notify']==0) echo 'checked="checked";';?> />
                <?php echo _JSHOP_NO; ?>
            </td>                     
        </tr>
        <tr>
            <td><?php echo _IAP_CLIENT_EMAIL_NOTIFY ?>:</td>
            <td style="width: 50px;">
                <input type = "radio" name = "params[client_email_notify]" id="client_email_notify1" value = "1" <?php if($this->params['client_email_notify']==1) echo 'checked="checked";';?> />
                <?php echo _JSHOP_YES?>
            </td>
            <td>
                <input type = "radio" name = "params[client_email_notify]" id="client_email_notify0" value = "0" <?php if($this->params['client_email_notify']==0) echo 'checked="checked";';?> />
                <?php echo _JSHOP_NO ?>
            </td>                     
        </tr>
        <tr>
            <td><?php echo _IAP_SHOW_PRODUCT_CODE ?>:</td>
            <td style="width: 50px;">
                <input type = "radio" name = "params[show_product_code]" id="show_product_code1" value = "1" <?php if($this->params['show_product_code']==1) echo 'checked="checked";';?> />
                <?php echo _JSHOP_YES?>
            </td>
            <td>
                <input type = "radio" name = "params[show_product_code]" id="show_product_code0" value = "0" <?php if($this->params['show_product_code']==0) echo 'checked="checked";';?> />
                <?php echo _JSHOP_NO ?>
            </td>                     
        </tr>
        
        <tr>
            <td><?php echo _IAP_SHOW_IN_PRODUCTS_LIST ?>:</td>
            <td style="width: 50px;">
                <input type = "radio" name = "params[show_on_products_list]" id="show_on_products_list1" value = "1" <?php if($this->params['show_on_products_list']==1) echo 'checked="checked";';?> />
                <?php echo _JSHOP_YES?>
            </td>
            <td>
                <input type = "radio" name = "params[show_on_products_list]" id="show_on_products_list0" value = "0" <?php if($this->params['show_on_products_list']==0) echo 'checked="checked";';?> />
                <?php echo _JSHOP_NO ?>
            </td>                     
        </tr>
        
        <tr>
            <td><?php echo _IAP_SHOW_POPUP_SIZE ?>:</td>
            <td colspan="2">
                <input type = "text" name = "params[popup_size_width]" id="popup_size_width" style="width: 100px;" value = "<?php echo $this->params['popup_size_width']; ?>" /> x
                <input type = "text" name = "params[popup_size_height]" id="popup_size_height" style="width: 100px;" value = "<?php echo $this->params['popup_size_height']; ?>" />
            </td>
        </tr>

        <tr>
			<td colspan="3">
				<legend style="line-height:1.1;">
					<?php echo _IAP_TEXT_MAILS; ?>
					<small><?php echo InformAvailabilityHelper::mailParamsImplode(); ?></small>
				</legend>
			</td>
		</tr>
		<?php foreach(InformAvailabilityHelper::langs() as $l):
			$editor = JFactory::getEditor()->display('params[text_'.$l->lang.']', $this->params['text_'.$l->lang], '100%', '400', '70', '15',false);
			$flag = InformAvailabilityHelper::htmlLangFlag($l);
		?>
			<tr>
				<td><?php echo _IAP_SUBJECT.' '.$flag; ?></td>
				<td colspan="2">
					<input type="text" name = "params[subject_<?php echo $l->lang; ?>]" style="width:90%;" value="<?php echo htmlspecialchars($this->params['subject_'.$l->lang])?>" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo _IAP_BODY.' '.$flag; ?>
				</td>
				<td colspan="2">
					<?php echo $editor; ?>
				</td>
			</tr>
			<tr>
                <td colspan="3"><hr /></td>
            </tr>
		<?php endforeach; ?>

    </table>
</fieldset>