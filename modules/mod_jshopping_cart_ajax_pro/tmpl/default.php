<?php 
/**
* @version      1.0.1 2.6.2017
* @author       MatBlunic
* @package      JoomShopping
* @copyright    Copyright (C) 2017 matblunic.com. All rights reserved.
*/
$document = JFactory::getDocument();
$document->addStyleSheet(Juri::base() . "modules/mod_jshopping_cart_ajax_pro/css/simple-line-icons.css",'text/css',"screen");
$document->addStyleSheet(Juri::base() . "modules/mod_jshopping_cart_ajax_pro/css/mat_jshopping_ajax.css",'text/css',"screen");
JHtml::script(Juri::base() . 'modules/mod_jshopping_cart_ajax_pro/js/mat_jshopping_ajax.js');
if($load_jquery == 0){JHtml::script(Juri::base() . 'modules/mod_jshopping_cart_ajax_pro/js/jquery-3.2.1.min.js');}
?>
<div>
	<div class="<?php if($position == 1){print "mat_cart_left";} else{print "mat_cart_right";} ?> icon-cart-top-wrapper">
		<div class="mat_cart_button <?php if($cart_style == 0){print "mat_cart_white";} else{print "mat_cart_black";}?>">
		<span class="mat_cart_counter"><?= count($cart_ins->products);?></span>
		</div>
	</div>
	<div class="mat_cart_top_list <?php if($cart_style_box == 1){print "mat_cart_box_light";}else{print "mat_cart_box_dark";}?>">
		<div class = "mat_cart_info_box">
			<div class="mat_cart_close">
				<i class="mat_cart-cloase-button icon-close"></i>
			</div>
			<?php 
			$JoomConf = JSFactory::getConfig(); 
			$image_live_path = $JoomConf->image_product_live_path;
				$countprod = 0;
				$array_products = array();
				if(count($cart_ins->products)!= 0){
				foreach($cart_ins->products as $value){
					$array_products [$countprod] = $value;
			?>
			<div class = "mat_cart_prod col-xs-12">
				<div class="mat_cart_image_block col-md-4">
					<img src="<?php print $image_live_path ?>/
						<?php
							if ($array_products [$countprod]['thumb_image']){
								print $array_products [$countprod]['thumb_image'];}
							else{
								print  "noimage.gif";}
						?>"/>
				</div>
				<div class="mat_prod-cart-info col-md-8">
					<div class="mat_prod-cat-name">
						<?php print $array_products [$countprod]["product_name"]; ?>
					</div>
					<?php if ($display_cont =='1') {?>
					<div class="mat_prod-cat-quan">
						<?php print $array_products [$countprod]["quantity"] . " x"; ?>
					</div>
					<div class="mat_prod-cat-sum">
						<?php print formatprice($array_products [$countprod]["price"]); ?>
					</div>
					<?php } else{ ?>
					<div class="mat_prod-cat-quan">
					</div>
					<div class="mat_prod-cat-sum">
						<?php print formatprice($array_products [$countprod]["price"] * $array_products [$countprod]["quantity"]); ?>
					</div>
					<?php } ?>
				</div>
			</div>
				<?php $countprod++; } } else{?> <div class="mat_prod-cat-empty"><?php print JText::_('CART_EMPTY');?></div><?php } ?>
		</div>
		<?php 
		switch($cart_button){
			case 0 : $butoon_style = "btn-default";
			break;
			case 1 : $butoon_style = "btn-primary";
			break;
			case 2 : $butoon_style = "btn-success";
			break;
			case 3 : $butoon_style = "btn-warning";
			break;
			case 4 : $butoon_style = "btn-danger";
			break;
			case 5 : $butoon_style = "btn-mat-dark";
			break;
			case 6 : $butoon_style = "btn-mat-light";
			break;
		} ?>
		<div class="mat_control_cart_button col-xs-12">
			<div class="mat_go_to_cart col-xs-6">
				<a class="btn <?php print $butoon_style ?>" href = "<?php print SEFLink('index.php?option=com_jshopping&controller=cart&task=view', 1)?>"><i class="icon-basket"></i> <?php print JText::_('GO_TO_CART')?></a>
			</div>
			<div class="mat_total-price col-xs-6">
				<span id = "jshop_quantity_products"><?php print JText::_('SUM_TOTAL')?>:&nbsp;</span>
				<span id = "jshop_summ_product"><?php print formatprice($cart_ins->getSum(0,1))?></span>
			</div>
			<input type="hidden" name="curr" class="mat_curr" value="<?php print $currency ?>"/>
		</div>
	</div>
</div>

<!-- Pop Up Section!-->
<?php if($pop_up == 0){ ?>
<div class="mat_popup_load">
	<div class="mat_infinity_loader">
	</div>
	<div class="mat_popup_load_box <?php if($cart_style_box == 1){print "mat_cart_box_light";}else{print "mat_cart_box_dark";} ?>">
		<div class="mat_prod-cloase-button"><i class="icon-close"></i></div>
		<div class="mat_popup_title"><h2><?php print JText::_('CART_POPUP_TITLE') ?></h2></div>
		<div class="mat_popup_product">
			<?php 
				if(count($cart_ins->products)!= 0){
				foreach($cart_ins->products as $value){
					$array_products [$countprod] = $value;
			?>
					<div class = "mat_cart_prod col-xs-12">
						<div class="mat_cart_image_block col-md-4">
							<img src="<?php print $image_live_path ?>/
										<?php
											if ($array_products [$countprod]['thumb_image']){
												print $array_products [$countprod]['thumb_image'];}
											else{
												print  "noimage.gif";}
										?>"/>
						</div>
						<div class="mat_prod-cart-info col-md-8">
							<div class="mat_prod-cat-name">
								<?php print $array_products [$countprod]["product_name"]; ?>
							</div>
							<?php if ($display_cont =='1') {?>
							<div class="mat_prod-cat-quan">
								<?php print $array_products [$countprod]["quantity"] . " x"; ?>
							</div>
							<div class="mat_prod-cat-sum">
								<?php print formatprice($array_products [$countprod]["price"]); ?>
							</div>
							<?php } else{ ?>
								<div class="mat_prod-cat-quan">
								</div>
								<div class="mat_prod-cat-sum">
									<?php print formatprice($array_products [$countprod]["price"] * $array_products [$countprod]["quantity"]); ?>
								</div>
							<?php } ?>
						</div>
					</div>
				<?php $countprod++; } } else{?> <div class="mat_prod-cat-empty"><?php print JText::_('CART_EMPTY');?></div><?php } ?>
		</div>
		<div class="mat_popup_buttons">
			<div class="mat_go_to_cart col-xs-6">
				<a class="btn <?php print $butoon_style ?>" href = "<?php print SEFLink('index.php?option=com_jshopping&controller=cart&task=view', 1)?>"><i class="icon-basket"></i> <?php print JText::_('GO_TO_CART')?></a>
			</div>
		</div>
	</div>
</div>
<?php }?>