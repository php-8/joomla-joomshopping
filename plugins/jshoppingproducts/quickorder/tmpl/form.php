<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author joom-shopping.com
* @website https://joom-shopping.com/
* @email info@joom-shopping.com
* @copyright Copyright © All rights reserved.
* @license GNU GPL v3
**/

defined('_JEXEC') or die;
?>
<div id="quickorderpopup">
	<div class="quickord_wrapper">
		<form class="form-horizontal" action="" method="POST" onsubmit="return quickOrder.submitForm()">
			<a class="close" onclick="quickOrder.closeForm(this)">&times;</a>
			<div class="header"> <?php echo JText::_('PLG_JSHOPPINGPRODUCTS_QUICKORDER_LINK');?></div>
			<div class="clearfix"> </div>
			<?php if ($this->addonParams->show_f_name) { ?>
				<div class="quickorderformrow control-group">
						<div class="input-prepend">
						  
						  <input type="text" name="f_name" value="<?php print $adv_user->f_name ?>" <?php if ($this->addonParams->show_f_name == 1) { ?>data-required<?php } ?> placeholder="<?php echo JText::_('PLG_JSHOPPINGPRODUCTS_QUICKORDER_USER_F_NAME');if ($this->addonParams->show_f_name == 1) echo JText::_('PLG_JSHOPPINGPRODUCTS_QUICKORDER_REQUIRED'); ?>" />
					</div>
				</div>
			<?php } ?>
			<?php if ($this->addonParams->show_l_name) { ?>
				<div class="quickorderformrow control-group">
						<div class="input-prepend ">
						  
						  <input type="text" name="l_name" value="<?php print $adv_user->l_name ?>" <?php if ($this->addonParams->show_l_name == 1) { ?>data-required<?php } ?> placeholder="<?php echo JText::_('PLG_JSHOPPINGPRODUCTS_QUICKORDER_USER_L_NAME');if ($this->addonParams->show_l_name == 1) echo JText::_('PLG_JSHOPPINGPRODUCTS_QUICKORDER_REQUIRED'); ?>"/>
				</div>
				
				
			<?php } ?><?php echo '<br>'; ?>
			<?php if ($this->addonParams->show_phone) { ?>
				<div class="quickorderformrow control-group">
					<div class="input-prepend">
					  
					  <input type="text" name="phone" value="<?php print $adv_user->{$this->addonParams->which_phone} ?>" <?php if ($this->addonParams->show_phone == 1) { ?>data-required<?php } ?> placeholder="<?php echo JText::_('PLG_JSHOPPINGPRODUCTS_QUICKORDER_USER_PHONE');if ($this->addonParams->show_phone == 1) echo JText::_('PLG_JSHOPPINGPRODUCTS_QUICKORDER_REQUIRED'); ?>"/>
					</div>
				</div>
			<?php } ?>
			<?php if ($this->addonParams->show_email) { ?>
				<div class="quickorderformrow control-group">
					<div class="input-prepend">
					  
					  <input type="text" name="email" value="<?php print $adv_user->email ?>" <?php if ($this->addonParams->show_email == 1) { ?>data-required<?php } ?> placeholder="<?php
				echo JText::_('PLG_JSHOPPINGPRODUCTS_QUICKORDER_USER_EMAIL');if ($this->addonParams->show_email == 1) echo JText::_('PLG_JSHOPPINGPRODUCTS_QUICKORDER_REQUIRED'); ?>" />
					</div>
				</div>
			<?php } ?>
			<?php if ($this->addonParams->show_comment) { ?>
				<div class="quickorderformrow control-group">
					<textarea name="comment" rows="4" <?php if ($this->addonParams->show_comment == 1) { ?>data-required<?php } ?> placeholder="<?php
					echo JText::_('PLG_JSHOPPINGPRODUCTS_QUICKORDER_USER_COMMENT'); if ($this->addonParams->show_comment == 1) echo JText::_('PLG_JSHOPPINGPRODUCTS_QUICKORDER_REQUIRED'); ?>"></textarea>
				</div>
			<?php } ?>
				<div class="control-group">
					<button type="submit" class="btn btn-info button" ><?php echo JText::_('PLG_JSHOPPINGPRODUCTS_QUICKORDER_SUBMIT') ?></button>
				</div>
			<input type="hidden" name="option" value="com_jshopping" />
			<input type="hidden" name="controller" value="cart" />
			<input type="hidden" name="task" value="add" />
			<input type="hidden" name="to" value="" />
			<input type="hidden" name="category_id" />
			<input type="hidden" name="product_id" />
			<?php echo JHtml::_( 'form.token' ) ?>
		</form>
	</div>
</div>