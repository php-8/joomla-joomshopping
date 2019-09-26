<?php

defined('_JEXEC') or die;

class plgJshoppingProductsQuickOrder extends JPlugin {

	private $addonParams;
	private $addonForm;

	private function _init() {
		if (!$this->addonParams) {
			JFactory::getLanguage()->load('plg_jshoppingproducts_quickorder', JPATH_SITE.'/plugins/jshoppingproducts/quickorder', null, false, 'en-GB');
			$addon = JTable::getInstance('Addon', 'jshop');
			$addon->loadAlias('addon_quickorder');
			$this->addonParams = (object)$addon->getParams();
			if ($this->addonParams->enable) {
				$adv_user = JSFactory::getUser();
				ob_start();
				if (is_file(__DIR__ . '/tmpl/form.custom.php')) {
					include __DIR__ . '/tmpl/form.custom.php';
				} else {
					include __DIR__ . '/tmpl/form.php';
				}
				$this->addonForm = ob_get_contents();  
				ob_end_clean();

				if ($this->addonParams->load_assets) {
					$document = JFactory::getDocument();
					$document->addScript(JURI::base(true).'/plugins/jshoppingproducts/quickorder/assets/script.js');
					$document->addStyleSheet(JURI::base(true).'/plugins/jshoppingproducts/quickorder/assets/style.css');
				}
			}
		}
	}

	function onAfterRender() {
        if (!$this->addonForm) {
            return;
        }
        $app = JFactory::getApplication();
        $app->setBody(str_ireplace('</body>', $this->addonForm.'</body>', $app->getBody(false)));
    }

    function onBeforeDisplayProductView(&$view){
		$this->_init();
		if (!$this->addonParams->enable) {
			return;
		}
		if (!$this->addonParams->insert_var) {
			$this->addonParams->insert_var = '_tmp_product_html_buttons';
		}
		if (!isset($view->{$this->addonParams->insert_var})) {
			$view->{$this->addonParams->insert_var} = '';
		}
		ob_start();
		if (is_file(__DIR__ . '/tmpl/button_product.custom.php')) {
			include __DIR__ . '/tmpl/button_product.custom.php';
		} else {
			include __DIR__ . '/tmpl/button_product.php';
		}
		$view->{$this->addonParams->insert_var} .= ob_get_contents();  
		ob_end_clean();
	}
    //// joom-shopping.com
    function onBeforeDisplayProductList(&$products){
		$this->_init();
		if (!$this->addonParams->enable || !$this->addonParams->show_in_list) {
			return;
		}
		if (!$this->addonParams->insert_var_list) {
			$this->addonParams->insert_var_list = '_tmp_var_buttons';
		}
		foreach ($products as $key=>$product) {
			if (!isset($products[$key]->{$this->addonParams->insert_var_list})) {
				$products[$key]->{$this->addonParams->insert_var_list} = '';
			}
			ob_start();
			if (is_file(__DIR__ . '/tmpl/button_list.custom.php')) {
				include __DIR__ . '/tmpl/button_list.custom.php';
			} else {
				include __DIR__ . '/tmpl/button_list.php';
			}
			$products[$key]->{$this->addonParams->insert_var_list} .= ob_get_contents();  
			ob_end_clean();
		}
	}

}