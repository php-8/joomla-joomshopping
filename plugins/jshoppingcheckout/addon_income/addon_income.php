<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgJshoppingCheckoutAddon_income extends JPlugin {
    
	function __construct(& $subject, $config){
		parent::__construct($subject, $config);
	}
	
	function onBeforeSaveNewProductToCart(&$cart, &$temp_product, &$product)
    {
        $jshopConfig = JSFactory::getConfig();
        $product_buy_price = 0;
        if ( isset($product->attribute_active_data->buy_price) && floatval($product->attribute_active_data->buy_price) > 0 ) 
        {
            $product_buy_price = $product->attribute_active_data->buy_price;
        } else {
            $product_buy_price = $product->product_buy_price;
        }
        if ($product_buy_price){
            $product_buy_price = getPriceFromCurrency($product_buy_price, $product->currency_id);
            $product_buy_price = getPriceCalcParamsTax($product_buy_price, $product->product_tax_id);
        }
        if ($jshopConfig->price_product_round){
            $product_buy_price = round($product_buy_price, $jshopConfig->decimal_count);
        }
        $temp_product['product_buy_price'] = $product_buy_price;
	}
}