<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author joom-shopping.com
* @website https://joom-shopping.com/
* @email info@joom-shopping.com
* @copyright Copyright © All rights reserved.
* @license GNU GPL v3
**/

defined('_JEXEC') or die;
?>



<input type="submit"  value="<?php echo JText::_('PLG_JSHOPPINGPRODUCTS_QUICKORDER_LINK') ?>" onclick="quickOrder.openForm(this,<?php echo $view->category_id ?>,<?php echo $view->product->product_id ?>)">


