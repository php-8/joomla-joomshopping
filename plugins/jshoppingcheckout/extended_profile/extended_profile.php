<?php

defined('_JEXEC') or die;

class plgJshoppingcheckoutExtended_profile extends JPlugin {

	protected static $prod;

    function onBeforeLoadWishlistRemoveToCart(&$number_id) {
        $wishlist = JModelLegacy::getInstance('cart', 'jshop');
        $wishlist->load('wishlist');
        $this->prod = $wishlist->products[$number_id];
	}

    function onAfterWishlistRemoveToCart(&$cart) {
        $wishlist = JModelLegacy::getInstance('cart', 'jshop');
        $wishlist->load('wishlist');
        $attr = unserialize($this->prod['attributes']);
        $freeattribut = unserialize($this->prod['freeattributes']);
        $wishlist->add($this->prod['product_id'], $this->prod['quantity'], $attr, $freeattribut);
		unset($this->prod);
	}

    function onBeforeDisplayWishlistView(&$view) {
		if (JFactory::getUser()->id) {
			JFactory::getApplication()->redirect(SEFLink('index.php?option=com_jshopping&controller=user', 0, 1).'#exac_wishlist');
		}
	}

    function onBeforeDisplayMyAccountView(&$view) {
		JSFactory::loadExtLanguageFile('extended_profile');
		$db = JFactory::getDBO();
		$jshopConfig = JSFactory::getConfig();
        $lang =  JSFactory::getLang();
		
        JPluginHelper::importPlugin('jshoppingorder');
        JPluginHelper::importPlugin('jshoppingcheckout');
        $dispatcher = JDispatcher::getInstance();
		
        $field_name = $lang->get('name');
        $country = JTable::getInstance('country', 'jshop');
        $country->load($view->user->d_country);
        $order = JTable::getInstance('order', 'jshop');
        $view->user->d_country = $country->$field_name;
		
		$cart = JModelLegacy::getInstance('cart', 'jshop');
		$cart->load('wishlist');
        $cart->setDisplayFreeAttributes();
        $dispatcher->trigger( 'onBeforeDisplayWishlist', array(&$cart) );
		
		$db->setQuery('SELECT * FROM `#__jshopping_coupons` WHERE coupon_publish=1 AND for_user_id='.$view->user->user_id);
		$coupons = $db->loadObjectList();
        $dispatcher->trigger( 'onBeforeDisplayExtendedCoupons', array(&$coupons, $view->user) );
		
		$db->setQuery('SELECT payment_id, `'.$field_name.'` as name FROM `#__jshopping_payment_method` WHERE 1');
		$order_payment_methods = $db->loadObjectList('payment_id');
		$db->setQuery('SELECT shipping_id, `'.$field_name.'` as name FROM `#__jshopping_shipping_method` WHERE 1');
		$order_shipping_methods = $db->loadObjectList('shipping_id');
		$orders = $order->getOrdersForUser($view->user->user_id);
		$order_total_qnt = 0;
		$order_total_ammount = 0;
		$order_total_shipping = 0;
		$order_total_payment = 0;
		$order_status = array();
        foreach($orders as $key=>$value){
			if (!isset($order_status[$value->status_name])) {
				$order_status[$value->status_name] = new stdClass();
				$order_status[$value->status_name]->qty = 0;
				$order_status[$value->status_name]->sum = 0;
			}
			$order_total_qnt += 1;
			$order_total_ammount += $value->order_total * $jshopConfig->currency_value / $value->currency_exchange;
			$order_total_shipping += $value->order_shipping * $jshopConfig->currency_value / $value->currency_exchange;
			$order_total_payment +=  $value->order_payment * $jshopConfig->currency_value / $value->currency_exchange;
			$order_status[$value->status_name]->qty += 1;
			$order_status[$value->status_name]->sum += $value->order_total * $jshopConfig->currency_value / $value->currency_exchange;
			$orders[$key]->status_name = '<span onmouseover="viewStatusTooltip(this, \'show\')" onmouseout="viewStatusTooltip(this, \'hide\')">'.$orders[$key]->status_name.'</span>';
            $orders[$key]->order_href = SEFLink('index.php?option=com_jshopping&controller=user&task=order&order_id='.$value->order_id,0,0,$jshopConfig->use_ssl);
            $orders[$key]->payment_method = $orders[$key]->payment_method_id ? $order_payment_methods[$orders[$key]->payment_method_id]->name : '';
            $orders[$key]->shipping_method = $orders[$key]->shipping_method_id ? $order_shipping_methods[$orders[$key]->shipping_method_id]->name : '';
			$db->setQuery('SELECT oh.status_date_added, os.`'.$field_name.'` as name FROM `#__jshopping_order_history` AS oh 
							LEFT JOIN `#__jshopping_order_status` AS os ON os.status_id = oh.order_status_id WHERE oh.order_status_id > 0 AND oh.order_id ='.$orders[$key]->order_id.' ORDER BY oh.status_date_added');
			$orders[$key]->order_history = $db->loadObjectList();
			$db->setQuery('SELECT * FROM `#__jshopping_order_item` WHERE order_id = '.$orders[$key]->order_id);
			$orders[$key]->order_items = $db->loadObjectList();
        }
        $dispatcher->trigger( 'onBeforeDisplayListOrder', array(&$orders) );
		
		$view->assign('config',$jshopConfig);
		$view->assign('products', $cart->products);
		$view->assign('coupons',$coupons);
		$view->assign('orders',$orders);
		$view->assign('order_total_qnt',$order_total_qnt);
		$view->assign('order_total_ammount',$order_total_ammount);
		$view->assign('order_total_shipping',$order_total_shipping);
		$view->assign('order_total_payment',$order_total_payment);
		$view->assign('order_status',$order_status);

        $dispatcher->trigger( 'onBeforeDisplayExtendedProfile', array(&$view) );
		
		$view->addTemplatePath(JPATH_COMPONENT.'/templates/addons/extended_profile');
		$view->setLayout('myextendedaccount');
    }

}
?>