<?php

defined('_JEXEC') or die;

class plgJshoppingExtended_profile extends JPlugin {

	private $wishlistProducts;
	private $addonParams;

    function onBeforeLoadWishlistRemoveToCart(&$number_id) {
		if (!$this->_init()) {
			return;
		}
		if (!$this->addonParams->not_delete_wishlist) {
			return;
		}

        $wishlist = JModelLegacy::getInstance('cart', 'jshop');
        $wishlist->load('wishlist');
        $this->wishlistProducts = $wishlist->products;
	}

    function onAfterWishlistRemoveToCart(&$cart) {
		if (!$this->wishlistProducts) {
			return;
		}

        $wishlist = JModelLegacy::getInstance('cart', 'jshop');
        $wishlist->load('wishlist');
        $wishlist->products = $this->wishlistProducts;
        $wishlist->saveToSession();
		unset($this->wishlistProducts);
	}

    function onBeforeDisplayWishlistView(&$view) {
		if (!$this->_init()) {
			return;
		}

		if (JFactory::getUser()->id) {
			JFactory::getApplication()->redirect(SEFLink('index.php?option=com_jshopping&controller=user', 0, 1).'#exac_wishlist');
		}
	}
    function onAfterLoadOrder( &$order, &$user ){
		if (!$this->_init()) {
			return;
		}

		if ($this->addonParams->admin_access) {
			$order->user_id = $user->id;
		}
    }

