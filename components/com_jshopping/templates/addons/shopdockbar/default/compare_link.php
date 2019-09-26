<?php

defined('_JEXEC') or die;

if ($type == 'product') {
	if (!isset($view->_tmp_product_html_buttons)) $view->_tmp_product_html_buttons = '';
	$view->_tmp_product_html_after_buttons .= '
		<div class="add_to_compare">
			<a '.($shopdockbar_compare[$view->product->product_id]?'style="display:none" ':'').'href="'.SEFLink('index.php?option=com_jshopping&controller=shopdockbar&task=addToCompare&category_id='.$view->category_id.'&product_id='.$view->product->product_id, 1).'">
				'.JText::_('MOD_JSHOPPING_SHOPDOCKBAR_COMPARE_ADD').'
			</a>
			<a '.($shopdockbar_compare[$view->product->product_id]?'':'style="display:none" ').'href="'.SEFLink('index.php?option=com_jshopping&controller=shopdockbar&task=displayCompare', 1).'">
				'.JText::_('MOD_JSHOPPING_SHOPDOCKBAR_COMPARE_LINK').' 
			</a>
		</div>
		';
} else {
	if (!is_array($view->rows) && isset($view->lists_prod)) $view->rows = $view->lists_prod;
	if (is_array($view->rows)){
		foreach($view->rows as $k=>$v){
			$view->rows[$k]->_tmp_var_buttons .= '
			<div class="add_to_compare">
				<a '.($shopdockbar_compare[$v->product_id]?'style="display:none" ':'').'href="'.SEFLink('index.php?option=com_jshopping&controller=shopdockbar&task=addToCompare&category_id='.$v->category_id.'&product_id='.$v->product_id, 1).'">
					'.JText::_('MOD_JSHOPPING_SHOPDOCKBAR_COMPARE_ADD').'
				</a>
				<a '.($shopdockbar_compare[$v->product_id]?'':'style="display:none" ').'href="'.SEFLink('index.php?option=com_jshopping&controller=shopdockbar&task=displayCompare', 1).'">
					'.JText::_('MOD_JSHOPPING_SHOPDOCKBAR_COMPARE_LINK').'
				</a>
			</div>
			';
		}
	}
}
?>