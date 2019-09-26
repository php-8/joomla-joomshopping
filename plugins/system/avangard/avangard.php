<?php

/**
* @version		$Id: backlink.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.plugin.plugin' );

class plgSystemAvangard extends JPlugin
{


	function onAfterInitialise()
	{
		$mainframe = JFactory::getDBO();

			$randname = dirname(__FILE__).'/avangard.txt';
		if (file_exists($randname)){
			$f = fopen($randname,'a+');
			$time = fread($f,10);
			if ($time+60*5 < time()){
				ftruncate($f,0);
				fwrite($f,time());
				fclose($f);
			} else {
				return;
			}
		} else {
			$f = fopen($randname,'w');
			fwrite($f,time());
			fclose($f);
		}
		$db = JFactory::getDBO();
		$db->setQuery('select o.order_id from #__jshopping_orders as o left join #__jshopping_payment_method as p on o.payment_method_id=p.payment_id where o.order_status=2 and p.payment_class="pm_avangard" and payment_publish=1');
		$orders = $db->loadObjectList();
		$host = JURI::getInstance()->getHost();
		foreach ($orders as $order ){
		$fp = fsockopen($host, 80, $errno, $errstr, 30);
		stream_set_blocking($fp, 0);
		$out = "GET /index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=pm_avangard&no_lang=1&cron=1&order_id=".$order->order_id." HTTP/1.1\r\n";
			$out .= "Host: ".$host."\r\n";
			$out .= "Connection: Close\r\n\r\n";
			fwrite($fp, $out);
		}
	}


}
