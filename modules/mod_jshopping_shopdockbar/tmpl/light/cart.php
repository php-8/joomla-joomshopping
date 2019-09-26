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

if (!count($cart->products)) return;
?>
<div id="carousel-cart" class="touchcarousel minimal-light">
	<ul class="touchcarousel-container">
		<?php foreach($cart->products as $product) { ?>
		<li class="touchcarousel-item">
			<div class="item-block">
				<div class="delet"><a href="<?php print $product['href_delete']?>" onclick="return confirm('<?php print _JSHOP_CONFIRM_REMOVE?>')">X</a></div>
				<div class="item_image">
				<div class="qtynvg"><?php print $product['quantity']?></div>
					<a href="<?php print $product['href']?>">
						<img src = "<?php print $jshopConfig->image_product_live_path?>/<?php print $product['thumb_image'] ?  $product['thumb_image'] : $noimage?>" />
					</a>
				
				</div>

				<div class="item_name">
					<a href="<?php print $product['href']?>"><?php print $product['product_name']?></a>
				</div>
			</div>
		</li>
		<?php } ?>
	</ul>
</div>