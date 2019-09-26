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
<div class="uk-panel uk-panel-box uk-panel-header finish-page">
	<h3 class="uk-panel-title">
		<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_TITLE'.(($this->addonParams->finish_register && $this->register) ? '_REGISTER' : '')) ?>
	</h3>
	<?php if ($this->addonParams->finish_register && $this->register) { ?>
	<div class="registr-success">
		<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_TEXT_REGISTER') ?>
	</div>
	<hr class="uk-grid-divider" />
	<?php } ?>
	<?php if ($this->addonParams->finish_extended) { ?>
	<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_TEXT_ORDER_BEFORE') ?>
	<dl class="uk-description-list uk-description-list-horizontal"> 
		<?php if ($this->addonParams->order_number) { ?>
		<dt><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_ORDER_NUMBER') ?></dt>
		<dd><?php echo $this->order->order_number ?></dd>
		<?php } ?>
		<?php if ($this->addonParams->order_subtotal) { ?>
		<dt><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_ORDER_SUBTOTAL') ?></dt>
		<dd><?php echo formatprice($this->order->order_subtotal, $this->order->currency_code) ?></dd>
		<?php } ?>
		<?php if ($this->addonParams->order_discount) { ?>
		<?php if ($this->order->order_discount > 0) { ?>
		<dt><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_ORDER_DISCOUNT') ?></dt>
		<dd><?php echo formatprice(-$this->order->order_discount, $this->order->currency_code) ?></dd>
		<?php } ?>
		<?php if ($this->order_total && $this->order->order_total != $this->order_total) { ?>
		<dt><?php echo $this->order_total > $this->order->order_total ? JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_ORDER_ADDITIONAL_DISCOUNT') : JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_ORDER_ADDITIONAL_SURCHARGE') ?></dt>
		<dd><?php echo formatprice($this->order->order_total - $this->order_total, $this->order->currency_code) ?></dd>
		<?php } ?>
		<?php } ?>
		<?php if ($this->addonParams->order_payment && $this->order->payment_method_id > 0) { ?>
		<dt><?php echo $this->order->payment_name ?></dt>
		<dd><?php echo formatprice($this->order->order_payment, $this->order->currency_code) ?></dd>
		<?php } ?>
		<?php if ($this->addonParams->order_shipping && $this->order->shipping_method_id > 0) { ?>
		<dt><?php echo $this->order->shipping_name ?></dt>
		<dd><?php echo formatprice($this->order->order_shipping, $this->order->currency_code) ?></dd>
		<?php } ?>
		<?php if ($this->addonParams->order_package && ($this->order->order_package>0 || $this->config->display_null_package_price)) { ?>
		<dt><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_ORDER_PACKAGE') ?></dt>
		<dd><?php echo formatprice($this->order->order_package, $this->order->currency_code) ?></dd>
		<?php } ?>
		<?php if ($this->addonParams->order_total) { ?>
		<dt><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_ORDER_TOTAL') ?></dt>
		<dd><?php echo formatprice($this->order->order_total, $this->order->currency_code) ?></dd>
		<?php } ?>
		<?php
		if ($this->addonParams->order_products) {
			$this->order->items = $this->order->getAllItems();
			if (count($this->order->items)) {
		?>
		<dt><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_ORDER_PRODUCTS') ?></dt>
		<dd>
			<table class="minicart uk-table">
				<thead>
					<tr>
						<th><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_ITEM_NAME') ?></th>     
						<th><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_SINGLE_PRICE') ?></th>
						<th><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_QTY') ?></th>
						<th><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_TOTAL_PRICE') ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ($this->order->items as $item) {
					$product = JTable::getInstance('Product', 'jshop');
					$product->load($item->product_id);
					$product->getCategory();
				?>
				<tr class="jshop_prod_cart">
					<td>
						<a href="<?php echo SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$product->category_id.'&product_id='.$product->product_id, 1)?>">
							<?php
							echo $item->product_name;
							if ($this->config->show_product_code_in_order) {
							?>
							<span class="jshop_code_prod">(<?php echo $item->product_ean ?>)</span>
							<?php }	?>
						</a>
						<br />
						<?php echo sprintAtributeInOrder($item->product_attributes).sprintFreeAtributeInOrder($item->product_freeattributes) ?>
					</td>
					<td>
						<?php echo formatprice($item->product_item_price, $this->order->currency_code) ?>
					</td>
					<td>
						<?php echo formatqty($item->product_quantity) ?>
					</td>
					<td>
						<?php echo formatprice($item->product_quantity * $item->product_item_price, $this->order->currency_code) ?>
					</td>
				</tr>
				<?php } ?>
				</tbody>
			</table>
		</dd>
		<?php 
			}
		}
		?>
	</dl>
	<?php if (is_array($this->addonParams->order_shipping_desc) && in_array($this->order->shipping_method_id, $this->addonParams->order_shipping_desc)) { ?>
	<div id="shipping_description">
		<div class="shipping_description_title"><i class="uk-icon-truck"></i> <?php echo JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_ORDER_SHIPPING_DESC') ?></div>
		<div class="shipping_description_text"><?php echo $this->order->shipping_desc ?></div>
	</div>
	<?php } ?>
	<br/>
	<?php if (is_array($this->addonParams->order_payment_desc) && in_array($this->order->payment_method_id, $this->addonParams->order_payment_desc)) { ?>
	<div id="payment_description">
		<div class="payment_description_title"><i class="uk-icon-money"></i> <?php echo JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_ORDER_PAYMENT_DESC') ?></div>
		<div class="payment_description_text"><?php echo $this->order->payment_desc ?></div>
	</div>
	<?php } ?>
	<div class="uk-alert"><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_TEXT_ORDER_AFTER') ?></div>
	<hr class="uk-grid-divider" />
	<?php } ?>
	<?php if ($this->contentRegistration) { ?>
	<div id="preContentRegistration">
		<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_TEXT_BEFORE_BUTTON_REGISTRATION') ?>
		<div class="uk-text-center uk-margin-top uk-margin-large-bottom">
			<a href="<?php echo SEFLink('index.php?option=com_jshopping&controller=user&task=register',1,0, $this->config->use_ssl) ?>" class="uk-button uk-button-success uk-button-large" onclick="jQuery('#contentRegistration').show();jQuery('#preContentRegistration').hide();return false">
				<i class="uk-icon-user"></i>
				<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_BUTTON_REGISTRATION') ?>
			</a>
		</div>
	</div>
	<div id="contentRegistration" style="display:none">
		<?php echo $this->contentRegistration ?>
	</div>
	<hr class="uk-grid-divider" />
	<?php } ?>
</div>
<?php echo $this->text ?>