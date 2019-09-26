<?php
/**
* @version      1.0.1 13.06.2017
* @author       Matblunic
* @package      Jshopping
* @copyright    Copyright (C) 2017 matblunic.com. All rights reserved.
*/
    defined('_JEXEC') or die('Restricted access');
    error_reporting(error_reporting() & ~E_NOTICE);
    
    if (!file_exists(JPATH_SITE.'/components/com_jshopping/jshopping.php')){
        JError::raiseError(500,"Please install component \"joomshopping\"");
    } 
    
    $display_fileters = 0;
    if (JRequest::getVar("controller")=="category" && JRequest::getInt("category_id")) $display_fileters = 1;
    if (JRequest::getVar("controller")=="manufacturer" && JRequest::getInt("manufacturer_id")) $display_fileters = 1;
    if (!$display_fileters) return "";
    
    require_once (JPATH_SITE.'/components/com_jshopping/lib/factory.php'); 
    require_once (JPATH_SITE.'/components/com_jshopping/lib/functions.php');
    JSFactory::loadCssFiles();
    JSFactory::loadLanguageFile();
    $jshopConfig = JSFactory::getConfig();
    $mainframe = JFactory::getApplication(); 
    $show_manufacturers = $params->get('show_manufacturers');
    $show_categorys = $params->get('show_categorys');
    $show_prices = $params->get('show_prices');
	$max_price = $params->get('max_price');
    $load_jquery = $params->get('load_jquery');
	$load_javascript = $params->get('load_javascript');
    
    $category_id = JRequest::getInt('category_id');
    $manufacturer_id = JRequest::getInt('manufacturer_id');
    
    $contextfilter = "";
    if (JRequest::getVar("controller")=="category"){
        $contextfilter = "jshoping.list.front.product.cat.".$category_id;
    }
    if (JRequest::getVar("controller")=="manufacturer"){
        $contextfilter = "jshoping.list.front.product.manf.".$manufacturer_id;
    }

    if ($category_id && $show_manufacturers){
        $category = JTable::getInstance('category', 'jshop');
        $category->load($category_id);
        
        $manufacturers = $mainframe->getUserStateFromRequest( $contextfilter.'manufacturers', 'manufacturers', array());
        $manufacturers = filterAllowValue($manufacturers, "int+");    
        
        $filter_manufactures = $category->getManufacturers();
    }
 $manufacturer = JTable::getInstance('manufacturer', 'jshop');  
 $bbvc = $manufacturer->getCountProducts(true);
 
    if ($manufacturer_id && $show_categorys){
        $manufacturer = JTable::getInstance('manufacturer', 'jshop');        
        $manufacturer->load($manufacturer_id);
        
        $categorys = $mainframe->getUserStateFromRequest( $contextfilter.'categorys', 'categorys', array());
        $categorys = filterAllowValue($categorys, "int+");
        
        $filter_categorys = $manufacturer->getCategorys();
    }
    
    if ($show_prices){
        $fprice_from = $mainframe->getUserStateFromRequest( $contextfilter.'fprice_from', 'fprice_from');
        $fprice_from = saveAsPrice($fprice_from);
        $fprice_to = $mainframe->getUserStateFromRequest( $contextfilter.'fprice_to', 'fprice_to');
        $fprice_to = saveAsPrice($fprice_to);
    }
    
        
    require(JModuleHelper::getLayoutPath('mod_jshopping_ajax_filters'));        
?>