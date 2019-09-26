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

if (!count($shopdockbar_compare)) return;
?>
<a class="comparebtn" href="<? echo SEFLink('index.php?option=com_jshopping&controller=shopdockbar&task=displayCompare', 1) ?>" title="<?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_COMPARE_LINK') ?>" ><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_COMPARE_LINK') ?></a>
<div id="carousel-compare" class="touchcarousel minimal-light">       
	<ul class="touchcarousel-container">
		<?php foreach($shopdockbar_compare as $product) { ?>
		<li class="touchcarousel-item">
			<div class="item-block">
				<div class="delet"><a href="<?php print $product->href_delete?>" onclick="return confirm('<?php print _JSHOP_CONFIRM_REMOVE?>')">X</a></div>
				<div class="item_image">
					<a href="<?php print $product->href?>">
						<img src = "<?php print $jshopConfig->image_product_live_path?>/<?php print $product->image ?  $product->image : $noimage?>" />
					</a>
				</div>
				<div class="item_name">
					<a href="<?php print $product->href?>"><?php print $product->name?></a>
				</div>
			</div>
		</li>
		<?php } ?>
	</ul> 	
</div>