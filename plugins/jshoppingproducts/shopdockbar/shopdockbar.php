<?php

defined('_JEXEC') or die;

class plgJshoppingProductsShopDockBar extends JPlugin {

	function _getModuleParams(){
		JFactory::getLanguage()->load('mod_jshopping_shopdockbar', JPATH_BASE.'/modules/mod_jshopping_shopdockbar/');
		$module = JModuleHelper::getModule('mod_jshopping_shopdockbar');
		$moduleParams = new JRegistry();
		$moduleParams->loadString($module->params);
		return $moduleParams->get('layoutdigit') ? $moduleParams : $moduleParams->get('layoutdigit');
	}

	function onAfterDisplayProduct($product) {
		if (!$moduleParams = $this->_getModuleParams()) {
			return;
		}
		$jshopConfig = JSFactory::getConfig();
		$session = JFactory::getSession();
		$shopdockbar_history = $session->get('shopdockbar_history', array());
		if (isset($shopdockbar_history[$product->product_id])) {
			unset($shopdockbar_history[$product->product_id]);
		}
		$shopdockbar_history[$product->product_id] = new stdClass();
		$shopdockbar_history[$product->product_id]->name = $product->name;
		if (isset($product->image)) {
			$shopdockbar_history[$product->product_id]->product_thumb_image = 'thumb_'.$product->image;
		} else if (isset($product->product_thumb_image)) {
			$shopdockbar_history[$product->product_id]->product_thumb_image = $product->product_thumb_image;
		} else if (isset($jshopConfig->noimage)) {
			$shopdockbar_history[$product->product_id]->product_thumb_image = $jshopConfig->noimage;
		} else {
			$shopdockbar_history[$product->product_id]->product_thumb_image = 'noimage.gif';
		}
		$shopdockbar_history[$product->product_id]->product_link = $_SERVER['REQUEST_URI'];
		$session->set('shopdockbar_history', $shopdockbar_history);
	}

	function onBeforeDisplayProductListView(&$view){
		$this->_getCompareLink($view, 'productlist');
	}

	function onBeforeDisplayProductView(&$view){        
		$this->_getCompareLink($view, 'product');
	}   

	function _getCompareLink(&$view, $type){
		if (!$moduleParams = $this->_getModuleParams()) return;
		$shopdockbar_compare = JFactory::getSession()->get('shopdockbar_compare');
		
		if ($moduleParams->get('show_compare')) {
			include_once JPATH_SITE.'/components/com_jshopping/templates/addons/shopdockbar/'.substr($moduleParams->get('layout', 'default'),$moduleParams->get('layoutdigit')).'/compare_link.php';
		}
	}   

}
?>