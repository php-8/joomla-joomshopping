<?php
defined('_JEXEC') or die('Restricted access');

class plgJshoppingAdminAddon_Income extends JPlugin
{
    public function __construct(&$subject, $config = array()){
        
        parent::__construct($subject, $config);  
    }
    
    public function onAfterSaveOrder(&$order, &$file_generete_pdf_order){
        $order_items = $order->getAllItems();
        $jshopConfig = JSFactory::getConfig();
        
        $currency_value = $jshopConfig->currency_value;
        $jshopConfig->currency_value = $order->currency_exchange;
        if (count($order_items)){
            $buy_price_subtotal = 0;
            $product = JTable::getInstance('product', 'jshop');
            $orderItem = JTable::getInstance('orderitem', 'jshop');
            
            foreach ($order_items as $item) {
                $product->load($item->product_id);
                $product->product_buy_price;
                
                if ($product->product_buy_price){
                    $product->product_buy_price = getPriceFromCurrency($product->product_buy_price, $product->currency_id);
                    $product->product_buy_price = getPriceCalcParamsTax($product->product_buy_price, $product->product_tax_id);
                }
                if ($jshopConfig->price_product_round){
                    $product->product_buy_price = round($product->product_buy_price, $jshopConfig->decimal_count);
                }
                
                $orderItem->load($item->order_item_id);
                $orderItem->product_buy_price = $product->product_buy_price;
                $orderItem->store();
                
                $buy_price_subtotal += $product->product_buy_price * $item->product_quantity;
            }
            
            $_order = JTable::getInstance('order', 'jshop');
            $_order->load($order->order_id);
            $_order->buy_price_subtotal = $buy_price_subtotal;
            $_order->store();
        }
        
        $jshopConfig->currency_value = $currency_value;
    }
}