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
<?php echo $this->_tmp_ext_html_shipping_start ?>
<table id = "table_shippings" cellspacing="0" cellpadding="0">
<?php foreach($this->shipping_methods as $shipping){?>
  <tr>
    <td style = "padding-top:5px; padding-bottom:5px">
      <label for = "shipping_method_<?php echo $shipping->sh_pr_method_id ?>">
					<input type="radio" name="sh_pr_method_id" id="shipping_method_<?php echo $shipping->sh_pr_method_id ?>" value="<?php echo $shipping->sh_pr_method_id ?>" <?php if ($shipping->sh_pr_method_id==$this->active_shipping){ ?>checked="checked"<?php } ?> onclick="oneStepCheckout.showShippingForm('<?php echo $shipping->sh_pr_method_id ?>', 1)" />
					<?php if ($shipping->image) { ?>
					<span class="shipping_image">
						<img src="<?php echo $shipping->image ?>" alt="<?php echo htmlspecialchars($shipping->name) ?>" />
					</span>
					<?php }	?>
					<b><?php echo $shipping->name ?></b>
					<?php if ($shipping->calculeprice) { ?>
					(<?php echo formatprice($shipping->calculeprice) ?>)
					<?php } ?>
				</label>
			</td>
		</tr>
		<tr id="tr_shipping_<?php echo $shipping->sh_pr_method_id ?>" <?php if ($this->addonParams->shipping_params && $shipping->sh_pr_method_id!=$this->active_shipping){ ?>style="display:none"<?php } ?>>
			<td class="jshop_shipping_method">
      <?php if ($this->config->show_list_price_shipping_weight && count($shipping->shipping_price)){ ?>
          <br />
          <table class="shipping_weight_to_price">
          <?php foreach($shipping->shipping_price as $price){?>
              <tr>
                <td class="weight">
							<?php
							if ($price->shipping_weight_to != 0) {
								echo formatweight($price->shipping_weight_from).' - '.formatweight($price->shipping_weight_to);
							} else {
								echo _JSHOP_FROM.' '.formatweight($price->shipping_weight_from);
							}
							?>
                </td>
                <td class="price">
                    <?php echo formatprice($price->shipping_price) ?>
                </td>
            </tr>
          <?php } ?>
          </table>
      <?php } ?>
		<div class="shipping_descr">
			<?php echo $shipping->description ?>
		</div>
		<div id="shipping_form_<?php echo $shipping->shipping_id?>" class="shipping_form <?php if ($shipping->sh_pr_method_id==$this->active_shipping) echo 'shipping_form_active'?>">
			<?php echo $shipping->form ?>
		</div>
      <?php if ($shipping->delivery){?>
				<div class="shipping_delivery">
					<?php echo _JSHOP_DELIVERY_TIME.': '.$shipping->delivery ?>
				</div>
      <?php }?>
      <?php if ($shipping->delivery_date_f){?>
				<div class="shipping_delivery_date">
					<?php echo _JSHOP_DELIVERY_DATE.': '.$shipping->delivery_date_f ?>
				</div>
      <?php }?>  
      </td>
  </tr>
<?php } ?>
</table>
<br/>
<?php echo $this->_tmp_ext_html_shipping_end ?>
</div>
<?php if ($this->addonParams->package) { ?>
<div class="jshop">
	<input type="checkbox" name="package" id="package" value="1" <?php if ($this->package) echo 'checked="checked"' ?> onclick="oneStepCheckout.updateForm(<?php echo $this->config->step_4_3 ? 3 : 4 ?>)" />
	<label for="package"><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_PACKAGE_LABEL') ?></label>
	<?php if ($this->addonParams->package_image != '-1') { ?>
	<div id="package_image">
		<img src="<?php echo JURI::root(true).'/components/com_jshopping/templates/addons/onestepcheckout/'.$this->addonParams->template.'/images/'.$this->addonParams->package_image ?>" />
	</div>
	<?php } ?>
	<?php if ($this->addonParams->package_text) { ?>
	<div id="package_text"><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_PACKAGE_TEXT') ?></div>
	<?php } ?>
</div>
<?php } ?>