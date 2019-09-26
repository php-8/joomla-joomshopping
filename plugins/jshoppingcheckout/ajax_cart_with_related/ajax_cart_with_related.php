<?php

defined('_JEXEC') or die;

class plgJshoppingcheckoutAjax_cart_with_related extends JPlugin {

	private $_db;
	private $current_product;
	private $new_product;

	protected function init(){
		$this->_db = JFactory::getDBO();
		JFactory::getLanguage()->load('mod_jshopping_ajax_cart_with_related', JPATH_SITE.'/modules/mod_jshopping_ajax_cart_with_related');
		$module = JModuleHelper::getModule('mod_jshopping_ajax_cart_with_related');
		$moduleParams = new JRegistry;
		$moduleParams->loadString($module->params);
		$params = new stdClass;
		$params->in_cart = $moduleParams->get('in_cart', 1);
		$params->after_add = $moduleParams->get('after_add', 1);
		$params->layout = $moduleParams->get('layout', 'default');
		$params->template = substr($params->layout, 2);
		$params->cart_var = $moduleParams->get('cart_var', '_tmp_ext_html_before_discount');
		$params->link_category = $moduleParams->get('link_category', 1);

		return $params;
	}

	protected function getCategorys(){
		$query = 'SELECT category_id, `'.JSFactory::getLang()->get('name').'` as name FROM `#__jshopping_categories`';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList('category_id');
	}

	protected function getRelatedProducts($cart_products, $product_id=null){
		$products = array();
		foreach ($cart_products as $product) {
			$products[$product['product_id']] = $product['product_id'];
		}
		$products = implode(',', $products);
		if (!$product_id) {
			$product_id = $products;
		}
		if (!$products || !$product_id || !JSFactory::getConfig()->admin_show_product_related) {
			return array();
		}

		$table = JTable::getInstance('Product', 'jshop');
		$adv_query = '';
		$adv_from = '';
		$adv_result = $table->getBuildQueryListProductDefaultResult();
		$filters = array();
		$table->getBuildQueryListProductSimpleList('related', null, $filters, $adv_query, $adv_from, $adv_result);

		$this->_db = JFactory::getDBO();
		$query = 'SELECT '.$adv_result.' FROM `#__jshopping_products_relations` AS relation
				INNER JOIN `#__jshopping_products` AS prod ON relation.product_related_id = prod.product_id
				LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat ON pr_cat.product_id = relation.product_related_id
				LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
				'.$adv_from.'
				WHERE relation.product_id IN ('.$product_id.')
				AND relation.product_related_id NOT IN ('.$products.')
				AND cat.category_publish=1
				AND prod.product_publish = 1
				'.$adv_query.'
				GROUP BY prod.product_id
				ORDER BY relation.id';
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		$rows = listProductUpdateData($rows, 1);
		$products_related = array();
		foreach ($rows as $product_related) {
			if (!isset($products_related[$product_related->category_id])) {
				$products_related[$product_related->category_id] = array();
			}
			$products_related[$product_related->category_id][$product_related->product_id] = $product_related;
		}
		return $products_related;
	}

	function onBeforeSaveNewProductToCart(&$cart, &$temp_product, &$product, &$errors, &$displayErrorMessage){
		if ($cart->type_cart == 'cart') {
			$this->current_product = $product;
			$this->new_product = true;
		}
	}

	function onBeforeSaveUpdateProductToCart(&$cart, &$product, $key, &$errors, &$displayErrorMessage, &$product_in_cart, &$quantity){
		if ($cart->type_cart == 'cart') {
			$product->getDescription();
			$this->current_product = $product;
			$this->new_product = false;
		}
	}

	function onAfterAddProductToCart(&$cart, &$product_id, &$quantity, &$attr_id, &$freeattributes, &$errors, &$displayErrorMessage){
		if (!$this->current_product || $this->current_product->product_id != $product_id) {
			return;
		}
		$params = $this->init();
		$jshopConfig = JSFactory::getConfig();
		$cart->format_price = formatprice($cart->getSum(0,1));
		$cart->format_qty = formatqty($cart->count_product);
		$all_categorys = array();
		if ($params->after_add) {
			$products_related = $this->getRelatedProducts($cart->products, $this->current_product->product_id);
			if (count($products_related)) {
				$all_categorys = $this->getCategorys();
			}
		} else {
			$products_related = array();
		}
		if (property_exists($this->current_product, 'image')) {
			if ($this->current_product->image) {
				$this->current_product->thumb_image = 'thumb_'.$this->current_product->image;
			} else {
				$this->current_product->thumb_image = $jshopConfig->noimage;
			}
		} else {
			if ($this->current_product->product_thumb_image) {
				$this->current_product->thumb_image = $this->current_product->product_thumb_image;
			} else {
				$this->current_product->thumb_image = $jshopConfig->noimage;
			}
		}
		ob_start();
		include_once dirname(__FILE__).'/tmpl/'.$params->template.'/modal.php';
		$cart->html = ob_get_contents();
		ob_end_clean();
		$cart->pr = $this->current_product;

	}

	function onBeforeDisplayCartView(&$view){
		$params = $this->init();
		if (!$params->in_cart) {
			return;
		}
		$products_related = $this->getRelatedProducts($view->products);
		if (!count($products_related)) {
			return;
		}
		$all_categorys = $this->getCategorys();
		$_tmp_var = $params->cart_var;
		if (!isset($view->$_tmp_var)) {
			$view->$_tmp_var = '';
		}
		ob_start();
		include_once dirname(__FILE__).'/tmpl/'.$params->template.'/related.php';
		$view->$_tmp_var .= ob_get_contents();
		ob_end_clean();
	}

}
?>