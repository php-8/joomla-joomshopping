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
<div class="jshop">
	<div id="error_min_max_price_order" <?php if ($this->min_price_order || $this->max_price_order) { ?>class="uk-text-danger uk-text-bold uk-alert uk-alert-danger"<?php } ?>>
		<?php
		if ($this->min_price_order) {
			printf(_JSHOP_ERROR_MIN_SUM_ORDER, formatprice($this->min_price_order * $this->config->currency_value));
		}
		if ($this->max_price_order) {
			printf(_JSHOP_ERROR_MAX_SUM_ORDER, formatprice($this->max_price_order * $this->config->currency_value));
		}
		?>
	</div>
	<table class="minicart uk-table">
		<thead>
			<tr>
				<th></th>
				<th><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_ITEM_NAME') ?></th>     
				<th><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_SINGLE_PRICE') ?></th>
				<th><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_QTY') ?></th>
				<th><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_TOTAL_PRICE') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$i=1;
			$countprod = count($this->products);
			foreach ($this->products as $key_id=>$prod) {
			?> 
			<tr class="jshop_prod_cart">
				<td>
					<a href="<?php echo SEFLink('index.php?option=com_jshopping&controller=cart&task=delete&number_id='.$key_id, 1)?>" onclick="if(confirm('<?php echo JText::_("JSHOP_ONESTEPCHECKOUT_CONFIRM_REMOVE") ?>')){jQuery('#quantity<?php echo $key_id ?>').val(0);oneStepCheckout.refreshForm()}return false;" title = "<?php echo JText::_("JSHOP_ONESTEPCHECKOUT_REMOVE") ?>">
						<i class="uk-icon-trash-o"></i>
					</a>
				</td>
				<td class="jshop_img_description_center" style="max-width:35%">
					<a href="<?php echo $prod['href'] ?>">
						<?php if ($this->addonParams->product_image) { ?>
						<img src="<?php echo $this->image_product_path ?>/<?php echo $prod['thumb_image'] ? $prod['thumb_image'] : $this->no_image ?>" alt="<?php echo htmlspecialchars($prod['product_name']) ?>" class="uk-thumbnail" />
						<?php } ?>
						<?php echo $prod['product_name'] ?>
					</a>
					<?php if ($this->config->show_product_code_in_cart && $prod['ean']){?>
					<span class="jshop_code_prod">(<?php echo $prod['ean']?>)</span>
					<?php } ?>
					<?php if ($prod['manufacturer'] != ''){ ?>
					<div class="manufacturer">
						<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_MANUFACTURER') ?>: <span><?php echo $prod['manufacturer']?></span>
					</div>
					<?php }?>
					<?php echo sprintAtributeInCart($prod['attributes_value']) ?>
					<?php echo sprintFreeAtributeInCart($prod['free_attributes_value']) ?>
					<?php echo sprintFreeExtraFiledsInCart($prod['extra_fields']) ?>
					<?php echo $prod['_ext_attribute_html'] ?>
					<?php if ($this->config->show_delivery_time_step5 && $this->step==5 && $prod['delivery_times_id']){ ?>
					<div class="deliverytime">
						<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_DELIVERY_TIME') ?>: <?php echo $this->deliverytimes[$prod['delivery_times_id']]?>
					</div>
					<?php }?>
				</td>    
				<td>
					<?php echo formatprice($prod['price']) ?>
					<?php echo $prod['_ext_price_html'] ?>
					<?php if ($this->config->show_tax_product_in_cart && $prod['tax']>0){ ?>
					<span class="taxinfo"><?php echo productTaxInfo($prod['tax']) ?></span>
					<?php }?>
					<?php if ($this->config->cart_basic_price_show && $prod['basicprice']>0){ ?>
					<div class="basic_price">
						<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_BASIC_PRICE') ?>: <span><?php echo sprintBasicPrice($prod) ?></span>
					</div>
					<?php }?>
				</td>
				<td>
					<div class="quantity">
						<span class="quantitymore" onclick="qty=jQuery('#quantity<?php echo $key_id ?>');qty.val(parseFloat(qty.val())+1);qty.change()"></span>
						<input type="text" id="quantity<?php echo $key_id ?>" name="quantity[<?php echo $key_id ?>]" value="<?php echo $prod['quantity'] ?>" data-quantity="<?php echo $prod['quantity'] ?>" onkeyup="oneStepCheckout.refreshForm(this,800)" onchange="oneStepCheckout.refreshForm(this,0)" />
						<span class="quantityless" onclick="qty=jQuery('#quantity<?php echo $key_id ?>');qty.val(parseFloat(qty.val())-1);qty.change()"></span>
					</div>
					<?php echo $prod['_qty_unit'] ?>
				</td>
				<td>
					<?php echo formatprice($prod['price']*$prod['quantity']) ?>
					<?php echo $prod['_ext_price_total_html'] ?>
					<?php if ($this->config->show_tax_product_in_cart && $prod['tax']>0){ ?>
					<span class="taxinfo"><?php echo productTaxInfo($prod['tax']) ?></span>
					<?php } ?>
				</td>
			</tr>
			<?php 
			$i++;
			} 
			?>
		</tbody>
	</table>
	<?php if ($this->config->show_weight_order){ ?>  
	<div class="weightorder">
		<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_WEIGHT_PRODUCTS') ?>: <span><?php echo formatweight($this->weight);?></span>
	</div>
	<?php }?>
      
    <div class = "cartdescr"><?php echo $this->cartdescr ?></div>

	<table class = "jshop jshop_subtotal">
		<?php if (!$this->hide_subtotal){?>
		<tr>    
			<td class="name">
				<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_SUBTOTAL') ?>
			</td>
			<td class="value">
				<?php echo formatprice($this->summ) ?>
				<?php echo $this->_tmp_ext_subtotal ?>
			</td>
		</tr>
		<?php } ?>
		<?php if ($this->discount > 0){ ?>
		<tr class="preview_discount">
			<td class="name">
				<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_RABATT_VALUE') ?>
			</td>
			<td class="value">
				<?php echo formatprice(-$this->discount) ?>
				<?php echo $this->_tmp_ext_discount ?>
			</td>
		</tr>
		<?php } ?>
		<?php if (isset($this->summ_delivery)){ ?>
		<tr>
			<td class="name">
				<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_SHIPPING_PRICE') ?>
			</td>
			<td class="value">
				<?php echo formatprice($this->summ_delivery) ?>
				<?php echo $this->_tmp_ext_shipping?>
			</td>
		</tr>
		<?php } ?>
		<?php if (isset($this->summ_package)){?>
		<tr>
			<td class="name">
				<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_PACKAGE_PRICE') ?>
			</td>
			<td class="value">
				<?php echo formatprice($this->summ_package) ?>
				<?php echo $this->_tmp_ext_shipping_package ?>
			</td>
		</tr>
		<?php } ?>
		<?php if ($this->summ_payment != 0){ ?>
		<tr>
			<td class="name">
				<?php echo $this->payment_name ?>
			</td>
			<td class="value">
				<?php echo formatprice($this->summ_payment) ?>
				<?php echo $this->_tmp_ext_payment ?>
			</td>
		</tr>
		<?php } ?>  
		<?php if (!$this->config->hide_tax){ ?>
		<?php foreach($this->tax_list as $percent=>$value){ ?>
		<tr>
			<td class="name">
				<?php echo displayTotalCartTaxName() ?>
				<?php if ($this->show_percent_tax) echo formattax($percent)."%" ?>
			</td>
			<td class="value">
				<?php echo formatprice($value) ?>
				<?php echo $this->_tmp_ext_tax[$percent] ?>
			</td>
		</tr>
		<?php } ?>
		<?php } ?>
		<tr class="total">
			<td class="name">
				<?php echo $this->text_total ?>
			</td>
			<td class="value">
				<?php echo formatprice($this->fullsumm) ?>
				<?php echo $this->_tmp_ext_total ?>
			</td>
		</tr>
		<?php if ($this->free_discount > 0){ ?>  
		<tr class="one-step-discount">
			<td colspan="2" align="right">    
				<span class="free_discount"><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_FREE_DISCOUNT') ?>: <?php echo formatprice($this->free_discount) ?></span>  
			</td>
		</tr>
		<?php }?>  
	</table>
	<?php if ($this->config->use_rabatt_code){ ?>
	<nav class="uk-navbar">
		<div class="rabatt_input">
			<?php if (!$this->addonParams->placeholder) { ?>
			<div class="uk-width-1-1 os-name">
				<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_RABATT_INPUT') ?> 
			</div>
			<?php } ?>
			<input type="text" name="rabatt" <?php if ($this->addonParams->placeholder) { ?>placeholder="<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_RABATT_INPUT') ?>"<?php } ?> value="" />
			<input type="button" class="uk-button" value="<?php echo JText::_('JSHOP_ONESTEPCHECKOUT_RABATT_ACTIVE') ?>" onclick="oneStepCheckout.rabbatForm()" />
		</div>
	</nav>
	<?php } ?>
</div>