<?php

/**
* @package Joomla
* @subpackage JoomShopping
* @author Garry
* @website https://joom-shopping.com
* @email info@joom-shopping.com
**/

defined( '_JEXEC' ) or die( 'Restricted access' );
require_once JPATH_SITE.'/components/com_jshopping/addons/addon_jshopping_save_cart/JshSCHelper.php';


class plgJshoppingCheckoutAddon_jshopping_save_cart extends JPlugin {
	
	const AADDON = 'addon_jshopping_save_cart';

	var $table = '#__jshopping_cart_for_user';
	
	public function __construct(& $subject, $config){
		parent::__construct($subject, $config);
	}

	private function _deleteCartForUser($user_id, $type_cart){
        $db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete($this->table);
		$query->where('user_id='.$user_id)->where('type_cart="'.$db->escape($type_cart).'"');
		$db->setQuery($query);
        return $db->query();
	}

	private function _loadCartForUser($user_id, $type_cart){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('products')->from($this->table);
		$query->where('user_id='.$user_id)->where('type_cart="'.$db->escape($type_cart).'"');
		$db->setQuery($query);
		$result = $db->loadResult();
		return strlen($result) ? unserialize($result) : array();
	}

	private function _insertCartForUser($user_id, $products = array(), $type_cart){
        $date_create = getJsDate();
		$lang = JshSCHelper::getLangCurrentShortTag();
		$cur = JSFactory::getConfig()->cur_currency;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->insert($this->table);
		$query->columns('user_id, products, type_cart, date_create, lang, cur');
		$query->values($user_id.", '".$db->escape(serialize($products))."', '".$db->escape($type_cart)."', '".$db->escape($date_create)."', '".$db->escape($lang)."', '".$cur."'");
		$db->setQuery($query);
		return $db->query();
	}

	private function _loadConfig() {
        $alias_addon = self::AADDON;
        $tableAddon = JTable::getInstance('addon', 'jshop');
        $tableAddon->loadAlias($alias_addon);
        $params = $tableAddon->getParams();
		return array('cart' => $params['save_for_cart'], 'wishlist' => $params['save_for_wishlist']);
	}

    private function _addProductsToCart(&$cart, array $products) {
        $res    = [];
        $errors = [];
        foreach ($products as $product) {
            $product_db = JSFactory::getTable('product', 'jshop');
            $product_db->load($product['product_id']);
            if (!$product_db->product_publish) {
                continue;
            }
            $res[] = (bool) $cart->add(
                $product['product_id'],
                $product['quantity'],
                unserialize($product['attributes']),
                unserialize($product['freeattributes']),
                [],
                true,
                $errors,
                false
            );
        }
        return !in_array(false, $res);
    }

	public function onBeforeCartLoad(&$cart){
		$config = $this->_loadConfig();
		if ($config[$cart->type_cart]) {
			$user = JFactory::getUser();
            $controller = JRequest::getVar('controller');
            $task = JRequest::getVar('task');
            $noSave = 0;
            if ($controller=='checkout' && ($task=="step6" || $task=="step7" || $task=="finish")){
                $noSave = 1;
            }
			if ($user->id && $noSave==0){
				$session = JFactory::getSession();
				$objcart = $session->get($cart->type_cart);
				if (isset($objcart) && $objcart!=''){
					$temp_cart = unserialize($objcart);
					if (count($temp_cart->products)) {
						$this->_deleteCartForUser($user->id, $cart->type_cart);
						$this->_insertCartForUser($user->id, $temp_cart->products, $cart->type_cart);
					} else {
                        $this->_addProductsToCart(
                            $temp_cart,
                            $this->_loadCartForUser($user->id, $cart->type_cart)
                        );
						$session->set($cart->type_cart, serialize($temp_cart));
					}
				} else {
                    $this->_addProductsToCart(
                        $cart,
                        $this->_loadCartForUser($user->id, $cart->type_cart)
                    );
					$session->set($cart->type_cart, serialize($cart));
				}
			}
		}
	}

	public function onAfterDeleteProductInCart($number_id, &$cart) {
		$config = $this->_loadConfig();
		if ($config[$cart->type_cart] && (count($cart->products) == 0)) {
			$user = JFactory::getUser();
			if ($user->id){
				$this->_deleteCartForUser($user->id, $cart->type_cart);
			}
		}
	}

    public function onEndCheckoutStep5(&$order, &$cart){
        if (isset($order->user_id) && $order->user_id) {
            $this->_deleteCartForUser($order->user_id, "cart");
        }
    }
    
    public function onBeforeShowEndFormStep6(&$order, &$cart, $pm_method){
        if (isset($order->user_id) && $order->user_id) {
            $this->_deleteCartForUser($order->user_id, "cart");
        }
    }

    public function onAfterDisplayCheckoutFinish(){
        $user = JFactory::getUser();
        if ($user->id) {
            $this->_deleteCartForUser($user->id, "cart");

            $session = JFactory::getSession();
            $session->set("cart", NULL);
        }
    }

    public function onStep7OrderCreated(&$order, &$res, &$checkout, &$pmconfigs){
        if (isset($order->user_id) && $order->user_id) {
            $this->_deleteCartForUser($order->user_id, "cart");

            $session = JFactory::getSession();
            $session->set("cart", NULL);
        }
    }
    
    

}