<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Garry
* @website https://joom-shopping.com
* @email info@joom-shopping.com
**/

    defined('_JEXEC') or die('Restricted access');
    
    $db = JFactory::getDbo();

    $db->setQuery("DELETE FROM `#__extensions` WHERE `element` = 'addon_jshopping_save_cart' AND `folder` = 'jshoppingadmin' AND `type` = 'plugin'");
    $db->query();

    $db->setQuery("DELETE FROM `#__extensions` WHERE `element` = 'addon_jshopping_save_cart' AND `folder` = 'jshoppingcheckout' AND `type` = 'plugin'");
    $db->query();    
	
	$db->setQuery("DELETE FROM `#__extensions` WHERE `element` = 'addon_jshopping_save_cart' AND `folder` = 'jshopping' AND `type` = 'plugin'");
    $db->query();  
        
    $db->setQuery('DROP TABLE IF EXISTS `#__jshopping_cart_for_user`');
    $db->query();
		
    jimport('joomla.filesystem.folder');
    foreach(array(
			'components/com_jshopping/addons/addon_jshopping_save_cart/',
            'plugins/jshoppingadmin/addon_jshopping_save_cart/',
			'plugins/jshopping/addon_jshopping_save_cart/',
            'plugins/jshoppingcheckout/addon_jshopping_save_cart/'
    ) as $folder){JFolder::delete(JPATH_ROOT.'/'.$folder);}
	
	jimport('joomla.filesystem.file');
	JFile::delete(JPATH_ROOT.'/administrator/components/com_jshopping/controllers/cart_save.php');