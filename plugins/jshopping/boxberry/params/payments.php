<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Nevigen.com
* @website https://nevigen.com/
* @email support@nevigen.com
* @copyright Copyright © Nevigen.com. All rights reserved.
* @license Proprietary. Copyrighted Commercial Software
* @license agreement https://nevigen.com/license-agreement.html
**/

defined('_JEXEC') or die;

class JFormFieldPayments extends JFormField {

	public $type = 'payments';

	protected function getInput(){
		require_once JPATH_SITE.'/components/com_jshopping/lib/factory.php'; 

		return JHTML::_( 'select.genericlist', JTable::getInstance('PaymentMethod', 'jshop')->getAllPaymentMethods(0), $this->name.'[]', 'class="inputbox" multiple="multiple" size="3"', 'payment_id', 'name', empty($this->value) ? '' : $this->value );
	}

}