<?php
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelRequests_availability extends JModelLegacy{
    
     function getAllRequests_availability($category_id = null, $product_id = null, $sent_email=0, $limitstart = null, $limit = null, $text_search = null, $result = "list", $vendor_id = 0, $order = null, $orderDir = null, $addonParams = null) {
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO(); 
        $where = "";
        if ($product_id) $where .= " AND pr_rew.product_id='".$db->escape($product_id)."' ";
        if ($vendor_id) $where .= " AND pr.vendor_id='".$db->escape($vendor_id)."' ";
        if($sent_email) {
            if($sent_email==2) $sent_email=0;
            $where .= " AND pr_rew.email_send='".$db->escape($sent_email)."' ";           
            }
        if($limit > 0) {
            $limit = " LIMIT " . $limitstart . " , " . $limit;
        }
        $where .= ($text_search) ? ( " AND CONCAT_WS('|',pr.`".$lang->get('name')."`,pr.`".$lang->get('short_description')."`,pr.`".$lang->get('description')."`, pr_rew.user, pr_rew.email ) LIKE '%".$db->escape($text_search)."%' " ) : ('');
        $ordering = 'pr_rew.id desc';
        
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }

        if ($category_id){
            $query = "SELECT pr.`".$lang->get('name')."` as product_name, pr.product_ean, pr_rew.* , pr_rew.`date` as dateadd, pr_rew.product_attr_id as product_attr ".((isset($addonParams['show_product_code']) && $addonParams['show_product_code'] == 1) ? ', prodAttr.`ean`' : '').
            "FROM #__jshopping_requests_availability_product as pr_rew
            LEFT JOIN #__jshopping_products as pr USING (product_id)
            LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat USING (product_id) ";
            if (isset($addonParams['show_product_code']) && $addonParams['show_product_code'] == 1){
                $query .= " LEFT JOIN `#__jshopping_products_attr` AS prodAttr ON prodAttr.`product_attr_id` = pr_rew.`product_attr_id` ";
            }
            $query .= "WHERE pr_cat.category_id = '" . $db->escape($category_id) . "' ".$where." ORDER BY ". $ordering ." ". $limit;
        }else {
            $query = "SELECT pr.`".$lang->get('name')."` as product_name, pr.product_ean, pr_rew.*, pr_rew.`date` as dateadd, pr_rew.product_attr_id as product_attr ".((isset($addonParams['show_product_code']) && $addonParams['show_product_code'] == 1) ? ', prodAttr.`ean`' : '').
            "FROM #__jshopping_requests_availability_product as pr_rew
            LEFT JOIN #__jshopping_products as pr USING (product_id) ";
            if (isset($addonParams['show_product_code']) && $addonParams['show_product_code'] == 1){
                $query .= " LEFT JOIN `#__jshopping_products_attr` AS prodAttr ON prodAttr.`product_attr_id` = pr_rew.`product_attr_id` ";
            }
            $query .= "WHERE 1 ".$where." ORDER BY ". $ordering ." ". $limit;
        }
        $db->setQuery($query);
        if ($result=="list"){
            return $db->loadObjectList();
        }else{
            $db->query();
            return $db->getNumRows();    
        }
    }
       
    function getProdNameById($id){
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();   
        $query = "select pr.`".$lang->get('name')."` as name from #__jshopping_products  as pr where pr.product_id = '$id' LIMIT 1";
        $db->setQuery($query); 
        return $db->loadResult(); 
    }
    
    function deleteRequests_availability($id){
        $db = JFactory::getDBO(); 
        $query = "DELETE FROM #__jshopping_requests_availability_product WHERE `id` = ".$id;
        $db->setQuery($query);
        return $db->query();
    }
}