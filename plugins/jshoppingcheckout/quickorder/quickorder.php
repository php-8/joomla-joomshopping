<?php

defined('_JEXEC') or die;

class plgJshoppingCheckoutQuickOrder extends JPlugin {

	function __construct($properties = null) {
		JFactory::getLanguage()->load('plg_jshoppingproducts_quickorder', JPATH_SITE.'/plugins/jshoppingproducts/quickorder', null, false, 'en-GB');
		$addon = JTable::getInstance('addon', 'jshop');
		$addon->loadAlias('addon_quickorder');
		if (true) {
			$this->addonParams = (object)$addon->getParams();
			JDispatcher::getInstance()->register(
				'onConstructJshoppingControllerCart',
				function(&$controller){
					$to = JFactory::getApplication()->input->getRaw('to');
					if ($to == 'quickorder') {
						JFactory::getSession()->clear($to);
					}
				}
			);
			JDispatcher::getInstance()->register(
				'onBeforeAddProductToCart',
				function(&$cart, &$product_id, &$quantity, &$attr_id, &$freeattributes, &$updateqty, &$errors, &$displayErrorMessage, &$additional_fields, &$usetriggers){
					if ($cart->type_cart != 'quickorder') {
						return;
					}
					if (!$this->addonParams->enable) {
						return;
					}
					JSession::checkToken() or die;
					$app = JFactory::getApplication();
					$adv_user = JSFactory::getUser();
					$adv_user->f_name = $app->input->getString('f_name');
					$adv_user->l_name = $app->input->getString('l_name');
					$adv_user->email = $app->input->getString('email');
					$adv_user->{$this->addonParams->which_phone} = $app->input->getString('phone');
					$adv_user->store();
					if ($this->addonParams->attr_require) {
						return;
					}
					$product = JSFactory::getTable('product', 'jshop');
					$product->load($product_id);
					$requireAttribute = $product->getRequireAttribute();
					if (count($requireAttribute) > count($attr_id) || in_array(0, $attr_id)){
						$attributesDatas = $product->getAttributesDatas();
						foreach ($requireAttribute as $k) {
							if (!isset($attr_id[$k]) || !$attr_id[$k]) {
								if (isset($attributesDatas['attributeValues'][$k]) && isset($attributesDatas['attributeValues'][$k][0])) {
									$attr_id[$k] = $attributesDatas['attributeValues'][$k][0]->val_id;
								}
							}
						}
					}
				}
			);
			JDispatcher::getInstance()->register(
				'onAfterCartAddOk',
				function(&$cart, &$product_id, &$quantity, &$attribut, &$freeattribut){
					if ($cart->type_cart != 'quickorder') {
						return;
					}
					if (!$this->addonParams->enable) {
						return;
					}
					
					$app = JFactory::getApplication();
					$jshopConfig = JSFactory::getConfig();

					if ($this->addonParams->check_summ) {
						if ($jshopConfig->min_price_order && ($cart->getPriceProducts() < ($jshopConfig->min_price_order * $jshopConfig->currency_value) )){
							$app->enqueueMessage(sprintf(_JSHOP_ERROR_MIN_SUM_ORDER, formatprice($jshopConfig->min_price_order * $jshopConfig->currency_value)), 'error');
							$app->redirect($_SERVER['HTTP_REFERER']);
						}
						
						if ($jshopConfig->max_price_order && ($cart->getPriceProducts() > ($jshopConfig->max_price_order * $jshopConfig->currency_value) )){
							$app->enqueueMessage(sprintf(_JSHOP_ERROR_MAX_SUM_ORDER, formatprice($jshopConfig->max_price_order * $jshopConfig->currency_value)), 'error');
							$app->redirect($_SERVER['HTTP_REFERER']);
						}
					}

					$dispatcher = JDispatcher::getInstance();
					$adv_user = JSFactory::getUser();
					
					$cart->setDisplayFreeAttributes();
					$checkout = JSFactory::getModel('checkoutOrder', 'jshop');
					$checkout->setCart($cart);

					$post = array(
						'order_add_info' => $app->input->getString('comment')
					);

					$order = $checkout->orderDataSave($adv_user, $post);
					
					$dispatcher->trigger('onEndCheckoutStep5', array(&$order, &$cart));
					
					$checkout->setSendEndForm(0);
					
					if ($this->addonParams->redirect_finish) {
						$checkout->setMaxStep(10);
						$app->redirect(JSFactory::getModel('checkoutStep', 'jshop')->getCheckoutUrl('finish'));
					}

					$app->enqueueMessage(JText::_('PLG_JSHOPPINGPRODUCTS_QUICKORDER_THANX'));
					$app->redirect($_SERVER['HTTP_REFERER']);
				}
			);
			//// joom-shopping.com
			JDispatcher::getInstance()->register(
				'onBeforeCreateOrder',
				function(&$order, &$cart, &$model){
					if ($cart->type_cart != 'quickorder') {
						return;
					}
					if (!$this->addonParams->enable) {
						return;
					}
					$order->order_created = 1;
				}
			);
		}
	}

}