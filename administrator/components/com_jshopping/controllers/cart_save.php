<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Garry
* @website https://joom-shopping.com
* @email info@joom-shopping.com
**/
	defined('_JEXEC') or die;

	require_once JPATH_SITE.'/components/com_jshopping/addons/addon_jshopping_save_cart/JshSCHelper.php';
			

	class JshoppingControllerCart_save extends JControllerLegacy{
		

		public function sendMail(){
			$this->addon = new JshSCHelper();
			$user_id = $this->input->getInt('user_id');
			$api = new JshSCHelper();
			$api->sendList(null, null, $user_id);
			$this->setRedirect('index.php?option=com_jshopping&controller=users', _MESSAGE_SENT);
		} 
		
	}

?>