    function onBeforeDisplayMyAccountView(&$view) {
		if (!$this->_init()) {
			return;
		}

		$document = JFactory::getDocument();
		$document->addScript(JUri::root(true).'/components/com_jshopping/templates/addons/extended_profile/js/'.$this->addonParams->template.'.js');
		if (is_file(JPATH_SITE.'/components/com_jshopping/templates/addons/extended_profile/js/'.$this->addonParams->template.'.custom.js')) {
			$document->addScript(JUri::root(true).'/components/com_jshopping/templates/addons/extended_profile/js/'.$this->addonParams->template.'.custom.js');
		}
		$document->addStyleSheet(JUri::root(true).'/components/com_jshopping/templates/addons/extended_profile/css/'.$this->addonParams->template.'.css');
		if (is_file(JPATH_SITE.'/components/com_jshopping/templates/addons/extended_profile/css/'.$this->addonParams->template.'.custom.css')) {
			$document->addStyleSheet(JUri::root(true).'/components/com_jshopping/templates/addons/extended_profile/css/'.$this->addonParams->template.'.custom.css');
		}

		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$jshopConfig = JSFactory::getConfig();
        $lang =  JSFactory::getLang();
        $adv_user = new jshopUserShop($db);
		$adv_user->load($view->user->user_id);
		
        JPluginHelper::importPlugin('jshoppingorder');
        JPluginHelper::importPlugin('jshoppingcheckout');
        $dispatcher = JDispatcher::getInstance();
		
        $field_name = $lang->get('name');
        $country = JTable::getInstance('country', 'jshop');
        $order = JTable::getInstance('order', 'jshop');
		$group = JTable::getInstance('userGroup', 'jshop');

        if (!$adv_user->country) {
			$adv_user->country = $jshopConfig->default_country;
		}
        if (!$adv_user->d_country) {
			$adv_user->d_country = $jshopConfig->default_country;
		}
        $adv_user->birthday = getDisplayDate($adv_user->birthday, $jshopConfig->field_birthday_format);
        $adv_user->d_birthday = getDisplayDate($adv_user->d_birthday, $jshopConfig->field_birthday_format);

        $list_country = $country->getAllCountries();
        $option_country[] = JHTML::_('select.option', 0, _JSHOP_REG_SELECT, 'country_id', 'name' );
        $option_countrys = array_merge($option_country, $list_country);
        $view->select_countries = JHTML::_('select.genericlist', $option_countrys,'country','class = "inputbox" size = "1"','country_id', 'name',$adv_user->country );
        $view->select_d_countries = JHTML::_('select.genericlist', $option_countrys,'d_country','class = "inputbox" size = "1"','country_id', 'name',$adv_user->d_country );

		$view->groupList = $group->getList();

		$view->d_adress = 0;
		foreach ($view->config_fields as $fieldName=>$fieldData) {
			if (substr($fieldName, 0, 2) == 'd_' && $fieldData['display']) {
				$view->d_adress = 1;
				break;
			}
		}
		$option_title = array();
        foreach($jshopConfig->user_field_title as $key => $value){
            $option_title[] = JHTML::_('select.option', $key, $value, 'title_id', 'title_name' );
        }
        $view->select_titles = JHTML::_('select.genericlist', $option_title,'title','class = "inputbox"','title_id','title_name',$adv_user->title );
        $view->select_d_titles = JHTML::_('select.genericlist', $option_title,'d_title','class = "inputbox"','title_id','title_name',$adv_user->d_title );
		
		$cart = JModelLegacy::getInstance('cart', 'jshop');
		$cart->load('wishlist');
        $cart->setDisplayFreeAttributes();
        $dispatcher->trigger('onBeforeDisplayWishlist', array(&$cart));
		
		$db->setQuery('SELECT * FROM `#__jshopping_coupons` WHERE coupon_publish=1 AND for_user_id='.$view->user->user_id);
		$coupons = $db->loadObjectList();
        $dispatcher->trigger('onBeforeDisplayExtendedCoupons', array(&$coupons, $view->user));
		
		$db->setQuery('SELECT payment_id, `'.$field_name.'` as name FROM `#__jshopping_payment_method` WHERE 1');
		$order_payment_methods = $db->loadObjectList('payment_id');
		$db->setQuery('SELECT shipping_id, `'.$field_name.'` as name FROM `#__jshopping_shipping_method` WHERE 1');
		$order_shipping_methods = $db->loadObjectList('shipping_id');
		$db->setQuery('SELECT status_id, `'.$field_name.'` as name FROM `#__jshopping_order_status` WHERE 1');
		$order_statuses = $db->loadObjectList('status_id');
		
        $context = "jshopping.list.user.orders";
        $limit = $app->getUserStateFromRequest( $context.'limit', 'limit', $app->getCfg('list_limit'), 'int' );
        $limitstart = $app->input->getInt('limitstart');
        $exac_tab = $app->input->getString('exac_tab');
		$where = '';
		if (!$this->addonParams->admin_access) {
			$where = ' AND user_id='.$db->escape($view->user->user_id);
		}
		$db->setQuery('SELECT COUNT(order_id) FROM `#__jshopping_orders` WHERE 1'.$where);
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);
		$pageNav->prefix = 'exac_tab=orders&';
		$db->setQuery('SELECT * FROM `#__jshopping_orders` WHERE 1'.$where.' ORDER BY order_number DESC', $pageNav->limitstart, $pageNav->limit);
		$orders = $db->loadObjectList('order_id');
		$order_total_qnt = 0;
		$order_total_ammount = 0;
		$order_total_shipping = 0;
		$order_total_payment = 0;
		$order_status = array();
        foreach($orders as $key=>$order){
			if (!isset($order_status[$order->order_status])) {
				$order_status[$order->order_status] = new stdClass();
				$order_status[$order->order_status]->qty = 0;
				$order_status[$order->order_status]->sum = 0;
			}
			$order_total_qnt += 1;
			$order_total_ammount += $order->order_total * $jshopConfig->currency_value / $order->currency_exchange;
			$order_total_shipping += $order->order_shipping * $jshopConfig->currency_value / $order->currency_exchange;
			$order_total_payment +=  $order->order_payment * $jshopConfig->currency_value / $order->currency_exchange;
			$order_status[$order->order_status]->qty += 1;
			$order_status[$order->order_status]->sum += $order->order_total * $jshopConfig->currency_value / $order->currency_exchange;
			$orders[$key]->status_name = $order_statuses[$order->order_status]->name;
            $orders[$key]->order_href = SEFLink('index.php?option=com_jshopping&controller=user&task=order&order_id='.$order->order_id,0,0,$jshopConfig->use_ssl);
            $orders[$key]->payment_method = $orders[$key]->payment_method_id ? $order_payment_methods[$orders[$key]->payment_method_id]->name : '';
            $orders[$key]->shipping_method = $orders[$key]->shipping_method_id ? $order_shipping_methods[$orders[$key]->shipping_method_id]->name : '';
			$db->setQuery("SELECT i.*, c.category_id FROM `#__jshopping_order_item` as i LEFT JOIN `#__jshopping_products_to_categories` as c ON i.product_id = c.product_id WHERE order_id =".$orders[$key]->order_id." GROUP BY i.order_item_id");
			$orders[$key]->order_items = $db->loadObjectList();
			addLinkToProducts($orders[$key]->order_items, 0, 1);
			if ($this->addonParams->show_orders_from) {
				if (count($orders[$key]->order_items) >= $this->addonParams->show_orders_from) {
					$orders[$key]->show_order_items = 0;
				} else {
					$orders[$key]->show_order_items = 1;
				}
			} else {
				$orders[$key]->show_order_items = 1;
			}
			if ($jshopConfig->client_allow_cancel_order && $order->order_status!=$jshopConfig->payment_status_for_cancel_client && !in_array($order->order_status, $jshopConfig->payment_status_disable_cancel_client) ){
				$orders[$key]->cancel_link = SEFLink('index.php?option=com_jshopping&controller=user&task=cancelorder&order_id='.$order->order_id);
			} else {
				$orders[$key]->cancel_link = '';
			}
			$orders[$key]->_tmp_after_order_date = $orders[$key]->_tmp_after_order_summ = '';
        }
        $dispatcher->trigger( 'onBeforeDisplayListOrder', array(&$orders) );
		
