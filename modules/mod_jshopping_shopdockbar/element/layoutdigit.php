<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Dmitry Stashenko
* @website http://nevigen.com/
* @email support@nevigen.com
* @copyright Copyright © Nevigen.com. All rights reserved.
* @license Proprietary. Copyrighted Commercial Software
* @license agreement http://nevigen.com/license-agreement.html
**/

defined('_JEXEC') or die;
require_once JPATH_SITE.'/components/com_jshopping/lib/factory.php';
require_once JPATH_SITE.'/components/com_jshopping/lib/functions.php';

class JFormFieldLayoutDigit extends JFormField {

	protected function getLabel(){
		return;
	}

	protected function getInput(){
		$key = $this->id;
		$name = $this->name;
		$value = $this->value;

		return '<input id="'.$key.'" type="hidden" value="'.$value.'" name="'.$name.'" class="" aria-invalid="false" />';
	}
}
?>
