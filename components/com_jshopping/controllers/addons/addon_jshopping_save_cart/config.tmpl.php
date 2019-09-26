<?php
	require_once JPATH_SITE.'/components/com_jshopping/addons/addon_jshopping_save_cart/JshSCHelper.php';
?>
<fieldset class = "adminform">
	<legend></legend>
	<table class = "admintable" width="90%">
		<tr>
			<td style="width:220px">
				<?php print _JSHOP_ADDON_SAVE_CART; ?>
			</td>
			<td>
				<?php print JshSCHelper::htmlCheckbox('save_for_cart', $this->params['save_for_cart']); ?>
			</td>
		</tr>
		<tr>
			<td style="width:220px">
				<?php print _JSHOP_ADDON_SAVE_WISHLIST; ?>
			</td>
			<td>
				<?php print JshSCHelper::htmlCheckbox('save_for_wishlist', $this->params['save_for_wishlist']); ?>
			</td>
		</tr>	
		<tr>
			<td style="width:220px">
				<?php print _JSHOP_ADDON_SAVE_CART_EMAIL_NOTIFICATION; ?>
			</td>
			<td>
				<?php print JshSCHelper::htmlCheckbox('email_notification', $this->params['email_notification']); ?>
				<?php echo _JSHOP_ADDON_SAVE_AFTER; ?>
				<input type="text" style="width: 30px;" name="params[notification_after]" value="<?php echo $this->params['notification_after']; ?>" />
				<?php echo _JSHOP_ADDON_SAVE_HOURS; ?>
			</td>
		</tr>
		
		<tr>
			<td style="width:220px">
				<?php print _JSHOP_ADDON_SAVE_CART_TEST_EMAIL; ?>
			</td>
			<td>
				<input type="text" name="params[test_email]" value="<?php echo $this->params['test_email']; ?>" />
				<input type="checkbox" name="params[test_send]" value="1" />
				<?php print _JSHOP_ADDON_SAVE_CART_TEST_SEND; ?>
			</td>
		</tr>
		
		
		
		<tr>
			<td colspan="2">
				<legend style="line-height:1.1;">
					<?php echo _JSHOP_ASC_TEXT_MAILS; ?>
					<small><?php echo JshSCHelper::mailParamsImplode(); ?></small>
				</legend>
			</td>
		</tr>
		<?php foreach(JshSCHelper::langs() as $l):
			$editor = JFactory::getEditor()->display('params[text_'.$l->lang.']', $this->params['text_'.$l->lang], '100%', '400', '70', '15',false);
			$flag = JshSCHelper::htmlLangFlag($l);
		?>
			<tr>
				<td style="text-align:right;"><?php echo _JSHOP_ASC_SUBJECT.' '.$flag; ?></td>
				<td>
					<input type="text" name = "params[subject_<?php echo $l->lang; ?>]" style="width:90%;" value="<?php echo $this->params['subject_'.$l->lang]; ?>" />
				</td>
			</tr>         
			<tr>
				<td style="text-align:right;">
					<?php echo _JSHOP_ASC_BODY.' '.$flag; ?>
				</td>
				<td>
					<?php echo $editor; ?>
				</td>
			</tr>
			<tr><td colspan="2"><hr /></td></tr>
		<?php endforeach; ?>
			
	</table>
</fieldset>

<input type="hidden" name="addon_alias" value="<?php echo $this->row->alias;?>" />