		foreach ($this->addonParams->tabs as $tab) {
			$view->extendedTabs[$tab] = new stdClass;
			$view->extendedTabs[$tab]->id = 'exac_'.$tab;
			$view->extendedTabs[$tab]->name = JText::_('EXAC_TAB_'.strtoupper($tab));
			$view->extendedTabs[$tab]->image = '/components/com_jshopping/templates/addons/extended_profile/images/tab_'.$tab.'.png';
		}
		$view->active_tab = $exac_tab != '' ? $exac_tab : $this->addonParams->active_tab;
		
		$view->config = $jshopConfig;
		$view->products = (array)$cart->products;
		$view->coupons = (array)$coupons;
		$view->orders = (array)$orders;
		$view->order_total_qnt = $order_total_qnt;
		$view->order_total_ammount = $order_total_ammount;
		$view->order_total_shipping = $order_total_shipping;
		$view->order_total_payment = $order_total_payment;
		$view->order_statuses = $order_statuses;
		$view->order_status = $order_status;
		$view->pageNav = $pageNav;
		
        $view->action = SEFLink('index.php?option=com_jshopping&controller=user&task=accountsave',0,0,$jshopConfig->use_ssl);
        $view->live_path = JUri::root();
		$view->date_now = strtotime(date('Y-m-d'));
		if ($this->addonParams->show_acymailing_link && JComponentHelper::isEnabled('com_acymailing')){
			$view->acymailing_link = JRoute::_('index.php?option=com_acymailing&view=user&layout=modify');
		} else {
			$view->acymailing_link = '';
		}
		if ($this->addonParams->show_slogin_link && JComponentHelper::isEnabled('com_slogin')){
			$view->slogin_link = JRoute::_('index.php?option=com_slogin&view=fusion');
		} else {
			$view->slogin_link = '';
		}

        $dispatcher->trigger('onBeforeDisplayExtendedProfile', array(&$view));
        $dispatcher->trigger('onBeforeDisplayEditAccountView', array(&$view));
		
		$view->addTemplatePath(JPATH_COMPONENT.'/templates/addons/extended_profile');
		$view->setLayout($this->addonParams->template);
    }

	private function _init() {
		if (!$this->addonParams) {
			$addon = JTable::getInstance('addon', 'jshop');
			$addon->loadAlias('addon_extended_profile');
			$this->addonParams = new stdClass;
			$this->addonParams->enable = false;
			$addonParams = $addon->getParams();
			if ($addonParams['enable']) {
				JFactory::getLanguage()->load('plg_jshopping_extended_profile', JPATH_SITE . '/plugins/jshopping/extended_profile', null, false, 'en-GB');
				$this->addonParams = (object)$addonParams;
				$authorisedViewLevels = JFactory::getUser()->getAuthorisedViewLevels();
				$this->addonParams->admin_access = in_array($this->addonParams->admin_access, $authorisedViewLevels);
				$jshopConfig = JSFactory::getConfig();
				$extendedTabs = array();
				if (is_array($jshopConfig->extendedAccountTabs)) {
					$configExtendedTabs = $jshopConfig->extendedAccountTabs;
				} else {
					$configExtendedTabs = array('info', 'orders', 'wishlist', 'coupons', 'discount');
				}
				foreach ($configExtendedTabs as $configExtendedTab) {
					if (!$this->addonParams->{'disable_tab_'.$configExtendedTab}) {	
						$extendedTabs[] = $configExtendedTab;
					}
					unset($this->addonParams->{'disable_tab_'.$configExtendedTab});
				}
				$this->addonParams->tabs = $extendedTabs;
				$this->addonParams->active_tab = $jshopConfig->extendedAccountActiveTab ? $jshopConfig->extendedAccountActiveTab : (isset($extendedTabs[0]) ? $extendedTabs[0] : '');
			}
		}
		return $this->addonParams->enable;
	}
	
}