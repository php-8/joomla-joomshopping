<?php

defined('_JEXEC') or die;

class plgJshoppingCheckoutShopDockBar extends JPlugin {

	function _getModuleParams(){
		JFactory::getLanguage()->load('mod_jshopping_shopdockbar', JPATH_BASE.'/modules/mod_jshopping_shopdockbar/');
		$module = JModuleHelper::getModule('mod_jshopping_shopdockbar');
		$moduleParams = new JRegistry();
		$moduleParams->loadString($module->params);
		return $moduleParams->get('layoutdigit') ? $moduleParams : $moduleParams->get('layoutdigit');
	}

    function _saveUserCart($cart){
		$user_id = JFactory::getUser()->id;
		if (!$user_id) return;
		
		$usershop = JTable::getInstance('UserShop', 'jshop');
        $usershop->load($user_id);
		$type_cart = $cart->type_cart;
		$usershop->$type_cart = serialize($cart);
		$usershop->store();
	}

    function _loadUserCart($type_cart){
		$user_id = JFactory::getUser()->id;
		if (!$user_id) return;
		
		$usershop = JTable::getInstance('UserShop', 'jshop');
        $usershop->load($user_id);

		$cart = JModelLegacy::getInstance('cart', 'jshop');
		$cart->load($type_cart);
		
		if (count($cart->products)) {
			$this->_saveUserCart($cart);
		} else {
			JFactory::getSession()->set($type_cart, $usershop->$type_cart);
		}
	}

    function onAfterCartLoad(&$cart){
    }

    function onBeforeAddProductToCart(&$cart, &$product_id, &$quantity, &$attr_id, &$freeattributes, &$updateqty){
		$app = JFactory::getApplication();
		if (!$app->input->getInt('ajax')) return;
		if (!$moduleParams = $this->_getModuleParams()) return;

        $jshopConfig = JSFactory::getConfig();
        $attr_serialize = serialize($attr_id);
        $free_attr_serialize = serialize($freeattributes);

        $product = JTable::getInstance('product', 'jshop');
        $product->load($product_id);

        if ( (count($product->getRequireAttribute()) > count($attr_id)) || in_array(0, $attr_id)){
            JError::raiseNotice('', _JSHOP_SELECT_PRODUCT_OPTIONS);
			JError::raiseNotice('redirect_url', SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$app->input->getInt('category_id').'&product_id='.$product->product_id,1,1));
			jexit(getMessageJson());
        }

        if ($jshopConfig->admin_show_freeattributes){
            $allfreeattributes = $product->getListFreeAttributes();
            $error = 0;
            foreach($allfreeattributes as $k=>$v){
                if ($v->required && trim($freeattributes[$v->id])==""){
                    $error = 1;
                    JError::raiseNotice('', sprintf(_JSHOP_PLEASE_ENTER_X, $v->name));
                }
            }
            if ($error){
				JError::raiseNotice('redirect_url', SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$app->input->getInt('category_id').'&product_id='.$product->product_id,1,1));
				jexit(getMessageJson());
            }
        }
    }

    function onAfterAddProductToCart(&$cart, &$product_id, &$quantity, &$attr_id, &$freeattributes){
		if (!JFactory::getApplication()->input->getInt('ajax')) return;
		if (!$moduleParams = $this->_getModuleParams()) return;

		$jshopConfig = JSFactory::getConfig();
		$noimage = $jshopConfig->noimage;
		$noimage = $noimage ? $noimage : 'noimage.gif';
		$cart->addLinkToProducts(1,$cart->type_cart);
		$wishlist = $cart;
		$this->_saveUserCart($cart);
		ob_start();
		
		include_once str_replace('.php', '/'.$cart->type_cart.'.php', JModuleHelper::getLayoutPath('mod_jshopping_shopdockbar', 'default'));
		
		$cart_html = ob_get_contents();  
		ob_end_clean();
		
		jexit(json_encode(array('summ'=>formatprice($cart->getSum(0,1)),'count'=>formatqty($cart->count_product),'count_item'=>count($cart->products),'html'=>$cart_html,'type'=>$cart->type_cart)));
	}

    function onAfterRefreshProductInCart(&$quantity, &$cart){
		if (!$moduleParams = $this->_getModuleParams()) return;
		$this->_saveUserCart($cart);
	}

    function onAfterDeleteProductInCart(&$number_id, &$cart){
		if (!$moduleParams = $this->_getModuleParams()) return;
		$this->_saveUserCart($cart);
		$app = JFactory::getApplication();
		$app->redirect($_SERVER['HTTP_REFERER']);
		jexit();
	}

    function onAfterDeleteDataOrder(){
		if (!$moduleParams = $this->_getModuleParams()) return;
        $cart = JModelLegacy::getInstance('cart', 'jshop');
        $cart->load();
		$this->_saveUserCart($cart);
	}

    function onAfterLogin(){
		if (!$moduleParams = $this->_getModuleParams()) return;
		JFactory::getSession()->set('shopdockbar_login', 1);
		$this->_loadUserCart('cart');
		$this->_loadUserCart('wishlist');
	}

}
?>