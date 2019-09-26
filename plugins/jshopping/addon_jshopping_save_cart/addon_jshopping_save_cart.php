<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Garry
* @website https://joom-shopping.com
* @email info@joom-shopping.com
**/
defined('_JEXEC') or die('Restricted access');

class plgjshoppingAddon_Jshopping_Save_Cart extends JPlugin{

	public function __construct(&$subject, $config = array()){
		parent::__construct($subject, $config);
	}

	public function onAfterLoadShopParams(){
		include_once JPATH_SITE.'/components/com_jshopping/addons/addon_jshopping_save_cart/JshSCHelper.php'; 
		$params = JshSCHelper::AP();
		if (isset($params['email_notification'])
			&& $params['email_notification']
			&& isset($params['notification_after'])
			&& $params['notification_after']>0
		) 
		JshSCHelper::sendList();
	}

}