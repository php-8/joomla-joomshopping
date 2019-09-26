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
?>
<div class="jshop address_block">
    <?php echo $this->_tmp_ext_html_address_start?>
    <div class="jshop_nvg_register">
		<?php
		foreach ($this->user_fields as $v) {
			if ($this->config_fields[$v]['display']){
				if (($v=='firma_code' || $v=='tax_number') && isset($this->config_fields['client_type']) && $this->config_fields['client_type']['display'] && $this->user->client_type!="2") {
					$style = 'style="display:none"';
				} else {
					$style = '';
				}
		?>
		<div class="uk-grid" id="tr_field_<?php echo $v ?>" <?php echo $style ?>>
			<?php if (!$this->addonParams->placeholder) { ?>
			<div class="uk-width-1-1 os-name">
				<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_USER_FIELD_'.$v) ?> 
			</div>
			<?php } ?>
			<div class="uk-width-1-1 os-value">
				<span>
					<?php
					if ($v=='country') {
						echo $this->select_countries;
					} else if ($v=='title') {
						echo $this->select_titles;
					} else if ($v=='client_type') {
						echo $this->select_client_types;
					} else if ($v=='birthday') {
						echo JHTML::_('calendar', $this->user->birthday, 'birthday', 'birthday', $this->config->field_birthday_format);
					} else {
					?>
					<input type="text" name="<?php echo $v ?>" id="<?php echo $v ?>" value="<?php echo $this->user->$v ?>" <?php if ($this->addonParams->placeholder) { ?>placeholder="<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_USER_FIELD_'.$v) ?>"<?php } ?> />
					<?php } ?>
				</span>
				<?php if ($this->config_fields[$v]['require']) { ?>
				<span class="requiredtext" rel="tooltip" title="<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_REQUIRED') ?>">
					<i class="uk-icon-warning"></i>
				</span>
				<?php } else if ($v=='email') { ?>
				<span id="requiredemail" class="requiredtext uk-hidden" rel="tooltip"  title="<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_REQUIRED') ?>">
					<i class="uk-icon-warning"></i>
				</span>
				<?php } ?>
			</div>
		</div>
		<?php
			}
			if ($v=='birthday') {
				echo $this->_tmpl_address_html_2;
			} else if ($v=='country') {
				echo $this->_tmpl_address_html_3;
			}
		}
		echo $this->_tmpl_address_html_4;
		?>
    </div>
    
    <?php if ($this->count_filed_delivery > 0){?>
    <div class="jshop_register">
		<div class="uk-grid">
			<?php if (!$this->addonParams->placeholder) { ?>
			<div class="uk-width-1-1 os-name">
				<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_DELIVERY_ADRESS') ?>
			</div>
			<?php } ?>
			<div class="uk-width-1-1 os-value">
				<?php
				$options = array(
					JHtml::_('select.option', '', ($this->addonParams->placeholder ? JText::_('JSHOP_ONESTEPCHECKOUT_DELIVERY_ADRESS') . ' ' : '') . JText::_('JSHOP_ONESTEPCHECKOUT_DELIVERY_ADRESS_NO')),
					JHtml::_('select.option', '1', ($this->addonParams->placeholder ? JText::_('JSHOP_ONESTEPCHECKOUT_DELIVERY_ADRESS') . ' ' : '') .JText::_('JSHOP_ONESTEPCHECKOUT_DELIVERY_ADRESS_YES'))
				);
				echo JHTML::_('select.genericlist', $options, 'delivery_adress', 'class="inputbox" size="1" onchange="oneStepCheckout.toggleDeliveryAdress()"','value', 'text', $this->delivery_adress, 'delivery_adress_2' );
				?>
			</div>
		</div>
	</div>
    <?php }?>
    
    <div id="div_delivery" class="jshop_register" <?php if (!$this->delivery_adress){ ?>style="display:none"<?php } ?>>
		<?php
		foreach ($this->user_fields as $v) {
			$d_v = 'd_'.$v;
			if ($this->config_fields[$d_v]['display']){
		?>
		<div class="uk-grid">
			<?php if (!$this->addonParams->placeholder) { ?>
			<div class="uk-width-1-1 os-name">
				<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_USER_FIELD_'.$v) ?> 
			</div>
			<?php } ?>
			<div class="uk-width-1-1 os-value">
				<span>
					<?php
					if ($d_v=='d_country') {
						echo $this->select_d_countries;
					} else if ($d_v=='d_title') {
						echo $this->select_d_titles;
					} else if ($d_v=='d_client_type') {
						echo $this->select_d_client_types;
					} else if ($d_v=='d_birthday') {
						echo JHTML::_('calendar', $this->user->d_birthday, 'd_birthday', 'd_birthday', $this->config->field_birthday_format);
					} else {
					?>
					<input type="text" name="<?php echo $d_v ?>" id="<?php echo $d_v ?>" value="<?php echo $this->user->$d_v ?>" <?php if ($this->addonParams->placeholder) { ?>placeholder="<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_USER_FIELD_'.$v) ?>"<?php } ?> />
					<?php } ?>
				</span>
				<?php if ($this->config_fields[$d_v]['require']){ ?>
				<span class="requiredtext" rel="tooltip" title="<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_REQUIRED') ?>">
					<i class="uk-icon-warning"></i>
				</span>
				<?php } ?>
			</div>
		</div>
		<?php
			}
			if ($d_v=='d_birthday') {
				echo $this->_tmpl_address_html_5;
			} else if ($d_v=='d_country') {
				echo $this->_tmpl_address_html_6;
			}
		}
		echo $this->_tmpl_address_html_7;
		?>

    </div>
    <?php if ($this->config_fields['privacy_statement']['display']){?>
		<div class="jshop_block_privacy_statement">    
			<input type="checkbox" name="privacy_statement" id="privacy_statement" value="1" />
			<a class="privacy_statement" href="#" onclick="window.open('<?php echo SEFLink('index.php?option=com_jshopping&controller=content&task=view&page=privacy_statement&tmpl=component', 1);?>','window','width=800, height=600, scrollbars=yes, status=no, toolbar=no, menubar=no, resizable=yes, location=no');return false;">
			<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_USER_FIELD_PRIVACY_STATEMENT') ?> 
			</a>
			<?php if ($this->config_fields['privacy_statement']['require']){ ?>
				<span class="requiredtext" rel="tooltip"  title="<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_REQUIRED') ?>">
					<i class="uk-icon-warning"></i>
				</span>
			<?php } ?>					
		</div>
    <?php } ?>    
    <?php echo $this->_tmp_ext_html_address_end?>

	<?php
	if ($this->allowUserRegistration) {
		if ($this->config->shop_user_guest == 4) {
	?>
	<strong><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_CREATE_USER_ACCOUNT') ?></strong>
	<input type="checkbox" name="register" id="register" value="1" onclick="oneStepCheckout.toggleRegistration()" />
	<?php } else { ?>
	<input type="hidden" name="register" id="register" value="1" />
	<?php } ?>

	<?php
		if ($this->register_fields['u_name']['display'] || $this->register_fields['password']['display'] || $this->register_fields['password_2']['display']) {
	?>
	<div id="div_register" class="jshop_register" <?php echo $this->config->shop_user_guest == 4 ? 'style="display:none"' : '' ?> >
		<?php
		foreach ($this->register_fields as $k=>$v) {
			if ($k != 'u_name' && $k != 'password' && $k != 'password_2') {
				continue;
			}
			if ($v['display']){
		?>
		<div class="uk-grid">
			<?php if (!$this->addonParams->placeholder) { ?>
			<div class="uk-width-1-1 os-name">
				<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_USER_FIELD_'.$k) ?> 
			</div>
			<?php } ?>
			<div class="uk-width-1-1 os-value">
				<input type="<?php echo $k == 'u_name' ? 'text' : 'password' ?>" name="<?php echo $k ?>" id="<?php echo $k ?>" value="" <?php if ($this->addonParams->placeholder) { ?>placeholder="<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_USER_FIELD_'.$k) ?>"<?php } ?> />
				<?php if ($v['require']){ ?>
				<span class="requiredtext" rel="tooltip"  title="<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_REQUIRED') ?>">
					<i class="uk-icon-warning"></i>
				</span>
				<?php } ?>
			</div>
		</div>
		<?php
			}
		}
		?>
	</div>
	<?php
		}
	}
	?>
    
    <div style="padding-top:10px;">
        <?php echo $this->_tmpl_address_html_8?>
        <div class="requiredtext"><i class="uk-icon-warning"></i> - <?php echo JText::_('JSHOP_ONESTEPCHECKOUT_REQUIRED') ?></div>
        <?php echo $this->_tmpl_address_html_9?>
    </div>
</div>