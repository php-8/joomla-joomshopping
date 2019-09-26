<?php

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelAddon_income extends JModelLegacy
{
    function getIncomeGeneral($time='day', $payment_status)
    {
        $db = JFactory::getDBO();
        
        if ($time=='day') $where=" DATE_FORMAT(ord.`order_date`,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d') ";
        if ($time=='week') $where=" WEEK(DATE_FORMAT(ord.`order_date`,'%Y-%m-%d'))=WEEK(DATE_FORMAT(NOW(),'%Y-%m-%d')) ";
        if ($time=='month') $where=" MONTH(DATE_FORMAT(ord.`order_date`,'%Y-%m-%d'))=MONTH(DATE_FORMAT(NOW(),'%Y-%m-%d')) ";
        if ($time=='year') $where=" YEAR(DATE_FORMAT(ord.`order_date`,'%Y-%m-%d'))=YEAR(DATE_FORMAT(NOW(),'%Y-%m-%d')) ";
        if ($time=='month' || $time == 'week') $where .=" and YEAR(DATE_FORMAT(ord.`order_date`,'%Y-%m-%d'))=YEAR(DATE_FORMAT(NOW(),'%Y-%m-%d')) ";
        
        $query = "SELECT SUM((ord.`order_subtotal`-ord.`order_discount`)/ord.`currency_exchange`) AS total_sum, SUM(ord.`buy_price_subtotal`/ord.`currency_exchange`) AS buy_total_sum
        FROM `#__jshopping_orders` AS ord  
        WHERE ".$where." AND ord.`order_status` IN (".$payment_status.")"; 
        
        $db->setQuery($query);
        return $db->loadAssoc();         
    }
    
    function getIncomeOrdersCount($payment_status, $filter = array())
    {
        $db = JFactory::getDBO(); 
        
        $query = "SELECT count(*)
        FROM `#__jshopping_orders`
        WHERE order_status IN (".$payment_status.")"; 
        if(isset($filter['from_date'])){
			$query .=" and `order_date`>= '".$filter['from_date']."'";
		}
		if(isset($filter['to_date'])){
			$query .=" and `order_date`<= '".$filter['to_date']."'";
		}
		
        $db->setQuery($query);
        return $db->loadResult();         
    }
    
    function getIncomeOrders($limitstart, $limit, $payment_status,$filter)
    {
        $db = JFactory::getDBO();
        
        $query = "SELECT *, (order_subtotal-order_discount)/currency_exchange AS total_sum, buy_price_subtotal/currency_exchange AS buy_total_sum
        FROM `#__jshopping_orders`
        WHERE order_status IN (".$payment_status.")";
        
		if(isset($filter['from_date'])){
			$query .=" and `order_date`>= '".$filter['from_date']."'";
		}
		if(isset($filter['to_date'])){
			$query .=" and `order_date`<= '".$filter['to_date']."'";
		}
		
		
		$query .= " ORDER BY order_date DESC";
        
        $db->setQuery($query, $limitstart, $limit);
        return $db->loadAssocList();         
    }
    
    function getIncomeProductsCount($payment_status,$filter)
    {
        $db = JFactory::getDBO(); 
        
        $query = "SELECT count(*) 
        FROM #__jshopping_order_item AS oi
        LEFT JOIN #__jshopping_orders AS o ON oi.order_id = o.order_id
        WHERE order_status IN (".$payment_status.")";
        if(isset($filter['from_date'])){
			$query .=" and `order_date`>= '".$filter['from_date']."'";
		}
		if(isset($filter['to_date'])){
			$query .=" and `order_date`<= '".$filter['to_date']."'";
		}
		
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    function getIncomeProducts($limitstart, $limit, $payment_status, $filter)
    {
        $db = JFactory::getDBO();        
        $query = "SELECT *, product_quantity * product_item_price AS total_price, 
                  product_quantity * product_buy_price AS total_buyprice, 
                  product_quantity *(product_item_price - product_buy_price) AS total_income
                  FROM #__jshopping_order_item AS oi
                  LEFT JOIN #__jshopping_orders AS o ON oi.order_id = o.order_id
                  WHERE order_status IN (".$payment_status.")";
        
		if (isset($filter['from_date'])){
			$query .=" and `order_date`>= '".$filter['from_date']."'";
		}
        
		if (isset($filter['to_date'])){
			$query .=" and `order_date`<= '".$filter['to_date']."'";
		}
		
        $query .= " ORDER BY oi.order_item_id DESC";        
        $db->setQuery($query, $limitstart, $limit);
        return $db->loadAssocList();
    }
}