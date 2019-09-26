<?php

defined('_JEXEC') or die;

?>
<div id="ajax_cart_with_related">
	<div class="wrapper_cart_quantity_products">
		<?php echo JText::_('MOD_JSHOPPING_AJAX_CART_WITH_RELATED_PRODUCTS') ?> <span id="jshop_quantity_products"><?php echo formatqty($cart->count_product) ?></span>
	</div>
	<div class="wrapper_cart_summ_products">
		<?php echo JText::_('MOD_JSHOPPING_AJAX_CART_WITH_RELATED_SUMM') ?> <span id="jshop_summ_product"><?php echo formatprice($cart->getSum(0,1))?></span>
	</div>
	<div class="wrapper_cart_link">
		<a href="<?php echo SEFLink('index.php?option=com_jshopping&controller=cart&task=view', 1) ?>"><?php echo JText::_('MOD_JSHOPPING_AJAX_CART_WITH_RELATED_TO_CART')?></a>
	</div>
</div>