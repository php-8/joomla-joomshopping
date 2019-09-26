<?php

defined('_JEXEC') or die;

if (!count($products_related)) {
	return;
}
?>
<h2 class="relatedincartheader"><?php echo JText::_('MOD_JSHOPPING_AJAX_CART_WITH_RELATED_MAYBE_NEED') ?></h2>
<div class="cart_related_general_list js-grid">
	<?php foreach ($products_related as $category_id=>$products_category) { ?>
		<div class="js-width-1-2">
		<h3>
			<?php if ($params->link_category) { ?>
			<a class="js_car_category_link" href="<?php echo SEFLink('index.php?option=com_jshopping&controller=category&task=view&category_id='.$category_id, 1) ?>">
			<?php } ?>
			<?php echo $all_categorys[$category_id]->name ?>
			<?php if ($params->link_category) { ?>
			</a>
			<?php } ?>
		</h3>
		<div class="category_bloсk">
			<?php foreach ($products_category as $product) { ?>
				<div class="cart_related_product_wrapper">
					<div class="cart_related_product_name">
						<a class="js-normal" href="<?php echo $product->product_link ?> "><?php echo $product->name ?></a>
					</div>
					<div class="cart_related_product_price">
						<?php echo formatprice($product->product_price) ?></a>
					</div>
					<div class="cart_related_product_link buttons">
						<a class="button_buy" href="<?php echo $product->buy_link ?> "><?php echo JText::_('MOD_JSHOPPING_AJAX_CART_WITH_RELATED_BUY') ?></a>
					</div>
				</div>
			<?php } ?>
		</div>
		</div>
	<?php } ?>
</div>
<hr>