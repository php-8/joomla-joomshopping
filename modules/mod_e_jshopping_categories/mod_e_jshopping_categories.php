<?php
/**
* @version      3.0.1 20.02.2011
* @author       MAXXmarketing GmbH
** @author2		IceTheme
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
ini_set('display_errors','Off');

	 defined('_JEXEC') or die('Restricted access');
	    if( !defined('PhpThumbFactoryLoaded') ) {
		if(!defined('DS')){
    define('DS',DIRECTORY_SEPARATOR);
	}
	require_once dirname(__FILE__).DS.'libs'.DS.'phpthumb'.DS.'ThumbLib.inc.php';
		define('PhpThumbFactoryLoaded',1);
	} 
    if (!file_exists(JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS.'jshopping.php')){
        JError::raiseError(500,"Please install component \"joomshopping\"");
    } 
    require_once (dirname(__FILE__).DS.'helper.php');
    require_once (JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS."lib".DS."factory.php"); 
    require_once (JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS."lib".DS."jtableauto.php");
    require_once (JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS.'tables'.DS.'config.php'); 
    require_once (JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS."lib".DS."functions.php");
    require_once (JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS."lib".DS."multilangfield.php");
    $mainframe = JFactory::getApplication();
	$document = JFactory::getDocument();
	$style_module = $params->get('style_module');
	if ($style_module == 1) {
	if ($menu_view == accordion) {
	$tPath = JPATH_BASE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.$module->module.DS.'assets'.DS.'style_acc.css';
	if( file_exists($tPath) ){
		JHTML::stylesheet( 'templates/'.$mainframe->getTemplate().'/html/'.$module->module.'/assets/style_acc.css');
	}else{
		$document->addStyleSheet(JURI::base().'modules/mod_e_jshopping_categories/assets/style_acc.css');
	}
	} else{$tPath = JPATH_BASE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.$module->module.DS.'assets'.DS.'style.css';
	if( file_exists($tPath) ){
		JHTML::stylesheet( 'templates/'.$mainframe->getTemplate().'/html/'.$module->module.'/assets/style.css');
	}else{
		$document->addStyleSheet(JURI::base().'modules/mod_e_jshopping_categories/assets/style.css');
	}}
	}
    JSFactory::loadCssFiles();
	JSFactory::loadJsFiles();
	JSFactory::loadLanguageFile();
	$jshopConfig = JSFactory::getConfig();
    $lang = JFactory::getLanguage();
    if(file_exists(JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS . 'lang'. DS . $lang->getTag() . '.php')) 
        require_once (JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS . 'lang'. DS . $lang->getTag() . '.php'); 
    else 
        require_once (JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS . 'lang'.DS.'en-GB.php'); 
    
    JTable::addIncludePath(JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS.'tables'); 
    
    $field_sort = $params->get('sort', 'id');
    $ordering 	= $params->get('ordering', 'asc');
    $show_image = $params->get('show_image',0);
    $categories = jShopCategoriesHelperIce::getHtml($field_sort, $ordering,1, $params);
    require(JModuleHelper::getLayoutPath('mod_e_jshopping_categories'));

?>
<br>