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
<span class="btn btn-info quickorder" onclick="quickOrder.openForm(this,<?php echo $product->category_id ?>,<?php echo $product->product_id ?>)">
	<?php echo JText::_('PLG_JSHOPPINGPRODUCTS_QUICKORDER_LINK') ?>
</span>