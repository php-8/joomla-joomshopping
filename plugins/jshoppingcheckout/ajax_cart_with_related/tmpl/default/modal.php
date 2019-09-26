<?php

defined('_JEXEC') or die;
?>
<div class="js_car_modal_title" >
	<div class="js_car_modal_title_success"></div>
	<img src="<?php echo $jshopConfig->image_product_live_path .'/'. $this->current_product->thumb_image ?>" />
	<span class="js_car_prod_title_added_name"><?php echo $this->current_product->name ?></span> 
	<span><?php echo JText::_('MOD_JSHOPPING_AJAX_CART_WITH_RELATED_TO_CART_SUCCESSFUL') ?></span>
</div>
<a class="js_car_modal_btn_tocart" href="<?php echo SEFLink('index.php?option=com_jshopping&controller=cart&task=view', 1) ?>"><?php echo JText::_('MOD_JSHOPPING_AJAX_CART_WITH_RELATED_TO_CART') ?></a> 
<a class="js_car_modal_btn_continue" href="javascript:ajaxCart.hideModal()"><?php echo JText::_('MOD_JSHOPPING_AJAX_CART_WITH_RELATED_CONTINUE') ?></a> 
<div class="jshop_list_product">
<?php include_once dirname(__FILE__).'/related.php' ?>
</div>