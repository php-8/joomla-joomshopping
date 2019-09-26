<?php
	if(count($this->products)):
	JshSCHelper::translateSet($this->lang);
	$image_product_live_path = JSFactory::getConfig()->image_product_live_path;
	$noimage = JSFactory::getConfig()->noimage;
	$show_product_code_in_cart  = JSFactory::getConfig()->show_product_code_in_cart;
	$show_tax_product_in_cart  = JSFactory::getConfig()->show_tax_product_in_cart;
	$cart_basic_price_show  = JSFactory::getConfig()->cart_basic_price_show;
?>
	<table class="products_details">
		<?php foreach($this->products as $product):
			$url = JURI::getInstance()->toString(array("scheme",'host', 'port')).SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$product['category_id'].'&product_id='.$product['product_id'], 1);
			$img = $image_product_live_path.'/'.($product['thumb_image'] ? $product['thumb_image'] : $noimage);
		?>
			<tr>
				<td class="image">
					<a href="<?php print $url; ?>">
						<img src="<?php print $img; ?>" alt="<?php print htmlspecialchars($product['product_name']); ?>" />
					</a>
				</td>
				
				<td class="title">
					<a href="<?php print $url; ?>">
						<?php echo $product['product_name']; ?>
					</a>					
					 <?php if ($show_product_code_in_cart): ?>
						<span class="jshop_code_prod">(<?php print $product['ean'] ?>)</span>
					<?php endif;?>
					<?php if ($product['manufacturer'] != ''): ?>
						<div class="manufacturer">
							<?php print JshSCHelper::_('_JSHOP_MANUFACTURER'); ?>: <span><?php print $product['manufacturer'] ?></span>
						</div>
					<?php endif; ?>
					<?php print sprintAtributeInCart($product['attributes_value']); ?>
					<?php print sprintFreeAtributeInCart($product['free_attributes_value']); ?>
					<?php print sprintFreeExtraFiledsInCart($product['extra_fields']); ?>
				</td>
				
				<td class="price">
					<?php print formatprice($product['price']); ?>
					<?php if ($show_tax_product_in_cart && $product['tax'] > 0): ?>
						<span class="taxinfo"><?php print productTaxInfo($product['tax']); ?></span>
					<?php endif; ?>
					<?php if ($this->config->cart_basic_price_show && $product['basicprice'] > 0): ?>
						<div class="basic_price">
							<?php print JshSCHelper::_('_JSHOP_BASIC_PRICE'); ?>: 
							<span><?php print sprintBasicPrice($product); ?></span>
						</div>
					<?php endif; ?>
				</td>
				
				<td class="quantity">
					<?php echo $product['quantity'].' '.$product['_qty_unit']; ?>
				</td>
				
				<td class="summ">
					<?php print formatprice($product['price'] * $product['quantity']); ?>
					<?php if ($show_tax_product_in_cart && $product['tax'] > 0): ?>
						<span class="taxinfo"><?php print productTaxInfo($product['tax']); ?></span>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>