<?php
/**
* @version      1.0 01.07.2017
* @author       Matblunic
* @package      Joomshopping
* @copyright    Copyright (C) 2017 matblunic.com. All rights reserved.
*/
    defined('_JEXEC') or die('Restricted access');
    error_reporting(error_reporting() & ~E_NOTICE);    
    if (!file_exists(JPATH_SITE.'/components/com_jshopping/jshopping.php')){
        JError::raiseError(500,"Please install component \"joomshopping\"");
    } 
    //require_once (dirname(__FILE__).'/helper.php');
    require_once (JPATH_SITE.'/components/com_jshopping/lib/factory.php'); 
    require_once (JPATH_SITE.'/components/com_jshopping/lib/jtableauto.php');
    require_once (JPATH_SITE.'/components/com_jshopping/tables/config.php'); 
    require_once (JPATH_SITE.'/components/com_jshopping/lib/functions.php');
    require_once (JPATH_SITE.'/components/com_jshopping/lib/multilangfield.php');
	require_once (JPATH_SITE.'/administrator/components/com_jshopping/models/categories.php');
	
	
    
	JSFactory::loadCssFiles();
    $lang = JFactory::getLanguage();
    if(file_exists(JPATH_SITE.'/components/com_jshopping/lang/' . $lang->getTag() . '.php')) 
        require_once (JPATH_SITE.'/components/com_jshopping/lang/'.  $lang->getTag() . '.php'); 
    else 
        require_once (JPATH_SITE.'/components/com_jshopping/lang/en-GB.php'); 
    JTable::addIncludePath(JPATH_SITE.'/components/com_jshopping/tables'); 
	
	$_categories = JSFactory::getModel("categories");
	
	$display_count = $params->get('display_count');
	
	if($display_count == 1){
		$countproducts = JshoppingModelCategories::getAllCatCountProducts();
	}
    
    $field_sort = $params->get('sort', 'id');
    $ordering = $params->get('ordering', 'asc');
	$load_jquery = $params->get('load_jquery');
	
    $request_id = JRequest::getInt('category_id');
	
    $jshopConfig = JSFactory::getConfig();

    require(JModuleHelper::getLayoutPath('mod_joomshopping_categories_menu'));
?>