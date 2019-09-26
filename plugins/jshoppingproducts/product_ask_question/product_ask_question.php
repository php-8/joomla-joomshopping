<?php

defined('_JEXEC') or die;

class plgJshoppingProductsProduct_Ask_Question extends JPlugin {

    function onBeforeDisplayProductView(&$view){
		if (!$this->params->get('show_in_product', 1)) {
			return;
		}

		JSFactory::loadExtLanguageFile('product_ask_question');
		JHTML::_('behavior.modal');
		if (!isset($view->_tmp_product_html_buttons)) {
			$view->_tmp_product_html_buttons = '';
		}

		$view->_tmp_product_html_buttons .= '<a style="display:inline-block;" href="'.SEFLink('index.php?option=com_jshopping&controller=product_ask_question&category_id='.JRequest::getInt('category_id').'&product_id='.JRequest::getInt('product_id'), 1).'&tmpl=component" class="modal ask" rel="{handler: \'iframe\', size: {x: 700, y: 450}}" title="'._JSHOP_PRODUCT_ASK_QUESTION_LINK.'" ><input type="submit"  value="'._JSHOP_PRODUCT_ASK_QUESTION_LINK.'"></a>';



		//$view->_tmp_product_html_buttons .= '<a style="display:inline-block;" href="'.SEFLink('index.php?option=com_jshopping&controller=product_ask_question&category_id='.JRequest::getInt('category_id').'&product_id='.JRequest::getInt('product_id'), 1).'&tmpl=component" class="modal ask" rel="{handler: \'iframe\', size: {x: 700, y: 450}}" title="'._JSHOP_PRODUCT_ASK_QUESTION_LINK.'" >'._JSHOP_PRODUCT_ASK_QUESTION_LINK.'</a>';
	}
	
	function onBeforeDisplayProductListView(&$view){
		if (!$this->params->get('show_in_category', 0)) {
			return;
		}

		JSFactory::loadExtLanguageFile('product_ask_question');
		JHTML::_('behavior.modal');
		foreach ($view->rows as $product){
			if (!isset($product->_tmp_var_buttons)) {
				$product->_tmp_var_buttons = '';
			}
			$product->_tmp_var_buttons .= '<a style="display:inline-block;" href="'.SEFLink('index.php?option=com_jshopping&controller=product_ask_question&category_id='.$product->category_id.'&product_id='.$product->product_id, 1).'&tmpl=component" class="modal ask" rel="{handler: \'iframe\', size: {x: 700, y: 450}}" title="'._JSHOP_PRODUCT_ASK_QUESTION_LINK.'" >'._JSHOP_PRODUCT_ASK_QUESTION_LINK.'</a>';
		}
	}
	
}