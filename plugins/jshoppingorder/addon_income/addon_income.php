<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgJshoppingOrderAddon_income extends JPlugin {
    
	function __construct(& $subject, $config){
		parent::__construct($subject, $config);
	}
    
	function onBeforeCreateOrder(&$order)
    {
        $cart = JModelLegacy::getInstance('cart', 'jshop');
        $cart->load();
        $cart->setDisplayItem(1, 1);
        $cart->setDisplayFreeAttributes();
        
        $buy_price_subtotal=0;
        if ( isset($cart->products) && count($cart->products) > 0 ) {
            foreach ($cart->products as $p) {
                $buy_price_subtotal += ($p["product_buy_price"] * $p["quantity"]);
            }
        }
        $order->buy_price_subtotal = $buy_price_subtotal;
	}
    
    function onBeforeSaveOrderItem(&$order_item, &$value)
    {
        $order_item->product_buy_price = $value['product_buy_price'];
	}
}
?>