<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Garry
* @website https://joom-shopping.com
* @email info@joom-shopping.com
* @copyright Copyright © All rights reserved.
* @license GNU GPL v3
* @license agreement https://joom-shopping.com/polzovatelskoe-soglashenie
**/

defined('_JEXEC') or die;

if ($this->step5number == 4 && $this->addonParams->columns_number == 3) {
	$columnNumber = 3;
} else if ($this->step5number > 2 && $this->addonParams->columns_number != 1) {
	$columnNumber = 2;
} else {
	$columnNumber = 1;
}
if ($this->step2show) {
	$col1class = $columnNumber == 3 ? 'uk-width-4-10' : ($columnNumber == 2 ? 'uk-width-1-2' : 'uk-width-1-1');
} else {
	$col1class = 'uk-hidden';
}
if ($this->step3show == 1) {
	$col2class = $columnNumber == 3 ? 'uk-width-3-10' : ($columnNumber == 2 ? 'uk-width-1-2' : 'uk-width-1-1');
} else {
	$col2class = 'uk-hidden';
}
if ($this->step4show == 1) {
	$col3class = $columnNumber == 3 ? 'uk-width-3-10' : ($columnNumber == 2 ? 'uk-width-1-2' : 'uk-width-1-1');
} else {
	$col3class = 'uk-hidden';
}
if ($this->addonParams->step_number) {
	$step2number = $this->step2number.'. ';
	$step3number = $this->step3number.'. ';
	$step4number = $this->step4number.'. ';
	$step5number = $this->step5number.'. ';
} else {
	$step2number = $step3number = $step4number = $step5number = '';
}
if ($this->addonParams->step_name) {
	$step2name = JText::_('JSHOP_ONESTEPCHECKOUT_ADRESS_STEP');
	$step3name = $this->config->step_4_3 ? JText::_('JSHOP_ONESTEPCHECKOUT_SHIPPING_STEP') : JText::_('JSHOP_ONESTEPCHECKOUT_PAYMENT_STEP');
	$step4name = $this->config->step_4_3 ? JText::_('JSHOP_ONESTEPCHECKOUT_PAYMENT_STEP') : JText::_('JSHOP_ONESTEPCHECKOUT_SHIPPING_STEP');
	$step5name = JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_STEP');
} else {
	$step2name = $step3name = $step4name = $step5name = '';
}
include __DIR__ . '/main.js.php';
if (file_exists(__DIR__ . '/custom.js.php')) {
	include __DIR__ . '/custom.js.php';
}
?>
<div id="step2errors" class="uk-width-1-1"></div>
<div id="step3errors" class="uk-width-1-1"></div>
<div id="step4errors" class="uk-width-1-1"></div>
<?php
if ($this->addonParams->login_form && $this->config->shop_user_guest > 2 && $this->user->user_id == -1) {
?>
<form id="oneStepLoginForm" name="oneStepLoginForm" class="uk-form" action="<?php echo SEFLink('index.php?option=com_jshopping&controller=user&task=loginsave', 1,0, $this->config->use_ssl) ?>" method="post" >
	<div class="uk-width-1-1 uk-panel-title">
		<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_LOGIN_USER') ?>
	</div>
	<div class="uk-panel uk-panel-box">
		<?php if (!$this->addonParams->placeholder) { ?>
		<label for="username" class="uk-form-help-inline"><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_USER_FIELD_U_NAME') ?></label>
		<?php } ?>
		<div class="uk-form-icon">
			<i class="uk-icon-user"></i>
			<input id="username" type="text" name="username" class="uk-margin-small-top" <?php if ($this->addonParams->placeholder) { ?>placeholder="<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_USER_FIELD_U_NAME') ?>"<?php } ?> />
		</div>
		<?php if (!$this->addonParams->placeholder) { ?>
		<label for="passwd" class="uk-form-help-inline"><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_USER_FIELD_PASSWORD') ?></label>
		<?php } ?>
		<div class="uk-form-icon">
			<i class="uk-icon-key"></i>
			<input id="passwd" type="password" name="passwd" class="uk-margin-small-top" <?php if ($this->addonParams->placeholder) { ?>placeholder="<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_USER_FIELD_PASSWORD') ?>"<?php } ?> />
		</div>
		<button type="submit" class="uk-button uk-button-large">
			<i class="uk-icon-sign-in uk-text-large"></i>
		</button>
	</div>
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="return" value="<?php echo base64_encode($_SERVER['REQUEST_URI']) ?>" />
	<input type="hidden" name="onestepcheckout" value="1" />
</form>
<?php
}
?>
<form id="oneStepCheckoutForm" name="oneStepCheckoutForm" class="uk-form" action="<?php echo $this->action ?>" method="post" onsubmit="return oneStepCheckout.validateForm(this)" >
	<div class="uk-grid" data-uk-grid-margin="" data-uk-grid-match="{target:'.uk-panel-box'}">
		<div class="uk-width-1-1 uk-panel-title">
			<?php echo $this->user->user_id == -1 ? JText::_('JSHOP_ONESTEPCHECKOUT_NEW_USER') : JText::_('JSHOP_ONESTEPCHECKOUT_EXIST_USER') ?>
		</div>
	</div>
	<div class="uk-grid" data-uk-grid-margin="" data-uk-grid-match="{target:'.uk-panel-box'}">
		<div class="data-uk-grid-margin uk-width-1-1">
			<div class="uk-panel uk-panel-box uk-panel-header">
				<div id="step5top">
				</div>
			</div>
		</div>
		<div class="data-uk-grid-margin <?php echo $col1class ?>">
			<div class="uk-panel uk-panel-box uk-panel-header">
				<?php if ($step2number || $step2name) { ?>
				<h3 class="uk-panel-title step-header">
					<?php echo $step2number ?>
					<?php echo $step2name ?>
				</h3>		
				<?php } ?>
				<div id="step2">
					<?php echo $this->step2 ?>
				</div>
			</div>
		</div>
		<div class="data-uk-grid-margin <?php echo $col2class ?>" > 
			<div class="uk-panel uk-panel-box uk-panel-header">
				<?php if ($step3number || $step3name) { ?>
				<h3 class="uk-panel-title step-header">
					<?php echo $step3number ?>
					<?php echo $step3name ?>
				</h3>	
				<?php } ?>
				<div id="step3">
					<?php echo $this->step3 ?>
				</div>
			</div>
		</div>
		<div class="data-uk-grid-margin <?php echo $col3class ?>" > 
			<div class="uk-panel uk-panel-box uk-panel-header">
				<?php if ($step4number || $step4name) { ?>
				<h3 class="uk-panel-title step-header">
					<?php echo $step4number ?>
					<?php echo $step4name ?>
				</h3>	
				<?php } ?>
				<div id="step4">
					<?php echo $this->step4 ?>
				</div>
			</div>
		</div>
		<div class="data-uk-grid-margin uk-width-1-1">
			<div class="uk-panel uk-panel-box uk-panel-header">
				<?php if ($step5number || $step5name) { ?>
				<h3 class="uk-panel-title step-header">
					<?php echo $step5number ?>
					<?php echo $step5name ?>
				</h3>	
				<?php } ?>
				<div id="step5">
					<?php echo $this->step5 ?>
				</div>      
				<?php if ($this->config->display_agb){?>
				<div class="row_agb uk-container-center uk-text-center">
					<input type="checkbox" name="agb" id="agb" />        
					<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_AGB_CONFIRM') ?>        
					<a class="policy" href="#" onclick="window.open('<?php echo SEFLink('index.php?option=com_jshopping&controller=content&task=view&page=agb&tmpl=component', 1);?>','window','width=800, height=600, scrollbars=yes, status=no, toolbar=no, menubar=no, resizable=yes, location=no');return false;">
						<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_AGB_POLICY') ?>
					</a>
					<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_AGB_AND') ?>
					<a class="policy" href="#" onclick="window.open('<?php echo SEFLink('index.php?option=com_jshopping&controller=content&task=view&page=return_policy&tmpl=component&cart=1', 1);?>','window','width=800, height=600, scrollbars=yes, status=no, toolbar=no, menubar=no, resizable=yes, location=no');return false;">
						<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_RETURN_POLICY') ?>
					</a>
				</div>
				<?php }?>
				<?php if($this->no_return){?>
				<div class="row_no_return uk-container-center uk-text-center">            
					<input type="checkbox" name="no_return" id="no_return" />        
					<?php echo _JSHOP_NO_RETURN_DESCRIPTION ?>     
				</div>
				<?php }?>
				<?php echo $this->_tmp_ext_html_previewfinish_agb ?>
				<div class="uk-width-1-1 uk-container-center uk-text-center add_info">
					<?php if (!$this->addonParams->placeholder) { ?>
					<div class="uk-width-1-1 os-name">
						<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_ADD_INFO') ?> 
					</div>
					<?php } ?>
					<textarea class="inputbox" <?php if ($this->addonParams->placeholder) { ?>placeholder="<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_ADD_INFO') ?>"<?php } ?> width="100%" id="order_add_info" name="order_add_info"></textarea>
				</div> 
				<?php echo $this->_tmp_ext_html_previewfinish_end?>
			</div>
		</div>
	</div>
	<div class="uk-text-center">
		<button type="submit" id="button_order_finish" class="uk-button uk-button-primary uk-button-large uk-text-large button_order_finish">
			<i class="uk-icon-check"></i>
			<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_ORDER_FINISH') ?>
		</button>
	</div>
	<?php echo JHtml::_('form.token'); ?>
</form>
<?php if (!$this->config->without_payment) { ?>
<form id="payment_form" name="payment_form" action="javascript:void(0)" method="post">
	<input type="hidden" name="check_payment_form" value="1" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<?php
}
?>