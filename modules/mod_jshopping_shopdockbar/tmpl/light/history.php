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

if (!count($shopdockbar_history)) return;
?>
<div id="carousel-history" class="touchcarousel minimal-light">
	<ul class="touchcarousel-container">
		<?php foreach($shopdockbar_history as $product) { ?>
		<li class="touchcarousel-item">
			<div class="block_item">
				<div class="item_image">
					<a href="<?php print $product->product_link?>">
						<img src = "<?php print $jshopConfig->image_product_live_path?>/<?php print $product->product_thumb_image ? $product->product_thumb_image : $noimage?>" />
					</a>
				</div>
				<div class="item_name">
					<a href="<?php print $product->product_link?>"><?php print $product->name?></a>
				</div>
			</div>
		</li>
		<?php } ?>
	</ul>
</div>