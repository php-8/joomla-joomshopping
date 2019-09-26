<?php JSFactory::loadExtLanguageFile('addon_rus_invoices_for_payment');?>
<fieldset class = "adminform">
	<legend><?php print _JSHOP_DISPLAY; ?></legend>
    <table class = "admintable">
        <tr>
            <td style="width:220px">
				<?php echo _JSHOP_ADDON_RUS_INVOICES_PAYMENT_GUIDE_ENTERPRISES; ?>
			</td>
            <td>	
				<input type='text' name='params[guide_enterprises]' value='<?php print $this->params['guide_enterprises'];?>'>
			</td>
        </tr>
		<tr>
            <td style="width:220px">
				<?php echo _JSHOP_ADDON_RUS_INVOICES_PAYMENT_CHIEF_ACCOUNTANT; ?>
			</td>
            <td>	
				<input type='text' name='params[chief_accountant]' value='<?php print $this->params['chief_accountant'];?>'>
			</td>
        </tr>
		<tr>
            <td style="width:220px">
				<?php echo _JSHOP_ADDON_RUS_INVOICES_PAYMENT_ACCOUNT_CORRESPONDENT; ?>
			</td>
            <td>	
				<input type='text' name='params[account_correspondent]' value='<?php print $this->params['account_correspondent'];?>'>
			</td>
        </tr>
    </table>
</fieldset>
