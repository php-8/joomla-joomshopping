<?php
	/**
	* @package     Joomla.Site
	* @subpackage  com_users
	* @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
	* @license     GNU General Public License version 2 or later; see LICENSE.txt
	*/

defined('_JEXEC') or die;
$cookieLogin = $this->user->get('cookieLogin');
if ($this->user->get('guest') || !empty($cookieLogin))
{
	echo $this->loadTemplate('login');
}
else
{
	echo $this->loadTemplate('logout');
}